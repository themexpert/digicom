<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 341 $
 * @lastmodified	$LastChangedDate: 2013-10-10 14:28:28 +0200 (Thu, 10 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license


*/

defined ('_JEXEC') or die ("Go away.");

jimport ('joomla.application.component.controller');

class DigiComControllerProducts extends DigiComController {
	var $_model = null;
	var $_config = null;
	var $_product = null;

	function __construct () {
		parent::__construct();
		$this->registerTask ("", "showProduct");
		$this->_model = $this->getModel("Product");
		$this->_config = $this->getModel("Config");
		$this->_category = $this->getModel("Category");
	}

	function listProducts(){
		JRequest::setVar ("view", "Products");
		$conf = $this->_config->getConfigs();
		//print_r($conf);die;
		$catid =  JRequest::getVar('cid', array(), 'request', 'array');
		if (is_array($catid)&&count($catid) > 0)	$catid = intval($catid[0]);
		elseif(!is_array($catid) ) $catid = intval($catid);
		else $catid = 0;
		if( !$catid ) {
			$cats = $this->_category->getListCategories();
		} else {
			$cats = $this->_category->getCategoryCategories($catid);
		}
		// var_dump($cats);
		// exit();
		if( $catid && count($cats) ){
			$catview = $this->getView('Categories', 'html');
			$catlayoutstyle = array('0'=>'list','1'=>'listThumbs','2'=>'dropdown');
			if(array_key_exists($conf->get('catlayoutstyle','list'),$catlayoutstyle)){
				$catlayout = $catlayoutstyle[$conf->get('catlayoutstyle','list')];
			} else {
				$catlayout = $catlayoutstyle[0];
			}
			$catview->setModel($this->_category, true);
			$catview->setModel($this->_config);
			$catview->setModel($this->_model);
			$catview->setLayout($catlayout);
			$catview->display();
		}
		
		$view = $this->getView('Products', 'html');
		$view->setModel($this->_model, true);
		$view->setModel($this->_config);
		$view->setModel($this->_category);
		
	   	switch($conf->get('prodlayoutstyle','grid')){
			case "0":
				$view->setLayout("grid");
				break;
			case "1":
				$view->setLayout("grid");
				break;
			case "2":
				$view->setLayout("list");
				break;
		}
		$view->display();
	}


	function showProduct () {
		$view = $this->getView( "Products", "html" );
		$view->setModel( $this->_model, true );
		$view->setModel( $this->_config );
		$view->setModel( $this->_category );
		$view->display();
	}

	function getImageList(){
		$database = JFactory::getDBO();
		$pid = JRequest::getVar("pid", "");
		$sql = "select path from #__digicom_products_images where product_id=".intval($pid)." order by `default` desc, `order` asc";
		$database->setQuery($sql);
		$database->query();
		$result = $database->loadColumn();
		return $result;
	}

	function previwimage(){
		global $isJ25; if($isJ25) JHTML::_('behavior.mootools');
		$doc = JFactory::getDocument();
		$doc->addScript(JURI::root().DIGICOM_ASSET_PATH."/js/createpopup.js");

		$position = JRequest::getVar("position", "");
		$database = JFactory::getDBO();
		$sql = "SELECT `prodlayoutlightimgprev` FROM #__digicom_settings";
		$database->setQuery($sql);
		$database->query();
		$size = $database->loadResult();
		$all_images_array = $this->getImageList();
		if(count($all_images_array) > 0){
			$product_id = JRequest::getVar("pid", "0");
			$image_name = $all_images_array[$position];

			$sql = "SELECT title FROM #__digicom_products_images WHERE product_id=".intval($product_id)." AND path='".$image_name."'";
			$database->setQuery($sql);
			$database->query();
			$title = $database->loadResult();

			$page = '<style type="text/css">
						#main {
							min-height : 0px !important;
						}
					</style>';
			$page .= '<table width="100%" style="text-align: center;" id="all_layout">
						<tr>
							<td align="left" width="50%">';
			if($position != "0"){
				$src = ImageHelper::GetProductThumbImageURLBySize($all_images_array[$position-1], $size);
				$size_array = @getimagesize($src);
				$size_w = $size;
				$size_h = $size;
				if(isset($size_array)){
					$size_w = $size_array["0"]+100;
					$size_h = $size_array["1"]+100;
				}
				$page .=	   '<input type="button" class="btn btn-small" name="back" value="'.JText::_("DIGI_BACK").'" onclick="javascript:changeImage('.($position-1).', '.intval($product_id).', '.$size_w.', '.$size_h.')" />';
			}

			$page .=	   '</td>
							<td align="right" width="50%">';

			if($position != (count($all_images_array)-1)){
				$src = ImageHelper::GetProductThumbImageURLBySize($all_images_array[$position+1], $size);
				$src = str_replace(" ", "%20", $src);
				$size_array = @getimagesize($src);
				$size_w = $size;
				$size_h = $size;
				if(isset($size_array)){
					$size_w = $size_array["0"]+100;
					$size_h = $size_array["1"]+100;
				}
				$page .=	   '<input type="button" class="btn btn-small" name="back" value="'.JText::_("DIGI_NEXT").'" onclick="javascript:changeImage('.($position+1).', '.intval($product_id).', '.$size_w.', '.$size_h.')" />';
			}
			$src = ImageHelper::GetProductThumbImageURLBySize($all_images_array[$position], intval($size));
			$page .=	   '</td>
						</tr>
						<tr>
							<td colspan="2">
								<img src="'.$src.'"/>
							</td>
						</tr>
						<tr>
							<td colspan="2" class="digi_image_title">
								'.trim($title).'
							</td>
						</tr>
					  </table>';
			echo $page;
		}
		exit();
	}

	function getimage(){
		$database = JFactory::getDBO();
		$position = JRequest::getVar("position", "");
		$type = JRequest::getVar("type", "");
		$pid = JRequest::getVar("pid", "");

		$sql = "select `prodlayoutlightimgprev` from #__digicom_settings";
		$database->setQuery($sql);
		$database->query();
		$size = $database->loadResult();
		$size_w = $size;
		$size_h = $size;
		$all_images_array = $this->getImageList();

		if(count($all_images_array) > 0){
			$return = '';
			if(isset($all_images_array[$position])){
				$src = ImageHelper::GetProductThumbImageURLBySize($all_images_array[$position], $size);
				$size_array = @getimagesize($src);
				if(isset($size)){
					$size_w = $size_array["0"]+50;
					$size_h = $size_array["1"]+50;
				}

				if($type == "image"){
					$return .= '<a onclick="javascript:grayBoxiJoomla(\'index.php?option=com_digicom&controller=products&task=previwimage&tmpl=component&position='.$position.'&pid='.$pid.'\', '.$size_w.', '.$size_h.')"><img src="'.ImageHelper::GetProductThumbImageURL($all_images_array[$position], "prev").'"/></a>';
				}
				else{
					$return .= '<a onclick="javascript:grayBoxiJoomla(\'index.php?option=com_digicom&controller=products&task=previwimage&tmpl=component&position='.$position.'&pid='.$pid.'\', '.$size_w.', '.$size_h.')"><img src="'.ImageHelper::GetProductImageURL($all_images_array[$position]).'"/></a>';
				}
			}
			echo $return;
		}
	}
}
