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
global $isJ25;
$jv = new JVersion();
$isJ25 = $jv->RELEASE == '2.5';
if ($isJ25) {
	jimport( 'joomla.application.component.controller' );
	jimport('joomla.application.component.model');
	jimport( 'joomla.application.component.view');
	class DigiComModel		extends JModel {}
	class DigiComView		extends JView {}
}else{
	class DigiComModel		extends JModelLegacy {}
	class DigiComView		extends JViewLegacy {}
}
// Config Singelton
require_once( JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers' . DS . 'config.php' );
// Debug and Log helper
require_once( JPATH_COMPONENT_SITE . DS . 'helpers' . DS . 'helper.php' );
require_once( JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers' . DS . 'log.php' );
// Image Helper
require_once( JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers' . DS . 'image.php' );
// Google Analitics
require_once( JPATH_COMPONENT_SITE . DS . 'helpers' . DS . 'google.php' );
// session handler
require_once( JPATH_COMPONENT_SITE . DS . 'helpers' . DS . 'session.php' );

require_once( JPATH_COMPONENT_SITE . DS . 'helpers' . DS . 'template.php' );

//get the var value
$controller = JRequest::getCmd('controller');
$view = JRequest::getWord('view');
$task = JRequest::getCmd('task');
$cronparam = JRequest::getVar('cron','');

//cron jobs
if ($cronparam != '') {
	echo "Cron execute";
	require_once( JPATH_COMPONENT_SITE . DS . 'helpers' . DS . 'cronjobs.php' );
	cronjobs();
	exit;
}

if($task=='get_cursym'){
	DigiComHelper::get_cursym();
	die();
}

if(strlen(trim($view)) > 0 && strlen(trim($controller)) < 1){
	$view_to_controller = array("cart" => "cart",
			"licenses" => "licenses",
			"orders" => "orders",
			"categories" => "categories",
			"products" => "products",
			"profile" => "profile");
	$layout_to_task = array("");
	$controller = @$view_to_controller[strtolower($view)];
}

JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables');

$ajax_req = JRequest::getVar("no_html", 0, "request");
