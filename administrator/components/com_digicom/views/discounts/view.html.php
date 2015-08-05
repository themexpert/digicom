<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class DigiComViewDiscounts extends JViewLegacy
{

	function display ($tpl =  null )
	{

		$condition = JRequest::getVar("condition", '1');
		$this->assign ("condition", $condition);

		$status = JRequest::getVar("status", '');
		$this->assign ("status", $status);

		$this->promos = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->configs = $this->get('configs');


		//set toolber
		$this->addToolbar();

		DigiComHelperDigiCom::addSubmenu('discounts');
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
		JToolBarHelper::title(JText::_('COM_DIGICOM_DISCOUNTS_TOOLBAR_TITLE'), 'generic.png');
		$canDo = JHelperContent::getActions('com_digicom', 'component');

		$bar = JToolBar::getInstance('toolbar');
		// Instantiate a new JLayoutFile instance and render the layout
		$layout = new JLayoutFile('toolbar.title');
		$title=array(
			'title' => JText::_( 'COM_DIGICOM_DISCOUNTS_TOOLBAR_TITLE' ),
			'class' => 'title'
		);
		$bar->appendButton('Custom', $layout->render($title), 'title');

		$layout = new JLayoutFile('toolbar.settings');
		$bar->appendButton('Custom', $layout->render(array()), 'settings');

		if ($canDo->get('core.create'))
		{
			JToolBarHelper::addNew('discount.add');
			JToolBarHelper::divider();
		}
		if ($canDo->get('core.edit.state'))
		{
			JToolBarHelper::publishList('discounts.publish');
			JToolBarHelper::unpublishList('discounts.unpublish');
		}
		if ($canDo->get('core.edit.delete'))
		{
			JToolBarHelper::deleteList(JText::_('COM_DIGICOM_DISCOUNTS_DELETE_CONFIRMATION'),'discounts.delete');
			JToolBarHelper::divider();
		}

		$layout = new JLayoutFile('toolbar.video');
		$bar->appendButton('Custom', $layout->render(array()), 'video');
	}
}
