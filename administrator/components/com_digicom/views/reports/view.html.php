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

class DigiComViewReports extends JViewLegacy {

	protected $latest_orders;

	protected $most_sold;

	protected $totalOrder;

	protected $reportOrders;

	protected $reportCustomer;

	protected $configs;

	function display ($tpl =  null )
	{

		$this->latest_orders = DigiComHelperDigiCom::getOrders(5);
		$this->most_sold = DigiComHelperDigiCom::getMostSoldProducts(5);

		$this->totalOrder = $this->get('reportTotal');
		$this->reportOrders = $this->get('reportOrders');
		$this->reportCustomer = $this->get('reportCustomer');
		$this->configs = $this->get('configs');

		//load the toolber
		$this->addToolbar();

		DigiComHelperDigiCom::addSubmenu('reports');
		$this->sidebar = DigiComHelperDigiCom::renderSidebar();

		parent::display($tpl);
	}

	function getTotal($type=''){
		$total = $this->getModel()->getreportTotal($type);
		return $total;
	}

	function getNrOrders(){
		$total = $this->getModel()->getreportOrders();
		return $total;
	}

	function getNrLicenses($type){
		$total = $this->getModel()->getreportLicenses($type);
		return $total;
	}

	function getStartEndDate($report){
		$total = $this->getModel()->getStartEndDate($report);
		return $total;
	}

	function getPaginationDate($configs){
		return $this->getModel()->getPaginationDate($configs);
	}

	function prepereJoomlaDataFormat($format = '%m-%d-%Y') {

		$result = $format;
		if ( strpos($result,'%') === false) {
			$r = array('m' => '%m', 'd' => '%d', 'Y' => '%Y');
			$result = str_replace(array_keys($r),array_values($r),$format);
		}

		return $result;
	}

	/**
	 * Add the page title and toolbar.
		*
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('COM_DIGICOM_REPORTS_TOOLBAR_TITLE'), 'generic.png');

		$bar = JToolBar::getInstance('toolbar');
		// Instantiate a new JLayoutFile instance and render the layout
		$layout = new JLayoutFile('toolbar.title');
		$title=array(
			'title' => JText::_( 'COM_DIGICOM_SIDEBAR_MENU_REPORTS' ),
			'class' => 'title'
		);
		$bar->appendButton('Custom', $layout->render($title), 'title');

		$layout = new JLayoutFile('toolbar.settings');
		$bar->appendButton('Custom', $layout->render(array()), 'settings');

	}

}
