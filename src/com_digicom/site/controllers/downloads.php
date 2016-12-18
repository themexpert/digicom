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

	/*
	* repricated old method, to shorten the url
	*/
	function makeDownload()
	{
		$this->go();
	}

	function go()
	{
		global $Itemid;
		$dispatcher	= JDispatcher::getInstance();
		$model 			= $this->getModel("Downloads");
		$customer   = new DigiComSiteHelperSession();

		$result = $dispatcher->trigger('onDigicomDownloadInitialize',array('com_digicom.download', &$customer));
		
		if( 
			(
				$customer->_user->id < 1 
				and 
				( 
					!isset($result[0]) 
					or 
					in_array(false, $result) 
				)
			)
			or
			in_array(false, $result) 
		)
		{

			$this->setRedirect(JRoute::_("index.php?option=com_digicom&view=profile&layout=login&returnpage=downloads&Itemid=".$Itemid, false));
			return;
		}

		$fileInfo = $model->getfileinfo();

		$access = DigiComSiteHelperDigiCom::checkUserAccessToFile($fileInfo, $customer->_user->id);
		$result = $dispatcher->trigger('onDigicomDownloadCheckAccess',array('com_digicom.download', $fileInfo, $customer->_user));

		if(
			(
				!$access
				and
				(
					!isset($result[0]) 
					or 
					in_array(false, $result) 
				)
			)
			or 
			in_array(false, $result)
		)
		{

			// Wrong Download ID
			$msg = array(
				'access' => JText::_('COM_DIGICOM_DOWNLOADS_ACCESS_DENIED')
			);
			$msgcode = json_encode($msg);
			echo $msgcode;
			JFactory::getApplication()->close();
		}
		

		if(empty($fileInfo->url)){
			$itemid = JFactory::getApplication()->input->get('itemid',0);
			$msg = JText::sprintf('COM_DIGICOM_DOWNLOADS_FILE_DONT_EXIST_DETAILS',$fileInfo->name);
			JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_digicom&view=downloads&Itemid='.$itemid), $msg, 'warning');
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

		$downloadfile = new DigiComSiteHelperDownloadFile($fileLink, $basefileLink);

		$info = array(
			'fileinfo' => $fileInfo
		);

		$params = JComponentHelper::getParams('com_digicom');
		$type = 0; 
		$directLink = $params->get('directfilelink', 0);
		
		$logid = DigiComSiteHelperLog::setLog('download', 'downloads go method', $fileInfo->id, 'Download product : '.$fileInfo->product_name . ', file : '. $fileInfo->name, json_encode($info), 'complete', $fileInfo->product_id);
		
		if (!$downloadfile->download($type, $directLink))
		{
			DigiComSiteHelperLog::update($logid, 'failed', array('error' => $downloadfile->__toString()));

			$itemid = JFactory::getApplication()->input->get('itemid',0);
			$msg = JText::sprintf("COM_DIGICOM_FILE_DOWNLOAD_FAILED",$fileInfo->name);
			JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_digicom&view=downloads&Itemid='.$itemid), $msg, 'warning');

		}

	}

}
