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

	protected $state;
	protected $params;
	protected $orders;

	function display($tpl = null)
	{
		$app 			= JFactory::getApplication();
		$input 		= $app->input;
		$customer = new DigiComSiteHelperSession();

		$this->state		= $this->get('State');
		$this->params 	= $this->state->get('params');
		$this->orders 	= $this->get('listOrders');
		$this->configs 	= JComponentHelper::getComponent('com_digicom')->params;


		$template = new DigiComSiteHelperTemplate($this);
		$template->rander('orders');

		$this->_prepareDocument();
		parent::display($tpl);
	}

	/**
	 * Prepares the document.
	 *
	 * @return  void.
	 */
	protected function _prepareDocument()
	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu();
		$pathway	= $app->getPathway();
		$title		= null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		if (isset($menu->title) && !empty($menu->title))
		{
			$title = $menu->title;
		}
		//JText::_('COM_DIGICOM_ORDERS_PAGE_TITLE')

		// Check for empty title and add site name if param is set
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

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}

	}
}
