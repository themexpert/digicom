<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

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

		$this->session = JFactory::getSession();;

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

}
