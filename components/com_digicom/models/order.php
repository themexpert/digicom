<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class DigiComModelOrder extends JModelItem
{
	var $_id = null;

	function __construct () {
		parent::__construct();
		$id = JRequest::getVar('id', 0);
		$this->setId((int)$id);
	}

	function setId($id) {
		$this->_id = $id;
		$this->_order = null;
	}

	function getOrder($id = 0) {
		$custommer = new DigiComSiteHelperSession();

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

			$sql = "SELECT p.id, p.name, p.images,p.language, p.catid, od.package_type, od.amount_paid as price , od.userid";
			$sql .= " FROM #__digicom_products as p, #__digicom_orders_details as od WHERE p.id=od.productid AND od.orderid='". $this->_order->id ."'";
			$db->setQuery($sql);
			$prods = $db->loadObjectList();

			$this->_order->products = $prods;
		}
		//print_r($this->_order);die;
		return $this->_order;
	}

}
