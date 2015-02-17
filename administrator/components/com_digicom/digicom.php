<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 355 $
 * @lastmodified	$LastChangedDate: 2013-10-11 13:18:38 +0200 (Fri, 11 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

defined ('_JEXEC') or die ("Go away.");
defined ('DS') or define('DS', DIRECTORY_SEPARATOR);

include_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'loader.php');

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_digicom'))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

require_once(JPATH_COMPONENT.DS.'controller.php');
$controller = JRequest::getCmd('controller');
$task = JRequest::getVar("task", "");

if($controller)
{
	$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
	if(file_exists($path))
	{
		require_once($path);
	}
	else
	{
	 	$controller = '';
	}
}

$classname = "DigiComAdminController".$controller;
$controller = new $classname();
$controller->execute ($task);
$controller->redirect();

DigiComAdminHelper::setSidebarRight();