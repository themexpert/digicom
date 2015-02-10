<?php
define( '_JEXEC', 1 );
define('JPATH_BASE', substr(substr(dirname(__FILE__), 0, strpos(dirname(__FILE__), "administra")),0,-1));
if (!isset($_SERVER["HTTP_REFERER"])) exit("Direct access not allowed.");
$mosConfig_absolute_path =substr(JPATH_BASE, 0, strpos(JPATH_BASE, "/administra")); 
define( 'DS', DIRECTORY_SEPARATOR );
require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );
require_once ( JPATH_BASE .DS.'libraries'.DS.'joomla'.DS.'methods.php');
require_once ( JPATH_BASE .DS.'configuration.php' );
require_once ( JPATH_BASE .DS.'libraries'.DS.'joomla'.DS.'base'.DS.'object.php');
require_once ( JPATH_BASE .DS.'libraries'.DS.'joomla'.DS.'database'.DS.'database.php');
require_once ( JPATH_BASE .DS.'libraries'.DS.'joomla'.DS.'database'.DS.'database'.DS.'mysql.php');
require_once ( JPATH_BASE .DS.'libraries'.DS.'joomla'.DS.'filesystem'.DS.'folder.php');
$config = JFactory::getConfig();
$options = array (
		"host" => $config->get('host'),
		"user" => $config->get('user'),
		"password" => $config->get('password'),
		"database" => $config->get('db'),
		"prefix" => $config->get('dbprefix'));

class iJoomlaDatabase extends JDatabaseMySQL{
	public function __construct($options){
		parent::__construct($options);
	}
}

$database = new iJoomlaDatabase($options);

$task = JRequest::getVar("task", "", "get", "string");
switch($task){
	case "addnote" : addNote(); 
		break;
}

function addNote(){
	global $database;

	$licid = JRequest::getVar("licid", "0");
	$text = JRequest::getVar("text", "");
	$expire = JRequest::getVar("expire", "");

	$sql = "insert into #__digicom_licenses_notes (`lic_id`, `notes`, `expires`) values (".intval($licid).", '".trim(addslashes($text))."', '".trim($expire)."')";
	$database->setQuery($sql);
	$database->query();

	$sql = "select max(id) from #__digicom_licenses_notes";
	$database->setQuery($sql);
	$database->query();
	$max_id = $database->loadResult();
	echo $max_id;
}

?>	