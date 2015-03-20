<?php
/**
 * @package		DigiCom
 * @copyright	Copyright (c)2010-2015 ThemeXpert
 * @license 	GNU General Public License version 3, or later
 * @author 		ThemeXpert http://www.themexpert.com
 * @since 		1.0.0
 */

defined('JPATH_BASE') or die;

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
		$comparams = JComponentHelper::getComponent('com_digicom');
		$params = $comparams->params;
		$country = $params->get('country','United-States');
		$db = JFactory::getDBO();
		$query = "select state FROM #__digicom_states where country='".$country."'";
		$db->setQuery( $query );
		$countries = $db->loadObjectList();
		
		$return = array();
		//print_r($items);die;
		if($this->value){
			foreach($countries as $key=>$item){
				$return[] = JHTML::_( 'select.option', $item->state, $item->state );
			}
			return $return;
		}else{
			return $return[] = JHTML::_( 'select.option', '', JText::_('HELPERSELECTCOUNTY') );
		}
	}

}