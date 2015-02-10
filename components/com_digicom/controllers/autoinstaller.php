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

class DigiComControllerAutoinstaller extends DigiComController {
	
	function __construct(){
		parent::__construct();
		$this->registerTask("get_license_details", "getLicenseDetails");
		$this->registerTask("check_license", "checkLicense");
		$this->registerTask("dowload_component", "downloadComponent");
		$this->registerTask("renew", "renew");
		$this->registerTask("get_domain_by_license", "getDomainByLicense");
	}
	
	function renew(){
		$license = JRequest::getVar("license", "");
		$component = JRequest::getVar("component", "");
		$domain = JRequest::getVar("domain", "");
		
		$component_id = 0;
		$pid = 0;
		$site = 'https://www.ijoomla.com/';
		switch($component){
			case "adagency" : {
				$component_id = array(81, 176);
				$pid = 176;
				$site = 'https://www.ijoomla.com/';
				break;
			}
			case "guru" : {
				$component_id = 2;
				break;
			}
			case "publisher" : {
				$component_id = 3;
				break;
			}
			case "seo" : {
				$component_id = 4;
				break;
			}
			case "community_std" : {
				$component_id = array(1);
				$pid = 1;
				$site = 'http://www.jomsocial.com';
				break;
			}
			case "community_pro" : {
				$component_id = array(2);
				$pid = 2;
				$site = 'http://www.jomsocial.com';
				break;
			}
			case "community_dev" : {
				$component_id = array(4);
				$pid = 4;
				$site = 'http://www.jomsocial.com';
				break;
			}
		}
		
		$db = JFactory::getDbo();
		$sql = "select `plan_id` from #__digicom_products_renewals where `product_id` in (".implode(", ", $component_id).") and `default`=1";
		$db->setQuery($sql);
		$db->query();
		$plan_id = $db->loadResult();
		
		$form = '<form name="addproduct" id="addproduct" method="post" action="'.$site.'">
					<input type="hidden" name="option" value="com_digicom">
					<input type="hidden" name="controller" value="Cart">
					<input type="hidden" name="task" value="add">
					<input type="hidden" name="renew" value="1">
					<input type="hidden" name="renewlicid" value="'.$license.'">
					<input type="hidden" name="plan_id" value="'.intval($plan_id).'">
					<input type="hidden" name="pid" value="'.intval($pid).'">
				</form>
				
				<script type="text/javascript" language="javascript">
					var form = document.getElementById("addproduct");
					form.submit();
				</script>
				';
		echo $form;
	}
	
	function getLicenseDetails(){
		$component = JRequest::getVar("component", "");
		$domain = JRequest::getVar("domain", "");
		$component_id = 0;
		
		switch($component){
			case "adagency" : {
				$component_id = array(81, 176);
				break;
			}
			case "guru" : {
				$component_id = 2;
				break;
			}
			case "publisher" : {
				$component_id = 3;
				break;
			}
			case "seo" : {
				$component_id = 4;
				break;
			}
			case "community_std" : {
				$component_id = array(1);
				break;
			}
			case "community_pro" : {
				$component_id = array(2);
				break;
			}
			case "community_dev" : {
				$component_id = array(4);
				break;
			}
		}
		
		$db = JFactory::getDbo();
		$sql = "select l.licenseid, l.productid, l.domain, l.dev_domain, l.hosting_service, l.published, l.package_id, l.expires from #__digicom_licenses l where (l.domain like '%".trim($domain)."%' OR l.hosting_service like '%".trim($domain)."%' OR l.dev_domain like '%".trim($domain)."%') and l.productid in (".implode(", ", $component_id).")";
		$db->setQuery($sql);
		$db->query();
		$license_details = $db->loadAssocList();
		
		if(count($license_details) > 1){
			foreach($license_details as $key=>$value){
				if($value["productid"] == 176){
					$license_details["0"] = $value;
					break;
				}
			}
		}
		
		echo json_encode($license_details);
		die();
	}
	
