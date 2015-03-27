<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

jimport ("joomla.application.component.view");

class DigiComViewAbout extends JViewLegacy {

	function display($tpl =  null){
		JToolBarHelper::title(JText::_('COM_DIGICOM_ABOUT_TOOLBAR_TITLE'), 'generic.png');
		
		$bar = JToolBar::getInstance('toolbar');
		// Instantiate a new JLayoutFile instance and render the layout
		$layout = new JLayoutFile('toolbar.title');
		$title=array(
			'title' => JText::_( 'COM_DIGICOM_ABOUT_TOOLBAR_TITLE' ),
			'class' => 'title'
		);
		$bar->appendButton('Custom', $layout->render($title), 'title');
		
		$layout = new JLayoutFile('toolbar.settings');
		$bar->appendButton('Custom', $layout->render(array()), 'settings');
		
		DigiComHelperDigiCom::addSubmenu('about');
		$this->sidebar = DigiComHelperDigiCom::renderSidebar();
		
		parent::display($tpl);
	}

}