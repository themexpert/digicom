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

class DigiComViewCart extends JViewLegacy
{
	function display ($tpl = null)
	{

		$this->configs = JComponentHelper::getComponent('com_digicom')->params;

		$this->Itemid = JRequest::getvar("Itemid", "0");

		$this->customer = new DigiComSiteHelperSession();
		
		$this->items = $this->get('CartItems');		
		
		$this->plugins = $this->get('PluginList');

		$this->cat_url = $this->get('cat_url');
		
		$maxfields = 0;
		$disc = 0;
		$optlen = array();
		$select_only = array();

		foreach ($this->items as $i => $item) {

			if ($i < 0 ) continue;

			if (isset($item->productfields) && count($item->productfields) > 0)
				$maxfields = DigiComHelper::check_fields($item->productfields, $totalfields, $optlen, $select_only, $maxfields, $item->id);

			if (isset($item->discounted_price) && $item->discounted_price && $item->discount > 0) $disc = 1;

			
		}

		$this->discount = $disc;
		$this->maxfields = $maxfields;

		$this->tax = $this->get('calc_price');

		$promo = $this->get('promo');
		if(isset($promo)){
			$this->promocode = $promo->code;
			$this->promoerror = $promo->error;			
		}else{
			$this->promocode = '';
			$this->promoerror = '';		
		}
		
		$template = new DigiComSiteHelperTemplate($this);

		$from = JRequest::getVar("from", "");
		if($from == "ajax"){
			$template->rander('cart_popup');			
		}else{
			$template->rander('cart');			
		}
		
		parent::display($tpl);
	}

	function paymentwait($tpl = null)
	{
		parent::display($tpl);
	}
	
	function submitOrder($tpl = null)
	{
		$pg_plugin 	= JRequest::getVar("processor", "", "", "string");
		$Itemid 	= JRequest::getInt("Itemid", "0");
		$order_id 	= JRequest::getInt("order_id", "0");
		$dispatcher = JDispatcher::getInstance();
		$plugin = JPluginHelper::importPlugin( 'digicom_pay', $pg_plugin );
		$session 	= JFactory::getSession();
		$customer 	= new DigiComSessionHelper();
		$configs 	= $this->_models['config']->getConfigs();
		$order 		=$this->_models['cart']->getOrder( $order_id );
		$params 	= json_decode($order->params,true);
		//print_r($order);die;
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
		//print_r($items);die;
		for($i=0; $i<count($items)-2; $i++)
		{
			//print_r($items[$i]);die;
			$vars->item_name.= $items[$i]['name'] . ', ';
		}
		$vars->item_name = substr($vars->item_name, 0, strlen($vars->item_name)-2);

		// downloads page
		if ($configs->get('afterpurchase') == 0)
		{
			$vars->return = JRoute::_(JURI::root()."index.php?option=com_digicom&view=orders&orderid=".$params['order_id'], true, 0);
		}
		// orders page
		else
		{
			$vars->return = JRoute::_(JURI::root()."index.php?option=com_digicom&view=orders&orderid=".$params['order_id'], true, 0);
		}
		
		
		$vars->return = str_replace('https', 'http', $vars->return);
		$vars->cancel_return = JRoute::_(JURI::root()."index.php?option=com_digicom&Itemid=".$Itemid."&controller=cart&task=cancel&processor={$pg_plugin}", true, 0);
		$vars->url = $vars->notify_url = JRoute::_(JURI::root()."index.php?option=com_digicom&controller=cart&task=processPayment&processor={$pg_plugin}&order_id=".$params['order_id']."&sid=".$customer->_sid, true, false);
		$vars->currency_code = $configs->get('currency','USD');
		$vars->amount = $items[-2]['taxed'];//+$items[-2]['shipping'];
		
		// Triggre plugin event
		JPluginHelper::importPlugin('digicom_pay');
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onSendPayment', array(& $params));
		$html = $dispatcher->trigger('onTP_GetHTML', array($vars));
		
		
		//print_r($html);die;
		if (!isset($html[0])) {
			$html[0] = '';
		}
		$html[0] = $html[0] . '<script type="text/javascript">';
		if ($pg_plugin == 'paypal')
		{
			$html[0] = $html[0] . 'jQuery(".akeeba-bootstrap").hide();';
		}
		$html[0] = $html[0] . 'jQuery(".akeeba-bootstrap form").submit();';
		$html[0] = $html[0] . '</script>';

		//echo $html[0];
		
		$this->assign("pg_plugin", $pg_plugin);
		$this->assign("configs", $configs);
		$this->assign("data", $html);
		
		$template = new DigiComTemplateHelper($this);
		$template->rander('submitorder');			
		
		parent::display($tpl);
		
	}
	
	public function MostraFormPagamento($configs){
		
		$db = JFactory::getDBO();

		$condtion = array(0 => '\'digicom_pay\'');
		$condtionatype = join(',',$condtion);
		if(JVERSION >= '1.6.0')
		{
			$query = "SELECT extension_id as id,name,element,enabled as published
					  FROM #__extensions
					  WHERE folder in ($condtionatype) AND enabled=1";
		}
		else
		{
			$query = "SELECT id,name,element,published
					  FROM #__plugins
					  WHERE folder in ($condtionatype) AND published=1";
		}
		$db->setQuery($query);
		$gatewayplugin = $db->loadobjectList();

		$lang = JFactory::getLanguage();
		$options = array();
		$options[] = JHTML::_('select.option', '', 'Select payment gateway');
		foreach($gatewayplugin as $gateway)
		{
			$gatewayname = strtoupper(str_replace('plugpayment', '',$gateway->element));
			$lang->load('plg_digicom_pay_' . strtolower($gatewayname), JPATH_ADMINISTRATOR);
			$options[] = JHTML::_('select.option',$gateway->element, JText::_($gatewayname));
		}

		return JHTML::_('select.genericlist', $options, 'processor', 'class="inputbox required" style="float:right;"', 'value', 'text', $configs->get('default_payment','bycheck'), 'processor' );

	}

}
