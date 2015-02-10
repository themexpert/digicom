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

class ImageHelper {
	/**
	 *	Genarate Category Image 
	 *
	 * 	return: full url to image
	 */
	public static function ShowCategoryImage($imagename=null){
		if(is_null($imagename) || empty($imagename)){
			return null;
		}

		$path = JPATH_ROOT.DS."images".DS."stories".DS."digicom".DS."categories".DS;
		$pathfly = JPATH_ROOT.DS."images".DS."stories".DS."digicom".DS."categories".DS.'fly'.DS;
		$filepath = $path.$imagename;
		$filepathfly = $pathfly.$imagename;
		$resize_flag = true;

		if(file_exists($filepathfly)){
			$filesize = getimagesize($filepathfly);
			if(DCConfig::get('catlayoutimagetype') == 1){
				if(DCConfig::get('catlayoutimagesize') == $filesize["0"]){
					$resize_flag = false;
				}
			}
			else{
				if(DCConfig::get('catlayoutimagesize') == $filesize["1"]){
					$resize_flag = false;
				}
			}
		}

		if($resize_flag){
			if(file_exists($filepath)){
				if (!file_exists($pathfly)){
					@mkdir($pathfly, 0755);
				}

				// resize source image file
				require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'libs'.DS.'thumbnail.inc.php';

				// fullpath
				$thumbresizer = new Thumbnail($filepath);
				// 1- wide, 0 - high
				if(DCConfig::get('catlayoutimagetype') == 1){
					$thumbresizer->maxHeight = 0;
					$thumbresizer->maxWidth = DCConfig::get('catlayoutimagesize');
					$newsize = $thumbresizer->calcWidth( DCConfig::get('catlayoutimagesize'), $thumbresizer->getCurrentHeight() );
				}
				else{
					$thumbresizer->maxWidth = 0;
					$thumbresizer->maxHeight = DCConfig::get('catlayoutimagesize');
					$newsize = $thumbresizer->calcHeight( $thumbresizer->getCurrentWidth(), DCConfig::get('catlayoutimagesize','100') );
				}
				$thumbresizer->resize($newsize['newWidth'], $newsize['newHeight']);
				$thumbresizer->save($filepathfly);
				$thumbresizer->destruct();
				$url = JURI::root() . 'images/stories/digicom/categories/fly/'.$imagename;
			}
			else{
				$url = null;
			}
		}
		else{
			$url = JURI::root().'images/stories/digicom/categories/fly/'.$imagename;
		}
		return $url;
	}

	/**
	 *	Genarate Product Image 
	 *
	 * 	return: full url to image
	 */
	public static function GetProductImageURL($imagename=null, $popup=""){
		if(is_null($imagename) || empty($imagename)){
			return null;
		}

		$path = JPATH_ROOT . DS . "images" . DS . "stories" . DS .  "digicom" . DS . "products" . DS;
		$pathfly = JPATH_ROOT . DS . "images" . DS . "stories" . DS .  "digicom" . DS . "products" . DS . 'fly' . DS;

		$filepath = $path . $imagename;
		$filepathfly = $pathfly . $imagename;

		$resize_flag = true;

		$true_size = DCConfig::get('prodlayoutthumbnails');
		if($popup == "popup"){
			$true_size = DCConfig::get('cart_popoup_image');
			$pathfly = JPATH_ROOT . DS . "images" . DS . "stories" . DS .  "digicom" . DS . "products" . DS . 'popup' . DS;
			$filepathfly = $pathfly . $imagename;
		}

		if(file_exists($filepathfly)){
			$filesize = getimagesize($filepathfly);
			if(DCConfig::get('prodlayoutthumbnailstype') == 1){
				if($true_size == $filesize[0]){
					$resize_flag = false;
				}
			}
			else{
				if($true_size == $filesize[1]){
					$resize_flag = false;
				}
			}
		}

		if($resize_flag){
			if(file_exists($filepath)){
				if(!file_exists($pathfly)){
					@mkdir($pathfly, 0755); 
				}
				// resize source image file
				require_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'libs' . DS . 'thumbnail.inc.php';

				// fullpath
				$thumbresizer = new Thumbnail($filepath);
				// 1- wide, 0 - high
				if(DCConfig::get('prodlayoutthumbnailstype') == 1){
					$thumbresizer->maxHeight = 0;
					$thumbresizer->maxWidth = $true_size;
					$newsize = $thumbresizer->calcWidth( $true_size, $thumbresizer->getCurrentHeight() );
				}
				else{
					$thumbresizer->maxWidth = 0;
					$thumbresizer->maxHeight = $true_size;
					$newsize = $thumbresizer->calcHeight( $thumbresizer->getCurrentWidth(), $true_size );
				}
				$thumbresizer->resize($newsize['newWidth'], $newsize['newHeight']);
				$thumbresizer->save($filepathfly);
				$thumbresizer->destruct();
				if($popup == "popup"){
					$url = JURI::root() . 'images/stories/digicom/products/popup/'.$imagename;
				}
				else{
					$url = JURI::root() . 'images/stories/digicom/products/fly/'.$imagename;
				}
			}
			else{
				$url = null;
			}
		}
		else{
			if($popup == "popup"){
				$url = JURI::root() . 'images/stories/digicom/products/popup/'.$imagename;
			}
			else{
				$url = JURI::root() . 'images/stories/digicom/products/fly/'.$imagename;
			}
		}
		return $url;
	}


