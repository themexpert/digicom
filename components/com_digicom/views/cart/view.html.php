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

jimport ("joomla.application.component.view");
jimport('joomla.html.parameter');

class DigiComViewCart extends DigiComView
{
	function display ($tpl = null)
	{
		global $isJ25;
		$db = JFactory::getDBO();
		$lists = array();
		$task = JRequest::getWord('task');
		$configs = JComponentHelper::getComponent('com_digicom')->params;

		$document = JFactory::getDocument();
		JHtml::_('bootstrap.framework');

		$Itemid = JRequest::getvar("Itemid", "0");

		$this->assignRef("configs", $configs);
		$customer = new DigiComSessionHelper();
		
		$this->assign("customer", $customer);

		$items = $this->_models['cart']->getCartItems($customer, $configs);		

		$this->assignRef("items", $items);
		
		// Plugins
		$plugin_items = $this->get('PluginList');
		$plugins = array();
		foreach($plugin_items as $plugin_item){
			$plugin_params = new JRegistry($plugin_item->params);
			$pluginname = $plugin_params->get($plugin_item->name.'_label');
			$plugins[] = JHTML::_('select.option',  $plugin_item->name,  $pluginname );
		}
		$processor = '';
		if (isset($plan_details['processor'])) $processor = $plan_details['processor'];
		if (!empty($plugins)) {
			$lists['plugins'] = '<span class="digicom_details" style="display: inline-block; margin-left: 5px;">'. JHTML::_('select.genericlist',  $plugins, 'processor', 'class="inputbox" ', 'value', 'text', $processor) . '</span>';
		} else {
			$lists['plugins'] = '<span class="digicom_details" style="display: inline-block;" margin-left: 5px;>'.JText::_('Payment plugins not installed').'</span>';
		}

		$sid = $customer->_sid;
		$sql = "select cart_details from `#__digicom_session` where sid='".$sid."'";
		$db->setQuery($sql);
		$data = $db->loadResult();
		$promo = explode ("=", $data);

		$sql = "select shipping_details from #__digicom_session where sid='".$sid."'";
		$db->setQuery($sql);
		$shipto = $db->loadResult();
		$price_format = '%'.$configs->get('totaldigits','').'.'.$configs->get('decimaldigits','2').'f';
		$categ_digicom = '';	   
		if ( $categ_digicom != '' ) {
			$sql = "select id from #__digicom_categories where title like '".$categ_digicom."' or name like '".$categ_digicom."'";
			$database->setQuery($sql);
			$id = $database->loadResult();
			$cat_url = JRoute::_("index.php?option=com_digicom&view=categories&cid=" . $id."&Itemid=".$Itemid);
		} else {
			$cat_url = JRoute::_("index.php?option=com_digicom&view=categories&cid=0"."&Itemid=".$Itemid);
		}
		$this->assign ("cat_url", $cat_url);
		$maxfields = 0;
		$disc = 0;
		$optlen = array();
		$totalfields = 0;
		$select_only = array();

		foreach ($items as $i => $item) {

			if ($i < 0 ) continue;

			if (isset($item->productfields) && count($item->productfields) > 0)
				$maxfields = DigiComHelper::check_fields($item->productfields, $totalfields, $optlen, $select_only, $maxfields, $item->id);

			if (isset($item->discounted_price) && $item->discounted_price && $item->discount > 0) $disc = 1;

			if($task != 'summary'){
				//$lists[$item->cid]['quantity'] = JHTML::_('select.genericlist',  $qty, 'quantity['.$item->cid.']', 'size="1" class="inputbox" onchange="update_cart('.$item->cid.')" ', 'value', 'text', $item->quantity);
				$lists[$item->cid]['attribs'] = DigiComHelper::add_selector_to_cart($item, $optlen, $select_only, $i, $configs, $configs);
			}
			else{
				$lists[$item->cid]['attribs'] = DigiComHelper::add_selector_to_summary ( $item, $optlen, $select_only, $i, $configs, $configs);
				//$lists[$item->cid]['quantity'] = $item->quantity;//JHTML::_('select.genericlist',  $qty, 'quantity['.$item->cid.']', 'class="inputbox" ', 'value', 'text', $item->quantity);
			}
		}

		$this->assign("discount", $disc);
		$this->assign("maxfields", $maxfields);
		$this->assign("optlen", $optlen);
		$this->assign("totalfields", $totalfields);

		$tax = $this->_models["cart"]->calc_price($items, $customer, $configs);
		$this->assign("tax", $tax);

		$promo = $this->_models['cart']->get_promo($customer, 1);
		$this->assign("promocode", $promo->code);
		$this->assign("promoerror", $promo->error);
		$this->assign("lists", $lists);
		
		$template = new DigiComTemplateHelper($this);
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
