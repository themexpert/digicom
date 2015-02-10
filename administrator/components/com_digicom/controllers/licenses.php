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

class DigiComAdminControllerLicenses extends DigiComAdminController {
	public $_model = null;
	public $_customer = null;
	function __construct () {

		parent::__construct();

		$this->registerTask ("add", "add");
		$this->registerTask ("apply", "save");
		$this->registerTask ("edit", "edit");
		$this->registerTask ("list", "listLicenses");
		$this->registerTask ("", "listLicenses");
		$this->registerTask ("devdownload", "download");
		$this->registerTask ("devregister", "register");
		$this->registerTask ("selectplain", "selectplain");
		$this->registerTask ("deletenote", "deleteNote");
		$this->registerTask ("unpublish", "publish");
		$this->registerTask ("export", "export");
		$this->registerTask ("customerexport", "customrExport");
		$this->registerTask ("changeCategory", "changeCategory");

		$this->_model = $this->getModel("License"); 
		$my = JFactory::getUser();
		$this->_customer = new stdClass();
		$this->_customer->_user = new stdClass();
		$this->_customer->_user->id = $my->id;
	}

	function export(){
		$model = $this->getModel("License");
		$model->export();
	}

	function customrExport(){
		$model = $this->getModel("License");
		$model->customrExport();
	}

	function deleteNote(){
		$model = $this->getModel("License");
		$model->deleteNote();
		$cid = JRequest::getVar("cid", array(), "array");
		$cid = intval($cid["0"]);
		$link = "index.php?option=com_digicom&controller=licenses&task=edit&cid[]=".$cid;
		$this->setRedirect($link);
	}

	function selectnote() {

		$view = $this->getView("Licenses", "html");
		$view->setModel($this->_model, true);

		$view->setLayout("selectnote");

		$model = $this->getModel('Category');
		$view->setModel($model, false);

		$model = $this->getModel("Config");
		$view->setModel($model);

		$view->selectnote();		
	}

	function selectplain() {
		
		$view = $this->getView("Licenses", "html");
		$view->setModel($this->_model, true);

		$view->setLayout("selectplain");

		$model = $this->getModel('Category');
		$view->setModel($model, false);

		$model = $this->getModel("Config");
		$view->setModel($model);

		$view->selectplain();
	}

	function listLicenses() {
	
		$view = $this->getView("Licenses", "html");
		$view->setModel($this->_model, true);

		$model = $this->getModel('Category');
		$view->setModel($model, false);   
		
		$model = $this->getModel("Config");
		$view->setModel($model);

		$view->display();
	}

	function licenseitem() {
		$view = $this->getView("Licenses", "html");
		$view->setModel($this->_model, true);

		$model = $this->getModel('Category');
		$view->setModel($model, false);

		$model = $this->getModel("Config");
		$view->setModel($model);

		$view->setLayout("licenseitem");
		$view->licenseitem();
	}


	function edit () {


		JRequest::setVar ("hidemainmenu", 1);
		$view = $this->getView("Licenses", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);

		$model = $this->getModel("Config");
		$view->setModel($model);
		$model = $this->getModel("Product");
		$view->setModel($model);
		$model = $this->getModel('Category');
		$view->setModel($model, false);		 

		$view->editForm();

	}

	function add () {
		//die('redirect to order add');
		$this->setRedirect( "index.php?option=com_digicom&controller=orders&task=add" );
	}

	function save () {
		if($this->_model->store()){
			$msg = JText::_('LICSAVED');
		}
		else{
			$msg = JText::_('LICSAVEDFAILED');
		}
		$link = "index.php?option=com_digicom&controller=licenses";

		$keyword = JRequest::getVar("keyword", "");
		$status = JRequest::getVar("status", "");
		$on_link = "";
		if(trim($keyword) != ""){
			$on_link .= "&keyword=".trim($keyword);
		}
		if(trim($status) != ""){
			$on_link .= "&status=".trim($status);
		}

		if(JRequest::getVar('task','') == 'save'){
			$link = "index.php?option=com_digicom&controller=licenses".$on_link;
		}
		else{
			$lic_id = JRequest::getVar('id','');
			$link = "index.php?option=com_digicom&controller=licenses&task=edit&cid[]=".$lic_id.$on_link;
		}
		$this->setRedirect($link, $msg);
	}


	function remove () {
		if (!$this->_model->delete()) {
			$msg = JText::_('LICREMERR');
		} else {
		 	$msg = JText::_('LICREMSUCC');
			DigiComAdminHelper::expireUserProduct($this->_customer->_user->id);
		}

		$link = "index.php?option=com_digicom&controller=licenses";
		$this->setRedirect($link, $msg);

	}

	function cancel () {
	 	$keyword = JRequest::getVar("keyword", "");
		$status = JRequest::getVar("status", "");
		$on_link = "";
		if(trim($keyword) != ""){
			$on_link = "&keyword=".trim($keyword);
		}
		if(trim($status) != ""){
			$on_link .= "&status=".trim($status);
		}

		$msg = JText::_('LICCANCELED');
		$link = "index.php?option=com_digicom&controller=licenses".$on_link;
		$this->setRedirect($link, $msg);
	}

