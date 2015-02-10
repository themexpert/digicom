<?php
/*
// JoomlaWorks "AllVideos" Plugin for Joomla! 1.5.x - Version 3.3
// Copyright (c) 2006 - 2010 JoomlaWorks Ltd. All rights reserved.
// Released under the GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
// More info at http://www.joomlaworks.gr
// Designed and developed by the JoomlaWorks team
// *** Last update: February 18th, 2010 ***
*/

// Set flag that this is a parent file
define('_JEXEC',1);

define('DS',DIRECTORY_SEPARATOR);
define('JPATH_BASE', '..'.DS.'..'.DS.'..'.DS.'..');

// Includes
require_once(JPATH_BASE.DS.'includes'.DS.'defines.php');
require_once(JPATH_BASE.DS.'includes'.DS.'framework.php');
jimport('joomla.filesystem.file');

// API
$mainframe= JFactory::getApplication('site');
$document = JFactory::getDocument();

// Assign paths
$sitePath = str_replace(DS.'plugins'.DS.'content'.DS.'jw_allvideos'.DS.'includes','',dirname(__FILE__));
$siteUrl  = str_replace('/plugins/content/jw_allvideos/includes/','',JURI::root());

// Define error handling
$nogo = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>'.$mainframe->getCfg('sitename').'</title>
		<link rel="stylesheet" href="'.$siteUrl.'/templates/system/css/error.css" type="text/css" />
	</head>
	<body>
		<div align="center">
			<div id="outline">
			<div id="errorboxoutline">
				<div id="errorboxheader">'.JText::_('AllVideos Error').'</div>
				<div id="errorboxbody">
				<p><strong>'.JText::_('You may not be able to download the requested file for the following reasons:').'</strong></p>
					<ol>
						<li>'.JText::_('Download file unavailable').'</li>
						<li>'.JText::_('Wrong file path set').'</li>
						<li>'.JText::_('Illegal code execution').'</li>
					</ol>
				<p><strong>'.JText::_('Please try one of the following pages:').'</strong></p>
				<p>
					<ul>
						<li><a href="javascript:history.go(-1);">'.JText::_('Return to the previous page').'</a></li>
						<li><a href="'.$siteUrl.'/" title="'.JText::_('Go to the home page').'">'.JText::_('Home page').'</a></li>
					</ul>
				</p>
				<p>'.JText::_('If difficulties persist, please contact the system administrator of this site.').'</p>
				</div>
			</div>
			</div>
		</div>
	</body>
</html>
';

// Start the process
$pathToSourceFile = JRequest::getString('file');
$pathToSourceFile = preg_replace('#[/\\\\]+#', DS, $pathToSourceFile);

if(strpos($pathToSourceFile, '..') !== false || strpos($pathToSourceFile, './') !== false){
 echo $nogo;
 exit;
}

// Reference the "/images" or "/media/k2/videos" directory
$ref_com_content = $siteUrl.'/'.substr(str_replace(DS,'/',$pathToSourceFile),0,strlen('images/'));
$check_com_content = $siteUrl."/images/";

$ref_com_k2 = $siteUrl.'/'.substr(str_replace(DS,'/',$pathToSourceFile),0,strlen('media/k2/videos/'));
$check_com_k2 = $siteUrl."/media/k2/videos/";

if(isset($pathToSourceFile) && ($ref_com_content===$check_com_content || $ref_com_k2===$check_com_k2)){
	$getfile = $pathToSourceFile;
} else {
	$getfile = NULL;
}

if (!$getfile) {
	// go no further if filename not set
	echo $nogo;
} else {
	// define the pathname to the file
	$filepath = $sitePath.DS.str_replace('/',DS,$getfile);

	// check that it exists and is readable
	if (file_exists($filepath) && is_readable($filepath)) {
		// get the file's size and send the appropriate headers
		$size = filesize($filepath);
		header('Content-Type: application/force-download');
		header('Content-Length: '.$size);
		header('Content-Disposition: attachment; filename="'.basename($getfile).'"');
		header('Content-Transfer-Encoding: binary');
		// open the file in binary read-only mode - suppress error messages if the file cannot be opened
		$file = @ fopen($filepath, 'rb');
		if ($file) {
			// stream the file and exit the script when complete
			fpassthru($file);
			exit;
		} else {
			echo $nogo;
		}
	} else {
		echo $nogo;
	}
}
