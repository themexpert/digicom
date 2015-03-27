<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

// TODO : Remove depricated JRequest

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
