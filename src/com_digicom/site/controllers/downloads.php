<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

//TODO : PHP property need to be added eg : public, private

class DigiComControllerDownloads extends JControllerLegacy
{

	var $_model = null;
	var $_config = null;
	var $_order = null;
	var $_customer = null;

	function __construct () {
		global $Itemid;
		parent::__construct();

		$this->_model = $this->getModel("Downloads");
		$this->_config = $this->getModel("Config");
		$this->_order = $this->getModel("Order");
		$this->_customers_model = $this->getModel("Customer");

		$this->log_link = JRoute::_("index.php?option=com_digicom&view=profile&layout=login&returnpage=downloads&Itemid=".$Itemid, false);
		$this->_customer = new DigiComSiteHelperSession();;
	}

	function makeDownload()
	{
		$dispatcher	= JDispatcher::getInstance();
		$customer   = $this->_customer;
		if($customer->_user->id < 1)
		{
			$result = $dispatcher->trigger('onDigicomDownloadInitialize',array('com_digicom.download', $customer));

			if (!isset($result[0]) or in_array(false, $result))
			{
				$this->setRedirect(JRoute::_($this->log_link, false));
				return;
			}
		}

		$fileInfo = $this->_model->getfileinfo();

		DigiComSiteHelperDigiCom::checkUserAccessToFile($fileInfo, $customer->_user->id);
		
		if(empty($fileInfo->url)){
			$itemid = JFactory::getApplication()->input->get('itemid',0);
			$msg = JText::sprintf('COM_DIGICOM_DOWNLOADS_FILE_DONT_EXIST_DETAILS',$fileInfo->name);
			JFactory::getApplication()->redirect('index.php?option=com_digicom&view=downloads&Itemid='.$itemid, $msg, 'warning');
		}

		$parsed = parse_url($fileInfo->url);
		$fileLink = $fileInfo->url;

		if (empty($parsed['scheme'])) {
			$basefileLink = JPATH_BASE . $fileInfo->url;
		}else{
			$basefileLink = $fileInfo->url;
		}

		//update hits
		$files = JTable::getInstance('Files', 'Table');
		$files->load($fileInfo->id);
		$files->hits = $files->hits+1;
		$files->store();

		$downloadfile = new DigiComSiteHelperDownloadFile($fileLink,$basefileLink);

		$info = array(
			'fileinfo' => $fileInfo
		);

		if (!$downloadfile->download()){

			DigiComSiteHelperLog::setLog('download', 'downloads makeDownload', $fileInfo->id, 'Download product : '.$fileInfo->product_name . ', file : '. $fileInfo->name, json_encode($info),'failed');

			$itemid = JFactory::getApplication()->input->get('itemid',0);
			$msg = JText::sprintf("COM_DIGICOM_FILE_DOWNLOAD_FAILED",$fileInfo->name);
			JFactory::getApplication()->redirect('index.php?option=com_digicom&view=downloads&Itemid='.$itemid,$msg);

		}
		DigiComSiteHelperLog::setLog('download', 'downloads makeDownload', $fileInfo->id, 'Download product : '.$fileInfo->product_name . ', file : '. $fileInfo->name, json_encode($info));

	}

}
