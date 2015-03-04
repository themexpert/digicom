<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$app = JFactory::getApplication();
$form = $displayData->getForm();
$input = $app->input;
$component = $input->getCmd('option', 'com_digicom');
$saveHistory = JComponentHelper::getParams($component)->get('save_history', 0);

$fields0 = $displayData->get('fields') ?: array(
	'tags',
	'sticky',
	'language',
	'note'	
);

$fields = $displayData->get('fields') ?: array(
	array('category', 'catid'),
	array('parent', 'parent_id'),
	array('published', 'state', 'enabled'),
	'featured',
	'hide_public',
	'publish_up',
	'publish_down',
	'ordering',
	'access'
);
$fields2 = $displayData->get('fields') ?: array(
	'metatitle',
	'metakeywords',
	'metadescription'
);
$fields3 = $displayData->get('fields') ?: array(	
	'hits',
	'used',
	'version_note'
);

$hiddenFields = $displayData->get('hidden_fields') ?: array();
if (!$saveHistory)
{
	$hiddenFields[] = 'version_note';
}

$html = array();
$html[] = '<fieldset class="form-vertical">';
$html[] = '<div class="accordion" id="digicom-product">';

$html[] = '<div class="accordion-group">';
$html[] = '<div class="accordion-heading"><a class="accordion-toggle" data-toggle="collapse" data-parent="#digicom-product" href="#basic_option">'. JText::_('GENERAL') .'</a></div>';

$html[] = '<div id="basic_option" class="accordion-body collapse in">';
$html[] = '<div class="accordion-inner">';

foreach ($fields as $field)
{	
	$field = is_array($field) ? $field : array($field);
	foreach ($field as $f)
	{
		if ($form->getField($f))
		{
			if (in_array($f, $hiddenFields))
			{
				$form->setFieldAttribute($f, 'type', 'hidden');
			}

			$html[] = $form->renderField($f);
			break;
		}
	}
}
$html[] = '</div></div>';
$html[] = '</div>';

$html[] = '<div class="accordion-group">';
$html[] = '<div class="accordion-heading"><a class="accordion-toggle" data-toggle="collapse" data-parent="#digicom-product" href="#seo_option">'. JText::_('META_INFO') .'</a></div>';

$html[] = '<div id="seo_option" class="accordion-body collapse">';
$html[] = '<div class="accordion-inner">';

foreach ($fields2 as $field)
{	
	$field = is_array($field) ? $field : array($field);
	foreach ($field as $f)
	{
		if ($form->getField($f))
		{
			if (in_array($f, $hiddenFields))
			{
				$form->setFieldAttribute($f, 'type', 'hidden');
			}

			$html[] = $form->renderField($f);
			break;
		}
	}
}
$html[] = '</div></div>';
$html[] = '</div>';

$html[] = '<div class="accordion-group">';
$html[] = '<div class="accordion-heading"><a class="accordion-toggle" data-toggle="collapse" data-parent="#digicom-product" href="#stat_option">'. JText::_('STATS') .'</a></div>';

$html[] = '<div id="stat_option" class="accordion-body collapse">';
$html[] = '<div class="accordion-inner">';

foreach ($fields3 as $field)
{	
	$field = is_array($field) ? $field : array($field);
	foreach ($field as $f)
	{
		if ($form->getField($f))
		{
			if (in_array($f, $hiddenFields))
			{
				$form->setFieldAttribute($f, 'type', 'hidden');
			}

			$html[] = $form->renderField($f);
			break;
		}
	}
}
$html[] = '</div></div>';
$html[] = '</div>';



$html[] = '</div>';
$html[] = '</fieldset>';

echo implode('', $html);
