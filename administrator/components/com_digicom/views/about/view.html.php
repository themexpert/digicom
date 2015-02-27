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

class DigiComAdminViewAbout extends DigiComView {

	function display($tpl =  null){
		JToolBarHelper::title(JText::_('VIEWDSADMINABOUT'), 'generic.png');
		
		$bar = JToolBar::getInstance('toolbar');
		// Instantiate a new JLayoutFile instance and render the layout
		$layout = new JLayoutFile('toolbar.title');
		$title=array(
			'title' => JText::_( 'VIEWDSADMINABOUT' ),
			'class' => 'title'
		);
		$bar->appendButton('Custom', $layout->render($title), 'title');
		
		$layout = new JLayoutFile('toolbar.settings');
		$bar->appendButton('Custom', $layout->render(array()), 'settings');
		
		DigiComAdminHelper::addSubmenu('about');
		$this->sidebar = DigiComAdminHelper::renderSidebar();
		
		parent::display($tpl);
	}

	function vimeo($tpl = null) {
		$id = JRequest::getVar('id', '0');
		$this->assignRef('id', $id);
		parent::display($tpl);
	}
}