<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;
 jimport('joomla.log.log');
//TODO : PHP property need to be added eg : public, private

class DigiComControllerLicense extends JControllerLegacy
{
	/*
	* this function need to run through cron job
	* url: index.php?option=com_digicom&task=license.checkLicence
	* the url for cron job	
	*/
	function checkLicence()
	{
		try
		{
		    $db 	= JFactory::getDbo();
			$query 	= $db->getQuery(true);
			$query->update($db->quoteName('#__digicom_licenses'));
			$query->set($db->quoteName('active') . ' = ' . $db->quote('0'));
			$query->where('DATEDIFF('.$db->quoteName('expires').', now()) <= ' .$db->quote('-1'));		
			//echo $query->__tostring();die;
			$db->setQuery($query);
			$db->query();

			echo JText::_('COM_DIGICOM_LICENSE_CHECK_SUCCESSFUL');
		}
		catch (Exception $e)
		{
		    JLog::add(JText::sprintf('COM_DIGICOM_LICENSE_CHECK_FAILED_CHECK_LOG',$e->getMessage()), JLog::ERROR, 'com_digicom');
		    echo JText::_('COM_DIGICOM_LICENSE_CHECK_FAILED');
		}

		JFactory::getApplication()->close();
	}

}
