<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class DigiComViewLicenses extends JViewLegacy
{

	function display ($tpl =  null )
	{

		$this->items = $this->get('Items');
		$this->params = $this->get('State');
		$this->pagination = $this->get('Pagination');
		
		//set toolber
		$this->addToolbar();

		DigiComHelperDigiCom::addSubmenu('licenses');
		$this->sidebar = DigiComHelperDigiCom::renderSidebar();

		parent::display($tpl);

	}

	/**
	 * Add the page title and toolbar.
		*
	 * @since   1.6
	*/
	protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('COM_DIGICOM_LICENSES_TOOLBAR_TITLE_SITE'), 'generic.png');
		$canDo = JHelperContent::getActions('com_digicom', 'component');

		$bar = JToolBar::getInstance('toolbar');
	}
}
