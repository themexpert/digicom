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

class DigiComViewOrders extends JViewLegacy
{

	function display( $tpl = null )
	{
		// Access check.
		if (!JFactory::getUser()->authorise('digicom.orders', 'com_digicom'))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}

		$document = JFactory::getDocument();

		$orders = $this->get('Items');
		$pagination = $this->get('Pagination');
		
		$this->orders = $orders;
		$this->pagination = $pagination;

		$startdate = JRequest::getVar( "startdate", "", "request" );
		$startdate = strtotime($startdate);
		//$startdate = DigiComHelperDigiCom::parseDate( $configs->get('time_format','DD-MM-YYYY'), $startdate );
		$this->assign( "startdate", $startdate );
		$enddate = JRequest::getVar( "enddate", "", "request" );
		$enddate = strtotime($enddate);
		//$enddate = DigiComHelperDigiCom::parseDate( $configs->get('time_format','DD-MM-YYYY'), $enddate );
		$this->assign( "enddate", $enddate );

		$keyword = JRequest::getVar( "keyword", "", "request" );
		$this->assign( "keyword", $keyword );
		
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
		
		JToolBarHelper::addNew('ordernew.add');
		JToolBarHelper::divider();
		JToolBarHelper::deleteList(JText::_('COM_DIGICOM_ORDERS_ALERT_REMOVE'),'orders.remove');
		JToolBarHelper::spacer();
	}
	
}
