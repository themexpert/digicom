<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class DigiComViewCheckout extends JViewLegacy
{

	function display($tpl = null)
	{

		$app 			= JFactory::getApplication();
		$input 		= $app->input;
		$Itemid 	= $input->get("Itemid", 0);
		$return 	= base64_encode( JURI::getInstance()->toString() );
		$customer = new DigiComSiteHelperSession();
		if($customer->_user->id < 1)
		{
			$app->Redirect(JRoute::_('index.php?option=com_users&view=login&return='.$return.'&Itemid='.$Itemid, false));
			return true;
		}

		$session 		= JFactory::getSession();
		$processor	= JRequest::getVar("processor", "");

		if(empty($processor)){
			$pg_plugin 	= $session->get('processor');
		}else{
			$pg_plugin 	= $processor;
		}
		$Itemid 		= JRequest::getInt("Itemid", "0");
		$order_id 	= JRequest::getInt("order_id", "0");
		$dispatcher = JDispatcher::getInstance();
		$plugin 		= JPluginHelper::importPlugin( 'digicom_pay', $pg_plugin );

		$configs 	= JComponentHelper::getComponent('com_digicom')->params;
		$order 		= $this->get('Order');//print_r($order);die;
		$params 	= json_decode($order->params,true);print_r($params);die;
		$items 		= $params['products'];//print_r($items);die;

		$vars 						= new stdClass();
		$vars->items 			= $items;
		$vars->order_id 	= $params['order_id'];
		$vars->user_id 		= JFactory::getUser()->id;
		$vars->customer		= $customer->_customer;
		$vars->item_name 	= '';

		for($i=0; $i<count($items); $i++)
		{
			$vars->item_name.= $items[$i]['name'] . ', ';
		}
		$vars->item_name = substr($vars->item_name, 0, strlen($vars->item_name)-2);

		$vars->cancel_return = JRoute::_(JURI::root()."index.php?option=com_digicom&Itemid=".$Itemid."&task=cart.cancel&processor={$pg_plugin}", true, 0);
		$vars->return = $vars->url = $vars->notify_url = JRoute::_(JURI::root()."index.php?option=com_digicom&task=cart.processPayment&processor={$pg_plugin}&order_id=".$params['order_id']."&sid=".$customer->_sid, true, false);
		$vars->currency_code = $configs->get('currency','USD');
		//$vars->amount = $items[-2]['taxed'];//+$items[-2]['shipping'];
		$vars->amount = $params['order_amount'];

		// Triggre plugin event
		JPluginHelper::importPlugin('digicom_pay', $pg_plugin);
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
