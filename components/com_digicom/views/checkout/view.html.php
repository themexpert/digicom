<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 377 $
 * @lastmodified	$LastChangedDate: 2013-10-21 12:02:56 +0200 (Mon, 21 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

defined ('_JEXEC') or die ("Go away.");

class DigiComViewCheckout extends JViewLegacy
{
	
	function display($tpl = null)
	{
		
		$app = JFactory::getApplication();
		$pg_plugin 	= JRequest::getVar("processor", "", "", "string");
		$Itemid 	= JRequest::getInt("Itemid", "0");
		$order_id 	= JRequest::getInt("order_id", "0");
		$dispatcher = JDispatcher::getInstance();
		$plugin = JPluginHelper::importPlugin( 'digicom_pay', $pg_plugin );
		$session 	= JFactory::getSession();
		$customer 	= new DigiComSiteHelperSession();
		$configs 	= JComponentHelper::getComponent('com_digicom')->params;
		$order 		=$this->get('Order');
		//print_r($order);die;
		$params 	= json_decode($order->params,true);
		
		$items 		= $params['products'];
		
		/*
		 * $items
		 * [0]
		 *	->name
		 *	->discount
		 *	->quantity
		 *	->price
		 *	->promo
		 *	  amount = price - promo
		 */
		$vars = new stdClass();


		$vars->order_id = $params['order_id'];
		$vars->custom = $customer->_customer->id;
		$vars->user_firstname = $customer->_customer->firstname;
		$vars->user_lastname = $customer->_customer->lastname;
		$vars->user_id = JFactory::getUser()->id;
		$vars->user_email = $customer->_user->email;
		$vars->item_name = '';
		
		for($i=0; $i<count($items)-2; $i++)
		{
			$vars->item_name.= $items[$i]['name'] . ', ';
		}
		$vars->item_name = substr($vars->item_name, 0, strlen($vars->item_name)-2);
		
		$vars->cancel_return = JRoute::_(JURI::root()."index.php?option=com_digicom&Itemid=".$Itemid."&task=cart.cancel&processor={$pg_plugin}", true, 0);
		$vars->return = $vars->url = $vars->notify_url = JRoute::_(JURI::root()."index.php?option=com_digicom&task=cart.processPayment&processor={$pg_plugin}&order_id=".$params['order_id']."&sid=".$customer->_sid, true, false);
		$vars->currency_code = $configs->get('currency','USD');
		$vars->amount = $items[-2]['taxed'];//+$items[-2]['shipping'];
		
		// Triggre plugin event
		JPluginHelper::importPlugin('digicom_pay');
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onSendPayment', array(& $params));
		$html = $dispatcher->trigger('onTP_GetHTML', array($vars));

		if (!isset($html[0])) {
			$html[0] = '';
		}
		if ($pg_plugin == 'paypal')
		{
			$html[0] = $html[0] . '<script type="text/javascript">';
			$html[0] = $html[0] . 'jQuery(".akeeba-bootstrap").hide();';
			$html[0] = $html[0] . 'jQuery(window).load(function() {jQuery(".akeeba-bootstrap form").submit();});';
			$html[0] = $html[0] . '</script>';
		}

		$this->assign("pg_plugin", $pg_plugin);
		$this->assign("configs", $configs);
		$this->assign("data", $html);
		
		$template = new DigiComSiteHelperTemplate($this);
		$template->rander('checkout');			
		
		parent::display($tpl);
		
	}
	

}
