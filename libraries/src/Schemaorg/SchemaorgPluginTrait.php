<?php

/**
 * Joomla! Content Management System
 *
 * @copyright  (C) 2020 Open Source Matters, Inc. <https://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\CMS\Schemaorg;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Form\FormHelper;

/**
 * Trait for component schemaorg plugins.
 *
 * @since  4.0.0
 */
trait SchemaorgPluginTrait
{
    /**
     * Add different parameter options to the transition view, we need when executing the transition
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
}
