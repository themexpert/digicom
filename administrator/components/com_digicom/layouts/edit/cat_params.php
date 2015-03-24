<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$app = JFactory::getApplication();
$form = $displayData->getForm();

// JLayout for standard handling of metadata fields in the administrator content edit screens.
$fieldSets = $form->getFieldsets('params');
$html = array();
$html []= '<div class="form-horizontal">';
foreach ($fieldSets as $name => $fieldSet) :
	foreach ($form->getFieldset($name) as $field)
	{
		$html[] = $field->renderField();
	} 
endforeach;
$html [] = '</div>';
echo implode('', $html);