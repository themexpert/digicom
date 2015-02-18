<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 398 $
 * @lastmodified	$LastChangedDate: 2013-11-04 05:07:10 +0100 (Mon, 04 Nov 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license


*/

defined ('_JEXEC') or die ("Go away.");
jimport ('joomla.application.component.controller');
class DigiComControllerDownloads extends DigiComController
{

	var $_model = null;
	var $_config = null;
	var $_order = null;

	function __construct () {
		global $Itemid;
		parent::__construct();
		$this->registerTask ("", "listDownloads");
		$this->registerTask ("makeDownload", "makeDownload");
		
		$this->_model = $this->getModel("Downloads");
		$this->_config = $this->getModel("Config");
		$this->_order = $this->getModel("Order");
		$this->_customers_model = $this->getModel("Customer");

		$this->log_link = JRoute::_("index.php?option=com_digicom&view=profile&task=login&returnpage=orders&Itemid=".$Itemid, false);
		$this->prof_link = JRoute::_("index.php?option=com_digicom&view=profile&task=edit&returnpage=orders&Itemid=".$Itemid, false);
		$this->order_link = JRoute::_("index.php?option=com_digicom&view=orders&Itemid=".$Itemid, false);
	}
	
	function listDownloads()
	{
		global $Itemid;
		if($this->_customer->_user->id < 1)
		{
			$this->setRedirect(JRoute::_($this->log_link, false));
			return;
		}

		JRequest::setVar ("view", "Downloads");
		$view = $this->getView("Downloads", "html");
		$view->setModel($this->_model, true);
		$view->setModel($this->_config);
		$view->setModel($this->_order);
		$view->setModel($this->_customers_model);
		$conf = $this->_config->getConfigs();
		$view->display();
	}
	
	function makeDownload()
	{
		require_once( JPATH_COMPONENT_SITE . DS . 'helpers' . DS . 'downloadfileclass.inc' );
		$fileInfo = $this->_model->getfileinfo();
		if(empty($fileInfo->url)){
			$itemid = JFactory::getApplication()->input->get('itemid',0);
			$msg = JText::sprintf('COM_DIGICOM_FILE_DONT_EXIST_DETAILS',$fileInfo->name);
			JFactory::getApplication()->redirect('index.php?option=com_digicom&view=downloads&Itemid='.$itemid,$msg);
		}
		
		$parsed = parse_url($fileInfo->url);
		if (empty($parsed['scheme'])) {
			$fileLink = JPATH_BASE.DS.$fileInfo->url;
		}else{
			$fileLink = $fileInfo->url;
		}
		
		//update hits
		$files =   JTable::getInstance('Files', 'Table');
		$files->load($fileInfo->id);
		$files->hits = $files->hits+1;
		$files->store();
		
		$downloadfile = new DOWNLOADFILE($fileLink);
		if (!$downloadfile->df_download()){
			$itemid = JFactory::getApplication()->input->get('itemid',0);
			$msg = JText::_sprintf("COM_DIGICOM_FILE_DOWNLOAD_FAILED",$fileInfo->name);
			//$msg = JText::sprintf('COM_DIGICOM_FILE_DONT_EXIST_DETAILS',$fileInfo->name);
			JFactory::getApplication()->redirect('index.php?option=com_digicom&view=downloads&Itemid='.$itemid,$msg);
		}			
		die;
	}
	
}
