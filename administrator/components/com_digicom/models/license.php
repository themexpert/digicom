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

jimport('joomla.application.component.modellist');
jimport('joomla.utilities.date');

class DigiComAdminModelLicense extends JModelList
{
	protected $_context = 'com_digicom.License';
	var $_licenses;
	var $_license;
	var $_id = null;
	var $_total = 0;
	var $_pagination = null;
	var $_filter_prod = null;

	function __construct(){
		parent::__construct();
		$cids = JRequest::getVar('cid', 0, '', 'array');
		$this->setId((int)$cids[0]);
	}

	function setId($id) {
		$this->_id = $id;
		$this->_license = null;
	}

	function populateState($ordering = NULL, $direction = NULL){
		$app = JFactory::getApplication('administrator');
		$this->setState('list.start', $app->getUserStateFromRequest($this->_context . '.list.start', 'limitstart', 0, 'int'));
		$this->setState('list.limit', $app->getUserStateFromRequest($this->_context . '.list.limit', 'limit', $app->getCfg('list_limit', 25) , 'int'));
		$this->setState('selected', JRequest::getVar('cid', array()));
	}

	function getPagination(){
		$pagination=parent::getPagination();
		$pagination->total=$this->total;
		if($pagination->total%$pagination->limit>0){
			$nr_pages=intval($pagination->total/$pagination->limit)+1;
		}
		else{ 
			$nr_pages=intval($pagination->total/$pagination->limit);
		}
		$pagination->set('pages.total',$nr_pages);
		$pagination->set('pages.stop',$nr_pages);
		return $pagination;
	}

	function getLicenseitemSelect($product_id, $userid) {
		$sql = "SELECT * FROM #__digicom_licenses WHERE userid = '".$userid."' AND productid = '".$product_id."'";
		$this->_db->setQuery($sql);
		$licenses = $this->_db->loadObjectlist();
		return $licenses;
	}

	protected function getListQuery(){
		$db = JFactory::getDBO();
		$session = JFactory::getSession();
		$c = $this->getInstance("Config", "DigiComAdminModel");
		$configs = $c->getConfigs();

		$prd = JRequest::getVar("prd", 0, "request");
		$oids = JRequest::getVar('oid', 0, '', 'array');
		$oid = $oids[0];

		$startdate = JRequest::getVar("startdate", "", "request");
		$startdate = strtotime($startdate);

		$enddate = JRequest::getVar("enddate", "", "request");
		$enddate = strtotime($enddate);

		$filter_prod = JRequest::getVar("filter_prod", "", "request");
		$prd = JRequest::getVar("filter_exp_product", "", "request");
		$keyword = JRequest::getVar("keyword", "", "request");
		$keyword_where = " and (u.username like '%".$keyword."%' or c.firstname like '%".$keyword."%' or c.lastname like '%".$keyword."%'
						or l.licenseid  like '%".$keyword."%' 
						or l.domain  like '%".$keyword."%' 
						or l.dev_domain  like '%".$keyword."%' 
						or u.email  like '%".$keyword."%' 
							or p.name  like '%".$keyword."%' )";

		$ltype = JRequest::getVar("ltype", "", "request");
		$ltype_where = " and ltype='".$ltype."' ";

		$status = JRequest::getVar("status", "", "request");
		$status_where = "";
		if(trim($status) != "" && trim($status) != "-1"){
			$status_where = " and l.published=".$status." ";
		}

		$cancelled = JRequest::getVar("cancelled", "", "request");
		$cancelled_where = "";
		if(trim($cancelled) != "" && trim($cancelled) != "0"){
			$cancelled_where = " and l.cancelled=".$cancelled." ";
		}

