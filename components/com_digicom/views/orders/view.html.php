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

class DigiComViewOrders extends JViewLegacy {

	function display($tpl = null)
	{
		$customer = new DigiComSiteHelperSession();
		$app = JFactory::getApplication();
		$input = $app->input;
		$Itemid = $input->get("Itemid", 0);
		if($customer->_user->id < 1)
		{
			$app->Redirect(JRoute::_('index.php?option=com_digicom&view=login&returnpage=orders&Itemid='.$Itemid, false));
			return true;
		}
		
		$orders = $this->get('listOrders');
		$configs = JComponentHelper::getComponent('com_digicom')->params;
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
		$cart = JModelLegacy::getInstance( 'Cart', 'DigiComModel' );
		
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

		$this->assignRef('cartitems', $cartitems);
		$this->assign("caturl", $cat_url);

		$template = new DigiComSiteHelperTemplate($this);
		$template->rander('orders');

		parent::display($tpl);
	}
}
