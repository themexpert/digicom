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

		$this->customer = $this->get('Customer');

		$this->items = $model->getCartItems($this->customer, $this->configs);

		$this->plugins = $this->get('PluginList');

		$this->session = JFactory::getSession();

		$disc = 0;
		foreach ($this->items as $i => $item) {

			if (isset($item->discounted_price) && $item->discounted_price && $item->discount > 0) $disc = 1;

		}

		$this->discount = $disc;

		$this->tax = $model->calc_price($this->items, $this->customer, $this->configs);
		//print_r($this->tax);die;
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

		$this->prepareDocument();

		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function prepareDocument()
	{
		$app   = JFactory::getApplication();
		$menus = $app->getMenu();
		$user  = JFactory::getUser();
		$title = null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if(empty($menu)){
			$menu = $menus->getDefault();
		}
		$title = $menu->params->get('page_title');
		if (empty($title))
		{
			$title = $app->get('sitename');
		}
		elseif ($app->get('sitename_pagetitles', 0) == 1)
		{
			$title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
		}
		elseif ($app->get('sitename_pagetitles', 0) == 2)
		{
			$title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
		}

		$this->document->setTitle($title);

		if ($menu->params->get('menu-meta_description'))
		{
			$this->document->setDescription($menu->params->get('menu-meta_description'));
		}

		if ($menu->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $menu->params->get('menu-meta_keywords'));
		}

		if ($menu->params->get('robots'))
		{
			$this->document->setMetadata('robots', $menu->params->get('robots'));
		}
	}

}
