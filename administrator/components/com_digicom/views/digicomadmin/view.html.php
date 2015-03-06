<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 458 $
 * @lastmodified	$LastChangedDate: 2014-02-10 11:47:01 +0100 (Mon, 10 Feb 2014) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

defined ('_JEXEC') or die ("Go away.");

jimport ("joomla.application.component.view");

class DigiComAdminViewDigiComAdmin extends DigiComView {

	public $version			= null;
	public $newversion 		= null;
	
	function display ($tpl =  null ) {		
		
		DigiComAdminHelper::addSubmenu('digicomadmin');
		
		//load the toolber
		$this->addToolbar();
		$this->sidebar = DigiComAdminHelper::renderSidebar();
		
		$this->latest_orders = DigiComAdminHelper::getOrders(5);
		$this->most_sold = DigiComAdminHelper::getMostSoldProducts(5);
		
		$this->top_customers = DigiComAdminHelper::getCustomers(5);

		$this->totalOrder = $this->get('reportTotal');
		$this->reportOrders = $this->get('reportOrders');
		$this->reportCustomer = $this->get('reportCustomer');
		$this->configs = $this->get('configs');

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