	function downloadComponent(){
		$key = JRequest::getVar("key", "");
		$app = JFactory::getApplication();
		$license = JRequest::getVar("license", "");
		
		$time = time();
		$key = str_replace("LIC".$license."EXT", "", $key);

		if($time - $key < 30 && $time - $key > 0){
			ob_clean();
			ob_start();
			
			$db = JFactory::getDBO();
			$sql = "select `productid` from #__digicom_licenses where `licenseid`='".trim($license)."'";
			$db->setQuery($sql);
			$db->query();
			$product_id = $db->loadResult();
			
			$filepath = "";
			$filename = "";
			
			switch($product_id){
				case "81" :
				case "176" : {
					$filepath = JPATH_ROOT.DS."administrator/components/digicom_product_uploads_30/com_adagency.zip";
					$filename = "com_adagency.zip";
					break;
				}
				case "177" : {
					$filepath = JPATH_ROOT.DS."administrator/components/digicom_product_uploads_30/com_guru.zip";
					$filename = "com_guru.zip";
					break;
				}
				case "178" : {
					$filepath = JPATH_ROOT.DS."administrator/components/digicom_product_uploads_30/com_publisher.zip";
					$filename = "com_publisher.zip";
					break;
				}
				case "179" : {
					$filepath = JPATH_ROOT.DS."administrator/components/digicom_product_uploads_30/com_ijoomla_seo.zip";
					$filename = "com_ijoomla_seo.zip";
					break;
				}
				case "1" : {
					$filepath = JPATH_ROOT.DS."administrator/components/digicom_product_uploads_30/com_community_std.zip";
					$filename = "com_ijoomla_seo.zip";
					break;
				}
				case "2" : {
					$filepath = JPATH_ROOT.DS."administrator/components/digicom_product_uploads_30/com_community_pro.zip";
					$filename = "com_ijoomla_seo.zip";
					break;
				}
				case "4" : {
					$filepath = JPATH_ROOT.DS."administrator/components/digicom_product_uploads_30/com_community_dev.zip";
					$filename = "com_ijoomla_seo.zip";
					break;
				}
			}
			
			$fsize = filesize($filepath);
			if(!file_exists($filepath)){
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
		else{
			die("go away");
		}
	}
	
	function checkLicense(){
		$component = JRequest::getVar("component", "");
		$license = JRequest::getVar("license", "");
		$domain = urldecode(JRequest::getVar("domain", ""));
		$token_installer = JRequest::getVar("token_installer", "");
		
		$component_id = 0;
		$site = 'https://www.ijoomla.com/';
		
		switch($component){
			case "adagency" : {
				$component_id = array(81, 176);
				$site = 'https://www.ijoomla.com/';
				break;
			}
			case "guru" : {
				$component_id = 2;
				break;
			}
			case "publisher" : {
				$component_id = 3;
				break;
			}
			case "seo" : {
				$component_id = 4;
				break;
			}
			case "community_std" : {
				$component_id = array(1);
				$site = 'http://www.jomsocial.com/';
				break;
			}
			case "community_pro" : {
				$component_id = array(2);
				$site = 'http://www.jomsocial.com/';
				break;
			}
			case "community_dev" : {
				$component_id = array(4);
				$site = 'http://www.jomsocial.com/';
				break;
			}
		}
		
		$db = JFactory::getDbo();
		$sql = "select l.licenseid, l.productid, l.domain, l.dev_domain, l.hosting_service, l.published, l.package_id, l.expires from #__digicom_licenses l where l.licenseid='".trim($license)."'";
		$db->setQuery($sql);
		$db->query();
		$license_details = $db->loadAssocList();
		
		if(!isset($license_details) || count($license_details) == 0){
			echo "NO_LICENCE";
			die();
		}
		
		if($this->validExpired($license_details)){
			echo "LICENSE_EXPIRED";
			die();
		}
		
		if(!$this->checkDomainForLicense($license, $domain)){
			echo "NO_DOMAIN_FOR_LICENSE";
			die();
		}
		
		if(!$this->checkLicenseAndDomain($license, $domain)){
			echo "NO_LICENSE_FOR_DOMAIN";
			die();
		}
		
		if(	$this->validLicence($license, $license_details) && 
			$this->validDomain($domain, $license_details) && 
			$this->validPublished($license_details) && 
			!$this->validExpired($license_details) && 
			$this->validProduct($component_id, $license_details)){
				$time = time();
				$time = substr_replace($time, "LIC".$license."EXT", 3, 0);
				
				echo '
					<h3 style="text-align:center;">'.JText::_("INSTALLER_WAIT_MESSAGE").'</h3>
					<p style="text-align:center;">
						<img style="width:66px;height:66px;" src="'.$site.'components/com_digicom/assets/images/pleasewait.gif">
					</p>
					<form class="form-horizontal" id="adminForm" name="adminForm" method="post" action="index.php?option=com_installer&amp;view=install" enctype="multipart/form-data">
						<input type="hidden" value="'.$site.'index.php?option=com_digicom&controller=Autoinstaller&task=dowload_component&key='.$time.'&license='.$license.'" name="install_url" id="install_url" />
						<input type="hidden" value="" name="type" />
						<input type="hidden" value="url" name="installtype" />
						<input type="hidden" value="install.install" name="task" />
						<input type="hidden" value="1" name="'.$token_installer.'" />
					</form>
					
					<script type="text/javascript" language="javascript">
						var form = document.getElementById("adminForm");
						setInterval(function(){form.submit();}, 3000);
					</script>
				';
				die();
		}
		else{
			echo "NO_LICENCE";
			die();
		}
	}
	
	function validLicence($license, $license_details){
		if(trim($license_details["0"]["licenseid"]) == trim($license)){
			return true;
		}
		return false;
	}
	
	function validDomain($domain, $license_details){
		if(strpos($domain, "localhost") !== FALSE){
			return true;
		}
		else{
			if(strpos($license_details["0"]["domain"], $domain) !== FALSE || strpos($license_details["0"]["dev_domain"], $domain) !== FALSE || strpos($license_details["0"]["hosting_service"], $domain) !== FALSE){
				return true;
			}
			return false;
		}
	}
	
	function validPublished($license_details){
		if($license_details["0"]["published"] == 1){
			return true;
		}
		return false;
	}
	
	function validExpired($license_details){
		$joomla_date = JFactory::getDate();
		$today = $joomla_date->toUnix();
		$expires = strtotime($license_details["0"]["expires"]);
		
		if($license_details["0"]["expires"] != '0000-00-00 00:00:00' && $today > $expires){
			return true;
		}
		return false;
	}
	
	function validProduct($component_id, $license_details){
		if(in_array($license_details["0"]["productid"], $component_id)){
			return true;
		}
		return false;
	}
	
	function checkLicenseAndDomain($license, $domain){
		if(strpos($domain, "localhost") !== FALSE){
			return true;
		}
		
		$db = JFactory::getDBO();
		$sql = "select count(*) from #__digicom_licenses where `licenseid`='".trim($license)."' and (`domain` like '%".$domain."%' OR `dev_domain` like '%".$domain."%' OR `hosting_service` like '%".$domain."%')";
		$db->setQuery($sql);
		$db->query();
		$count = $db->loadResult();
		if($count > 0){
			return true;
		}
		return false;
	}
	
	function checkDomainForLicense($license, $domain){
		$db = JFactory::getDBO();
		$sql = "select `domain`, `dev_domain`, `hosting_service` from #__digicom_licenses where `licenseid`='".trim($license)."'";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadAssocList();
		if(trim($result["0"]["domain"]) != "" || trim($result["0"]["dev_domain"]) != "" || trim($result["0"]["hosting_service"]) != ""){
			return true;
		}
		return false;
	}
	
	function getDomainByLicense(){
		$license = JRequest::getVar("license", "");
		$db = JFactory::getDbo();
		$sql = "select `domain` from #__digicom_licenses where `licenseid`='".trim($license)."'";
		$db->setQuery($sql);
		$db->query();
		$domain = $db->loadColumn();
		$domain = $domain["0"];
		echo $domain;
		die();
	}
	
}

