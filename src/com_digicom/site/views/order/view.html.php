<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class DigiComViewOrder extends JViewLegacy {

	function display($tpl = null)
	{
		$app 			= JFactory::getApplication();
		$input 		= $app->input;
		$customer = new DigiComSiteHelperSession();

		$item 	= $app->getMenu()->getItems('link', 'index.php?option=com_digicom&view=orders', true);
		$Itemid = isset($item->id) ? '&Itemid=' . $item->id : '';

		$this->order = $this->_models['order']->getOrder();

		if($this->order->id < 1)
		{
			return JError::raiseError(404, JText::_('COM_DIGICOM_ORDER_NOT_FOUND'));
		}
		elseif($this->order->userid != $customer->_customer->id)
		{
			return JError::raiseError(203, JText::_('COM_DIGICOM_ORDER_NOT_OWN'));
		}

		$configs = JComponentHelper::getComponent('com_digicom')->params;
		$this->assign("configs", $configs);

		$this->assign("customer", $customer);
		$this->assign("Itemid", $Itemid);

		$layout = $input->get('layout','order');
		$template = new DigiComSiteHelperTemplate($this);
		$template->rander($layout);

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

		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', JText::sprintf('COM_DIGICOM_ORDER_PAGE_TITLE',$this->order->id));
		}

		$title = $this->params->get('page_title', '');

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
