<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 372 $
 * @lastmodified	$LastChangedDate: 2013-10-19 10:59:40 +0200 (Sat, 19 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license


*/

defined ('_JEXEC') or die ("Go away.");

jimport ('joomla.application.component.controller');

class DigiComAdminControllerProducts extends DigiComAdminController {
	var $_model = null;

	function __construct () {
		parent::__construct();
		$this->registerTask ('save2new', 'save');
		$this->registerTask ("", "listProducts");
		$this->registerTask ('apply', 'save');
		$this->registerDefaultTask('listProducts');
		$this->registerTask ('unpublish', 'publish');
		$this->registerTask ('orderup', 'shiftorder');
		$this->registerTask ("orderdown", "shiftorder");
		$this->registerTask ("move_image_down", "moveImageDown");
		$this->registerTask ("move_image_up", "moveImageUp");
		$this->registerTask ("delete_selected", "deleteImages");
		$this->registerTask ("delete_all", "deleteImages");
		$this->registerTask ("copy", "copyProduct");

		$this->_model = $this->getModel("Product");

		$prc = JRequest::getVar("catid");
		$state_filter = JRequest::getVar("state_filter", "-1", "request");
		$csel = "";

		if($prc > 0){
			$csel .= "&prc=".$prc;
		}
		if($state_filter != "-1"){
			$csel .= "&state_filter=".$state_filter;
		}
		$this->csel = $csel;
	}

	function listProducts() {
// 		JRequest::setVar ("view", "Products");
		$view = $this->getView("Products", "html");
		$view->setModel($this->_model, true);
		$model = $this->getModel("Config");
		$view->setModel($model);
		$model = $this->getModel("Category");
		$view->setModel($model);

		$view->display();
	}

	function uploadimages() {

		jimport('joomla.filesystem.file');

		$path_image = JPATH_ROOT . DS . "images" . DS . "stories" . DS .  "digicom" . DS . "products" . DS;

		$files = JRequest::getVar('prodimages', null, 'files', 'array');
		$product_id = JRequest::getVar("ProductId", "0");

		foreach( $files['name'] as $key => $file){
			$ext_array = explode(".", $file);
			$extension = $ext_array[count($ext_array)-1];
			$extension = strtoupper($extension);
			if($extension == "JPEG" || $extension == "JPG" || $extension == "PNG" || $extension == "GIF"){
				if($files['error'][$key] == 0){
					$uniqid = uniqid (rand (),true);
					$filename = JFile::makeSafe($files['name'][$key]);

					$filepath = JPath::clean($path_image . strtolower($uniqid.'_'.$files['name'][$key]));

					if (!JFile::upload( $files['tmp_name'][$key], $filepath )) {
						echo 'Cannot upload file to "' . $filepath . '"';
					}

					$db = JFactory::getDBO();
					$sql = "select max(`order`) from `#__digicom_products_images` where `product_id`=".intval($product_id);
					$db->setQuery($sql);
					$db->query();
					$max_order = $db->loadResult();
					$new_order = intval($max_order) + 1;

					echo "<div id='box".$uniqid."' style='padding-top:5px'>
							<table>
								<tr>
									<td width=\"3%\">
												<input type=\"checkbox\" name=\"selected_image[]\" value=\"\" />
									</td>
									<td width=\"4%\" align=\"center\">
										<input id='def".$uniqid."' type='radio' name='default_image' value='".strtolower($uniqid.'_'.$files['name'][$key])."'/>
									</td>
									<td width=\"15%\" align=\"center\">
										<a href='".ImageHelper::GetProductImageURL( strtolower($uniqid.'_'.$files['name'][$key]) )."' class='modal'>
											<img src='".ImageHelper::GetProductThumbImageURL( strtolower($uniqid.'_'.$files['name'][$key]) )."'/>
										</a>
									</td>
									<td width=\"12%\" align=\"center\">
										<input type=\"text\" name=\"title[]\" value=\"\">
									</td>
									<td width=\"10%\" align=\"center\">
										<span>
											<a title=\"Move Up\" onclick=\"\" href=\"#reorder\"><img height=\"16\" width=\"16\" border=\"0\" alt=\"Move Up\" src=\"".JURI::root()."administrator/components/com_digicom/assets/images/uparrow.png"."\"></a>
										</span>
										<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
									   <input type=\"text\" name=\"order[]\" size=\"5\" value=\"".$new_order."\" class=\"text_area\" style=\"text-align: center;\">
									</td>
									<td>
										<a href='javascript:void(0);'  onclick='document.getElements(\"div[id=box".$uniqid."]\").each( function(el) { el.getParent().removeChild(el); });'>Delete</a>
									</td>
							<input type='hidden' name='prodimageshidden[]' value='".strtolower($uniqid.'_'.$files['name'][$key])."'/>
						</div>";
				}
			}
			else{
				echo '<span style="color:red;">'.$file." ".JText::_("DIGI_UPLOAD_FALSE_IMAGE")."</span><br/>";
			}

		}

		die();
	}

	function selectProducts() {

		$view = $this->getView("Products", "html");
		$view->setModel($this->_model, true);

		$model = $this->getModel("Config");
		$view->setModel($model);

		$model = $this->getModel("Category");
		$view->setModel($model);

		$view->setLayout("select");

		$view->select();
	}

	function selectProductInclude(){
		$view = $this->getView("Products", "html");
		$view->setModel($this->_model, true);

		$model = $this->getModel("Config");
		$view->setModel($model);

		$model = $this->getModel("Category");
		$view->setModel($model);

		$view->setLayout("selectproductinclude");

		$view->selectproductinclude();
	}

	function add() {
		$producttype = JRequest::getVar('producttype',-1);
		if ( $producttype == -1 ) {
			JRequest::setVar ("hidemainmenu", 1);
			$view = $this->getView("Products", "html");
			$view->setLayout("addproduct");
			$view->addProduct();
		} else {
			$this->edit();
		}
	}

	function edit () {

		JRequest::setVar ("hidemainmenu", 1);
		$view = $this->getView("Products", "html");

		$view->setLayout("editForm");
		$view->setModel($this->_model, true);

		$model = $this->getModel("Category");
		$view->setModel($model);

		$model = $this->getModel("Config");
		$view->setModel($model);

		$model = $this->getModel("Plain");
		$view->setModel($model);

		$model = $this->getModel("Emailreminder");
		$view->setModel($model);

		$view->editForm();
	}


	public function save2copy(){
		$id = JRequest::getVar('id');
		$response = $this->_model->copyProduct(array($id));
		$this->save();
	}
	
	public function save(){
		$task 	= JRequest::getVar('task','');
		$result = $this->_model->store();
		if( $result["return"] === TRUE ) {
			$msg = JText::_( 'PRODSAVED' );
		} else {
			$msg = JText::_( 'PROFFILED' );
		}
		switch ($task){
			case 'save':
			case 'save2copy':
				$save_url =  "index.php?option=com_digicom&controller=products".$this->csel;
				$this->setRedirect($save_url, $msg);
				break;
			case 'apply':
				$product_id = JRequest::getVar('id','');
				if(trim($product_id) == "" || trim($product_id) == "0"){
					$product_id = $result["id"];
				}
				$tab = JRequest::getVar("tab", "0");
				$apply_url = "index.php?option=com_digicom&controller=products&task=edit&cid[]=".$product_id."&tab=".$tab;
				$this->setRedirect($apply_url, $msg);
				break;
			case 'save2new':
				$producttype = JRequest::getVar('domainrequired',0);
				$redirect_url = 'index.php?option=com_digicom&controller=products&task=add&producttype='.$producttype;
				$this->setRedirect($redirect_url);
				break;
			default:
				break;
		}
		return;
		if(JRequest::getVar('task','') == 'save'){
			$save_url =  "index.php?option=com_digicom&controller=products".$this->csel;
			$this->setRedirect($save_url, $msg);
		}
		else{
			$product_id = JRequest::getVar('id','');
			if(trim($product_id) == "" || trim($product_id) == "0"){
				$product_id = $result["id"];
			}
			$tab = JRequest::getVar("tab", "0");
			$apply_url = "index.php?option=com_digicom&controller=products&task=edit&cid[]=".$product_id."&tab=".$tab;
			$this->setRedirect($apply_url, $msg);
		}
		
// 		http://localhost/obexts/j30/administrator/index.php?option=com_digicom&controller=products&task=add&producttype=0
	}

	function remove () {
		if (!$this->_model->delete()) {
			$msg = JText::_('PRODREMERR');
		} else {
		 	$msg = JText::_('PRODREMSUCC');
		}

		$link = "index.php?option=com_digicom&controller=products".$this->csel;
		$this->setRedirect($link, $msg);

	}

	function cancel () {
	 	$msg = JText::_('PRODCANCEL');
		$link = "index.php?option=com_digicom&controller=products".$this->csel;
		$this->setRedirect($link, $msg);
	}

	function publish () {
		$res = $this->_model->publish();

		if (!$res) {
			$msg = JText::_('PRODPUBERR');
		} elseif ($res == -1) {
		 	$msg = JText::_('PRODUNPUB');
		} elseif ($res == 1) {
			$msg = JText::_('PRODPUB');
		} else {
				 	$msg = JText::_('PRODUNSPEC');
		}

		$link = "index.php?option=com_digicom&controller=products".$this->csel;
		$this->setRedirect($link, $msg);
	}


	function saveorder () {
		$res = $this->_model->reorder();

		if (!$res) {
			$msg = JText::_('PRODUCTORDERINGERROR');
		} else {
			$msg = JText::_('PRODUCTORDERINGSUCCESS');
		}
		$link = "index.php?option=com_digicom&controller=products".$this->csel;
		$this->setRedirect($link, $msg);
	}

	function shiftorder () {
		$task = JRequest::getVar("task", "orderup", "request");
		$direct = ($task == "orderup")?(-1):(1);
		$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
		$res = $this->_model->orderField( $cid[0], $direct );

		if (!$res) {
			$msg = JText::_('PRODUCTORDERINGERROR');
		} else {
			$msg = JText::_('PRODUCTORDERINGSUCCESS');
		}
		$link = "index.php?option=com_digicom&controller=products".$this->csel;
		$this->setRedirect($link, $msg);
	}

	function productincludeitem(){
		$view = $this->getView("Products", "html");

		$view->setLayout("productincludeitem");
		$view->setModel($this->_model, true);

		$view->productincludeitem();
	}

	function moveImageDown(){
		$this->_model->moveImageDown();
		$cid = JRequest::getVar("id", "0");
		$this->setRedirect("index.php?option=com_digicom&controller=products&task=edit&cid[]=".$cid);
	}

	function moveImageUp(){
		$this->_model->moveImageUp();
		$cid = JRequest::getVar("id", "0");
		$this->setRedirect("index.php?option=com_digicom&controller=products&task=edit&cid[]=".$cid);
	}

	function deleteImages(){
		$this->_model->deleteImages();
		$product_id = JRequest::getVar('id','');
		$apply_url = "index.php?option=com_digicom&controller=products&task=edit&cid[]=".$product_id."&tab=6";
		$this->setRedirect($apply_url, $msg);
	}

	function copyProduct()
	{
		$response = $this->_model->copyProduct();
		/*$catid = JRequest::getInt('catid', 0);
		if($response === TRUE)
		{
			$msg = JText::_("DIGI_COPY_SUCCESSFULLY");
			$this->setRedirect("index.php?option=com_digicom&controller=products&catid=$catid", $msg);
		}
		else
		{
			$msg = JText::_("DIGI_COPY_ERROR");
			$this->setRedirect("index.php?option=com_digicom&controller=productss&catid=$catid", $msg, "notice");
		}*/
		$this->listProducts();
	}

};

?>
