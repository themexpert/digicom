<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 422 $
 * @lastmodified	$LastChangedDate: 2013-11-16 11:37:09 +0100 (Sat, 16 Nov 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

defined ('_JEXEC') or die ("Go away.");
defined ('DS') or define('DS', DIRECTORY_SEPARATOR);
defined ('DIGICOM_ASSET_PATH') or define('DIGICOM_ASSET_PATH','media'.DS.'digicom'.DS.'assets');

require_once( JPATH_COMPONENT_SITE . DS . 'helpers' . DS . 'loader.php' );
require_once(JPATH_COMPONENT.DS.'controller.php');

$controller = ($controller ? $controller :'categories');
$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
if(file_exists($path)){
	require_once($path);
}else{
	$controller = 'categories';
	require JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
}



$classname = "DigiComController".$controller;
$controller = new $classname();
$controller->execute ($task);
$controller->redirect();


if (JRequest::getCmd('format') != 'json')
{
    echo "\n<!-- ThemeXpert \"DigiCom\" | Developed by abu-huraira.me -->\n\n";
}