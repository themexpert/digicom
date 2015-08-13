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

			$query = $db->getQuery(true);
			$query->select('o.*')
				  ->from($db->quoteName('#__digicom_orders','o'))
				  ->where($db->quoteName('o.id').'='.intval($id))
				  ->where($db->quoteName('o.published').'='.'1');


			// $sql = "SELECT o.*"
			// 		." FROM #__digicom_orders o"
			// 		." WHERE o.id='".intval($id)."' AND o.published='1'"
			// ;
			$db->setQuery($query);
			$this->_order = $db->loadObject();

			$db->clear();
			$query = $db->getQuery(true);
			$query->select($db->quoteName('p').'.*')
				  ->select($db->quoteName('od.quantity'))
				  ->select($db->quoteName('od.package_type'))
				  ->select($db->quoteName('od.price', 'price'))
				  ->select($db->quoteName('od.userid'))
				  ->from($db->quoteName('#__digicom_products','p'))
				  ->from($db->quoteName('#__digicom_orders_details','od'))
				  ->where($db->quoteName('p.id').'='.$db->quoteName('od.productid'))
				  ->where($db->quoteName('od.orderid').'='.$db->quote($this->_order->id));
			$db->setQuery($query);
			$prods = $db->loadObjectList();


			// $sql = "SELECT p.*, od.package_type, od.amount_paid as price , od.userid";
			// $sql .= " FROM #__digicom_products as p, #__digicom_orders_details as od 
			// WHERE p.id=od.productid AND od.orderid='". $this->_order->id ."'";
			// $db->setQuery($sql);
			//$prods = $db->loadObjectList();

			$this->_order->products = $prods;
		}
		//print_r($this->_order);die;
		return $this->_order;
	}

}
