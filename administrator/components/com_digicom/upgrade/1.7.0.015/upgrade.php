<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 341 $
 * @lastmodified	$LastChangedDate: 2013-10-10 14:28:28 +0200 (Thu, 10 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

	jimport( 'joomla.filesystem.path' );

	$configs015 = $this->getTableData('#__digicom_settings', 'id = 1');

	$config_set_default_value015 = array();

	if ($configs015->imagecatsizevalue == 0) $config_set_default_value015['imagecatsizevalue'] = '100';
 	if ($configs015->imagecatsizetype == 0) $config_set_default_value015['imagecatsizetype'] = '1';
	if ($configs015->imageprodsizefullvalue == 0) $config_set_default_value015['imageprodsizefullvalue'] = '300';
	if ($configs015->imageprodsizefulltype == 0) $config_set_default_value015['imageprodsizefulltype'] = '1';
	if ($configs015->imageprodsizethumbvalue == 0) $config_set_default_value015['imageprodsizethumbvalue'] = '100';
	if ($configs015->imageprodsizethumbtype == 0) $config_set_default_value015['imageprodsizethumbtype'] = '1';
	if ($configs015->imagecatdescvalue == 0) $config_set_default_value015['imagecatdescvalue'] = '10';
	//if ($configs015->imagecatdesctype == 0) $config_set_default_value015['imagecatdesctype'] = '0';
	if ($configs015->imageproddescvalue == 0) $config_set_default_value015['imageproddescvalue'] = '10';
	//if ($configs015->imageproddesctype == 0) $config_set_default_value015['imageproddesctype'] = '0';

	$this->updateTable('#__digicom_settings', $config_set_default_value015, 'id=1');

	$products_015 = $this->getTableData('#__digicom_products', 'images <> ""', '*', 'objectlist');

	foreach( $products_015 as $product015 ) {

		$imgs = explode("\n", $product015->images);

		$image = $thumb = "";
		if ( isset($imgs[0]) && (!empty($imgs[0])) ) {
			$thumb = $imgs[0];
		}

		if ( isset($imgs[1]) && !empty($imgs[1]) ) {
			$image = $imgs[1];
		}

		if ( empty($image) && !empty($thumb) ) {
			$image = $thumb;
		}

		if ( !empty($image) ) {

			require_once JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_digicom' . DS . 'libs' . DS . 'thumbnail.inc.php';

			$image = JPath::clean($image);
			$image = str_replace( '/', DS, $image );
			$image = str_replace( '\\', DS, $image );
			$image = str_replace( '..' . DS , '', $image );
			$image = trim(str_replace( 'images' . DS, JPATH_ROOT . DS . 'images' . DS, $image ));

			if (file_exists($image)) {

				$uniqid = uniqid( rand(), true );
				$filename = strtolower( $uniqid . '_' . JFile::getName($image) );

				$dest_file = JPATH_ROOT . DS . "images" . DS . "stories" . DS .  "digicom" . DS . "products" . DS .  $filename;
				$thumb_file = JPATH_ROOT . DS . "images" . DS . "stories" . DS .  "digicom" . DS . "products" . DS . "thumb" . DS .  $filename;

				if (!@ copy($image, $dest_file) ) {}

				// fullpath
				$thumbresizer = new Thumbnail( $dest_file );
				// 1- wide, 0 - high
				if ($configs015->imageprodsizefulltype == 1) {
					$thumbresizer->maxHeight = 0;
					$thumbresizer->maxWidth = $configs015->imageprodsizefullvalue;
					$newsize = $thumbresizer->calcWidth( $configs015->imageprodsizefullvalue, $thumbresizer->getCurrentHeight() );
				} else {
					$thumbresizer->maxWidth = 0;
					$thumbresizer->maxHeight = $configs015->imageprodsizefullvalue;
					$newsize = $thumbresizer->calcHeight( $thumbresizer->getCurrentWidth(), $configs015->imageprodsizefullvalue );
				}
				$thumbresizer->resize($newsize['newWidth'], $newsize['newHeight']);
				$thumbresizer->save($dest_file);
				$thumbresizer->destruct();

				// thumbpath
				$thumbresizer = new Thumbnail($dest_file);
				// 1- wide, 0 - high
				if ($configs015->imageprodsizethumbtype == 1) {
					$thumbresizer->maxHeight = 0;
					$thumbresizer->maxWidth = $configs015->imageprodsizethumbvalue;
					$newsize = $thumbresizer->calcWidth( $configs015->imageprodsizethumbvalue, $thumbresizer->getCurrentHeight() );
				} else {
					$thumbresizer->maxWidth = 0;
					$thumbresizer->maxHeight = $configs015->imageprodsizethumbvalue;
					$newsize = $thumbresizer->calcHeight( $thumbresizer->getCurrentWidth(), $configs015->imageprodsizethumbvalue );
				}
				$thumbresizer->resize($newsize['newWidth'], $newsize['newHeight']);
				$thumbresizer->save($thumb_file);
				$thumbresizer->destruct();

				$prodimages015 = explode(',\n', $product015->prodimages);
				$prodimages015[] = $filename;
				$prodimages015 = implode(',\n', $prodimages015);

				// update field
				$fields015 = array(
					'prodimages' => $prodimages015,
					'defprodimage' => $filename,
					'images' => ''
				);
				$this->updateTable('#__digicom_products', $fields015, 'id='.$product015->id);

			}
		}
	}

?>