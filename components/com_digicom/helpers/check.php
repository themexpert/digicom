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

$task = JRequest::getVar("task", "");
if($task == "user.login" || $task == "user.logout"){
	$username = JRequest::getVar("username", "");
	$password = JRequest::getVar("password", "");
	$return = JRequest::getVar("return", "");
	define(JPATH_COMPONENT, JPATH_SITE.DS."components".DS."com_users");

	require_once(JPATH_SITE.DS."components".DS."com_users".DS."controllers".DS."user.php");
	if($task == "user.login"){
		UsersControllerUser::login();
	}
	else{
		UsersControllerUser::logout();
	}
}
