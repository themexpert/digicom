<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class DigiComViewOrders extends JViewLegacy
{
	public $app;
	public $state;

	function display( $tpl = null )
	{
		$this->app = JFactory::getApplication();
		$document = JFactory::getDocument();

		$this->orders 	= $this->get('Items');
		$this->state 		= $this->get('State');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		
		$pagination 		= $this->get('Pagination');
		$this->pagination = $pagination;

		// $startdate = JRequest::getVar( "startdate", "", "request" );
		// $startdate = strtotime($startdate);
		// //$startdate = DigiComHelperDigiCom::parseDate( $configs->get('time_format','DD-MM-YYYY'), $startdate );
		// $this->assign( "startdate", $startdate );
		// $enddate = JRequest::getVar( "enddate", "", "request" );
		// $enddate = strtotime($enddate);
		// //$enddate = DigiComHelperDigiCom::parseDate( $configs->get('time_format','DD-MM-YYYY'), $enddate );
		// $this->assign( "enddate", $enddate );

		//set toolber
		$this->addToolbar();

		DigiComHelperDigiCom::addSubmenu('orders');
		$this->sidebar = DigiComHelperDigiCom::renderSidebar();

		parent::display( $tpl );
	}

	/**
	 * Add the page title and toolbar.
		*
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		$canDo = JHelperContent::getActions('com_digicom', 'component');
		JToolBarHelper::title( JText::_( 'COM_DIGICOM_ORDERS_TOOLBAR_TITLE' ), 'generic.png' );

		$bar = JToolBar::getInstance('toolbar');
		// Instantiate a new JLayoutFile instance and render the layout
		$layout = new JLayoutFile('toolbar.title');
		$title=array(
			'title' => JText::_( 'COM_DIGICOM_ORDERS_TOOLBAR_TITLE' ),
			'class' => 'title'
		);
		$bar->appendButton('Custom', $layout->render($title), 'title');

		$layout = new JLayoutFile('toolbar.settings');
		$bar->appendButton('Custom', $layout->render(array()), 'settings');

		$layout = new JLayoutFile('toolbar.video');
		$bar->appendButton('Custom', $layout->render(array()), 'video');

		if ($canDo->get('core.create')){
			JToolBarHelper::addNew('ordernew.add');
			JToolBarHelper::divider();
		}
		if ($canDo->get('core.delete')){
			JToolBarHelper::deleteList(JText::_('COM_DIGICOM_ORDERS_ALERT_REMOVE'),'orders.remove');
			JToolBarHelper::spacer();
		}
	}

}
