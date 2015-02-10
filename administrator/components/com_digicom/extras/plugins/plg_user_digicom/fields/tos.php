<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  User.profile
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('checkbox');

/**
 * Provides input for TOS
 *
 * @package     Joomla.Plugin
 * @subpackage  User.profile
 * @since       2.5.5
 */
class JFormFieldTos extends JFormFieldCheckbox
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  2.5.5
	 */
	protected $type = 'Tos';

	/**
	 * Method to get the field label markup.
	 *
	 * @return  string  The field label markup.
	 *
	 * @since   2.5.5
	 */
	protected function getLabel()
	{
		// Set required to true as this field is not displayed at all if not required.
		$this->required = true;

		// Add CSS and JS for the TOS field
//		$doc = JFactory::getDocument();
//		$css = "#jform_profile_tos {width: 18em; margin: 0 !important; padding: 0 2px !important;}
//				#jform_profile_tos input {margin:0 5px 0 0 !important; width:10px !important;}
//				#jform_profile_tos label {margin:0 15px 0 0 !important; width:auto;}
//				";
//		$doc->addStyleDeclaration($css);
//		JHtml::_('behavior.modal');

		// Add the label text and closing tag.
		//$label .= '>' . $link . '<span class="star">&#160;*</span></label>';
		$label = '<label><span class="star">&#160;*</span></label>';

		return $label;
	}
	
	protected function getInput()
	{
		$label = '';

		if ($this->hidden)
		{
			return $label;
		}
		
		// Get the label text from the XML element, defaulting to the element name.
		$text = $this->element['label'] ? (string) $this->element['label'] : (string) $this->element['name'];
		$text = $this->translateLabel ? JText::_($text) : $text;

		// If a description is specified, use it to build a tooltip.
		if (!empty($this->description))
		{
			$label .= ' title="'
				. htmlspecialchars(
				trim($text, ':') . '::' . ($this->translateDescription ? JText::_($this->description) : $this->description),
				ENT_COMPAT, 'UTF-8'
			) . '"';
		}
		
		// Build the class for the label.
		$class = !empty($this->description) ? 'hasTip' : '';
		$class = $class . ' required';
		$class = !empty($this->labelClass) ? $class . ' ' . $this->labelClass : $class;

		// Add the opening label tag and main attributes attributes.
//		$label .= '<label id="' . $this->id . '-lbl" for="' . $this->id . '" class="' . $class . '"';

		
		require_once (JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');
		$tosarticle = $this->element['article'] ? (int) $this->element['article'] : 1;
		$link = $text . ' <a title="" href="'.JRoute::_(ContentHelperRoute::getArticleRoute($tosarticle)).'" target="_blank"><i class="fa fa-external-link"></i></a>';
		
		
		// Initialize some field attributes.
		$class     = !empty($this->class) ? ' class="' . $this->class . '"' : '';
		$disabled  = $this->disabled ? ' disabled' : '';
		$value     = !empty($this->default) ? $this->default : '1';
		$required  = $this->required ? ' required aria-required="true"' : '';
		$autofocus = $this->autofocus ? ' autofocus' : '';
		$checked   = $this->checked || !empty($this->value) ? ' checked' : '';

		// Initialize JavaScript field attributes.
		$onclick  = !empty($this->onclick) ? ' onclick="' . $this->onclick . '"' : '';
		$onchange = !empty($this->onchange) ? ' onchange="' . $this->onchange . '"' : '';

		// Including fallback code for HTML5 non supported browsers.
		JHtml::_('jquery.framework');
		JHtml::_('script', 'system/html5fallback.js', false, true);

//		<label class="checkbox-inline">
//			<input type="checkbox" id="inlineCheckbox1" value="option1"> 1
//		</label>
		
		return '<div class="checkbox">
  <label><input type="checkbox" name="' . $this->name . '" id="' . $this->id . '" value="'
			. htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '"' . $class . $checked . $disabled . $onclick . $onchange
			. $required . $autofocus . ' />'.$link.'</label>
</div>';
	}
}