	function publish () {
		$res = $this->_model->publish();
		if(!$res){
			$msg = JText::_('LICBLOCKERR');
		}
		elseif($res == -1){
		 	$msg = JText::_('LICUNPUB');
		}
		elseif($res == 1){
			$msg = JText::_('LICPUB');
		}
		else{
			$msg = JText::_('LICUNSPEC');
		}

		$keyword = JRequest::getVar("keyword", "");
		$status = JRequest::getVar("status", "");
		$on_link = "";
		if(trim($keyword) != ""){
			$on_link = "&keyword=".trim($keyword);
		}
		if(trim($status) != ""){
			$on_link .= "&status=".trim($status);
		}

   		DigiComAdminHelper::expireUserProduct($this->_customer->_user->id);

		$link = "index.php?option=com_digicom&controller=licenses".$on_link;
		$this->setRedirect($link, $msg);
	}


	function register () {

		$my = JFactory::getUser();
		if ($my->id < 1) {
			$this->setRedirect( $this->log_link );
		}

		$license =  $this->getModel("License");
		$license = $license->getLicense();
//		if ($license->userid != $this->_customer->_user->id) {
//			$this->setRedirect( $this->lic_link);
//		}
		$view = $this->getView("Licenses", "html");
		$view->setModel($this->_model, true);
		$view->domainForm();
	}


	function saveDomain () {
		if ($this->_customer->_user->id < 1) {
			$this->setRedirect($this->log_link );
		}
//die ("A");
		$license = $this->_model->getLicense();
//		if ($license->userid != $this->_customer->_user->id) {
//			$this->setRedirect( $this->lic_link);
//		}

		$res = $this->_model->saveDomain();
		if (!$res ) $add = "i";
		$this->setRedirect(JRoute::_("index.php?option=com_digicom&controller=licenses&add=".$add."&Itemid=".$Itemid, false ));
	}

	function download () {

		$dev = JRequest::getVar("task", "", "request");
		$view = $this->getView("Licenses", "html");
		$view->setModel($this->_model, true);
		//$view->setModel($this->_plugins);

		if (strlen(trim($dev)) < 1) $dev = 0; else $dev = 1;
		if ($this->_customer->_user->id < 1) {
			$this->setRedirect( $this->log_link );
		}
//		$uid = $this->_customer->_user->id ;
		$db = JFactory::getDBO();
		$license =  $this->getModel("License");
		$license = $license->getLicense();
		if ($license->userid != $this->_customer->_user->id) {
			$this->setRedirect( $this->lic_link );
		}
		$product = $this->getModel("Product");
		$product = $product->getProduct( $license->productid );

		if ($product->id < 1) {
			$this->setRedirect($this->lic_link);
		}

		$method = JRequest::getVar("method", "", "request");
		if ($method == "" ) $method = 'ioncube';

		if ($product->main_zip_file){//product is encoded

			if ( (JRequest::getVar('submitted','','post') == '') || (JRequest::getVar('phpVersions','','post') == '') || (JRequest::getVar('platforms','','post') == '') || (JRequest::getVar('licenseonly','0','post') == "1") ) {			
			
				// check installed encoders plugin installed
				$plugin_handler = $this->getModel("Plugin");
				$encoders = $plugin_handler->getEncoders();
				if (empty($encoders)) {
					echo JText::_('DIGICOM_ENCODER_PLUGINS_NOT_INSTALLED');
					return;
				}			

				if (JRequest::getVar('licenseonly','0','post') == "1") {
					$text = $this->_model->getLicenseText($product, $license, $this->_customer->_user->id, $dev, $method);
					echo "<pre>".$text."</pre>";
				} else {
					$platform_options = $plugin_handler->getEncPlatformsForMethod($method);
					$view->setLayout("downloadform");
					$view->downloadForm($license, $product, $res, $platform_options);
					return;
				}
				
			} else {
			
				$this->_model->prepareEncodedFile($product, $license, $this->_customer->_user->id, $dev, $method);				
				$this->setRedirect(JRoute::_("index.php?option=com_digicom&controller=licenses&tmpl=component&no_html=1&task=getProduct&id=".$license->id ));

			}
			
		} else {
		
			$this->_model->prepareDownload($product, $this->_customer->_user->id);
			$this->setRedirect("index.php?option=com_digicom&controller=licenses&tmpl=component&no_html=1&task=getProduct&id=".$license->id );
			
		}

	}



