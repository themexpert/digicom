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
 * Supports a modal article picker.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_digicom
 * @since       1.6
 */
class JFormFieldStateOption extends JFormField {

	protected $type = 'StateOption';
	/**
	 * The initialised state of the document object.
	 *
	 * @var    boolean
	 * @since  1.6
	 */
	protected static $initialised = false;

	// getLabel() left out

	protected function getInput()
	{
		$html = array();
		$attr = '';

		// Initialize some field attributes.
		$attr .= !empty($this->class) ? ' class="' . $this->class . '"' : '';
		$attr .= !empty($this->size) ? ' size="' . $this->size . '"' : '';
		$attr .= $this->multiple ? ' multiple' : '';
		$attr .= $this->required ? ' required aria-required="true"' : '';
		$attr .= $this->autofocus ? ' autofocus' : '';

		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ((string) $this->readonly == '1' || (string) $this->readonly == 'true' || (string) $this->disabled == '1'|| (string) $this->disabled == 'true')
		{
			$attr .= ' disabled="disabled"';
		}

		// Initialize JavaScript field attributes.
		$attr .= $this->onchange ? ' onchange="' . $this->onchange . '"' : '';

		// Get the field options.
		$options = (array) $this->getItems();
		$value = json_decode($this->value,true);
		// Create a read-only list (no name) with a hidden input to store the value.
		if ((string) $this->readonly == '1' || (string) $this->readonly == 'true')
		{
			$html[] = JHtml::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $value, $this->id);
			$html[] = '<input type="hidden" name="' . $this->name . '" value="' . htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '"/>';
		}
		else
		// Create a regular list.
		{
			$html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $value, $this->id);
		}

		return implode($html);
	}

	function getItems(){
		$configs = JComponentHelper::getComponent('com_digicom')->params;
    $db 	= JFactory::getDBO();
		$query = $db->getQuery(true)
					->select('country')
					->from('#__digicom_states')
					->group('country')
					->order('country ASC');
		$db->setQuery($query);
		$countries = $db->loadObjectList();

		$default = '';

		## Initialize array to store dropdown options ##
		$options = array();
	    $options[] = JHTML::_('select.option', '', JText::_('COM_DIGICOM_SELECT_COUNTRY_TITLE'));

		#Top Countries#
		$topcountries = $configs->get('topcountries', array());

	    $options[] = JHTML::_('select.optgroup', JText::_('COM_DIGICOM_SELECT_FAVORITE_COUNTRY_TITLE'));

		if ( count( $topcountries ) > 0 ) {

			foreach($topcountries as $key=>$value) :
				## Create $value ##
				$options[] = JHTML::_('select.option', $value, $value);
			endforeach;

		}else{

			$options[] = JHTML::_('select.option', 'United-States', 'United-States');
			$options[] = JHTML::_('select.option', 'Canada', 'Canada');
			$options[] = JHTML::_('select.option', 'Bangladesh', 'Bangladesh');

		}


		$options[] = JHTML::_('select.optgroup', '');
		$options[] = JHTML::_('select.optgroup', JText::_('COM_DIGICOM_SELECT_COUNTRY_TITLE'));

		foreach($countries as $key=>$value) :
			## Create $value ##
			$options[] = JHTML::_('select.option', $value->country, $value->country);
		endforeach;


		return $options;
	}

}
