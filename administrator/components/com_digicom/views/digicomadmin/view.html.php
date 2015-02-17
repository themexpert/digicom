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
		// Options button.
		if (JFactory::getUser()->authorise('core.admin', 'com_digicom')) {
			JToolBarHelper::preferences('com_digicom');
		}
		
		DigiComAdminHelper::addSubmenu('digicomadmin');
		
		//load the toolber
		$this->addToolbar();
		$this->sidebar = JHtmlSidebar::render();
		
		$this->latest_orders = DigiComAdminHelper::getOrders(5);
		$this->latest_products = DigiComAdminHelper::getProducts(5);
		$this->top_customers = DigiComAdminHelper::getCustomers(5);
		
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
		// Instantiate a new JLayoutFile instance and render the layout
		// Instantiate a new JLayoutFile instance and render the layout
		$layout = new JLayoutFile('toolbar.title');
		$title=array(
			'title' => JText::_( 'DIGICOM_DASHBOARD' ),
			'class' => 'title'
		);
		$bar->appendButton('Custom', $layout->render($title), 'title');
		

		
		$layout = new JLayoutFile('toolbar.title');
		$title = array(
			'title' => JText::_( 'DIGICOM_DASHBOARD' ),
			'class' => 'product'
		);
		$bar->getName($layout->render($title));
		
		$layout = new JLayoutFile('toolbar.products');
		$bar->appendButton('Custom', $layout->render(array()), 'products');
		
	}
	function isCurlInstalled() {
		$array = get_loaded_extensions();
		if(in_array("curl", $array)){
			return true;
		}
		else{
			return false;
		}
	}
	
	function getVersion(){
		if( !$this->version ) {
			$db 	= JFactory::getDbo();
			$sql 	= "SELECT `manifest_cache` FROM `#__extensions` WHERE `type`='component' AND `element`='com_digicom'";
			$db->setQuery($sql);
			$res 		= $db->loadResult();
			$manifest 	= new JRegistry($res);
			$this->version 	= $manifest->get('version');
		}
		return $this->version;
	}
	
	function getNewVersion(){
		if(!$this->newversion){
			$db = JFactory::getDbo();
			$ext = JComponentHelper::getComponent('com_digicom');
			$sql = 'SELECT `version` FROM `#__updates` WHERE `extension_id`='.$ext->id.' ORDER BY update_id DESC LIMIT 1';
			$db->setQuery($sql);
			$this->newversion = $db->loadResult();
		}
		return $this->newversion;
	}
	
	function hashNewVersion() {
		$current_version 	= $this->getVersion();
		$update_version 	= $this->getNewVersion();
		if (version_compare( $update_version, $current_version ) == 1 )
		{
			return true;
		} else {
			return false;
		}
	}
	
	function versionNotify(){
		$html = '';
		if($this->hashNewVersion()){
			$html = sprintf(JText::_('DIGICOM_NEWVERSION_AVAILABLE_OLD'),$this->getVersion());
			$html .= '<br />';
			$html .= sprintf(JText::_('DIGICOM_NEWVERSION_AVAILABLE_NEW'),$this->getNewVersion());
			$html .= '<br />';
			$html .= '<br />';
			$html .= '<a class="btn btn-small btn-danger" href="index.php?option=com_installer&view=update&filter_search=digicom">';
			$html .= '<i class="icon-download"></i> '.JText::_('DIGICOM_UPDATE_NOW');
			$html .= '</a>';
		} else {
			$html = sprintf(JText::_('DIGICOM_NEWVERSION_NOTAVAILABLE'),$this->getVersion());
		}
		return $html;
	}
	
	
}
