<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_digicom
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined ('_JEXEC') or die ("Go away.");


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
		JToolBarHelper::title(JText::_('DIGICOM_DASHBOARD'), 'generic.png');
		
		$bar = JToolBar::getInstance('toolbar');
		
		$layout = new JLayoutFile('toolbar.title');
		$title = array(
			'title' => JText::_( 'DIGICOM_DASHBOARD' ),
			'class' => 'product'
		);
		$bar->appendButton('Custom', $layout->render($title), 'title');		
		
		$layout = new JLayoutFile('toolbar.products');
		$bar->appendButton('Custom', $layout->render(array()), 'products');
		
		$layout = new JLayoutFile('toolbar.settings');
		$bar->appendButton('Custom', $layout->render(array()), 'settings');

	}
	
}
