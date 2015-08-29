<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class DigiComViewOrders extends JViewLegacy {

	function display($tpl = null)
	{
		$customer = new DigiComSiteHelperSession();
		$app = JFactory::getApplication();
		$input = $app->input;
		$Itemid = $input->get("Itemid", 0);
		// $return = base64_encode( JURI::getInstance()->toString() );
		// if($customer->_user->id < 1)
		// {
		// 	$app->Redirect(JRoute::_('index.php?option=com_users&view=login&return='.$return.'&Itemid='.$Itemid, false));
		// 	return true;
		// }

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
