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
	public $customer;
	public $pay_plugin;
	public $items;
	public $order;
	public $data;
	public $configs;

	function display($tpl = null)
	{

		$app 			= JFactory::getApplication();
		$configs 	= JComponentHelper::getComponent('com_digicom')->params;
		$session 	= JFactory::getSession();
		$customer	= new DigiComSiteHelperSession();

		$input 		= $app->input;
		$return 	= base64_encode( JURI::getInstance()->toString() );

		if($customer->_user->id < 1)
		{
			$app->Redirect(JRoute::_('index.php?option=com_users&view=login&return='.$return, false));
			return true;
		}

		$processor	= $input->get("processor", "");

		if(empty($processor)){
			$pay_plugin 	= $session->get('processor');
		}else{
			$pay_plugin 	= $processor;
		}
		if(empty($pay_plugin)){
			$pay_plugin 	= $configs->get('default_payment','offline');
		}

		$order 		= $this->get('Order');//print_r($order);die;
		if(!isset($order->id) or $order->id <= 0){
			$app->redirect(JRoute::_('index.php?option=com_digicom&view=cart'));
		}
		if(empty($order->params))
		{
			$orderItems = $this->get('OrderItems');
			// print_r($orderItems);die;
			$params = new stdClass;
			$params->order_id = $order->id;
			$params->order_amount = $order->amount;
			$params->products = $orderItems;
		}
		else
		{
			$params 	= json_decode($order->params);//print_r($params);die;
		}

		$items 		= $params->products;//print_r($items);die;

		$vars 						= new stdClass();
		$vars->items 			= $items;
		$vars->order_id 	= $params->order_id;
		$vars->user_id 		= JFactory::getUser()->id;
		$vars->customer		= $customer->_customer;
		$vars->item_name 	= '';

		foreach ($items as $key => $value)
		{
			$vars->item_name .= $value->name . ', ';
		}

		$vars->item_name = substr($vars->item_name, 0, strlen($vars->item_name)-2);

		$vars->cancel_return = JRoute::_(JURI::root()."index.php?option=com_digicom&task=cart.cancel&processor={$pay_plugin}", true, 0);

		//prepare the url
		///processPayment
		$url = JRoute::_(JURI::root()."index.php?option=com_digicom&task=cart.processPayment&processor={$pay_plugin}&order_id=".$params->order_id."&sid=".$customer->_customer->id, true, false);
		//echo $url;die;

		$vars->return = $vars->notify_url = $vars->url = $url;
		$vars->currency_code = $configs->get('currency','USD');
		$vars->amount = $params->order_amount;

		// Triggre plugin event
		JPluginHelper::importPlugin('digicom_pay', $pay_plugin);
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onDigicom_PaySendPayment', array($vars, &$params));

		$html = $dispatcher->trigger('onDigicom_PayGetHTML', array($vars, $pay_plugin));
		// print_r($html);die;
		if (!isset($html[0]))
		{
			$html[0] = '';
		}

		$this->assign("pg_plugin", $pay_plugin);
		$this->assign("configs", $configs);
		$this->assign("data", $html);
		$this->assign("order", $order);
		$this->assign("items", $items);

		$this->assign("customer", $customer);

		$template = new DigiComSiteHelperTemplate($this);
		$template->rander('checkout');

		parent::display($tpl);

	}


}
