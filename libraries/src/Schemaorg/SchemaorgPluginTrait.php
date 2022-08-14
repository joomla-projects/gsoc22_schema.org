<?php

/**
 * Joomla! Content Management System
 *
 * @copyright  (C) 2020 Open Source Matters, Inc. <https://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\CMS\Schemaorg;

use Joomla\CMS\Event\Table\AbstractEvent;
use Joomla\CMS\Form\Form;


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
     * @param   $schemaType Schema Type to add
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
     * @param   Form $form  Form to manipulate
     * @param   $schemaType Schema Type to add
     *
     * @return  boolean
     *
     * @since   4.0.0
     */
    protected function saveSchema(AbstractEvent $event, $form)
    {
        $context    = $event->getArgument('extension');
        $article    = $event->getArgument('article');
        $isNew    = $event->getArgument('isNew');
        $data    = $event->getArgument('data');

        //Check if $data has the form data
        if (isset($data['schema']) && \count($data['schema'])) {
            $db = $this->db;

            //Delete the existing row to add updated data
            if (!$isNew) {
                $res = $db->getQuery(true)
                ->delete($db->quoteName('#__schemaorg'))
                ->where('itemId = ' . $article->id);
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
            $result = $db->insertObject('#__schemaorg', $query);
        }
    }

    /**
     * updates schema form
     *
     * @param   Form $form  Form to manipulate
     * @param   $schemaType Schema Type to add
     *
     * @return  boolean
     *
     * @since   4.0.0
     */
    protected function updateSchemaForm($data)
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
}
