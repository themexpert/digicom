<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;


class digicomViewdigicom extends JViewLegacy {

	protected $latest_orders;

	protected $most_sold;

	protected $totalOrder;

	protected $reportOrders;

	protected $reportCustomer;

	protected $configs;

	/**
	 * Display the view
	 *
	 * @return  void
	 */

	function display ($tpl =  null ) {

		$this->latest_orders = DigiComHelperDigiCom::getOrders(5);
		$this->most_sold = DigiComHelperDigiCom::getMostSoldProducts(5);

		$this->totalOrder = $this->get('reportTotal');
		$this->reportOrders = $this->get('reportOrders');
		$this->reportCustomer = $this->get('reportCustomer');
		$this->configs = $this->get('configs');

		//load the toolber
		$this->addToolbar();

		DigiComHelperDigiCom::addSubmenu('digicom');
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
		JToolBarHelper::title(JText::_('COM_DIGICOM_DASHBOARD_TOOLBAR_TITLE'), 'generic.png');
		$canDo = JHelperContent::getActions('com_digicom', 'component');

		$bar = JToolBar::getInstance('toolbar');

		$layout = new JLayoutFile('toolbar.title');
		$title = array(
			'title' => JText::_( 'COM_DIGICOM_DASHBOARD_TOOLBAR_TITLE' ),
			'class' => 'product'
		);
		$bar->appendButton('Custom', $layout->render($title), 'title');

		if ($canDo->get('core.create')){
			$layout = new JLayoutFile('toolbar.products');
			$bar->appendButton('Custom', $layout->render(array()), 'products');			
		}

		$layout = new JLayoutFile('toolbar.settings');
		$bar->appendButton('Custom', $layout->render(array()), 'settings');

		$layout = new JLayoutFile('toolbar.video');
		$bar->appendButton('Custom', $layout->render(array()), 'video');

	}

}
