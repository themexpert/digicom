<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

/**
 * Form Field class for the Joomla Platform.
 * Field for assigning email_settings to groups for a given asset
 *
 * @see    JAccess
 * @since  11.1
 */
class JFormFieldSystemEmail extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'SystemEmail';


	/**
	 * Method to get the field input markup for Access Control Lists.
	 * Optionally can be associated with a specific component and section.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 * @todo:   Add access check.
	 */
	protected function getInput()
	{
		JHtml::_('bootstrap.tooltip');

		
		$conf = JFactory::getConfig();
		$editor = $conf->get('editor');
		$jeditor = JEditor::getInstance($editor);

		// Get the available user groups.
		$groups = $this->getEmailGroups();
		// Prepare output
		$html = array();

		// Description
		$html[] = '<p class="alert alert-info">' . JText::_('COM_DIGICOM_SETTINGS_SYSTEM_EMAIL_DESCRIPTION') . '</p>';

		// Begin tabs
		$html[] = '<div id="email_settings-sliders" class="tabbable tabs-left">';

		// Building tab nav
		$html[] = '<ul class="nav nav-pills">';

		foreach ($groups as $group)
		{
			// Initial Active Tab
			$active = "";

			if ($group->value == 'new_order')
			{
				$active = "active";
			}

			$html[] = '<li class="' . $active . '">';
			$html[] = '<a href="#email_setting-' . $group->value . '" data-toggle="tab">';
			$html[] = $group->text;
			$html[] = '</a>';
			$html[] = '</li>';
		}

		$html[] = '</ul>';

		$html[] = '<div class="tab-content">';

		// Start a row for each user group.
		foreach ($groups as $group)
		{
			// Initial Active Pane
			$active = "";

			if ($group->value == 'new_order')
			{
				$active = " active";
			}

			$html[] = '<div class="tab-pane' . $active . '" id="email_setting-' . $group->value . '">';
			$html[] = '<h3>'.$group->desc.'</h3>';

			//start control group
			$html[] = '<div class="control-group ">';

			$html[] = '<div class="control-label">';
			$html[] = '<label id="jform_subject-lbl" for="jform_subject" title="'.JText::_('COM_DIGICOM_SETTINGS_SYSTEM_EMAIL_SUBJECT_LABEL_DESC').'">';
			$html[] =  JText::_('COM_DIGICOM_SETTINGS_SYSTEM_EMAIL_SUBJECT_LABEL');
			$html[] =  '</label>';
			$html[] =  '</div>';
			
			$html[] =  '<div class="controls">';
			$html[] =  '<input type="text" class="input-xxlarge" value="'.$this->value[$group->value]['subject'].'" name="'.$this->name.'['. $group->value .'][subject]" size="60">';
			$html[] =  '</div>';

			$html[] =  '</div>';
			//end control group
			
			//start control group
			$html[] = '<div class="control-group ">';

			$html[] = '<div class="control-label">';
			$html[] = '<label id="jform_body-lbl" for="jform_body" title="'.JText::_('COM_DIGICOM_SETTINGS_SYSTEM_EMAIL_BODY_LABEL_DESC').'">';
			$html[] =  JText::_('COM_DIGICOM_SETTINGS_SYSTEM_EMAIL_BODY_LABEL');
			$html[] =  '</label>';
			$html[] =  '</div>';
			
			$html[] =  '<div class="controls">';
			//$html[] =  '<textarea name="'.$this->name.'['. $group->value .'][body]"></textarea>';
			$bodyname = $this->name.'['.$group->value.'][body]';
			$bodyvalue = $this->value[$group->value]['body'];
			$html[] =  $this->getEditor($bodyname,$bodyvalue,$this->form);
			$html[] =  '</div>';

			$html[] =  '</div>';
			//end control group

			$html[] = '</div>';
		}

		$html[] = '</div>'; //end tab content

		$html[] = '</div>'; //end tab

		$html[] = '<div class="alert">';
		$html[] = JText::_('COM_DIGICOM_SETTINGS_EMAIL_BOTTOM_NOTICE');
		$html[] = '</div>';

		return implode("\n", $html);
	}

	/**
	 * Get a list of the user groups.
	 *
	 * @return  array
	 *
	 * @since   11.1
	 */
	protected function getEmailGroups()
	{

		$emailtype = array(
			0 => 'new_order', // on place order to admin
			1 => 'process_order', // on processing order to customer
			2 => 'complete_order', // on complete order to customer
			3 => 'cancel_order' //on cancel order
		);

		$emailinfo = array(
			0 => 'subject',
			1 => 'body'
		);

		$options = new StdClass();
		foreach($emailtype as $key=>$email){
			$options->$email = new StdClass();
			$options->$email->text = JText::_('COM_DIGICOM_SETTINGS_EMAIL_'.strtoupper($email).'_LABEL');
			$options->$email->desc = JText::_('COM_DIGICOM_SETTINGS_EMAIL_'.strtoupper($email).'_DESC');
			$options->$email->value = $email;
		}

		return $options;
	}

	/**
	 * Method to get the field input markup for the editor area
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   1.6
	 */
	protected function getEditor($name,$value,$form)
	{

		$editor = new JFormFieldEditor();
		$editor->name = $name;
		$editor->value = $value;
		$editor->form = $form;
		return $editor->getInput();
	}
}
