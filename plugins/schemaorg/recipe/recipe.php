<?php

/**
 * @package     Joomla.Plugin
 *
 * @copyright   (C) 2022 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Form\FormHelper;


/**
 * Schemaorg Plugin
 *
 * @since  4.0.0
 */
class PlgSchemaorgRecipe extends CMSPlugin
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
	 *  Add a new option to the schema type in the article editing page
	 *
	 *  @param   Form  $form  The form to be altered.
	 *
	 *  @return  boolean
	 */
	public function onSchemaPrepareForm(Form $form)
	{
		if ($form->getName() != 'com_content.article')
		{
			return;
		}

		$schemaType=$form->getField('schemaType','schema');
		$schemaType->addOption('Recipe', ['value' => 'recipe']);

		//Load the form fields
		FormHelper::addFormPath(__DIR__ . '/forms');
		$form->loadFile('schema');

	}

}
