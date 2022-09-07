<?php

/**
 * Joomla! Content Management System
 *
 * @copyright  (C) 2020 Open Source Matters, Inc. <https://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\CMS\Schemaorg;

use Joomla\CMS\Form\Form;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use stdClass;

/**
 * Trait for component schemaorg plugins.
 *
 * @since  4.0.0
 */
trait SchemaorgPluginTrait
{
    /**
     * Add a new option to schemaType list field in schema form
     *
     * @param   Form $form  Form to manipulate
     * @param   $type Schema Type to add
     * @param   $value Value of the type
     *
     * @return  boolean
     *
     * @since   4.0.0
     */
    protected function addSchemaType(Form $form, $type)
    {
        $schemaType = $form->getField('schemaType', 'schema');
        $schemaType->addOption($type, ['value' => $type]);
    }

    /**
     * Saves unfiltered and filtered JSON data of the form fields in database
     *
     * @param   $event  EventInterface
     * @param   $form   Name of the form
     *
     * @return  boolean
     *
     * @since   4.0.0
     */
    protected function saveSchema($event)
    {
        $context    = $event->getArgument('extension');
        $table    = $event->getArgument('table');
        $isNew    = $event->getArgument('isNew');
        $registry    = $event->getArgument('data');

        $data = $registry->toArray();

        //Check if $data has the form data
        if (isset($data['schema']) && count($data['schema'])) {
            $db = $this->db;

            //Delete the existing row to add updated data
            if (!$isNew) {
                $res = $db->getQuery(true)
                ->delete($db->quoteName('#__schemaorg'))
                ->where('itemId = ' . $table->id);
                $db->setQuery($res)->execute();
            }

            //Create object to insert data into database
            $query = new \stdClass();
            $query->itemId = $table->id;
            $query->context = $context;
            $query->schemaType = $data['schema']['schemaType'];
            $form = $data['schema']['schemaType'];

            if (!empty($data['schema'][$form])) {
                $schema = new \stdClass();

                foreach ($data['schema'][$form] as $k => $v) {
                    $schema->$k = $v;
                }

                $query->schemaForm = json_encode($schema);
                $newSchema = new Registry($schema);
                $query->schema = json_encode($this->cleanupSchema($newSchema));
            } else {
                $query->schemaForm = false;
                $query->schema = false;
            }
            $result = $db->insertObject('#__schemaorg', $query);
        }
    }

    /**
     * updates schema form
     *
     * @param   $data
     *
     * @return  boolean
     *
     * @since   4.0.0
     */
    protected function updateSchemaForm($data)
    {
        if (is_object($data)) {
            $itemId = $data->id ?? 0;

            //Check if the form already has some data
            if (!isset($data->schema) && $itemId > 0) {
                // Load the table data from the database
                $db = $this->db;
                $query = $db->getQuery(true)
                ->select('*')
                ->from($db->quoteName('#__schemaorg'))
                ->where('itemId = ' . $itemId);
                $db->setQuery($query);
                $results = $db->loadAssoc();

                $schemaType = $results['schemaType'];
                $data->schema = [];
                $data->schema['schemaType'] = $schemaType;

                $form = json_decode($results['schemaForm'], true);
                if ($form) {
                    // Insert existing data into form fields
                    foreach ($form as $key => $val) {
                        if (is_array($val)) {
                            foreach ($val as $i => $j) {
                                if (is_array($j)) {
                                    foreach ($j as $l => $m) {
                                        $data->schema[$schemaType][$key][$i][$l] = $m;
                                    }
                                } else {
                                    $data->schema[$schemaType][$key][$i] = $j;
                                }
                            }
                        } else {
                                $data->schema[$schemaType][$key] = $val;
                        }
                    }
                } else {
               //Insert article id as it is a hidden field
                    $data->schema['itemId'] = $itemId;
                }
            } else {
                //Insert article id as it is a hidden field
                $data->schema['itemId'] = $itemId;
            }
        }
    }

