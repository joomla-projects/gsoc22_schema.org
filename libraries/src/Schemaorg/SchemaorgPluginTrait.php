<?php

/**
 * Joomla! Content Management System
 *
 * @copyright  (C) 2020 Open Source Matters, Inc. <https://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\CMS\Schemaorg;

use Joomla\CMS\Event\GenericEvent;
use Joomla\CMS\Event\Table\AbstractEvent;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Database\ParameterType;
use Joomla\Event\EventInterface;
use Joomla\Registry\Registry;

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
     * @param   EventInterface $event
     *
     * @return  boolean
     *
     * @since   4.0.0
     */
    protected function addSchemaType(EventInterface $event)
    {
        $form = $event->getArgument('subject');
        $name = $this->pluginName;

        if (!$form || !$name) {
            return false;
        }
        $schemaType = $form->getField('schemaType', 'schema');
        $schemaType->addOption($name, ['value' => $name]);
        return true;
    }

    /**
     * Saves unfiltered and filtered JSON data of the form fields in database
     *
     * @param   AbstractEvent $event Must have 'extension, 'table', 'isNew' and 'data'
     *
     * @return  boolean
     *
     * @since   4.0.0
     */
    protected function storeSchemaToStandardLocation(GenericEvent $event)
    {
        $context    = $event->getArgument('extension');
        $table    = $event->getArgument('table');
        $isNew    = $event->getArgument('isNew');
        $registry    = $event->getArgument('data');

        $data = $registry->toArray();

        //Check if $data has the form data
        if (!isset($data['schema']) || !count($data['schema'])) {
            return false;
        } else {
            $db = $this->db;

            //Delete the existing row to add updated data
            if (!$isNew) {
                $res = $db->getQuery(true)
                ->delete($db->quoteName('#__schemaorg'))
                ->where($db->quoteName('itemId') . '= :itemId')
                ->bind(':itemId', $table->id, ParameterType::INTEGER)
                ->where($db->quoteName('context') . '= :context')
                ->bind(':context', $context, ParameterType::STRING);

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
        return true;
    }

    /**
     * Add data to form fields from existing data in the database
     *
     * @param   $data
     *
     * @return  boolean
     *
     * @since   4.0.0
     */
    public function updateSchemaForm(EventInterface $event)
    {
        $data = $event->getArgument('subject');
        $context = $event->getArgument('context');

        if (!is_object($data)) {
            return false;
        } else {
            $itemId = $data->id ?? 0;

            //Check if the form already has some data
            if (!isset($data->schema) && $itemId > 0) {
                $db = $this->db;

                $query = $db->getQuery(true)
                ->select('*')
                ->from($db->quoteName('#__schemaorg'))
                ->where($db->quoteName('itemId') . '= :itemId')
                ->bind(':itemId', $itemId, ParameterType::INTEGER)
                ->where($db->quoteName('context') . '= :context')
                ->bind(':context', $context, ParameterType::STRING);

                $results = $db->setQuery($query)->loadAssoc();

                if (empty($results)) {
                    return false;
                }

                $schemaType = $results['schemaType'];
                $data->schema['schemaType'] = $schemaType;
                $data->schema['schema'] = json_encode(json_decode($results['schema']), JSON_PRETTY_PRINT);

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
        return true;
    }

    /**
     * Call update schema function only if the plugin is not listed in allowed or forbidden
     *
     * @param   EventInterface $event
     *
     * @return  boolean
     *
     * @since   4.0.0
     */
    public function isSchemaSupported(EventInterface $event){
        $data = $event->getArgument('subject');
        $context = $event->getArgument('context');

        if (!is_object($data)) {
            return false;
        } else {
            $itemId = $data->id ?? 0;
            if (!isset($data->schema) && $itemId > 0) {
                $db = $this->db;

                $query = $db->getQuery(true)
                ->select('*')
                ->from($db->quoteName('#__schemaorg'))
                ->where($db->quoteName('itemId') . '= :itemId')
                ->bind(':itemId', $itemId, ParameterType::INTEGER)
                ->where($db->quoteName('context') . '= :context')
                ->bind(':context', $context, ParameterType::STRING);

                $results = $db->setQuery($query)->loadAssoc();

                if (empty($results)) {
                    return false;
                }

                $schemaType = $results['schemaType'];

                if ($this->pluginName != $schemaType) {
                    return false;
                }
            }
        }
        return true;
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
    public function pushSchema()
    {
        $itemId = (int) $this->app->getInput()->getInt('id');
        $option = $this->app->getInput()->get('option');
        $view = $this->app->getInput()->get('view');
        $context = $option . '.' . $view;

        if ($itemId > 0) {
            // Load the table data from the database
            $db = $this->db;
            $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__schemaorg'))
            ->where($db->quoteName('itemId') . '= :itemId')
            ->bind(':itemId', $itemId, ParameterType::INTEGER)
            ->where($db->quoteName('context') . '= :context')
            ->bind(':context', $context, ParameterType::STRING);

            $db->setQuery($query);
            $results = $db->loadAssoc();

            if (!$results || !$this->isSupported($context) || $results['schemaType'] != $this->pluginName) {
                return false;
            }

            $schema = $results['schema'];

            if (!empty($schema)) {
                $wa = $this->app->getDocument()->getWebAssetManager();
                $wa->addInlineScript($schema, ['position' => 'after'], ['type' => 'application/ld+json']);
            }
        }
        return true;
    }

    /**
     *  To normalize duration to ISO format
     *
     *  @param   Registry $schema Schema form
     *  @param   Array $durationKeys Keys with duration fields
     *
     *  @return  boolean
     */
    protected function normalizeDurationsToISO(Registry $schema, array $durationKeys)
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

        return $schema;
    }

    /**
     *  To create an array from repeatable text field data
     *
     *  @param   Registry $schema Schema form
     *  @param   Array $repeatableFields Names of all the Repeatable fields
     *
     *  @return  array
     */
    protected function convertToArray(Registry $schema, array $repeatableFields)
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
     *  To clean up the date fields in
     *
     *  @param   Registry $schema Schema form
     *  @param   Array $dateKeys Keys with date fields
     *
     *  @return  boolean
     */
    protected function cleanupDate(Registry $schema, array $dateKeys)
    {
        foreach ($dateKeys as $dateKey) {
            $date = $schema->get($dateKey);
            if (!empty($date)) {
                $date = Factory::getDate($date)->format('Y-m-d');
                $schema->set($dateKey, $date);
            }
        }
        return $schema;
    }

    /**
     *  To cleanup sub-JSON with @type attribute eg: NutritionInformation
     *
     *  @param   Array $schema
     *
     *  @return  object
     */
    protected function cleanupJSON(array $schema)
    {
        $arr = array();
        $emty = true;
        foreach ($schema as $k => $v) {
            if (is_array($v) && !empty($v['@type'])) {
                $tmp = $this->cleanupJSON($v);
                if (!empty($tmp)) {
                    $arr[$k] = $tmp;
                }
            } elseif ($v != '') {
                $arr[$k] = $v;
                if ($k != '@type') {
                    $emty = false;
                }
            }
        }
        if ($arr['@type'] == 'ImageObject' && !empty($arr['url'])) {
            $img = HTMLHelper::_('cleanImageURL', $arr['url']);
            $arr['url'] = $img->url;
        }
        if (!$emty) {
            return $arr;
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
        //Write your code for extra filteration
        return $schema;
    }

    /**
     * Get the schemaorg for a given ID
     *
     * @param   int|null $schemaorgId ID of the schemaorg
     *
     * @return  CMSObject|boolean  Object on success, false on failure.
     *
     * @since   4.0.0
     */
    protected function getSchemaorg(int $schemaorgId = null)
    {
        $schemaorgId = !empty($schemaorgId) ? $schemaorgId : $this->app->input->getInt('schemaorg_id');

        if (is_array($schemaorgId)) {
            return false;
        }

        return $this->app->bootComponent('com_schemaorg')
            ->getMVCFactory()
            ->createModel('Schemaorg', 'Administrator', ['ignore_request' => true])
            ->getItem($schemaorgId);
    }

    /**
     * Check if the current plugin should execute schemaorg related activities
     *
     * @param   string  $context
     *
     * @return boolean
     *
     * @since   4.0.0
     */
    protected function isSupported($context)
    {
        if (!$this->checkAllowedAndForbiddenlist($context) || !$this->checkExtensionSupport($context, $this->supportFunctionality)) {
            return false;
        }

        $parts = explode('.', $context);

        // We need at least the extension + view for loading the table fields
        if (count($parts) < 2) {
            return false;
        }

        $component = $this->app->bootComponent($parts[0]);

        if (
            !$component instanceof SchemaorgServiceInterface
            || !$component->supportSchemaFunctionality($this->supportFunctionality, $context)
        ) {
            return false;
        }
        return true;
    }

    /**
     * Check if the context is listed in the allowed of forbidden lists and return the result.
     *
     * @param   string $context Context to check
     *
     * @return  boolean
     */
    protected function checkAllowedAndForbiddenlist($context)
    {
        $allowedlist = \array_filter((array) $this->params->get('allowedlist', []));
        $forbiddenlist = \array_filter((array) $this->params->get('forbiddenlist', []));

        if (!empty($allowedlist)) {
            foreach ($allowedlist as $allowed) {
                if ($context === $allowed) {
                    return true;
                }
            }

            return false;
        }

        foreach ($forbiddenlist as $forbidden) {
            if ($context === $forbidden) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if the context supports a specific functionality.
     *
     * @param   string  $context       Context to check
     * @param   string  $functionality The functionality
     *
     * @return  boolean
     */
    protected function checkExtensionSupport($context, $functionality)
    {
        $parts = explode('.', $context);

        $component = $this->app->bootComponent($parts[0]);

        if (
            !$component instanceof SchemaorgServiceInterface
            || !$component->supportSchemaFunctionality($functionality, $context)
        ) {
            return false;
        }
        return true;
    }
}
