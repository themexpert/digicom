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

jimport ("joomla.application.component.view");

class DigiComAdminViewStats extends DigiComView {

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
		
		$configs = $this->_models['config']->getConfigs();
		$this->assign("configs", $configs);
		
		DigiComAdminHelper::addSubmenu('stats');
		$this->sidebar = JHtmlSidebar::render();
		
		parent::display($tpl);
	}

	function getTotal($type=''){
		$total = $this->_models['stat']->getreportTotal($type);
		return $total;
	}

	function getNrOrders(){
		$total = $this->_models['stat']->getreportOrders();
		return $total;
	}

	function getNrLicenses($type){
		$total = $this->_models['stat']->getreportLicenses($type);
		return $total;
	}

	function getStartEndDate($report){
		$total = $this->_models['stat']->getStartEndDate($report);
		return $total;
	}

	function getPaginationDate($configs){
		$this->_models['stat']->getPaginationDate($configs);
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
