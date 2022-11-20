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
use Joomla\Event\SubscriberInterface;

/**
 * Schemaorg Plugin
 *
 * @since  4.0.0
 */
class PlgSchemaorgOrganization extends CMSPlugin implements SubscriberInterface
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
     * The name of the supported name to check against
     *
     * @var   string
     * @since 4.0.0
     */
    protected $supportFunctionality = 'core.state';

    /**
     * The name of the schema form
     *
     * @var   string
     * @since 4.0.0
     */
    protected $pluginName = 'Organization';

    /**
     * Returns an array of events this subscriber will listen to.
     *
     * @return  array
     *
     * @since   4.0.0
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'onSchemaPrepareData'                  => 'onSchemaPrepareData',
            'onSchemaPrepareForm'                  => 'onSchemaPrepareForm',
            'onSchemaAfterSave'                    => 'onSchemaAfterSave',
            'onSchemaBeforeCompileHead'            => 'pushSchema',
        ];
    }

    /**
     *  Update existing schema form with data from database
     *
     *  @param   $data  The form to be altered.
     *
     *  @return  boolean
     */
    public function onSchemaPrepareData(AbstractEvent $event)
    {
        $context = $event->getArgument('context');
        if (!$this->isSupported($context) || !$this->isSchemaSupported($event)) {
            return false;
        }
        $this->updateSchemaForm($event);
        return true;
    }

     /**
     *  Add a new option to the schema type in the article editing page
     *
     *  @param   Form  $form  The form to be altered.
     *
     *  @return  boolean
     */
    public function onSchemaPrepareForm(AbstractEvent $event)
    {
        $form = $event->getArgument('subject');
        $context = $form->getName();
        if (!$this->isSupported($context)) {
            return false;
        }
        $this->addSchemaType($event);
        //Load the form fields
        FormHelper::addFormPath(__DIR__ . '/forms');
        $form->loadFile('schema');
        return true;
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
        $data = $event->getArgument('data')->toArray();
        $form = $data['schema']['schemaType'];

        if ($form != $this->pluginName) {
            return false;
        }
        $this->storeSchemaToStandardLocation($event);
        return true;
    }
}
