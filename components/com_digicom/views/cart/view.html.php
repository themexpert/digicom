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

class DigiComViewCart extends JViewLegacy
{
	function display ($tpl = null)
	{
		global $isJ25;
		$db = JFactory::getDBO();
		$lists = array();
		$task = JRequest::getWord('task');
		$configs = $this->_models['config']->getConfigs();

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
	
	public function MostraFormPagamento($configs){
		
		$db = JFactory::getDBO();

		$condtion = array(0 => '\'payment\'');
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
			$lang->load('plg_payment_' . strtolower($gatewayname), JPATH_ADMINISTRATOR);
			$options[] = JHTML::_('select.option',$gateway->element, JText::_($gatewayname));
		}

		return JHTML::_('select.genericlist', $options, 'processor', 'class="inputbox required" style="float:right;"', 'value', 'text', $configs->get('default_payment','bycheck'), 'processor' );

	}

}
