<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class DigiComViewThankYou extends JViewLegacy
{
	public $item;
	public $configs;
	public $customer;

	function display($tpl = null)
	{
		$app 			= JFactory::getApplication();
		$session 	= JFactory::getSession();
		$digicom_session = $session->get('com_digicom', array());
		$this->state	= $this->get('State');
		
		if(isset($digicom_session['action']) && $digicom_session['action'] == 'payment_complete' && $digicom_session['id'])
		{
			$this->item 		= $this->get('Item');
			$session->set('com_digicom', array());
		}else{
			$msg = JText::_('COM_DIGICOM_THANKYOU_PAGE_WRONG_PID');
			$app->redirect(JRoute::_('index.php?option=com_digicom&view=orders'), $msg, 'warning');
			return true;
		}

		$this->configs 	= JComponentHelper::getComponent('com_digicom')->params;
		$this->customer	= JFactory::getUser($this->item->userid);

		// Triggre plugin event
		JPluginHelper::importPlugin('digicom');
		$dispatcher = JDispatcher::getInstance();
		$this->item->event = new stdClass;
		$results = $dispatcher->trigger('onDigicomBeforeThankyou', array('com_digicom.thankyou', &$this->item, &$this->customer));
		$this->item->event->beforeThankyou = trim(implode("\n", $results));

		$results = $dispatcher->trigger('onDigicomAfterThankyou', array('com_digicom.thankyou', &$this->item, &$this->customer));
		$this->item->event->afterThankyou = trim(implode("\n", $results));
		$this->params = $this->state->get('params');

		$template = new DigiComSiteHelperTemplate($this);
		$template->rander('thankyou');

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

		$title = JText::sprintf('COM_DIGICOM_THANKYOU_PAGE_TITLE',$this->item->id);

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
