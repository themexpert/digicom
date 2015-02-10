<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 341 $
 * @lastmodified	$LastChangedDate: 2013-10-10 14:28:28 +0200 (Thu, 10 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

jimport('joomla.filesystem.file');

// categories image fixed remove path
$dest_file016  = JPATH_ROOT . DS . "images" . DS . "stories" . DS .  "digicom" . DS . "categories" . DS;
$thumb_file016 = JPATH_ROOT . DS . "images" . DS . "stories" . DS .  "digicom" . DS . "categories" . DS . "thumb" . DS;

/*
 * this function removes a directory and its contents.
 * use with careful, no undo!
 */
 /*
function rmdir_recursive016($dir) {
	$files = scandir($dir);
	array_shift($files);	// remove '.' from array
	array_shift($files);	// remove '..' from array
   
	foreach ($files as $file) {
		$file = $dir . '/' . $file;
		if (is_dir($file)) {
			rmdir_recursive($file);
			rmdir($file);
		} else {
			unlink($file);
		}
	}
	rmdir($dir);
}
 
if (file_exists($thumb_file016)) 
	rmdir_recursive016($thumb_file016);
*/
$categories016 = $this->getTableData('#__digicom_categories', "image <> ''", '*', 'objectlist');

// resize source image file
require_once JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_digicom' . DS . 'libs' . DS . 'thumbnail.inc.php';

$configs016 = $this->getTableData('#__digicom_settings', 'id = 1');

foreach ( $categories016 as $cat16 ) {

	$image = str_replace('images/stories/digicom/categories/','',$cat16->image);

	// fullpath
	$thumbresizer = new Thumbnail( $dest_file016 . $image );
	// 1 - wide, 0 - high
	if ($configs016->imagecatsizetype == 1) {
		$thumbresizer->maxHeight = 0;
		$thumbresizer->maxWidth = $configs016->imagecatsizevalue;
		$newsize = $thumbresizer->calcWidth( $configs016->imagecatsizevalue, $thumbresizer->getCurrentHeight() );
	} else {
		$thumbresizer->maxWidth = 0;
		$thumbresizer->maxHeight = $configs016->imagecatsizevalue;
		$newsize = $thumbresizer->calcHeight( $thumbresizer->getCurrentWidth(), $configs016->imagecatsizevalue );
	}
	$thumbresizer->resize($newsize['newWidth'], $newsize['newHeight']);
	$thumbresizer->save($dest_file016 . $image);
	$thumbresizer->destruct();

	$fields16 = array(
		'image' => $image,
		'images' => '',
		'thumb' => ''
	);

	$this->updateTable( '#__digicom_categories', $fields16, 'id='.$cat16->id );
}

?>