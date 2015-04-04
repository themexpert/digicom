<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
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
