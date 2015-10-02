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
$form = $this->form;

$fieldSets = $form->getFieldsets('attribs');
if (empty($fieldSets))
{
	return;
}

//echo JHtml::_('bootstrap.addTab', 'digicomTab', 'params', JText::_('COM_DIGICOM_PRODUCT_ATTR_TITLE', true));

foreach ($fieldSets as $name => $fieldSet)
{

  if (!empty($fieldSet->label))
  {
    $label = JText::_($fieldSet->label, true);
  }
  else
  {
    $label = strtoupper('JGLOBAL_FIELDSET_' . $name);
    if (JText::_($label, true) == $label)
    {
      $label = strtoupper($app->input->get('option') . '_' . $name . '_FIELDSET_LABEL');
    }
    $label = JText::_($label, true);
  }

  echo JHtml::_('bootstrap.addTab', 'digicomTab', 'attrib-' . $name, $label);

  if (isset($fieldSet->description) && trim($fieldSet->description))
  {
    echo '<p class="alert alert-info">' . $this->escape(JText::_($fieldSet->description)) . '</p>';
  }

  $this->fieldset = $name;
  echo JLayoutHelper::render('joomla.edit.fieldset', $this);

  echo JHtml::_('bootstrap.endTab');
}

//echo JHtml::_('bootstrap.endTab');
