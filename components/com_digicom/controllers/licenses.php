<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 398 $
 * @lastmodified	$LastChangedDate: 2013-11-04 05:07:10 +0100 (Mon, 04 Nov 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license


*/

defined ('_JEXEC') or die ("Go away.");

jimport( 'joomla.application.component.controller' );
jimport( 'joomla.filesystem.file' );

class DigiComControllerLicenses extends DigiComController
{

	var $_model = null;

	function __construct()
	{
		global $Itemid;

		parent::__construct();

		$this->registerTask( "add", "edit" );
		$this->registerTask( "", "listLicenses" );
		$this->registerTask( "list", "listLicenses" );
		$this->registerTask( "show", "listLicenses" );
		$this->registerTask( "devdownload", "download" );
		$this->registerTask( "devregister", "register" );
		$this->registerTask( "showPackage", "showPackage" );
		$this->registerTask( "unpublish", "publish" );

		$this->_model = $this->getModel( "License" );
		//$this->_plugins = $this->getModel( "Plugin" );

		$this->log_link = JRoute::_( "index.php?option=com_digicom&controller=profile&task=login&returnpage=licenses&Itemid=" . $Itemid, false );
		$this->lic_link = JRoute::_( "index.php?option=com_digicom&controller=licenses&Itemid=" . $Itemid, false );
		$this->prof_link = JRoute::_( "index.php?option=com_digicom&controller=profile&task=edit&returnpage=licenses&Itemid=" . $Itemid, false );
	}

	function listLicenses()
	{
		// Set customer groups
		require_once( JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'helper.php' );
		$my = JFactory::getUser();
		//DigiComAdminHelper::expireUserProduct($my->id);

		if($this->_customer->_user->id < 1){
			$this->setRedirect($this->log_link);
			return;
		}
		$res = DigiComHelper::checkProfileCompletion( $this->_customer );
		if($res < 1){
			$msg = JText::_('DS_UPDATE_PROFILE');
			$this->setRedirect( $this->prof_link, $msg );
		}
		$view = $this->getView( "Licenses", "html" );
		$view->setModel( $this->_model, true );

		$conf = $this->getModel( "Config" );
		$configs = $conf->getConfigs();
		$view->setModel( $conf );
		$view->display();
	}

	function register(){
		if($this->_customer->_user->id < 1){
			$licid = JRequest::getVar("licid", "0");
			$this->setRedirect(JRoute::_("index.php?option=com_digicom&controller=profile&task=login&returnpage=licensesreg&licid=".$licid."&Itemid=".$Itemid."&tmpl=component", false));
			return;
		}

		$res = DigiComHelper::checkProfileCompletion( $this->_customer );
		if($res < 1){
			$msg = JText::_('DS_UPDATE_PROFILE');
			$this->setRedirect( $this->prof_link, $msg );
		}

		$license = $this->getModel("License");
		$license = $license->getLicense();

		if($license->userid != $this->_customer->_user->id){
			$this->setRedirect($this->lic_link);
		}
		$view = $this->getView("Licenses", "html");
		$view->setModel( $this->_model, true );
		$view->domainForm();
	}

	function saveDomain(){
		global $Itemid;

		if($this->_customer->_user->id < 1){
			$this->setRedirect($this->log_link);
			return;
		}

		$res = DigiComHelper::checkProfileCompletion($this->_customer);
		if($res < 1){
			$msg = JText::_('DS_UPDATE_PROFILE');
			$this->setRedirect( $this->prof_link, $msg );
		}

		$license = $this->_model->getLicense();
		if($license->userid != $this->_customer->_user->id){
			$this->setRedirect($this->lic_link);
		}

		$res = $this->_model->saveDomain();
		
		// echo "<script>window.parent.close_modal_and_refresh();</script>";
		// $app = JFactory::getApplication("site");
		// $app->close();

		$this->setRedirect("index.php?option=com_digicom&controller=licenses&updated=" . $this->_model->_license[0]->id . "&Itemid=" . $Itemid );
	}

	function download(){
		global $Itemid;
		if($this->_customer->_user->id < 1){
			$licid = JRequest::getVar("licid", "0");
			$this->setRedirect(JRoute::_("index.php?option=com_digicom&controller=profile&task=login&returnpage=licensesreg&licid=".$licid."&Itemid=".$Itemid."&tmpl=component", false));
			return;
		}

		$dev = JRequest::getVar( "task", "", "request" );
		$view = $this->getView( "Licenses", "html" );
		$view->setModel( $this->_model, true );
		//$view->setModel( $this->_plugins );

		if ( strlen( trim( $dev ) ) < 1 )
			$dev = 0; else
			$dev = 1;
		if ( $this->_customer->_user->id < 1 ) {
			$this->setRedirect( $this->log_link );
			return;
		}

		$db = JFactory::getDBO();
		$license = $this->getModel( "License" );
		$license = $license->getLicense();

		if ( $license->userid != $this->_customer->_user->id ) {
			$this->setRedirect( $this->lic_link );
		}

		$product = $this->getModel( "Product" );
		$product = $product->getProduct( $license->productid );

		if ( $product->id < 1 ) {
			$this->setRedirect( $this->lic_link );
		}

		// Check download count
		if (($license->duration_count == 0) && ($license->download_count > $license->duration_count) ) {
			$this->setRedirect( "index.php?option=com_digicom&controller=licenses&Itemid=" . $Itemid );
			return;
		}
		$plg_multifiles = JPluginHelper::getPlugin('digicom','multifiles');
		if( !$plg_multifiles || !count($plg_multifiles) ) {
			$this->_model->prepareDownload( $product, $this->_customer->_user->id );
		}
		
		JPluginHelper::importPlugin('digicom');
		$jv = new JVersion();
		$isJ25 = $jv->RELEASE == '2.5';
		if($isJ25){
			$dispatcher = JDispatcher::getInstance();
		} else {
			$dispatcher	= JEventDispatcher::getInstance();
		}
		$res = $dispatcher->trigger( 'onPrepareDownload', array( 'com_digicom' , $product, $this->_customer->_user->id ) );

		$file_path = (isset($res[0])&& $res[0])?$res[0]:'';
		$this->getProduct($file_path);
	}

	function downloadlicense(){
		$license = $this->getModel( "License" );
		$license = $license->getLicense();

		$product = $this->getModel( "Product" );
		$product = $product->getProduct( $license->productid );

//		$this->_model->prepareDownload($product, $this->_customer->_user->id);
//		$this->setRedirect("index.php?option=com_digicom&controller=licenses&task=getProduct&licid=".$license->id."&no_html=1&Itemid=".$Itemid);

		$url = "index.php?option=com_digicom&controller=licenses&task=getProduct&licid=" . $license->id . "&no_html=1&Itemid=" . $Itemid;
		$view = $this->getView( "Licenses", "html" );
		$view->setLayout( "downloadlicense" );
		$view->downloadLicense( $url );

	}

	function showPackage()
	{

		$view = $this->getView( "Licenses", "html" );
		$view->setModel( $this->_model, true );
		//$view->setModel( $this->_plugins );

		$license = $this->getModel( "License" );
		$license = $license->getLicense();

		if ( $this->_customer->_user->id < 1 ) {
			$this->setRedirect( $this->log_link );
			return;
		}
		if ( $license->userid != $this->_customer->_user->id ) {
			$this->setRedirect( $this->lic_link );
		}

		$product = $this->getModel( "Product" );
		$product = $product->getProduct( $license->productid );

		if ( $product->id < 1 ) {
			$this->setRedirect( $this->lic_link );
		}

		$view->setLayout( "showpackage" );
		$view->showPackage( $license, $product );

	}

	function getProduct($_filepath=''){
		$uid = $this->_customer->_user->id;
		$uemail = $this->_customer->_user->email;
		if ( $this->_customer->_user->id < 1 ) {
			$this->setRedirect( $this->log_link );
			return;
		}
		$res = DigiComHelper::checkProfileCompletion( $this->_customer );
		if ( $res < 1 ) {
			$msg = JText::_('DS_UPDATE_PROFILE');
			$this->setRedirect( $this->prof_link, $msg );
		}

		$db = JFactory::getDBO();
		$license = $this->getModel( "License" );

		$lid = JRequest::getInt( "id", "0", "request" );
		if ( $lid < 1 ) {
			$this->setRedirect( $this->lic_link );
		}

		$license = $license->getLicense( $lid );
		if ( $license->userid != $this->_customer->_user->id ) {
			$this->setRedirect( $this->lic_link );
		}
		$product = $this->getModel( "Product" );
		$product = $product->getProduct( $license->productid );

		if ( $product->id < 1 ) {
			$this->setRedirect( $this->lic_link );
		}

		$conf = $this->getModel( "Config" );
		$configs = $conf->getConfigs();

		$date = JFactory::getDate();
		$now = $date->toSql();
		$sql = "insert into #__digicom_logs (`userid`, `productid`, `to`, `download_date`) values (".$uid.", ".$product->id.", '".$uemail."', '".$now."')";
		$db->setQuery($sql);
		$db->query();

		// Incriment download_count + 1
		$this->_model->incrimentDownloadCount( $license->id );

		if($configs->get('directfilelink',0) != 0){
		
			$site = DigiComHelper::getLiveSite();
			$site = explode( "/", $site );
			if ( $site[count( $site ) - 1] == "index2.php" || $site[count( $site ) - 1] == "index.php" || $site[count( $site ) - 1] == "index3.php" )
				unset( $site[count( $site ) - 1] );
			$site = implode( "/", $site );

			$usermainDir = JPATH_ROOT . DS . "components/com_digicom/download/";
			DigiComHelper::CreateIndexFile( $usermainDir );

			$preDir = JPATH_ROOT . DS . "components/com_digicom/download/" . $uid . "/";
			DigiComHelper::CreateIndexFile( $preDir );

			$this->setRedirect( $site . "/components/com_digicom/download/" . $uid . "/" . $product->file );
			
		} else {
		
			$usermainDir = JPATH_ROOT . DS . "components/com_digicom/download/";
			DigiComHelper::CreateIndexFile( $usermainDir );

			$preDir = JPATH_ROOT . DS . "components/com_digicom/download/" . $this->_customer->_user->id . "/";
			DigiComHelper::CreateIndexFile( $preDir );

			ob_clean();
			ob_start();

			// documentation about download see: http://www.codenet.ru/webmast/php/faq/index18.php
			$prepDir 	= JPATH_ROOT . DS . "components/com_digicom/download/" . $this->_customer->_user->id . "/";
			$filepath 	= $prepDir . $product->file;
			$filename 	= $product->file;
			if($_filepath){
				$filepath = $_filepath;
				$filename = pathinfo($filepath,PATHINFO_BASENAME);
			}
			$fsize 		= filesize( $filepath );

			if ( !file_exists( $filepath ) ) {
				header( "HTTP/1.0 404 Not Found" );
				exit;
			}

			$ftime = date( "D, d M Y H:i:s T", filemtime( $filepath ) );
			$fd = @fopen( $filepath, "rb" );
			if ( !$fd ) {
				header( "HTTP/1.0 403 Forbidden" );
				exit;
			}

			// inscrices memory size if need bug in ticket OBQ-977176
			$memoryNeeded = round( $fsize * 2 );
			if ( function_exists( 'memory_get_usage' ) && (memory_get_usage() + $memoryNeeded) > (int) ini_get( 'memory_limit' ) * pow( 1024, 2 ) ) {
				$mem = (int) ini_get( 'memory_limit' ) + ceil( ((memory_get_usage() + $memoryNeeded) - (int) ini_get( 'memory_limit' ) * pow( 1024, 2 )) / pow( 1024, 2 ) ) . 'M';
				ini_set( 'memory_limit', $mem );
			}

			header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
			header( "Last-Modified: $ftime" );
			header( "Content-Length: $fsize" );
			header( "Content-type: application/octet-stream" );
			readfile( $filepath );
			exit;
		}

	}

}

