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

jimport ("joomla.aplication.component.model");


class DigiComModelOrder extends DigiComModel
{
	var $_licenses;
	var $_license;
	var $_id = null;

	function __construct () {
		parent::__construct();
		$cids = JRequest::getVar('orderid', 0, '', 'array');
		$this->setId((int)$cids[0]);
	}

	function setId($id) {
		$this->_id = $id;
		$this->_order = null;
	}

	function getlistOrders(){
		$user = new DigiComSessionHelper();
		$db = JFactory::getDBO();
		if (empty ($this->_orders)) {
				
			$sql = "SELECT o.*, u.username"
					." FROM #__digicom_orders o, #__digicom_orders_details od, #__users u"
					." WHERE o.userid=u.id and u.id=".$user->_user->id." group by o.id order by o.order_date desc";
			$this->_orders = $this->_getList($sql);
			
			//print_r($this->_orders);die;
			
		}
		return $this->_orders;
	}

	function getOrder($id = 0) {
		if (empty ($this->_order)) {
			$db = JFactory::getDBO();
			if ($id > 0) $this->_id = $id;
			else $id = $this->_id;
			
			$sql = "SELECT o.*"
					." FROM #__digicom_orders o"
					." WHERE o.id='".intval($id)."' AND o.published='1'"
			;
			$db->setQuery($sql);
			$this->_order = $db->loadObject();
			
			$sql = "SELECT p.id, p.name, p.catid, od.package_type, od.amount_paid as price FROM #__digicom_products as p, #__digicom_orders_details as od WHERE p.id=od.productid AND od.orderid='". $this->_order->id ."'";
			$db->setQuery($sql);
			$prods = $db->loadObjectList();
			
			$this->_order->products = $prods;
		}
		//print_r($this->_order);die;
		return $this->_order;
	}

}

