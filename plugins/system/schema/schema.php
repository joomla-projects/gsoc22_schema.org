<?php

/**
 * @package     Joomla.Plugin
 *
 * @copyright   (C) 2020 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt

 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 */

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\EventInterface;
use Joomla\Event\SubscriberInterface;
use Joomla\CMS\Event\AbstractEvent;
use Joomla\CMS\Plugin\PluginHelper;

/**
 * Schemaorg System Plugin
 *
 * @since  4.0.0
 */
class PlgSystemSchema extends CMSPlugin implements SubscriberInterface
{
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
     * Returns an array of events this subscriber will listen to.
     *
     * @return  array
     *
     * @since   4.0.0
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'onBeforeCompileHead'                  => 'onBeforeCompileHead',
            'onContentPrepareData'            => 'onContentPrepareData',
            'onContentPrepareForm'            => 'onContentPrepareForm',
            'onContentBeforeSave'            => 'onContentBeforeSave',
        ];
    }

    /**
     * Runs on content preparation
     *
     * @param   EventInterface  $event  The event
     *
     * @since   4.0.0
     *
     */
    public function onContentPrepareData(EventInterface $event)
    {
        $context = $event->getArgument('0');
        $data = $event->getArgument('1');

        // Check if we are manipulating a valid form.
        if (!in_array($context, ['com_content.article'])) {
            return true;
        }

        $event   = AbstractEvent::create(
            'onSchemaPrepareData',
            [
                'subject' => $data,
            ]
        );

        PluginHelper::importPlugin('schemaorg');
        $this->app->getDispatcher()->dispatch('onSchemaPrepareData', $event);

        return true;
    }

    /**
     * The form event.
     *
     * @param   EventInterface  $event  The event
     *
     * @since   4.0.0
     */
    public function onContentPrepareForm(EventInterface $event)
    {
        $form = $event->getArgument('0');

        // Check if we are manipulating a valid form
        $context = $form->getName();

        if (!in_array($context, ['com_content.article'])) {
            return true;
        }

        //Load the form fields
        FormHelper::addFormPath(__DIR__ . '/forms');
        $form->loadFile('schema');

        $event   = AbstractEvent::create(
            'onSchemaPrepareForm',
            [
                'subject' => $form,
            ]
        );

        PluginHelper::importPlugin('schemaorg');
        $this->app->getDispatcher()->dispatch('onSchemaPrepareForm', $event);

        return true;
    }

/**
     * Saves form field data in the database
     *
     * @param   EventInterface $event
     *
     * @return  boolean
     *
     */
    public function onContentBeforeSave(EventInterface $event)
    {
        $context = $event->getArgument('0');

        if (!in_array($context, ['com_content.article'])) {
            return true;
        }

        $article = $event->getArgument('1');
        $isNew = $event->getArgument('2');
        $data = $event->getArgument('3');

        PluginHelper::importPlugin('schemaorg');
        // $this->app->getDispatcher()->dispatch('onSchemaBeforeSave', $event);

        $this->app->getDispatcher()->dispatch(
            'onSchemaBeforeSave',
            AbstractEvent::create(
                'onSchemaBeforeSave',
                [
                    'subject'       => $this,
                    'extension'     => $context,
                    'article'       => $article,
                    'isNew'         => $isNew,
                    'data'          => $data,
                ]
            )
        );

        return true;
    }


    /**
     * This event is triggered before the framework creates the Head section of the Document
     *
     * @since   4.0.0
     */
    public function onBeforeCompileHead()
    {
        $context = $this->app;
        if (!$this->app->isClient('Site') && $context === 'com_content.article') {
            return;
        }
    }
}