	function getProduct() {
		$uid = $this->_customer->_user->id;
		if ($this->_customer->_user->id < 1) {
			$this->setRedirect( $this->log_link );
		}
//		$uid = $this->_customer->_user->id ;
		$db = JFactory::getDBO();
		$license =  $this->getModel("License");

		$lid = JRequest::getVar("id", "0", "request");
		if ($lid < 1) {
			$this->setRedirect( $this->lic_link);
		}

		$license = $license->getLicense($lid);
		//if ($license->userid != $this->_customer->_user->id) {
		//	$this->setRedirect( $this->lic_link);
		//}
		$product = $this->getModel("Product");
		$product = $product->getProduct( $license->productid );
		
		if ($product->id < 1) {
			$this->setRedirect( $this->lic_link );
		}


		$conf = $this->getModel("Config");
		$configs = $conf->getConfigs();
		if ($configs->get('directfilelink',0) != 0) {
			$site=DigiComHelper::getLiveSite();

			$site=explode("/", $site);
			if ($site[count($site)-1] == "index2.php" || $site[count($site)-1] == "index.php" || $site[count($site)-1] == "index3.php")
				unset($site[count($site)-1]);
			$site = implode("/", $site);
			
			$usermainDir = JPATH_ROOT.DS."components/com_digicom/download/";
			DigiComAdminHelper::CreateIndexFile($usermainDir);	   
			
			$preDir = JPATH_ROOT.DS."components/com_digicom/download/".$uid."/";			
			DigiComAdminHelper::CreateIndexFile($preDir);			

			$this->setRedirect($site."/components/com_digicom/download/".$uid."/".$product->file );

		} else { 

			ob_clean();
			ob_start();
			header ('Content-Type: multipart/form-data');
			header('Content-Disposition: attachment; filename="'.$product->file.'"');
			$prepDir = JPATH_ROOT.DS."components/com_digicom/download/".$this->_customer->_user->id."/";
			
			$usermainDir = JPATH_ROOT.DS."/components/com_digicom/download/";
			DigiComAdminHelper::CreateIndexFile($usermainDir);	   
			
			$preDir = $usermainDir.$uid."/".$product->file;			
			DigiComAdminHelper::CreateIndexFile($preDir);   
			
			readfile($prepDir.$product->file);
			die;
			
		}

	}
	
	function decode_license() {
		$database = JFactory::getDBO();
		$mosConfig_absolute_path = JPATH_ROOT;
		$my = JFactory::getUSer();
		$view = $this->getView("Licenses", "html");

		$dir = $mosConfig_absolute_path . "/administrator/components/digicom_product_uploads/tmp/" ;
		$dec_file = $dir . "license.txt";

		$decode_method = JRequest::getVar('decode_method', '', 'request');
		$sql = "SELECT * FROM #__digicom_plugins p, #__digicom_plugin_settings s WHERE name='{$decode_method}' and p.id=s.pluginid";
		$database->setQuery($sql);
		$encoders = $database->loadObjectList();

		if ( !empty($decode_method) && ($decode_method == "ioncube") ) {

			$fp = fopen( $dec_file, "w" );
			fwrite($fp, JRequest::getVar('decode_license', '', 'request'));
			fclose($fp);
/*
			$output = implode ("\n", $output);
			echo $output;
*/
			$passphrase = JRequest::getVar("passphrase", 'request', "test");
			$result = exec( $encoders[0]->value . "/make_license --passphrase ".$passphrase." --decode-license $dec_file > ".$dir."destfile.txt");	
			$sql = "SELECT * FROM #__digicom_plugins WHERE published='1' AND type='encoding'";
			$database->setQuery($sql);
			$encoders = $database->loadObjectList();
			$input = JRequest::getVar( 'decode_license');
			$output = file ($dir."destfile.txt");
			$output = implode ("\n", $output);

			echo $output;

			@unlink($dec_file);
			@unlink($dir."destfile.txt");

			$view->setLayout("downloadform");

			$view->decodeform($encoders, $input, $output);

		} else {
			echo "<script language='javascript'>document.history.go(-1)</script>";
		}
	}
 
	function decode () {
		$database = JFactory::getDBO();
		$view = $this->getView("Licenses", "html");

		$sql = "SELECT * FROM #__digicom_plugins WHERE published='1' AND type='encoding'";
		$database->setQuery($sql);
		$encoders = $database->loadObjectList();
		$view->setLayout("downloadform");

		$view->decodeform($encoders);
	}

	function changeCategory(){
		$db = JFactory::getDBO();
		$categ_id = JRequest::getVar("category_id", "0");
		$sql = "select p.`id`, p.`name` from #__digicom_products p, #__digicom_product_categories c where p.id=c.productid and c.catid=".intval($categ_id)." order by p.`name` asc";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadAssocList();

		$select  = '';
		$select .= '<select name="filter_exp_product" onchange="document.adminForm.task.value=\'\'; document.adminForm.submit()">';
		$select .= '<option value="0">'.JText::_("DSSELECTPRODUCT").'</option>';

		if(isset($result) && count($result) > 0){
			foreach($result as $key=>$value){
				$select .= '<option value="'.$value["id"].'">'.$value["name"].'</option>';
			}
		}

		$select .= "</select>";
		echo $select;
	}

};

?>