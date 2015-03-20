<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 441 $
 * @lastmodified	$LastChangedDate: 2013-11-20 04:59:31 +0100 (Wed, 20 Nov 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license
*/

defined ('_JEXEC') or die ("Go away.");

class DigiComModelOrders extends JModelList
{


	function __construct () {
		parent::__construct();
	}


	function getlistOrders(){
		$input = JFactory::getApplication()->input;
		$search = $input->get('search','');
		$user = new DigiComSiteHelperSession();
		$db = JFactory::getDBO();
		if (empty ($this->_orders)) {
				
			$sql = "SELECT o.*, u.username"
					." FROM #__digicom_orders o, #__digicom_orders_details od, #__users u"
					." WHERE ".($search ? 'o.id = "'.$search.'" and ' : '')."o.userid=u.id and u.id=".$user->_user->id." group by o.id order by o.order_date desc";
			$this->_orders = $this->_getList($sql);
			
			//print_r($this->_orders);die;
			
		}
		return $this->_orders;
	}


}

