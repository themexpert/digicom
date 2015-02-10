<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 341 $
 * @lastmodified	$LastChangedDate: 2013-10-10 14:28:28 +0200 (Thu, 10 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

defined ('_JEXEC') or die ("Go away.");

jimport ("joomla.application.component.view");

class DigiComAdminViewEmailreminders extends DigiComView {

	function display ($tpl =  null )
	{
		// Access check.
		if (!JFactory::getUser()->authorise('digicom.autoresponders', 'com_digicom'))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
		
		DigiComAdminHelper::addSubmenu('emailreminders');
		$this->sidebar = JHtmlSidebar::render();
		
		$emails = $this->get('Items');
		$pagination = $this->get('Pagination');

		$this->emails = $emails;
		$this->pagination = $pagination;

		$configs = $this->_models['config']->getConfigs();
		$this->assign("configs", $configs);
		
		//toolber
		$this->addToolbar();
		
		parent::display($tpl);

	}

	function editForm($tpl = null) {
		global $isJ25;
		$db = JFactory::getDBO();

		$email = $this->get('emailreminder');

		$isNew = ($email->id < 1);
		$text = $isNew?JText::_('New'):JText::_('Edit');
		$this->assign("action", $text);
		
		
		JToolBarHelper::title(JText::_('Email reminders').":<small>[".$text."]</small>");
		if ($isNew) {
			JToolBarHelper::save();			
			JToolBarHelper::divider();
			JToolBarHelper::cancel();
		} else {
			JToolBarHelper::apply();
			JToolBarHelper::save();
			JToolBarHelper::divider();
			JToolBarHelper::cancel ('cancel', 'Close');
		}

		$this->assign("email", $email);

		$configs = $this->_models['config']->getConfigs();
		$lists = array();

		$this->assign("configs", $configs);
		
		parent::display($tpl);

	}
	
	
	/**
	 * Add the page title and toolbar.
		*
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('Email reminders Manager'), 'generic.png');
		JToolBarHelper::custom( 'duplicate', 'save.png', 'save.png', 'Duplicate', true, false );
		JToolBarHelper::divider();
		JToolBarHelper::addNew();
		JToolBarHelper::editList();
		JToolBarHelper::divider();
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::divider();
		JToolBarHelper::deleteList();
	}
	
}

