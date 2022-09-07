<?php

/**
 * @package     Joomla.Plugin
 *
 * @copyright   (C) 2022 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt

 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 */

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Schemaorg\SchemaorgPluginTrait;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Event\AbstractEvent;
use Joomla\Registry\Registry;

/**
 * Schemaorg Plugin
 *
 * @since  4.0.0
 */
class PlgSchemaorgRecipe extends CMSPlugin
{
    use SchemaorgPluginTrait;

    /**
     * @var    \Joomla\Database\DatabaseDriver
     *
     */
    protected $db;

    /**
     * Load the language file on instantiation.
     *
     * @var    boolean
     * @since  4.0.0
     */
    protected $autoloadLanguage = true;

    /**
     * Loads the CMS Application for direct access
     *
     * @var   CMSApplicationInterface
     * @since 4.0.0
     */
    protected $app;

    /**
     *  Update existing schema form with data from database
     *
     *  @param   $data  The form to be altered.
     *
     *  @return  boolean
     */
    public function onSchemaPrepareData(AbstractEvent $event)
    {
        $data = $event->getArgument('subject');
        $da = $this->updateSchemaForm($data);
        $event->setArgument('subject', $data);
        return true;
    }

     /**
     *  Add a new option to the schema type in the article editing page
     *
     *  @param   Form  $form  The form to be altered.
     *
     *  @return  boolean
     */
    public function onSchemaPrepareForm(Form $form)
    {
        if ($form->getName() != 'com_content.article') {
            return;
        }

        $this->addSchemaType($form, 'Recipe');

        //Load the form fields
        FormHelper::addFormPath(__DIR__ . '/forms');
        $form->loadFile('schema');
    }

    /**
     *  Saves the schema to the database
     *
     *  @param   AbstractEvent $event
     *
     *  @return  boolean
     */
    public function onSchemaAfterSave(AbstractEvent $event)
    {
        $this->saveSchema($event);
    }

    /**
     *  Fetches schema and pushes to the head tag in the frontend
     *
     *  @param   AbstractEvent $event
     *
     *  @return  boolean
     */
    public function onSchemaBeforeCompileHead()
    {
        $this->pushSchema();
    }



    /**
     *  To add plugin specific functions
     *
     *  @param   Registry $schema Schema form
     *
     *  @return  boolean
     */
    public function cleanupIndividualSchema(Registry $schema)
    {
        if (is_object($schema)) {
            $schema = $this->changeDurationFormat($schema, ['cookTime', 'prepTime']);
        }
        if (is_object($schema)) {
            $schema = $this->convertToArray($schema, ['recipeIngredient']);
        }
        return $schema;
    }
}