	public static function shouStoreLogoThumb($imagename=null){
		if (is_null($imagename) || empty($imagename)) return null;

		$path = JPATH_ROOT . DS . "images" . DS . "stories" . DS .  "digicom" . DS . "store_logo" . DS;
		$pathfly = JPATH_ROOT . DS . "images" . DS . "stories" . DS .  "digicom" . DS . "store_logo" . DS . 'thumb' . DS;

		$filepath = $path . $imagename;
		$filepathfly = $pathfly . $imagename;

		$resize_flag = true;

		if(file_exists($filepathfly)) {
			$filesize = getimagesize($filepathfly);
			if (DCConfig::get('imageprodsizethumbtype') == 1) {
				if ( DCConfig::get('imageprodsizethumbvalue') ==  $filesize[0] ) { $resize_flag = false; }
			} else {
				if ( DCConfig::get('imageprodsizethumbvalue') ==  $filesize[1] ) { $resize_flag = false; }
			}
		}

		/*if($resize_flag) {

			if ( file_exists($filepath) ) {

				if (!file_exists($pathfly)) { @mkdir($pathfly, 0755); }

					// resize source image file
					require_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'libs' . DS . 'thumbnail.inc.php';

					// fullpath
					$thumbresizer = new Thumbnail($filepath);
					// 1- wide, 0 - high
					if (DCConfig::get('imageprodsizethumbtype') == 1) {
						$thumbresizer->maxHeight = 0;
						$thumbresizer->maxWidth = DCConfig::get('imageprodsizethumbvalue');
						$newsize = $thumbresizer->calcWidth( DCConfig::get('imageprodsizethumbvalue'), $thumbresizer->getCurrentHeight() );
					} else {
						$thumbresizer->maxWidth = 0;
						$thumbresizer->maxHeight = DCConfig::get('imageprodsizethumbvalue');
						$newsize = $thumbresizer->calcHeight( $thumbresizer->getCurrentWidth(), DCConfig::get('imageprodsizethumbvalue') );
					}
					$thumbresizer->resize($newsize['newWidth'], $newsize['newHeight']);
					$thumbresizer->save($filepathfly);
					$thumbresizer->destruct();

					$url = JURI::root() . 'images/stories/digicom/store_logo/thumb/'.$imagename;

			} else {
				$url = null;
			}

		} else {
			$url = JURI::root() . 'images/stories/digicom/store_logo/thumb/'.$imagename;
		}

		return $url;*/
		if($resize_flag){
			if(file_exists($filepath)){
				if (!file_exists($pathfly)){
					@mkdir($pathfly, 0755);
				}

				// resize source image file
				require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'libs'.DS.'thumbnail.inc.php';

				// fullpath
				$thumbresizer = new Thumbnail($filepath);
				// 1- wide, 0 - high
				if(DCConfig::get('catlayoutimagetype') == 1){
					$thumbresizer->maxHeight = 0;
					$thumbresizer->maxWidth = DCConfig::get('catlayoutimagesize');
					$newsize = $thumbresizer->calcWidth( DCConfig::get('catlayoutimagesize'), $thumbresizer->getCurrentHeight() );
				}
				else{
					$thumbresizer->maxWidth = 0;
					$thumbresizer->maxHeight = DCConfig::get('catlayoutimagesize');
					$newsize = $thumbresizer->calcHeight( $thumbresizer->getCurrentWidth(), DCConfig::get('catlayoutimagesize') );
				}
				$thumbresizer->resize($newsize['newWidth'], $newsize['newHeight']);
				$thumbresizer->save($filepathfly);
				$thumbresizer->destruct();
				$url = JURI::root() . 'images/stories/digicom/store_logo/thumb/'.$imagename;
			}
			else{
				$url = null;
			}
		}
		else{
			$url = JURI::root().'images/stories/digicom/store_logo/thumb/'.$imagename;
		}
		return $url;
	}