		$sql = "select l.*, p.name as productname, p.domainrequired, u.username, c.lastname, c.firstname, pl.duration_count, pl.duration_type, l.orderid "
			." from #__digicom_licenses l "
			." left join #__digicom_products p on (l.productid=p.id) "
			." left join #__users u on (l.userid=u.id) "
			." left join #__digicom_customers c on (l.userid=u.id and c.id=u.id)"
			." left join #__digicom_plans pl on (pl.id=l.plan_id)"
			.($filter_prod > 0 ? " left join #__digicom_product_categories pc on (l.productid=pc.productid)" : "")
			." where 1=1 and l.cancelled<>3 "
			.($prd>0?" and p.id=".$prd." ":"")
			.($filter_prod > 0 ? "and pc.catid=".intval($filter_prod)." ":"")
			.($oid>0?" and l.orderid=".$oid." ":"")
			.($startdate > 0 ? " and l.purchase_date >= '".date("Y-m-d 00:00:00", $startdate)."' ":"")
			.($enddate > 0?" and l.purchase_date < '".date("Y-m-d 00:00:00", $enddate)."' ":"")
			.(strlen(trim($keyword)) > 0?$keyword_where:"")
			.$status_where
			.$cancelled_where
			."order by l.id desc";
		return $sql;
	}

	function getItems()
	{
		$config = JFactory::getConfig();
		$app = JFactory::getApplication('administrator');
		$limistart = $app->getUserStateFromRequest($this->context.'.list.start', 'limitstart');
		$limit = $app->getUserStateFromRequest($this->context.'.list.limit', 'limit', $config->get('list_limit'));
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query = $this->getListQuery();

		$db->setQuery($query);
		$db->query();
		$result	= $db->getNumRows();//$db->loadObjectList();
		$this->total = $result;

		$db->setQuery($query, $limistart, $limit);
		$db->query();
		$result	= $db->loadObjectList();

		foreach ($result as $i => $v)
		{
			$sql = "select * from #__digicom_licensefields where licenseid=".$v->id;
			$db->setQuery($sql);
			$result[$i]->orig_fields = $db->loadObjectList();
			$sql = "select * from #__digicom_licenseprodfields where licenseid=".$v->id;
			$db->setQuery($sql);
			$result[$i]->cur_fields = $db->loadObjectList();

			$sql = "select f.* from #__digicom_customfields f "
				." where f.id in (select fieldid from  #__digicom_prodfields pf where pf.productid=".$v->productid." "
				." and pf.publishing=1) and f.published=1";
			$db->setQuery($sql);
			$result[$i]->prod_fields = $db->loadObjectList();
		}

		return $result;
	}

	function getProducts() {
		$db = JFactory::getDBO();
		$sql = "select id,name from #__digicom_products";
		$db->setQuery($sql);
		return $db->loadObjectList();
	}

	function getLicensesInOrder($order)
	{
		$db  = JFactory::getDBO();
		$sql = "SELECT COUNT(*)
				FROM `#__digicom_licenses`
				WHERE `orderid`=" . (int) $order;
		$db->setQuery($sql);
		return $db->loadResult();
	}

	function getLicense($lid = 0)
	{
		if ($lid > 0) $this->_id = $lid;
		$db  = JFactory::getDBO();
		//if (empty ($this->_license)) {
//			$this->_license = $this->getTable("License");
			$sql = "select l.*,p.name as productname, p.main_zip_file, p.domainrequired, u.username from #__digicom_licenses l, #__digicom_products p, #__users u where l.productid=p.id and l.userid=u.id and l.id=".$this->_id;
			$this->_total = $this->_getListCount($sql);

			$this->_license = $this->_getList($sql);//->load($this->_id);
			if (count ($this->_license) > 0) $this->_license = $this->_license[0];
			else {
				$this->_license = $this->getTable("License");
				$this->_license->username = "";
			}
			$v = $this->_license;
			$sql = "select * from #__digicom_licensefields where licenseid=".$v->id;
			$db->setQuery($sql);
			$this->_license->orig_fields = $db->loadObjectList();
			$sql = "select * from #__digicom_licenseprodfields where licenseid=".$v->id;
			$db->setQuery($sql);
			$this->_license->cur_fields = $db->loadObjectList();

			$sql = "select f.* from #__digicom_customfields f "
				." where f.id in (select fieldid from  #__digicom_prodfields pf where pf.productid=".$v->productid." "
				." and pf.publishing=1) and f.published=1";
			$db->setQuery($sql);
			$this->_license->prod_fields = $db->loadObjectList();

			$sql = "select * from #__digicom_licenses_notes where lic_id = ".$this->_id;
			$db->setQuery($sql);
			$this->_license->licence_notes = $db->loadObjectList();

			$sql = "select * from #__digicom_licenses_payments where lic_id = ".$this->_id;
			$db->setQuery($sql);
			$this->_license->licence_payments = $db->loadObjectList();

		//}
		return $this->_license;
	}

	function store()
	{
		$db = JFactory::getDBO();
		$item = $this->getTable('License');
		$data = JRequest::get('post');
		//dsdebug($data);die();
		$res = true;
		$c = $this->getInstance("Config", "DigiComAdminModel");
		$configs = $c->getConfigs();

		if (!$item->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			dsdebug($this->_db->getErrorMsg());
			$res = false;
		}

		if (!$item->check()) {
//			$this->setError($item->getErrorMsg());
			$this->setError($this->_db->getErrorMsg());
			dsdebug($this->_db->getErrorMsg());
			$res = false;
		}
/*
		if ($item->id < 1) {//new items should have order id assigned
			$set_order = 1;
		} else {
			$set_order = 0;
		}
*/
		if (!$item->store()) {
			$this->setError($this->_db->getErrorMsg());
			$res = false;
		}
/*
		if ($set_order == 1) {
			$order = $this->getTable('Order');
			$order->userid = $item->userid;
			$order->order_date = time();
			$order->amount = $item->amount_paid;
			$order->number_of_licenses = 1;
			$order->payment_method = "admin";
			$order->currency = $configs->get('currency','USD');
			$order->status = "Active";
			if (!$order->store()) {
				$res = false;
			} else {
				$item->orderid = $order->id;
				if (!$item->store()) $res = false;
			}
		}
*/

		// If the cancelled is false override the default refund value
		if (!$data['iscancelled'])
		{
			$sql = "UPDATE `#__digicom_licenses`
					SET `cancelled`=0
					WHERE `licenseid`='" . $data["licenseid"] . "'";
			$db->setQuery($sql);
			$db->query();
		}


		//save license selected attributes
		$this->_id = $item->id;
		$x = $this->getLicense();
		$db = JFactory::getDBO();
		$sql = "delete from #__digicom_licenseprodfields where licenseid=".$item->id;
		$db->setQuery($sql);
		$db->query();
		foreach ($x->prod_fields as $i => $v) {
			$oid = JRequest::getVar("field".$v->id, -1, "request");
			$sql = "insert into #__digicom_licenseprodfields (licenseid, fieldid, optionid) values
					('".$item->id."', '".$v->id."', '".$oid."')
				";
			$db->setQuery($sql);
			$db->query();
		}
				
		// Save Notes

		$notes = JRequest::getVar('notes');
		$expire = JRequest::getVar('expire');
		if($notes){ 
			foreach($notes as $key => $note){
				$sql = "insert into #__digicom_licenses_notes (lic_id, notes, expires) values (".$item->id.", '".$note."', '".$expire[$key]."');";
				$db->setQuery($sql);
				$db->query();
			}
		}
		$expires = JRequest::getVar('expires');
		if(isset($expires) && count($expires) > 0){
			foreach($expires as $key=>$value){
				$sql = "update #__digicom_licenses_notes set `expires`='".trim($value)."' where id=".intval($key);
				$db->setQuery($sql);
				$db->query();
			}
		}
		return $res;
	}

	function deleteNote(){
		$db = JFactory::getDBO();
		$cid = JRequest::getVar("cid", array(), "array");
		$cid = intval($cid["0"]);
		$note_id = JRequest::getVar("note", "0");
		$sql = "delete from #__digicom_licenses_notes where lic_id=".$cid." and id=".intval($note_id);
		$db->setQuery($sql);
		if($db->query()){
			return true;
		}
		return false;
	}

	function delete()
	{
		$db  = JFactory::getDBO();
		$cids = JRequest::getVar('cid', array(0), 'post', 'array');
		$item = $this->getTable('License');

		foreach ($cids as $cid)
		{
			$delete_order = 0;
			$sql = "SELECT `orderid`, `amount_paid`
					FROM `#__digicom_licenses`
					WHERE `id`=" . (int) $cid;
			$db->setQuery($sql);
			$order = $db->loadObject();

			$sql = "SELECT COUNT(*)
					FROM `#__digicom_licenses`
					WHERE `cancelled`<>3
					  AND `orderid`=" . (int) $order->orderid;
			$db->setQuery($sql);
			if ($db->loadResult() == 1)
			{
				$delete_order = 1;
			}
			else
			{
				$sql = "UPDATE `#__digicom_licenses`
						SET `cancelled`=3
						WHERE `id`=" . (int) $cid;
				$db->setQuery($sql);
				$db->query();

				$sql = "UPDATE `#__digicom_orders`
						SET `amount`=(`amount`-".$order->amount_paid."),
							`amount_paid`=(`amount_paid`-".$order->amount_paid.")
						WHERE `id`=" . (int) $order->orderid;
				$db->setQuery($sql);
				$db->query();
			}

			if ($delete_order)
			{
				if (!$item->delete($cid))
				{
					$this->setError($item->getErrorMsg());
					return false;
				}
				else
				{
					$sql = "DELETE FROM `#__digicom_orders`
							WHERE `id`=" . (int) $order->orderid;
					$db->setQuery($sql);
					$db->query();
				}
			}
		}

		return true;
	}


	function publish () {

		$db = JFactory::getDBO();

		$cids = JRequest::getVar('cid', array(0), 'post', 'array');
		$task = JRequest::getVar('task', '', 'post');
		$item = $this->getTable('License');

		$return = -1;

		// Licenses
		if ($task == 'publish'){
			$sql = "update #__digicom_licenses set published='1' where id in ('".implode("','", $cids)."')";
			$return = 1;
		} else {
			$sql = "update #__digicom_licenses set published='0' where id in ('".implode("','", $cids)."')";
			$return = -1;
		}
		$db->setQuery($sql);
		if (!$db->query() ){
			$this->setError($db->getErrorMsg());
			return false;
		}

		// Orders
		foreach( $cids as $cid ) {
			$item->load($cid);
			if ($task == 'publish'){
				$sql = "update #__digicom_orders set published='1' where id = '".$item->orderid."'";
			}
			$db->setQuery($sql);
			if (!$db->query() ){
				$this->setError($db->getErrorMsg());
				return false;
			}
		}

		return $return;
	}


	function saveDomain() {
		$this->getLicense();
		$license = $this->_license;
		$no_prod = true;
//		if (strlen(trim($license->domain)) > 0) $no_prod = false;

		$no_dev = true;
//		if (strlen(trim($license->dev_domain)) > 0) $no_dev = false;

//		if (!$no_prod && !$no_dev) {
//			$this->setRedirect("index.php?option=com_digicom&controller=licenses" );
//			return false;
//		}

		$prod = trim(JRequest::getVar("proddomain", "", "request"));
		$dev = trim(JRequest::getVar("devdomain", "", "request"));
		$db = JFactory::getDBO();
		if (strlen(trim($prod)) > 0 && $no_prod) {
			$sql = "update #__digicom_licenses set domain='".$prod."' where id=".$license->id;
			$db->setQuery($sql);
			$db->query();
		}
		if (strlen(trim($dev)) > 0 && $no_dev) {
			$sql = "update #__digicom_licenses set dev_domain='".$dev."' where id=".$license->id;
			$db->setQuery($sql);
			$db->query();
		}
		return true;
	}

	function prepareDownload($product, $uid) {
		jimport("joomla.filesystem.file");
		$resdir = JPATH_ROOT.DS."administrator".DS."components".DS."digicom_product_uploads".DS.$product->id.DS;

		$resdir_orig = $resdir."original".DS;
		$resdir_encoded = $resdir."encoded".DS;
		$srcFile = $resdir_orig.$product->file;
		   
			   
			$prepDir = JPATH_ROOT.DS."components/com_digicom/download/".$uid."/";
		if (file_exists($prepDir) ) {
			JFolder::delete ($prepDir);
		} 

		JFolder::create($prepDir);
		if (!JFile::copy ($srcFile, $prepDir.$product->file)) return false;//die ($srcFile." ".$prepDir.$product->file);;
		return true;
	}

	function prepareEncodedFile ($product, $license, $uid, $dev = 0, $method) {
		jimport("joomla.filesystem.file");
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
		//$srcFile = $mosConfig_absolute_path."/administrator/components/com_digicom/upload/".$product->productid."/encoded/".$method."/".$phpVer[0]."/".$product->product_file;
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


	function getLicenseText ($product, $license, $uid, $dev = 0, $method) {
		jimport("joomla.filesystem.file");
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
		
//					$subdomains = mosGetParam( $_POST, 'subdomains' ) ;
			$domain = $license->domain;
			$devdomain = $license->dev_domain;
		
	//remove the existing files
			$license_file = "license.txt";
		//$srcFile = $mosConfig_absolute_path."/administrator/components/com_digicom/upload/".$product->productid."/encoded/".$method."/".$phpVer[0]."/".$product->product_file;
			$srcFile = JPATH_ROOT."/administrator/components/digicom_product_uploads/".$product->id."/encoded/".$method."/".$phpVer[0]."/".$product->file;
		if (!file_exists ($srcFile)) $srcFile = JPATH_ROOT."/administrator/components/digicom_product_uploads/".$product->id."/original/".$product->file;
		
			$prepDir = JPATH_ROOT."/components/com_digicom/download/".$uid."/";

		if (file_exists($prepDir) ) {
			JFolder::delete ($prepDir);
		} 

		JFolder::create($prepDir);
		
//		   JFile::copy ($srcFile, $prepDir.$product->file);
//print_r($product);die;
	//gen the license file
		//$platform_options = $plugin_handler->getEncPlatformsForMethod($method);
//	  $platform_options = HandleDigiComPlugins::getEncPlatformsForMethod($method);
		$plugin_handler = $this->getInstance("Plugin", "DigiComAdminModel");
			$license_text = $plugin_handler->genLicenseFile( 
						$method, 
						$prepDir.$product->file, 
						$product->main_zip_file, 
						$domain,
						$devdomain, 
						$subdomains,
							$license_file, 
						$product->passphrase, 
						$product->trial_period,
						$prepDir );
						
		return $license_text;
	}

	function getLicensesByUserId($userid) {

		$db = JFactory::getDBO();
		$sql = "SELECT * FROM #__digicom_licenses WHERE userid=".$userid;
		$db->setQuery($sql);
		$lics = $db->loadObjectList();

		return $lics;
	}

	function customrExport(){
		$db = JFactory::getDBO();
		$and = "";
		$from = "";

		$prod_id = JRequest::getVar("filter_exp_product", "0");
		if($prod_id != "0"){
			$and .= " and p.id=".intval(trim($prod_id));
			$group_by .= " group by l.id";
		}

		$filter_prod = JRequest::getVar("filter_prod", "0");
		if($filter_prod != "0"){
			$and .= " and pc.catid=".intval($filter_prod);
		}

		$sql = "select c.firstname, c.lastname, u.email, p.name from #__digicom_customers c, #__users u, #__digicom_products p, #__digicom_licenses l, #__digicom_product_categories pc ".$from." where u.id=c.id and l.userid=u.id and l.productid=p.id ".$and." group by u.id";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadAssocList();

		if(isset($result) && count($result) > 0){
			$separator = ",";
			$csv_filename = JFilterOutput::stringURLSafe($result["0"]["name"])."_customers.csv";
			$file_content = "First Name".$separator."Last Name".$separator."Email"."\n";
			foreach($result as $key=>$customer){
				$file_content .= $customer["firstname"].$separator.$customer["lastname"].$separator.$customer["email"]."\n";
			}
			header("Content-Type: application/x-msdownload");
			header("Content-Disposition: attachment; filename=".$csv_filename);
			header("Pragma: no-cache");
			header("Expires: 0");
			echo utf8_decode($file_content);
			exit();
		}
		return true;
	}

	function export(){
		$db = JFactory::getDBO();
		$and = "";
		$from = "#__digicom_licenses l, #__digicom_products p, #__digicom_plans ps, #__digicom_customers c, #__users u, #__digicom_product_categories pc";
		$where = " p.id=l.productid and l.plan_id=ps.id and l.userid=c.id and u.id=l.userid and p.id=pc.productid";
		$group_by = "";

		$prod_id = JRequest::getVar("filter_exp_product", "0");
		if($prod_id != "0"){
			$and .= " and p.id=".intval(trim($prod_id));
			$group_by .= " group by l.id";
		}

		$filter_prod = JRequest::getVar("filter_prod", "0");
		if($filter_prod != "0"){
			$and .= " and pc.catid=".intval($filter_prod);
		}

		$keyword = JRequest::getVar("keyword", "");
		if(trim($keyword) != ""){
			$and .= " and (p.name like '%".trim(addslashes($keyword))."%' or c.firstname like '%".trim(addslashes($keyword))."%' or c.lastname like '%".trim(addslashes($keyword))."%')";
		}

		$status = JRequest::getVar("status", "-1");
		if($status != "-1"){
			$and .= " and l.published=".intval($status);
		}

		$cancelled = JRequest::getVar("status", "0");
		if($cancelled != "-1"){
			$and .= " and l.cancelled=".intval($cancelled);
		}

		$startdate = JRequest::getVar("startdate", "");
		if(trim($startdate) != ""){
			$and .= " and l.purchase_date >= '".trim($startdate)."'";
		}

		$enddate = JRequest::getVar("enddate", "");
		if(trim($startdate) != ""){
			$and .= " and l.purchase_date < '".trim($enddate)."'";
		}

		$sql = "select l.*, p.name product_name, ps.name plan_name, u.username, u.email, c.firstname, c.lastname
				from ".$from."
				where ".$where.$and.$group_by;
		$db->setQuery($sql);
		$db->query();
		$licenses = $db->loadAssocList();

		if(isset($licenses) && count($licenses) > 0){
			$separator = ",";
			$file_content = "License ID".$separator."Product".$separator."Username".$separator."First Name".$separator."Last Name".$separator."Email".$separator."Order id".$separator."Domain".$separator."Purchase Date".$separator."Expire Date".$separator."Plan"."\n";

			foreach($licenses as $key=>$license){
				$domain = trim($license["domain"]) == "" ? trim($license["dev_domain"]) : trim($license["domain"]);
				$file_content .= $license["licenseid"].$separator.'"'.$license["product_name"].'"'.$separator.$license["username"].$separator.$license["firstname"].$separator.$license["lastname"].$separator.$license["email"].$separator.$license["orderid"].$separator.$domain.$separator.$license["purchase_date"].$separator.$license["expires"].$separator.$license["plan_name"]."\n";
			}
			$csv_filename = "licenses_export.csv";
			header("Content-Type: application/x-msdownload");
			header("Content-Disposition: attachment; filename=".$csv_filename);
			header("Pragma: no-cache");
			header("Expires: 0");
			echo utf8_decode($file_content);
			exit();
		}
		else{
			return true;
		}
	}

	function getConfigs() {
		$comInfo = JComponentHelper::getComponent('com_digicom');
		return $comInfo->params;
	}

	function getlistCategories(){
		$db = JFactory::getDBO();
		$sql = "select * from #__digicom_categories order by parent_id, ordering asc";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadObjectList();
		return $result;
	}

	function getAllProducts(){
		$add = "";
		$filter_prod = JRequest::getVar("filter_prod", "0");
		if(intval($filter_prod) > 0){
			$add .= ", #__digicom_product_categories c where p.id=c.productid and c.catid=".intval($filter_prod);
		}
		$db = JFactory::getDBO();
		$sql = "select p.`id`, p.`name` from #__digicom_products p ".$add." order by `name` asc";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadAssocList();
		return $result;
	}

}

?>