    /**
     * Removes empty field and changes time duration to ISO format in schema form
     *
     * @param   $data JSON object of the data stored in schema form
     *
     * @return  object
     *
     * @since   4.0.0
     */
    protected function cleanupSchema($data)
    {
        if (is_object($data)) {
            //Create object to insert data into database
            $newSchema = new Registry();
            $newSchema->set('@context', 'https://schema.org');

            $schema = new Registry($this->cleanupIndividualSchema($data));
            if (is_object($schema)) {
                foreach ($schema as $key => $val) {
                    if (is_array($val) && !empty($val['@type'])) {
                        $tmp = $this->cleanupJSON($val);
                        if (!empty($tmp)) {
                            $newSchema->set($key, $tmp);
                        }
                    } elseif (!empty($val)) {
                        $newSchema->set($key, $val);
                    }
                }
            }
            $image = $schema->get('image');
            if (!empty($image)) {
                $img = HTMLHelper::_('cleanImageURL', $image);
                $newSchema->set('image', $img->url);
            }

            return $newSchema;
        }
    }

        /**
     * Push the schema to the head tag in the frontend
     *
     * @param   $schema JSON Schema
     *
     * @return  boolean
     *
     * @since   4.0.0
     */
    protected function pushSchema()
    {
        $itemId = $this->app->getInput()->getInt('id');

        if ($itemId > 0) {
            // Load the table data from the database
            $db = $this->db;
            $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__schemaorg'))
            ->where('itemId = ' . $itemId);
            $db->setQuery($query);
            $results = $db->loadAssoc();
            $schema = $results['schema'];

            if (!empty($schema)) {
                $wa = $this->app->getDocument()->getWebAssetManager();
                $wa->addInlineScript($schema, ['position' => 'after'], ['type' => 'application/ld+json']);
            }
        }
    }

    /**
     *  To add plugin specific functions
     *
     *  @param   Registry $schema Schema form
     *
     *  @return  boolean
     */
    protected function cleanupIndividualSchema(Registry $schema)
    {
    }

    /**
     *  To change hour and mins to duration ISO format
     *
     *  @param   Registry $schema Schema form
     *  @param   Array $durationKeys Keys with duration fields
     *
     *  @return  boolean
     */
    protected function changeDurationFormat(Registry $schema, $durationKeys)
    {
        foreach ($durationKeys as $durationKey) {
            $duration = $schema->get($durationKey, []);
            if (is_object($duration)) {
                $registry = new Registry($duration);
                $min = $registry->get('min');
                $hour = $registry->get('hour');

                if ($hour && $min && $min < 60) {
                    $newDuration = "PT" . $hour . "H" . $min . "M";
                } elseif ($hour) {
                    $newDuration = "PT" . $hour . "H";
                } elseif ($min && $min < 60) {
                    $newDuration = "PT" . $min . "M";
                } else {
                    $newDuration = false ;
                }

                if ($newDuration) {
                    $schema->set($durationKey, $newDuration);
                } else {
                    $schema->remove($durationKey);
                }
            }
        }
        $schema->toArray();

        return $schema;
    }

    /**
     *  To create an array from repeatable text field data
     *
     *  @param   Registry $schema Schema form
     *  @param   Array $durationKeys Keys with duration fields
     *
     *  @return  array
     */
    protected function convertToArray(Registry $schema, $repeatableFields)
    {
        foreach ($repeatableFields as $repeatableField) {
            $field = new Registry($schema->get($repeatableField, []));
            $arr = array();
            if (is_object($field)) {
                foreach ($field as $i => $j) {
                    if (is_object($j)) {
                        foreach ($j as $k => $m) {
                            if (!empty($m)) {
                                array_push($arr, $m);
                            }
                        }
                    } else {
                        array_push($arr, $j);
                    }
                }
                if (!empty($arr)) {
                    $schema->set($repeatableField, $arr);
                } else {
                    $schema->remove($repeatableField);
                }
            }
        }

        return $schema;
    }

    /**
     *  To cleanup the JSON with @type attribute
     *
     *  @param   Array $schema
     *
     *  @return  object
     */
    protected function cleanupJSON($schema)
    {
        $arr = array();
        $emty = true;
        foreach ($schema as $k => $v) {
            if ($v != '') {
                $arr[$k] = $v;
                if ($k != '@type') {
                    $emty = false;
                }
            }
        }
        if (!$emty) {
            return $arr;
        }
    }
}