	/**
	 *	Genarate Product Thumb Image 
	 *
	 * 	return: full url to image
	 */
	public static function GetProductThumbImageURL( $imagename=null, $prev="") {
		if (is_null($imagename) || empty($imagename)) return null;

		$path = JPATH_ROOT . DS . "images" . DS . "stories" . DS .  "digicom" . DS . "products" . DS;
		$pathfly = JPATH_ROOT . DS . "images" . DS . "stories" . DS .  "digicom" . DS . "products" . DS . 'thumb' . DS;

		$filepath = $path . $imagename;
		$filepathfly = $pathfly . $imagename;

		$resize_flag = true;

		$from_database_size = DCConfig::get('imageprodsizethumbvalue');
		if($prev != ""){
			$from_database_size = DCConfig::get('prodlayoutlargeimgprev');
			$pathfly = JPATH_ROOT . DS . "images" . DS . "stories" . DS .  "digicom" . DS . "products" . DS . "viewproduct" . DS;
			$filepathfly = $pathfly . $imagename;
		}

		if(file_exists($filepathfly)) {
			$filesize = getimagesize($filepathfly);
			if(DCConfig::get('prodlayoutlargeimgprevtype') == 1){
				if($from_database_size ==  $filesize[0]){
					$resize_flag = false;
				}
			}
			else{
				if($from_database_size ==  $filesize[1]){
					$resize_flag = false;
				}
			}
		}

		if($resize_flag){
			if ( file_exists($filepath) ) {

				if (!file_exists($pathfly)) { @mkdir($pathfly, 0755); }

					// resize source image file
					require_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'libs' . DS . 'thumbnail.inc.php';

					// fullpath
					$thumbresizer = new Thumbnail($filepath);
					// 1- wide, 0 - high
					if (DCConfig::get('prodlayoutlargeimgprevtype') == 1) {
						$thumbresizer->maxHeight = 0;
						$thumbresizer->maxWidth = $from_database_size;
						$newsize = $thumbresizer->calcWidth($from_database_size, $thumbresizer->getCurrentHeight() );
					} else {
						$thumbresizer->maxWidth = 0;
						$thumbresizer->maxHeight = $from_database_size;
						$newsize = $thumbresizer->calcHeight( $thumbresizer->getCurrentWidth(), $from_database_size);
					}
					$thumbresizer->resize($newsize['newWidth'], $newsize['newHeight']);
					$thumbresizer->save($filepathfly);
					$thumbresizer->destruct();
					if($prev == ""){
						$url = JURI::root() . 'images/stories/digicom/products/thumb/'.$imagename;
					}
					else{
						$url = JURI::root() . 'images/stories/digicom/products/viewproduct/'.$imagename;
					}

			} else {
				$url = null;
			}

		}
		else{
			if($prev == ""){
				$url = JURI::root() . 'images/stories/digicom/products/thumb/'.$imagename;
			}
			else{
				$url = JURI::root() . 'images/stories/digicom/products/viewproduct/'.$imagename;
			}
		}
		return $url;
	}


	public static function ShowImage($prod){
		$title = $prod->image_title;
		$title = str_replace('"', "&quot;", $title);
		if(trim($title) != ""){
			$title = 'title="'.$title.'"';
		}
		$tag_image = "<div class='dsimage'><img ".$title." src=\"".ImageHelper::GetProductThumbImageURL($prod->defprodimage)."\" alt=\"{$prod->name}  image\"  class=\"ijd-center\"></div>";

		return $tag_image;
	}

	public static function GetProductThumbImageURLBySize($imagename=null, $size) {
		if (is_null($imagename) || empty($imagename)) {
			return null;
		}
		if ($size == 0) {
			return ImageHelper::GetProductThumbImageURL($imagename);
		}

		$path = JPATH_ROOT . DS . "images" . DS . "stories" . DS .  "digicom" . DS . "products" . DS;
		$pathfly = JPATH_ROOT . DS . "images" . DS . "stories" . DS .  "digicom" . DS . "products" . DS . 'thumb' . DS . "preview" .DS;

		$filepath = $path . $imagename;
		$filepathfly = $pathfly . $imagename;

		$resize_flag = true;

		if(file_exists($filepathfly)){
			$filesize = getimagesize($filepathfly);
			if(DCConfig::get('imageprodsizethumbtype') == 1){
				if($size ==  $filesize["0"]){
					$resize_flag = false;
				}
			}
			else{
				if($size == $filesize["1"]){
					$resize_flag = false;
				}
			}
		}
		if($resize_flag){
			if(file_exists($filepath)){
				if(!file_exists($pathfly)){
					@mkdir($pathfly, 0755);
				}
				// resize source image file
				require_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'libs' . DS . 'thumbnail.inc.php';
				// fullpath
				$thumbresizer = new Thumbnail($filepath);
				// 1- wide, 0 - high
				if(DCConfig::get('imageprodsizethumbtype') == 1) {
					$thumbresizer->maxHeight = 0;
					$thumbresizer->maxWidth = $size;
					$newsize = $thumbresizer->calcWidth($size, $thumbresizer->getCurrentHeight());
				}
				else{
					$thumbresizer->maxWidth = 0;
					$thumbresizer->maxHeight = $size;
					$newsize = $thumbresizer->calcHeight($thumbresizer->getCurrentWidth(), $size);
				}
				$thumbresizer->resize($newsize['newWidth'], $newsize['newHeight']);
				$thumbresizer->save($filepathfly);
				$thumbresizer->destruct();
				$url = JURI::root().'images/stories/digicom/products/thumb/preview/'.$imagename;
// 				$url = JPATH_SITE.'/images/stories/digicom/products/thumb/preview/'.$imagename;
			} else {
				$url = null;
			}
		} else {
			$url = JURI::root() . 'images/stories/digicom/products/thumb/preview/'.$imagename;
// 			$url = JPATH_SITE . '/2images/stories/digicom/products/thumb/preview/'.$imagename;
		}
		return $url;
	}
}

?>