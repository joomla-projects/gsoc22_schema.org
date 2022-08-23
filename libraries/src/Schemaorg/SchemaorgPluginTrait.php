<?php

/**
 * Joomla! Content Management System
 *
 * @copyright  (C) 2020 Open Source Matters, Inc. <https://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\CMS\Schemaorg;

use Joomla\CMS\Form\Form;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

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
    protected function addSchemaType(Form $form, $type, $value)
    {
        $schemaType = $form->getField('schemaType', 'schema');
        $schemaType->addOption($type, ['value' => $value]);
    }

    /**
     * Saves unfiltered JSON data of the form fields in database
     *
     * @param   $event  EventInterface
     * @param   $form   Name of the form
     *
     * @return  boolean
     *
     * @since   4.0.0
     */
    protected function saveSchema($event, $form)
    {
        $context    = $event->getArgument('extension');
        $table    = $event->getArgument('table');
        $isNew    = $event->getArgument('isNew');
        $registry    = $event->getArgument('data');

        $data = $registry->toArray();

        //Check if $data has the form data
        if (isset($data['schema']) && \count($data['schema'])) {
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
            $query->itemId = $data['schema']['itemId'];
            $query->context = $context;
            $query->schemaType = $data['schema']['schemaType'];

            $schema = new \stdClass();
            foreach ($data['schema'][$form] as $k => $v) {
                $schema->$k = $v;
            }

            $query->schemaForm = json_encode($schema);
            $query->schema = json_encode($this->cleanupSchema($schema));
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
    protected function updateSchemaForm(Registry $data)
    {
        if (\is_object($data)) {
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

                // Insert existing data into form fields
                $data->schema = [];
                if (\is_array($results) || \is_object($results)) {
                    foreach ($results as $k => $v) {
                        $data->schema[$k] = $v;
                    }
                } else {
               //Insert article id as it is a hidden field
                    $data->schema = [];
                    $data->schema['itemId'] = $itemId;
                }
            } else {
                //Insert article id as it is a hidden field
                $data->schema = [];
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
        if (\is_object($data)) {
            //Create object to insert data into database
            $schema = new \stdClass();
            foreach ($data as $k => $v) {
                    $emp = true;
                foreach ($v as $i => $j) {
                    $emp = false;
                    $em = true;
                    foreach ($j as $y => $z) {
                        $em = false;
                        if (!empty($z)) {
                            $em = true;
                            break;
                        }
                    }
                    if (!empty($j) && $em) {
                        $emp = true;
                        break;
                    }
                }

                if (!empty($v) && $emp) {
                    $schema->$k = $v;
                }
            }

            $registrySchema = new Registry($schema);
            $newSchema = $this->cleanupIndividualSchema($registrySchema);
            return $newSchema;
        }

        return false;
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

        return true;
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
                } elseif ($min < 60) {
                    $newDuration = "PT" . $min . "M";
                } else {
                    return;
                }
                $schema->set($durationKey, $newDuration);
            }
        }
            $schema->toArray();

            return $schema;
    }
}
