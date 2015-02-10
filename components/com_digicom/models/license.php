<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 436 $
 * @lastmodified	$LastChangedDate: 2013-11-19 15:04:40 +0100 (Tue, 19 Nov 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

defined ('_JEXEC') or die ("Go away.");

jimport ("joomla.aplication.component.model");

class DigiComModelLicense extends DigiComModel
{
	var $_licenses;
	var $_license;
	var $_id = null;

	function __construct () {
		parent::__construct();
		$cids = JRequest::getVar('licid', 0, '', 'array');
		$this->setId((int)$cids[0]);
	}

	function setId($id) {
		$this->_id = $id;
		$this->_oid = $id;
		$this->_license = null;
	}

	function incrimentDownloadCount($licid) {
		$db = JFactory::getDBO();
		$sql = 'update #__digicom_licenses set download_count = download_count+1 where id='.intval($licid);
		$db->setQuery($sql);
		$db->query();
	}


	function getlistLicenses(){
		$user = new DigiComSessionHelper();
		$db = JFactory::getDBO();

		$search = JRequest::getVar('search', '');
		$search = trim($search);

		if (empty ($this->_licenses)) {

			$sql = "select l.*, o.order_date as date, p.name as productname, p.main_zip_file, p.domainrequired, u.username, p.offerplans, p.hide_public "
			." from #__digicom_licenses l, #__digicom_products p, #__users u, #__digicom_orders o "
			." where l.productid=p.id  and l.userid=u.id and l.userid=".$user->_user->id;
			$sql .= " and p.published=1  and l.published=1 and l.orderid=o.id ". ($this->_oid > 0? (" and l.orderid=".$this->_oid." ") : "");

			if($search != ''){
				$sql .= " and ( (p.name like '%".$search."%') or (l.id = '".$search."') or (l.licenseid = '".$search."') )";
			}
			$sql .= " and l.old_orders <> '' order by l.purchase_date desc";
			$licenses_1 = $this->_getList($sql);

			$sql = "select id from #__digicom_products where `domainrequired`=3";
			$db->setQuery($sql);
			$db->query();
			$packages_id = $db->loadColumn();

			if(!isset($packages_id) || count($packages_id) == 0){
				$packages_id = array("0"=>"0");
			}

			$sql = "select l.*, o.order_date as date, p.name as productname, p.main_zip_file, p.domainrequired, u.username, p.offerplans, p.hide_public "
			." from #__digicom_licenses l, #__digicom_products p, #__users u, #__digicom_orders o "
			." where l.productid=p.id and l.userid=u.id and l.userid=".$user->_user->id;
			$sql .= " and p.published=1 and l.published=1 and l.orderid=o.id and l.productid not in (".implode(",", $packages_id).") ". ($this->_oid > 0? (" and l.orderid=".$this->_oid." ") : "");

			if($search != ''){
				$sql .= " and ( (p.name like '%".$search."%') or (l.id = '".$search."') or (l.licenseid = '".$search."') )";
			}
			$sql .= " and l.old_orders = '' order by l.id desc";
			$licenses_2 = $this->_getList($sql);

			if(count($licenses_1) > 0 && count($licenses_2) > 0){
				$this->_licenses = array_merge($licenses_1, $licenses_2);
			}
			elseif(count($licenses_1) > 0){
				$this->_licenses = $licenses_1;
			}
			elseif(count($licenses_2) > 0){
				$this->_licenses = $licenses_2;
			}

			if(isset($this->_licenses)){
				foreach ($this->_licenses as $i => $v) {
					$sql = "select catid from #__digicom_product_categories where productid=".$v->productid;
					$db->setQuery($sql);
					$this->_licenses[$i]->catid = $db->loadResult();
				}
			}
		}

		if(isset($this->_licenses)){
			foreach ($this->_licenses as $i => $license){
					$sql = "select fieldname, optioname from #__digicom_licensefields where licenseid='".$license->id."'";
					$db->setQuery($sql);
					$fields = $db->loadObjectList();
					$this->_licenses[$i]->fields = $fields;
			}
		}

		/* Plains */
		if(isset($this->_licenses)){
			foreach ($this->_licenses as $i => $license){
				$sql = "select p.id, pp.product_id, pp.price, p.name, p.duration_count, p.duration_type  from #__digicom_products_plans pp
						left join #__digicom_plans p on (p.id = pp.plan_id) where pp.product_id = ".$license->productid;
				$db->setQuery($sql);
				$plains = $db->loadObjectList();
				$this->_licenses[$i]->plans = $plains;
			}
		}

		 /* Renewals */
		if(isset($this->_licenses)){ 
			foreach ($this->_licenses as $i => $license){
				$sql = "select p.id, pp.product_id, p.name, p.duration_count, p.duration_type from #__digicom_products_renewals pp
						left join #__digicom_plans p on (p.id = pp.plan_id) where pp.product_id = ".$license->productid;			
				$db->setQuery($sql);
				$renewals = $db->loadObjectList();
				$this->_licenses[$i]->renewals = $renewals;
			}
		}

		return $this->_licenses;
	}

	function getLicense($id = 0) {
		if (empty ($this->_license)) {
			if ($id > 0) $this->_id = $id;
			//$sql = "select l.*,p.name as productname, u.username from #__digicom_licenses l, #__digicom_products p, #__users u where l.productid=p.id and l.userid=u.id and l.id=".$this->_id;
			$db = JFactory::getDBO();
			$sql = "select l.plan_id from #__digicom_licenses l where l.id=".intval($this->_id);
			$db->setQuery($sql);
			$db->query();
			$plan_id = $db->loadResult();
			$and = "";
			if($plan_id != "0"){
				$and = " and pl.id = l.plan_id ";
			}
			$sql = "select l.*,p.name as productname, u.username, pl.name as planname,	pl.duration_count, pl.duration_type
				from #__digicom_licenses l, #__digicom_products p, #__users u, #__digicom_plans pl
				where l.productid=p.id and l.userid=u.id ".$and." and l.id=".$this->_id;
			$this->_license = $this->_getList($sql);
		}
		return $this->_license[0];

	}


	function saveDomain() {

		$license = $this->_license[0];

		/*$no_prod = true;
		if (strlen(trim($license->domain)) > 0) $no_prod = false;

		$no_dev = true;
		if (strlen(trim($license->dev_domain)) > 0) $no_dev = false;

		if (!$no_prod && !$no_dev) {
//			$this->setRedirect("index.php?option=com_digicom&controller=licenses" );
			return false;
		}*/

		$no_prod = true;
		$no_dev = true;

		$prod = trim(JRequest::getVar("proddomain", "", "request"));
		$dev = trim(JRequest::getVar("devdomain", "", "request"));
		$db = JFactory::getDBO();

		/*if (strlen(trim($prod)) > 0 && $no_prod) {
			$sql = "update #__digicom_licenses set domain='".$prod."' where id=".$license->id;
			$db->setQuery($sql);
			$db->query();
		}
		if (strlen(trim($dev)) > 0 && $no_dev) {
			$sql = "update #__digicom_licenses set dev_domain='".$dev."' where id=".$license->id;
			$db->setQuery($sql);
			$db->query();
		}*/

		// Domain change count
		if(strcasecmp($prod, $license->domain) != 0 && !empty($license->domain)) {
			$license->domain_change += 1;
		}

		$sql = "update #__digicom_licenses set dev_domain='".$dev."', domain='".$prod."', domain_change='".$license->domain_change."' where id=".$license->id;
		$db->setQuery($sql);
		$db->query();

		return true;
	}

	function prepareDownload($product, $uid) {
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		$resdir = JPATH_ROOT.DS."administrator".DS."components".DS."digicom_product_uploads".DS.$product->id.DS;
		$resdir_orig = $resdir."original".DS;
		$resdir_encoded = $resdir."encoded".DS;
		$srcFile = $resdir_orig.$product->file;
		
		if(!is_file($srcFile)){
			echo '<script> alert("'.JText::_("DIGI_NOT_FILE").'"); history.go(-1); </script>';
			die();
		}
			   
		$usermainDir = JPATH_ROOT.DS."components/com_digicom/download/";
		$prepDir = $usermainDir . $uid."/";  
		
		if (file_exists($prepDir) ) {
			JFolder::delete ($prepDir);
		} 
		JFolder::create($prepDir);		

		DigiComHelper::CreateIndexFile($usermainDir);
		DigiComHelper::CreateIndexFile($prepDir);		
		
		if (!JFile::copy ($srcFile, $prepDir.$product->file)) return false;//die ($srcFile." ".$prepDir.$product->file);;
		return true;
	}

	function prepareEncodedFile ($product, $license, $uid, $dev = 0, $method) {

			$phpVer = JRequest::getVar ('phpVersions', "", "request");
			$platform = JRequest::getVar ('platforms', "", "request");
			$platform = implode (" ", $platform);
			$hoster = JRequest::getVar ('hosting_service_name', "", "request");
			$panel = JRequest::getVar ('control_panel', "", "request");
			$db = JFactory::getDBO();

			$subdomains = JRequest::getVar( 'subdomains' , "", "request") ;
			if (trim( $subdomains ) != "" ) $subdomains = explode( "\n", $subdomains );
			if ( $dev)  
				$domain = $license->domain;
			else
				$domain = $license->dev_domain;
		
			$domain = $license->domain;
			$devdomain = $license->dev_domain;
		
		//remove the existing files
		$license_file = "license.txt";
		$srcFile = JPATH_ROOT."/administrator/components/digicom_product_uploads/".$product->id."/encoded/".$method."/".$phpVer[0]."/".$product->file;
		if (!file_exists ($srcFile)) $srcFile = JPATH_ROOT."/administrator/components/digicom_product_uploads/".$product->id."/original/".$product->file;
		
			$prepDir = JPATH_ROOT."/components/com_digicom/download/".$uid."/";

		if (file_exists($prepDir) ) {
			JFolder::delete ($prepDir);
		} 

		JFolder::create($prepDir);
		
		JFile::copy ($srcFile, $prepDir.$product->file);
	   
		return true;
	}

	function getPackageContents($license, $product) {

		jimport("joomla.filesystem.file");

		define( 'PCLZIP_TEMPORARY_DIR', JPATH_ROOT.DS.'administrator/components/digicom_product_uploads/tmp/' );

		DigiComHelper::CreateIndexFile(JPATH_ROOT.DS.'administrator/components/digicom_product_uploads/');
		DigiComHelper::CreateIndexFile(JPATH_ROOT.DS."components/com_digicom/download/");
		DigiComHelper::CreateIndexFile(JPATH_ROOT.DS."components/com_digicom/download/".$license->userid."/");

		require_once (JPATH_ROOT.DS."administrator".DS."includes".DS."pcl".DS."pclzip.lib.php");

		$srcZip = new PclZip(JPATH_ROOT.DS."components/com_digicom/download/".$license->userid."/".$product->file);
		return ($srcZip->listContent());
	}

	function getNrOrders($lic_id){
		$db = JFactory::getDBO();
		$count = "0";
		$sql = "select `orderid`, `old_orders` from #__digicom_licenses where `licenseid` = '".$lic_id."'";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadAssocList();

		if(trim($result["0"]["old_orders"]) == ""){
			$count = "1";
		}
		else{
			$temp_array = explode("|", trim($result["0"]["old_orders"]));
			$count = count($temp_array);
		}

		return $count;
	}
	
}
