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

class DigiComViewStats extends JViewLegacy {

	function display ($tpl =  null )
	{
		// Access check.
		if (!JFactory::getUser()->authorise('digicom.stats', 'com_digicom'))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}

		JToolBarHelper::title(JText::_('VIEWDSADMINSTATS'), 'generic.png');

		$bar = JToolBar::getInstance('toolbar');
		// Instantiate a new JLayoutFile instance and render the layout
		$layout = new JLayoutFile('toolbar.title');
		$title=array(
			'title' => JText::_( 'VIEWDSADMINSTATS' ),
			'class' => 'title'
		);
		$bar->appendButton('Custom', $layout->render($title), 'title');
		
		$layout = new JLayoutFile('toolbar.settings');
		$bar->appendButton('Custom', $layout->render(array()), 'settings');
		
		$configs = $this->get('Configs');
		$this->assign("configs", $configs);
		
		DigiComHelperDigiCom::addSubmenu('stats');
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

}
