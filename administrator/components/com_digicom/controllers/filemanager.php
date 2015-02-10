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

jimport ('joomla.application.component.controller');

class DigiComAdminControllerFileManager extends DigiComAdminController {

	var $_model = null;

	function __construct(){
		parent::__construct();
		require_once( JPATH_COMPONENT.DS.'helpers'.DS.'helper.php' );
		$this->registerTask ("", "FileManager");
	}

	function FileManager(){
		$view = $this->getView("FileManager", "html");
		$view->display();
	}

	function vimeo(){
   		JRequest::setVar( 'view', 'FileManager' );
		JRequest::setVar( 'layout', 'vimeo'  );
		$view = $this->getView("FileManager", "html");
		$view->setLayout("vimeo");
		$view->vimeo();
		die();
	}
	
	function connector()
	{
        $mainframe = JFactory::getApplication();
		$params = JComponentHelper::getParams('com_media');
		$root = $params->get('file_path', 'digicom/');
		$root = 'digicom';
		$folder = JRequest::getVar('folder', $root, 'default', 'path');
		if (JString::trim($folder) == "")
		{
			$folder = $root;
		}
		else
		{
			// Ensure that we are always below the root directory
			if (strpos($folder, $root) !== 0)
			{
				$folder = $root;
			}
		}
		
		// Disable debug
		JRequest::setVar('debug', false);
		
		$url = JURI::root(true).'/'.$folder;
		$path = JPATH_SITE.DS.JPath::clean($folder);
        JPath::check($path);
		include_once JPATH_COMPONENT_ADMINISTRATOR.DS.'libs'.DS.'elfinder'.DS.'elFinderConnector.class.php';
		include_once JPATH_COMPONENT_ADMINISTRATOR.DS.'libs'.DS.'elfinder'.DS.'elFinder.class.php';
		include_once JPATH_COMPONENT_ADMINISTRATOR.DS.'libs'.DS.'elfinder'.DS.'elFinderVolumeDriver.class.php';
		include_once JPATH_COMPONENT_ADMINISTRATOR.DS.'libs'.DS.'elfinder'.DS.'elFinderVolumeLocalFileSystem.class.php';
		function access($attr, $path, $data, $volume)
		{
			$mainframe = JFactory::getApplication();
			// Hide PHP files.
			$ext = strtolower(JFile::getExt(basename($path)));
			if ($ext == 'php')
			{
				return true;
			}
			
			// Hide files and folders starting with .
			if (strpos(basename($path), '.') === 0 && $attr == 'hidden')
			{
				return true;
			}
			// Read only access for front-end. Full access for administration section.
			switch($attr)
			{
				case 'read' :
				return true;
				break;
				case 'write' :
				return ($mainframe->isSite()) ? false : true;
				break;
				case 'locked' :
				return ($mainframe->isSite()) ? true : false;
				break;
				case 'hidden' :
				return false;
				break;
			}
			
		}
		
		if ($mainframe->isAdmin())
		{
			$permissions = array(
			'read' => true,
			'write' => true
			);
		}
		else
		{
			$permissions = array(
			'read' => true,
			'write' => false
			);
		}
        
		$options = array(
		'debug' => false,
		'roots' => array( array(
		'driver' => 'LocalFileSystem',
		'path' => $path,
		'URL' => $url,
		'accessControl' => 'access',
		'defaults' => $permissions
		))
		);
        
		$connector = new elFinderConnector(new elFinder($options));
		$connector->run();
        
	}
};

?>