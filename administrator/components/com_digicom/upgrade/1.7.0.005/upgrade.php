<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 341 $
 * @lastmodified	$LastChangedDate: 2013-10-10 14:28:28 +0200 (Thu, 10 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

	// Categories Images to Image and Thumbs
	$categories_005 = $this->getTableData('#__digicom_categories','','id, images, image, thumb', 'objectlist');

	// Images. The first will be used for thumbnails, the second as the large image
	foreach ( $categories_005 as $cat ) {

		$result_images = convertCategoriesImages_005( $cat );
		$this->updateTable('#__digicom_categories', $result_images, ' id='.$cat->id );
	}

function convertCategoriesImages_005( $cat ) {

		$z = explode("\n", $cat->images);

		$pathinfo_005 = $path_005 = '';

		// Large
		if ( isset($z[1]) && !empty($z[1]) ) {
			if ($path_005 = realpath($z[1])) {

			}
		} else if ( isset($z[0]) && !empty($z[0]) ) {
			// thumb
			if ($path_005 = realpath($z[0])) {

			}
		}

		if (!empty( $path_005 )) {
			$pathinfo_005 = pathinfo( $path_005 );
		}

		$result_image = $result_thumb = '';

		if ( !empty($pathinfo_005) ) {

			 $categories_rel_path = JPATH_ROOT . DS;
			 $categories_path = $categories_rel_path . 'images' . DS . 'stories' . DS . 'digicom' . DS . 'categories' . DS;
			 $categories_thumb_path = $categories_path . 'thumb' . DS;
			 
			if (!file_exists($categories_thumb_path)) {
				if (!@mkdir( $categories_thumb_path, 0755, true  )) {
					echo ('Can not create directory "' . $categories_thumb_path . '" for upload image');
				}
			}

			// Make the filename safe
			jimport('joomla.filesystem.file');
			$pathinfo_005['basename']	= JFile::makeSafe($pathinfo_005['basename']);

			$uniqid = uniqid (rand (),true);

			$filepath = $pathinfo_005['dirname'] . DS . $pathinfo_005['basename'];

			$store_filepath = JPath::clean($categories_path . strtolower($uniqid.'_'.$pathinfo_005['basename']));
			$store_thumbpath = JPath::clean($categories_thumb_path . strtolower($uniqid.'_'.$pathinfo_005['basename']));

			// resize source image file
			require_once JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_digicom' . DS . 'libs' . DS . 'thumbnail.inc.php';

			$thumbresizer = new Thumbnail($filepath);
			$thumbresizer->resize(200, 150);
			$thumbresizer->save($store_thumbpath);

			$sourceresizer = new Thumbnail($filepath);
			$sourceresizer->resize(800, 600);
			$sourceresizer->save($store_filepath);

			// prepare source image
			$image = str_replace(JPATH_ROOT, '', $store_filepath);
			$image = str_replace(DS, '/', $image);
			$image = ltrim($image,'/');
			$result_image = $image;

			// prepare thumb image
			$thumb = str_replace(JPATH_ROOT, '', $store_thumbpath);
			$thumb = str_replace(DS, '/', $thumb);
			$thumb = ltrim($thumb,'/');
			$result_thumb = $thumb;
		}

		return array('image' => $result_image, 'thumb' => $result_thumb);
}

?>