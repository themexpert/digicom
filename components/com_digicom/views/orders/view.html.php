<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 341 $
 * @lastmodified	$LastChangedDate: 2013-10-10 14:28:28 +0200 (Thu, 10 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

defined ('_JEXEC') or die ("Go away.");

jimport ("joomla.application.component.view");

class DigiComViewOrders extends DigiComView {

	function display($tpl = null)
	{
		$mainframe=JFactory::getApplication();
		$Itemid = JRequest::getInt("Itemid", 0);
		$ga = JRequest::getInt("ga", 0);
		if($ga){
			require_once JPATH_COMPONENT_SITE.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'google.php';
		}

		$orders = $this->get('listOrders');
		$configs = $this->_models['config']->getConfigs();
		$database = JFactory::getDBO();
		$db = $database;
		$sql = "select params
				from #__modules
				where `module`='mod_digicom_cart'";
		$db->setQuery($sql);
		$d = $db->loadResult();
		$d = explode ("\n", $d);
		$categ_digicom = '';

		foreach ($d as $i => $v)
		{
			$x = explode ("=", $v);
			if ($x[0] == "digicom_category")
			{
				$categ_digicom = $x[1];
				break;
			}
		}

		/* Get Cart items */
		$cart = $this->getModel('Cart');
		$customer = new DigiComSessionHelper();
		$cartitems = $cart->getCartItems($customer, $configs);
	   
		if ( $categ_digicom != '' )
		{
			$sql = "select id from #__digicom_categories where title like '".$categ_digicom."' or name like '".$categ_digicom."'";
			$database->setQuery($sql);
			$id = $database->loadResult();
			$cat_url = JRoute::_("index.php?option=com_digicom&view=categories&cid=" . $id."&Itemid=".$Itemid);
		}
		else
		{
			$cat_url = JRoute::_("index.php?option=com_digicom&view=categories&cid=0"."&Itemid=".$Itemid);
		}

		$this->assignRef('orders', $orders);
		$this->assign("configs", $configs);
		$this->assign("ga", $ga);
		$this->assignRef('cartitems', $cartitems);
		$this->assign("caturl", $cat_url);

		parent::display($tpl);
	}

	function showOrder($tpl = null)
	{
		$db = JFactory::getDBO();
		$order = $this->_models['order']->getOrder();
		$this->assign("order", $order);
		$configs = $this->_models['config']->getConfigs();
		$this->assign("configs", $configs);
		parent::display($tpl);
	}

	function showReceipt($tpl = null)
	{
		$db = JFactory::getDBO();
		$order = $this->_models['order']->getOrder();
		$this->assign("order", $order);
		$configs = $this->_models['config']->getConfigs();
		$this->assign("configs", $configs);
		$customer = new DigiComSessionHelper();
	   	$this->assign("customer", $customer);
		parent::display($tpl);
	}
}
