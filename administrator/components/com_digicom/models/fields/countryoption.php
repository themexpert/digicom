<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_digicom
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

/**
 * Supports a modal article picker.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_digicom
 * @since       1.6
 */
include_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'sajax.php');
class JFormFieldCountryOption extends JFormField {
 
	protected $type = 'CountryOption';
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
		
		if (!self::$initialised)
		{
			// Load the modal behavior script.
			JHtml::_('behavior.modal');

			// Include jQuery
			JHtml::_('jquery.framework');
			$eu = self::getEU();
			//print_r($eu);die;
			// Build the script.
			$script = array();
			$script[] = '	function changeProvince_cb(province_option) {';
			$script[] = '		document.getElementById("jform_province").innerHTML = province_option;';
			$script[] = '	}';

			$script[] = '	function changeProvince() {';
			$script[] = '		var country;';
			$script[] = '		country = document.getElementById("jform_country").value';
			$script[] = '		var euc = Array(\''.implode ("','" , $eu).'\')';
			$script[] = '		var flag = 0;';
			$script[] = '		for (i = 0; i< euc.length; i++) ';
			$script[] = '			if (country == euc[i]) flag = 1;';
			$script[] = '		x_phpchangeProvince(country, changeProvince_cb);';
			$script[] = '	}';

			$script[] = '	function changeCity_cb(city_option) {';
			$script[] = '		document.getElementById("jform_city").innerHTML = city_option;';
			$script[] = '	}';
			
			$script[] = '	function changeCity() {';
			$script[] = '		var province;';
			$script[] = '		province = document.getElementById("jform_sel_province").value;';
			$script[] = '		x_phpchangeCity(province, changeCity_cb);';
			$script[] = '	}';

			// Add the script to the document head.
			//JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));
			
			self::$initialised = true;
		}

		ob_start();
		echo '<script>';
		sajax_show_javascript();
		echo '</script>';
		$output = ob_get_contents();
		ob_end_clean();
		$html []= $output;
		return implode($html);
	}
	
	function getItems(){
		$db = JFactory::getDBO();
		$query = "SELECT country"
			. "\n FROM #__digicom_states"
			. "\n GROUP BY country"
			. "\n ORDER BY country ASC";
		$db->setQuery( $query );
		$countries = $db->loadObjectList();
		
		$return = array();
		//print_r($items);die;
		foreach($countries as $key=>$item){
			$return[] = JHTML::_( 'select.option', $item->country, $item->country );
		}
		
		return $return;
		
	}
	
	function getEU(){
		$db = JFactory::getDBO();
		$sql = "SELECT * FROM #__digicom_states WHERE eumember='1'";
		$db->setQuery($sql);
		$eucs = $db->loadObjectList();
		$eu   = array();
		foreach ($eucs as $euc) $eu[] = $euc->country;
		return $eu;
	}
}