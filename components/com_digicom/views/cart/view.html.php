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

		$model = $this->getModel('Cart');

		$this->configs = JComponentHelper::getComponent('com_digicom')->params;
		
		$this->Itemid = JRequest::getvar("Itemid", "0");
		
		$this->customer = new DigiComSiteHelperSession();
		
		$this->items = $model->getCartItems($this->customer, $this->configs);		

		$this->plugins = $this->get('PluginList');

		$disc = 0;
		foreach ($this->items as $i => $item) {

			if ($i < 0 ) continue;
			if (isset($item->discounted_price) && $item->discounted_price && $item->discount > 0) $disc = 1;
			
		}

		$this->discount = $disc;
		
		$this->tax = $model->calc_price($this->items, $this->customer, $this->configs);

		$promo = $model->get_promo($this->customer , 1);
		
		if(isset($promo)){
			$this->promocode = $promo->code;
			$this->promoerror = $promo->error;			
		}else{
			$this->promocode = '';
			$this->promoerror = '';		
		}
		
		$this->cat_url = $this->get('cat_url');

		$template = new DigiComSiteHelperTemplate($this);
		$input = JFactory::getApplication()->input;
		$layout = $input->get('layout','cart');
		$from = JRequest::getVar("from", "");

		if($from == "ajax"){
			$template->rander('cart_popup');			
		}else{
			$template->rander($layout);			
		}
		
		parent::display($tpl);
	}

	
	public function getPaymentPlugins($configs){
		
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
