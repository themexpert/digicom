<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 341 $
 * @lastmodified	$LastChangedDate: 2013-10-10 14:28:28 +0200 (Thu, 10 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport( 'joomla.utilities.date' );

class JFormFieldDigicategslist extends JFormField{

	protected $type = 'digicategslist';

	protected function getInput(){
		$params = $this->form->getValue('params');
		$category_id = 0;
		if(isset($params->category_id)){
			$category_id = $params->category_id;
		}

		$db = JFactory::getDBO();
		$date = new JDate();
		$sql = "select id, title, name from #__digicom_categories WHERE published=1 order by title asc";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadAssocList();
		$return  = '<select id="jform_params_category_id" name="jform[params][category_id]">';
		$return .= 		'<option value="0">-- Select Category --</option>';
		foreach($result as $key=>$values){
			$selected = '';
			if($category_id == $values["id"]){
				$selected = ' selected="selected" ';
			}
			$return .= '<option value="'.$values["id"].'" '.$selected.'>'.$values["name"].'</option>';
		}
		$return .= '</select>';
		return $return;
	}
}

?>