<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class DigiComModelOrders extends JModelList
{
	function __construct () {
		parent::__construct();
	}

	function getlistOrders(){
		$input = JFactory::getApplication()->input;
		$search = $input->get('search','');
		$custommer = new DigiComSiteHelperSession();
		//print_r($custommer);die;
		$db = JFactory::getDBO();
		if (empty ($this->_orders)) {
				
			$sql = "SELECT o.*"
					." FROM #__digicom_orders o, #__digicom_orders_details od"
					." WHERE ".($search ? 'o.id = "'.$search.'" and ' : '')."o.userid=".$custommer->_customer->id." group by o.id order by o.id desc";
			$this->_orders = $this->_getList($sql);
			
			//print_r($this->_orders);die;
			
		}
		return $this->_orders;
	}


}

