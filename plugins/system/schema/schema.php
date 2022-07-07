<?php

/**
 * @package     Joomla.Plugin
 *
 * @copyright   (C) 2020 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Event\View\DisplayEvent;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\EventInterface;
use Joomla\Event\SubscriberInterface;

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
			//'onAfterDisplay'                  => 'onAfterDisplay',
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

		// Check we are manipulating a valid form.
		if (!in_array($context, ['com_content.article']))
		{
			return true;
		}

		if (is_object($data))
		{
			$articleId = $data->id ?? 0;

			//Check if the form already has some data
			if (!isset($data->schema) && $articleId > 0)
			{
				// Load the table data from the database
				$db = $this->db;
				$query = $db->getQuery(true)
					->select('*')
					->from($db->quoteName('#__schemaorg'))
					->where('articleId = '.$articleId);
				$db->setQuery($query);
				$results = $db->loadAssoc();

				// Insert existing data into form fields
				$data->schema = [];
				if(is_array($results)||is_object($results)){
					foreach ($results as $k=>$v)
					{
						$data->schema[$k]=$v;
					}
				}
				else{
					//Insert article id as it is a hidden field
					$data->schema = [];
					$data->schema['articleId']=$articleId;
				}
			}
			else{
				//Insert article id as it is a hidden field
				$data->schema = [];
				$data->schema['articleId']=$articleId;
			}
		}
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
		
		// Check we are manipulating a valid form
		$context = $form->getName();

		if (!in_array($context, ['com_content.article']))
		{
			return true;
		}

		//Load the form fields
		FormHelper::addFormPath(__DIR__ . '/forms');
		$form->loadFile('schema');
		
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

		if (!in_array($context, ['com_content.article']))
		{
			return true;
		}

		$article = $event->getArgument('1');
		$isNew = $event->getArgument('2');
		$data = $event->getArgument('3');

		//Check if $data has the form data
		if (isset($data['schema']) && count($data['schema']))
		{
			$db = $this->db;

			//Delete the existing row to add updated data 
			if(!$isNew){
				$res=$db->getQuery(true)
				->delete($db->quoteName('#__schemaorg'))
				->where('articleId = '.$article->id);
				$db->setQuery($res);
				$result = $db->execute();
			}

			//Create object to insert data into database
			$query=new stdClass();
			foreach($data['schema'] as $k=>$v){
				$query->$k=$v;
			}
			$result=$db->insertObject('#__schemaorg', $query);
		}
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

		// $this->app->getDocument()->addScriptDeclaration('
		// 		alert("Script Added");
		// ');

	}

	/**
	 * Manipulate the generic list view
	 *
	 * @param   DisplayEvent    $event
	 *
	 * @since   4.0.0
	 */
	// public function onAfterDisplay(DisplayEvent $event)
	// {
	// 	$context = $event->getArgument('extension');

	// 	if (!$this->app->isClient('site') || $context != 'com_content.article') {
	// 		return;
	// 	}

	// 	$article=$event->getArgument('source');

	// 	$this->app->getDocument()->addScriptDeclaration('demo');

	// }
}
