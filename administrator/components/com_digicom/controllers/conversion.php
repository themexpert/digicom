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

class DigiComAdminControllerConversion extends DigiComAdminController {


	var $_model = null;

	function __construct () {

		parent::__construct();
		require_once( JPATH_COMPONENT.DS.'helpers'.DS.'helper.php' );
		$this->registerTask ("", "askParams");

	}


	function askParams() {

		$content = '
			<form action="index.php" method="POST" enctype="multipart/form-data">
				<div>
				   '.JText::_("Database name with old data").': 
				<input type="text" name="j1015" />
				</div>

				<div>
				   '.JText::_("Conversion method").': 
				<select name="testmode">
				<option value="0">Normal</option>
				<option value="1">Dump queries to file</option>
				<option value="2">Display queires without executing them</option>
				<option value="3">Import file with prepared queries</option>
				</select>

				</div>


				<div>
				   '.JText::_("File with prepared quries").': 
				<input type="file" name="preparedfile" id="preparedfile" />
				</div>

				<div>
				   '.JText::_("Import joomla users data").': 
				<input type="checkbox" name="iu" id="iu" value="1" />
				</div>


				<input type="hidden" name="controller" value="Conversion" />
				<input type="hidden" name="task" value="startConversion" />
				<input type="hidden" name="option" value="com_digicom" />
				<input type="submit" value="'.JText::_("Submit").'" />
			</form>


		';

		echo $content;
	}




function writeToFile($str) {
	$f = fopen (JPATH_COMPONENT.DS."sqldump.sql", "a+");
	fwrite ($f, "\n\n".$str."\n\n");
	fclose($f);
}

function convertFromFile() {
	global $database;
	$db = $database;
//	print_r($_FILES);
	$file = $_FILES['preparedfile']['tmp_name'];

	$data = explode ("#@@@#@@@#@@@#@@@#", implode("", file($file)));
	$i = 0;
	$import_users = JRequest::getVar("iu", 0);
	foreach ($data as $sql) {

		if (!$import_users) {
			if (strpos($sql, "#__users") || strpos($sql, "#__core_acl_")){
				continue;
			}

		}
		if (strlen(trim($sql)) > 0 ) {
			++$i;
			$db->setQuery(trim($sql));
			$db->query();
//			usleep(1000);
		//	echo $sql."<br /><br />";
			echo mysql_error();
//			echo "<br />";
		}
	}
	echo "<br /> i =".$i;

}


function startConversion() {

	global $database,$testmode, $j1015;
	$testmode = 1;
	$j1015 = "j1015";
	$method = 0;

	$database = JFactory::getDBO();

	$j1015 = JRequest::getVar("j1015");
	$testmode = JRequest::getVar("testmode");
	$import_users = JRequest::getVar("iu", 0);
	if ($testmode == 3 ) {
		$this->convertFromFile();
	} else {
		$this->convert_customers();
		if ($import_users) {

			$this->convert_acl();
			$this->convert_acl_grp_map();
		}
//		$this->convert_acl_grp();
		$this->	convert_orders();
		$this->	convert_products();
		$this->	convert_licenses();
		$this->	convert_featured();
		$this->	convert_fields();
		$this->	convert_productfields();
		$this->	convert_categories();
		$this->	convert_promos();
		$this->	convert_lfields();
		$this->	convert_lprodfields();
	}
}


function convert_customers() {
	//$db = JFactory::getDBO();
	global $database;
	global $j1015;
	$db = $database;
	$data = array();
	$new_udata = array();
	$sql = "select * from ".$j1015.".#__digicom_customer c, ".$j1015.".#__users u where c.userid = u.id";
	$db->setQuery($sql);
	$c_old = $db->loadObjectList();
	$cdata = array();

	$cdata['id'] = '';
	$cdata['address'] = '';
	$cdata['city'] = '';
	$cdata['state'] = '';
	$cdata['province'] = '';
	$cdata['zipcode'] = '';
	$cdata['country'] = '';
	$cdata['payment_type'] = '';
	$cdata['company'] = '';
	$cdata['firstname'] = '';
	$cdata['lastname'] = '';
	$cdata['shipaddress'] = '';
	$cdata['shipcity'] = '';
	$cdata['shipstate'] = '';
	$cdata['shipzipcode'] = '';
	$cdata['shipcountry'] = '';
	$cdata['person'] = '';
	$cdata['taxnum'] = '';


	$udata = array();
	$udata['id'] = '';
	$udata['name'] = '';
	$udata['username'] = '';
	$udata['email'] = '';
	$udata['password'] = '';
	$udata['usertype'] = '';
	$udata['block'] = '';
	$udata['gid'] = '';
	$udata['sendEmail'] = '';
	$udata['registerDate'] = '';
	$udata['lastvisitDate'] = '';
	$udata['activation'] = '';
	$udata['params'] = '';

	$sql = "insert into #__digicom_customers(`".implode("`,`", array_keys($cdata))."`) values ";
	$q = "insert into #__users(`".implode("`,`", array_keys($udata))."`) values ";

	if(count($c_old) > 0)
	foreach($c_old as $i=>$v) {
		
		$cdata['id'] = mysql_escape_string($v->userid);
		$cdata['address'] = mysql_escape_string($v->address);
		$cdata['city'] = mysql_escape_string($v->city);
		$cdata['state'] = mysql_escape_string($v->state);
		$cdata['province'] = mysql_escape_string($v->state);
		$cdata['zipcode'] = mysql_escape_string($v->zipcode);
		$cdata['country'] = mysql_escape_string($v->country);
		$cdata['payment_type'] = '';
		$cdata['company'] = mysql_escape_string($v->company);
		$cdata['firstname'] = mysql_escape_string($v->firstname);
		$cdata['lastname'] = mysql_escape_string($v->lastname);
		$cdata['shipaddress'] = mysql_escape_string($v->shipaddress);
		$cdata['shipcity'] = mysql_escape_string($v->shipcity);
		$cdata['shipstate'] = mysql_escape_string($v->shipstate);
		$cdata['shipzipcode'] = mysql_escape_string($v->shipzipcode);
		$cdata['shipcountry'] = mysql_escape_string($v->shipcountry);
		$cdata['person'] = mysql_escape_string($v->person);
		$cdata['taxnum'] = mysql_escape_string($v->taxnum);
		
		$data[] = "('".implode("','", $cdata)."')";

		foreach ($udata as $i1 => $v1) {
			$udata[$i1] = mysql_escape_string($v->$i1);
		}
		$new_udata[] =  "('".implode("','", $udata)."')";

		
	}
	global $testmode;
	if ($testmode) {
		foreach ($data as $i => $v) {
			$sql1 = $sql.$v;//implode(",", $data);
			echo $testmode==1?$this->writeToFile($sql1):$sql1;
		}

		$import_users = JRequest::getVar("iu", 0);
		if ($import_users) {
			foreach ($new_udata as $i => $v) {
				$sql1 = $q.$v;//implode(",", $data);
				echo $testmode==1?$this->writeToFile($sql1):$sql1;
			}
		}

		return;
	}

	if (count($data) > 0) {
		foreach ($data as $i => $v) {
			$sql1 = $sql.$v;//implode(",", $data);

		//	$sql .= implode(",", $data);
			$db->setQuery($sql1);
			$db->query();
			echo mysql_error();
		}
		$import_users = JRequest::getVar("iu", 0);
		if ($import_users) {

			foreach ($new_udata as $i => $v) {
				$sql1 = $q.$v;//implode(",", $data);

//			$sql1 = $q.implode(",", $new_udata);;//implode(",", $data);
				$db->setQuery($sql1);
				$db->query();
				echo mysql_error();
			}
		}


	}
	echo "Customers processed:".@mysql_num_rows();
	//echo $sql;
}




function convert_products(){ 
	global $database;	global $j1015;
	$db = $database;
	$data = array();
	$sql = "select * from ".$j1015.".#__digicom_product";
	$db->setQuery($sql);
	$p_old = $db->loadObjectList();
	$pdata = array();

	$pdata['id'] = null;
	$pdata['name'] = null;
	$pdata['images'] = null;
	$pdata['price'] = null;
	$pdata['discount'] = null;
	$pdata['ordering'] = null;
	$pdata['file'] = null;
	$pdata['description'] = null;
	$pdata['publish_up'] = null;
	$pdata['publish_down'] = null;
	$pdata['checked_out'] = null;
	$pdata['checked_out_time'] = null;
	$pdata['published'] = null;
	$pdata['passphrase'] = null;
	$pdata['main_zip_file'] = null;
	$pdata['encoding_files'] = null;
	$pdata['domainrequired'] = null;
	$pdata['articlelink'] = null;
	$pdata['articlelinkid'] = null;
	$pdata['articlelinkuse'] = null;
	$pdata['shippingtype'] = null;
	$pdata['shippingvalue0'] = null;
	$pdata['shippingvalue1'] = null;
	$pdata['shippingvalue2'] = null;
	$pdata['productemailsubject'] = null;
	$pdata['productemail'] = null;
	$pdata['sendmail'] = null;
	$pdata['popupwidth'] = null;
	$pdata['popupheight'] = null;
	$pdata['stock'] = null;
	$pdata['used'] = null;
	$pdata['usestock'] = null;
	$pdata['emptystockact'] = null;
	$pdata['showstockleft'] = null;
	$pdata['fulldescription'] = null;
	$pdata['metatitle'] = null;
	$pdata['metakeywords'] = null;
	$pdata['metadescription'] = null;
	$pdata['access'] = null;
	$pdata['prodtypeforplugin'] = null;
	$pdata['taxclass'] = null;
	$pdata['class'] = null;

	$sql = "insert into #__digicom_products(`".implode("`,`", array_keys($pdata))."`) values ";
	$q = "insert into #__digicom_product_categories(`productid`, `catid`) values ";
	$fdata = array();
	if(count($p_old) > 0)
	foreach ($p_old as $i => $v){

		$pdata['id'] = mysql_escape_string($v->productid);
		$pdata['name'] = mysql_escape_string($v->product_name);
		$img =explode("\n", $v->product_image);
		foreach ($img as $ii => $vv) $img[$ii] = "images/stories/".$vv;

		$pdata['images'] = mysql_escape_string(implode("\n",$img));

//		$pdata['images'] = mysql_escape_string($v->product_image);
		$pdata['price'] = mysql_escape_string($v->product_price);
		$pdata['discount'] = mysql_escape_string($v->percent_discount);
		$pdata['ordering'] = mysql_escape_string($v->ordering);
		$pdata['file'] = mysql_escape_string($v->product_file);
		$pdata['description'] = mysql_escape_string($v->product_description);
		$pdata['publish_up'] = mysql_escape_string($v->publish_up);
		$pdata['publish_down'] = mysql_escape_string($v->publish_down);
		$pdata['checked_out'] = mysql_escape_string($v->checked_out);
		$pdata['checked_out_time'] = mysql_escape_string($v->checked_out_time);
		$pdata['published'] = mysql_escape_string($v->state);
		$pdata['passphrase'] = mysql_escape_string($v->passphrase);
		$pdata['main_zip_file'] = mysql_escape_string($v->main_zip_file);
		$pdata['encoding_files'] = mysql_escape_string($v->encoding_files);
		$pdata['domainrequired'] = mysql_escape_string($v->domainrequired);
		$pdata['articlelink'] = mysql_escape_string($v->articlelink);
		$pdata['articlelinkid'] = mysql_escape_string($v->articlelinkid);
		$pdata['articlelinkuse'] = mysql_escape_string($v->articlelinkuse);
		$pdata['shippingtype'] = mysql_escape_string($v->shippingtype);
		$pdata['shippingvalue0'] = mysql_escape_string($v->shippingvalue0);
		$pdata['shippingvalue1'] = mysql_escape_string($v->shippingvalue1);
		$pdata['shippingvalue2'] = mysql_escape_string($v->shippingvalue2);
		$pdata['productemailsubject'] = mysql_escape_string($v->productemail);
		$pdata['productemail'] = mysql_escape_string($v->productemailsubject);
		$pdata['sendmail'] = mysql_escape_string($v->sendmail);
		$pdata['popupwidth'] = mysql_escape_string($v->popupwidth);
		$pdata['popupheight'] = mysql_escape_string($v->popupheight);
		$pdata['stock'] = mysql_escape_string($v->stock);
		$pdata['used'] = mysql_escape_string($v->used);
		$pdata['usestock'] = mysql_escape_string($v->usestock);
		$pdata['emptystockact'] = mysql_escape_string($v->emptystockact);
		$pdata['showstockleft'] = mysql_escape_string($v->showstockleft);
		$pdata['fulldescription'] = mysql_escape_string($v->product_fulldescription);
		$pdata['metatitle'] = '';
		$pdata['metakeywords'] = '';
		$pdata['metadescription'] = '';
		$pdata['access'] = $v->access;
		$pdata['prodtypeforplugin'] = '';
		$pdata['taxclass'] = '';
		$pdata['class'] = '';
		$data[] = "('".implode("','", $pdata)."')";

		$catid = explode (",", $v->catid);
		if (count($catid) > 0){
			foreach($catid as $cid)
				$fdata[] = " ('".$pdata['id']."', '".$cid."')";
		}


	}
	global $testmode;
	if ($testmode) {
		foreach ($data as $i => $v) {
			$sql1 = $sql.$v;//implode(",", $data);
			echo $testmode==1?$this->writeToFile($sql1):$sql1;
		}

		foreach ($fdata as $i => $v) {
			$sql1 = $q.$v;//implode(",", $data);
			echo $testmode==1?$this->writeToFile($sql1):$sql1;
		}

		return;
	}


	if (count($data) > 0) {
		foreach ($data as $i => $v) {
			$sql1 = $sql.$v;//implode(",", $data);
			$db->setQuery($sql1);
			$db->query();
			echo mysql_error();
		}
	}

	if (count($fdata) > 0) {
		foreach ($fdata as $i => $v) {
			$sql1 = $q.$v;//implode(",", $fdata);
			$db->setQuery($sql1);
			$db->query();
			echo mysql_error();
		}
	}

	echo "Products processed:".@mysql_num_rows();
}


function convert_featured(){
	global $database;	global $j1015;
	$db = $database;

	$data = array();
	$sql = "select * from ".$j1015.".#__digicom_featuredproducts";
	$db->setQuery($sql);
	$p_old = $db->loadObjectList();
	$pdata = array();
	$pdata['productid'] = '';
	$pdata['featuredid'] = '';

	$sql = "insert into #__digicom_featuredproducts(`".implode("`,`", array_keys($pdata))."`) values ";
	if(count($p_old) > 0)
	foreach ($p_old as $i => $v){
		$pdata['productid'] = $v->productid;
		$pdata['featuredid'] = $v->featuredid;
		$data[] = "('".implode("','", $pdata)."')";
	}
	global $testmode;
	if ($testmode) {
		foreach ($data as $i => $v) {
			$sql1 = $sql.$v;//implode(",", $data);
			echo $testmode==1?$this->writeToFile($sql1):$sql1;
		}
		return;
	}


	if (count($data) > 0) {
		foreach ($data as $i => $v) {
			$sql1 = $sql.$v;//implode(",", $data);
			$db->setQuery($sql1);
			$db->query();
			echo mysql_error();
		}
	}
	echo "Featureds processed:".@mysql_num_rows();

}


function convert_productfields(){
	global $database;	global $j1015;
	$db = $database;

	$data = array();
	$sql = "select * from ".$j1015.".#__digicom_prodfields";
	$db->setQuery($sql);
	$p_old = $db->loadObjectList();
	$pdata = array();
	$pdata['productid'] = '';
	$pdata['fieldid'] = '';
	$pdata['publishing'] = '';
	$pdata['mandatory'] = '';

	$sql = "insert into #__digicom_prodfields(`".implode("`,`", array_keys($pdata))."`) values ";
	if(count($p_old) > 0)
	foreach ($p_old as $i => $v){
		$pdata['productid'] = $v->productid;
		$pdata['fieldid'] = $v->fieldid;
		$pdata['publishing'] = $v->publishing;
		$pdata['mandatory'] = $v->mandatory;
		$data[] = "('".implode("','", $pdata)."')";
	}

	global $testmode;
	if ($testmode) {
		foreach ($data as $i => $v) {
			$sql1 = $sql.$v;//implode(",", $data);
			echo $testmode==1?$this->writeToFile($sql1):$sql1;
		}
		return;
	}


	if (count($data) > 0) {
		foreach ($data as $i => $v) {
			$sql1 = $sql.$v;//implode(",", $data);
			$db->setQuery($sql);
			$db->query();
			echo mysql_error();
		}
	}
	echo "Product Attributes processed:".@mysql_num_rows();

}

function convert_fields(){
	global $database;	global $j1015;
	$db = $database;

	$data = array();
	$sql = "select * from ".$j1015.".#__digicom_customfields";
	$db->setQuery($sql);
	$f_old = $db->loadObjectList();
	$fdata = array();
	$fdata['id'] = null;
	$fdata['name'] = null;
	$fdata['options'] = null;
	$fdata['published'] = null;
	$fdata['checked_out'] = null;
	$fdata['checked_out_time'] = null;
	$fdata['ordering'] = null;
	$fdata['size'] = null;

	$sql = "insert into #__digicom_customfields(`".implode("`,`", array_keys($fdata))."`) values ";
	if(count($f_old) > 0)
	foreach ($f_old as $i => $v){
		$fdata['id'] = $v->id;
		$fdata['name'] = mysql_escape_string($v->name);
		$fdata['options'] =mysql_escape_string($v->options);
		$fdata['published'] = $v->publishing;
		$fdata['checked_out'] = $v->checked_out;
		$fdata['checked_out_time'] = $v->checked_out;
		$fdata['ordering'] = $v->ordering;
		$fdata['size'] = $v->size;
		$data[] = "('".implode("','", $fdata)."')";
	}

	global $testmode;
	if ($testmode) {
		foreach ($data as $i => $v) {
			$sql1 = $sql.$v;//implode(",", $data);
			echo $testmode==1?$this->writeToFile($sql1):$sql1;
		}
		return;
	}



	if (count($data) > 0) {
		foreach ($data as $i => $v) {
			$sql1 = $sql.$v;//implode(",", $data);
			$db->setQuery($sql1);
			$db->query();
			echo mysql_error();
		}
	}
	echo "Attributes processed:".@mysql_num_rows();

}


function convert_promos(){
	global $database;	global $j1015;
	$db = $database;

	$data = array();
	$sql = "select * from ".$j1015.".#__digicom_promocodes";
	$db->setQuery($sql);
	$pr_old = $db->loadObjectList();
	$prdata = array();
	$prdata['id'] = null;
	$prdata['title'] = null;
	$prdata['code'] = null;
	$prdata['codelimit'] = null;
	$prdata['amount'] = null;
	$prdata['codestart'] = null;
	$prdata['codeend'] = null;
	$prdata['forexisting'] = null;
	$prdata['published'] = null;
	$prdata['aftertax'] = null;
	$prdata['promotype'] = null;
	$prdata['used'] = null;
	$prdata['ordering'] = null;
	$prdata['checked_out'] = null;
	$prdata['checked_out_time'] = null;

	$sql = "insert into #__digicom_promocodes(`".implode("`,`", array_keys($prdata))."`) values ";
	if(count($pr_old) > 0)
	foreach ($pr_old as $i => $v){
		$prdata['id'] = $v->id;
		$prdata['title'] = mysql_escape_string($v->title);
		$prdata['code'] = mysql_escape_string($v->code);
		$prdata['codelimit'] = mysql_escape_string($v->codelimit);
		$prdata['amount'] = mysql_escape_string($v->amount);
		$prdata['codestart'] = mysql_escape_string($v->codestart);
		$prdata['codeend'] = mysql_escape_string($v->codeend);
		$prdata['forexisting'] = $v->forexisting;
		$prdata['published'] = $v->publishing;
		$prdata['aftertax'] = $v->aftertax;
		$prdata['promotype'] = mysql_escape_string($v->promotype);
		$prdata['used'] = $v->used;
		$prdata['ordering'] = $v->ordering;
		$prdata['checked_out'] = $v->checked_out;
		$prdata['checked_out_time'] = $v->checked_out_time;
		$data[] = "('".implode("','", $prdata)."')";
	}
	global $testmode;
	if ($testmode) {
		foreach ($data as $i => $v) {
			$sql1 = $sql.$v;//implode(",", $data);
			echo $testmode==1?$this->writeToFile($sql1):$sql1;
		}
		return;
	}



	if (count($data) > 0) {
		foreach ($data as $i => $v) {
			$sql1 = $sql.$v;//implode(",", $data);

			$db->setQuery($sql1);
			$db->query();
			echo mysql_error();
		}
	}
	echo "Promocodes processed:".@mysql_num_rows();

}


function convert_categories() {

	global $database;	global $j1015;
	$db = $database;

	$data = array();
	$sql = "select * from ".$j1015.".#__digicom_categories";
	$db->setQuery($sql);
	$c_old = $db->loadObjectList();
	$cdata = array();

	$cdata['id'] = null;
	$cdata['parent_id'] = null;
	$cdata['title'] = null;
	$cdata['name'] = null;
	$cdata['section'] = null;
	$cdata['image_position'] = null;
	$cdata['description'] = null;
	$cdata['published'] = null;
	$cdata['checked_out'] = null;
	$cdata['checked_out_time'] = null;
	$cdata['editor'] = null;
	$cdata['ordering'] = null;
	$cdata['access'] = null;
	$cdata['count'] = null;
	$cdata['metakeywords'] = null;
	$cdata['metadescription'] = null;
	$cdata['images'] = null;
	$cdata['params'] = null;
	$sql = "insert into #__digicom_categories(`".implode("`,`", array_keys($cdata))."`) values ";
	if(count($c_old) > 0)
	foreach ($c_old as $i => $v){
		foreach ($cdata as $i1 => $v1) {
			$cdata[$i1] = mysql_escape_string($v->$i1);
		}
		$img =explode("\n", $v->images);
		foreach ($img as $ii => $vv) $img[$ii] = "images/stories/".$vv;
		$cdata['images'] = mysql_escape_string(implode("\n",$img));

		$data[] = "('".implode("','", $cdata)."')";
	}

	global $testmode;
	if ($testmode) {
		foreach ($data as $i => $v) {
			$sql1 = $sql.$v;//implode(",", $data);
			echo $testmode==1?$this->writeToFile($sql1):$sql1;
		}
		return;
	}

	if (count($data) > 0) {
		foreach ($data as $i => $v) {
			$sql1 = $sql.$v;//implode(",", $data);
			$db->setQuery($sql1);
			$db->query();
			echo mysql_error();
		}
	}
	echo "Categories processed:".@mysql_num_rows();

}


function convert_orders() {

	global $database;	global $j1015;
	$db = $database;

	$data = array();
	$sql = "select * from ".$j1015.".#__digicom_orders";
	$db->setQuery($sql);
	$o_old = $db->loadObjectList();
	$odata = array();

	$odata['id'] = null;
	$odata['userid'] = null;
	$odata['order_date'] = null;
	$odata['amount'] = null;
	$odata['payment_method'] = null;
	$odata['number_of_licenses'] = null;
	$odata['currency'] = null;
	$odata['status'] = null;
	$odata['tax'] = null;
	$odata['shipping'] = null;
	$odata['promocodeid'] = null;
	$odata['promocode'] = null;
	$odata['promocodediscount'] = null;
	$odata['shipto'] = null;
	$odata['fullshipto'] = null;

	$sql = "insert into #__digicom_orders(`".implode("`,`", array_keys($odata))."`) values ";
	if(count($o_old) > 0)
	foreach ($o_old as $i => $v){
		foreach ($odata as $i1 => $v1) {
			$odata[$i1] = mysql_escape_string($v->$i1);
		}
		$odata['id'] = $v->orderid;
		$data[] = "('".implode("','", $odata)."')";
	}

	global $testmode;
	if ($testmode) {
		foreach ($data as $i => $v) {
			$sql1 = $sql.$v;//implode(",", $data);
			echo $testmode==1?$this->writeToFile($sql1):$sql1;
		}
		return;
	}

	if (count($data) > 0) {
		foreach ($data as $i => $v) {
			$sql1 = $sql.$v;//implode(",", $data);
			$db->setQuery($sql1);
			$db->query();
			echo mysql_error();
		}
	}
	echo "Orders processed:".@mysql_num_rows();

}

function convert_licenses() {

	global $database;	global $j1015;
	$db = $database;

	$data = array();
	$sql = "select * from ".$j1015.".#__digicom_licenses";
	$db->setQuery($sql);
	$l_old = $db->loadObjectList();
	$ldata = array();

	$ldata['id'] = null;
	$ldata['licenseid'] = null;
	$ldata['userid'] = null;
	$ldata['productid'] = null;
	$ldata['domain'] = null;
	$ldata['amount_paid'] = null;
	$ldata['email'] = null;
	$ldata['orderid'] = null;
	$ldata['dev_domain'] = null;
	$ldata['hosting_service'] = null;
	$ldata['published'] = null;

	$sql = "insert into #__digicom_licenses(`".implode("`,`", array_keys($ldata))."`) values ";
	if(count($l_old) > 0)
	foreach ($l_old as $i => $v){
		$ldata['id'] = $v->id;
		$ldata['licenseid'] = $v->licenseid;
		$ldata['userid'] = $v->userid;
		$ldata['productid'] = $v->productid;
		$ldata['domain'] = mysql_escape_string($v->domain);
		$ldata['amount_paid'] = $v->amount_paid;
		$ldata['email'] = mysql_escape_string($v->email);
		$ldata['orderid'] = $v->orderid;
		$ldata['dev_domain'] = mysql_escape_string($v->dev_domain);
		$ldata['hosting_service'] = mysql_escape_string($v->hosting_service);
		$ldata['published'] = $v->publishing;

		$data[] = "('".implode("','", $ldata)."')";
	}

	global $testmode;
	if ($testmode) {
		foreach ($data as $i => $v) {
			$sql1 = $sql.$v;//implode(",", $data);
			echo $testmode==1?$this->writeToFile($sql1):$sql1;
		}
		return;
	}

	if (count($data) > 0) {
		foreach ($data as $i => $v) {
			$sql1 = $sql.$v;//implode(",", $data);
			$db->setQuery($sql1);
			$db->query();
			echo mysql_error();
		}
	}
	echo "Licenses processed:".@mysql_num_rows();

}

function convert_lfields() {

	global $database;	global $j1015;
	$db = $database;

	$data = array();
	$sql = "select * from ".$j1015.".#__digicom_licensefields";
	$db->setQuery($sql);
	$lf_old = $db->loadObjectList();
	$lfdata = array();

	$lfdata['licenseid'] = null;
	$lfdata['fieldname'] = null;
	$lfdata['optioname'] = null;

	$sql = "insert into #__digicom_licensefields(`".implode("`,`", array_keys($lfdata))."`) values ";
	if(count($lf_old) > 0)
	foreach ($lf_old as $i => $v){
		foreach ($lfdata as $i1 => $v1) {
			$lfdata[$i1] = mysql_escape_string($v->$i1);
		}
		$data[] = "('".implode("','", $lfdata)."')";
	}
	global $testmode;
	if ($testmode) {
		foreach ($data as $i => $v) {
			$sql1 = $sql.$v;//implode(",", $data);
			echo $testmode==1?$this->writeToFile($sql1):$sql1;
		}
		return;
	}

	if (count($data) > 0) {
		foreach ($data as $i => $v) {
			$sql1 = $sql.$v;//implode(",", $data);
			$db->setQuery($sql1);
			$db->query();
			echo mysql_error();
		}
	}
	echo "License fields processed:".@mysql_num_rows();

}


function convert_lprodfields() {

	global $database;	global $j1015;
	$db = $database;

	$data = array();
	$sql = "select * from ".$j1015.".#__digicom_licenseprodfields";
	$db->setQuery($sql);
	$lf_old = $db->loadObjectList();
	$lfdata = array();

	$lfdata['licenseid'] = null;
	$lfdata['fieldid'] = null;
	$lfdata['optionid'] = null;

	$sql = "insert into #__digicom_licenseprodfields(`".implode("`,`", array_keys($lfdata))."`) values ";
	if(count($lf_old) > 0)
	foreach ($lf_old as $i => $v){
		foreach ($lfdata as $i1 => $v1) {
			$lfdata[$i1] = $v->$i1;
		}
		$data[] = "('".implode("','", $lfdata)."')";
	}

	global $testmode;
	if ($testmode) {
		foreach ($data as $i => $v) {
			$sql1 = $sql.$v;//implode(",", $data);
			echo $testmode==1?$this->writeToFile($sql1):$sql1;
		}
		return;
	}

	if (count($data) > 0) {
		foreach ($data as $i => $v) {
			$sql1 = $sql.$v;//implode(",", $data);
			$db->setQuery($sql1);
			$db->query();
			echo mysql_error();
		}
	}
	echo "License production fields processed:".@mysql_num_rows();

}


function convert_acl() {

	global $database;	global $j1015;
	$db = $database;

	$data = array();
	$sql = "select * from ".$j1015.".#__core_acl_aro";
	$db->setQuery($sql);
	$acl_old = $db->loadObjectList();
	$acldata = array();

	$acldata['id'] = null;
	$acldata['section_value'] = null;
	$acldata['value'] = null;
	$acldata['order_value'] = null;
	$acldata['name'] = null;
	$acldata['hidden'] = null;

	$sql = "insert into #__core_acl_aro(`".implode("`,`", array_keys($acldata))."`) values ";
	if(count($acl_old) > 0)
	foreach ($acl_old as $i => $v){
		foreach ($acldata as $i1 => $v1) {
			$acldata[$i1] = $v->$i1;
		}
		$acldata['id'] = $v->aro_id;
		$data[] = "('".implode("','", $acldata)."')";
	}

	global $testmode;
	if ($testmode) {
		foreach ($data as $i => $v) {
			$sql1 = $sql.$v;//implode(",", $data);
			echo $testmode==1?$this->writeToFile($sql1):$sql1;
		}
		return;
	}

	if (count($data) > 0) {
		foreach ($data as $i => $v) {
			$sql1 = $sql.$v;//implode(",", $data);
			$db->setQuery($sql1);
			$db->query();
			echo mysql_error();
		}
	}
	echo "ACL processed:".@mysql_num_rows();

}


function convert_acl_grp_map() {

	global $database;	global $j1015;
	$db = $database;

	$data = array();
	$sql = "select * from ".$j1015.".#__core_acl_groups_aro_map";
	$db->setQuery($sql);
	$acl_old = $db->loadObjectList();
	$acldata = array();

	$acldata['group_id'] = null;
	$acldata['section_value'] = null;
	$acldata['aro_id'] = null;

	$sql = "insert into #__core_acl_groups_aro_map(`".implode("`,`", array_keys($acldata))."`) values ";
	if(count($acl_old) > 0)
	foreach ($acl_old as $i => $v){
		foreach ($acldata as $i1 => $v1) {
			$acldata[$i1] = $v->$i1;
		}
		$data[] = "('".implode("','", $acldata)."')";
	}

	global $testmode;
	if ($testmode) {
		foreach ($data as $i => $v) {
			$sql1 = $sql.$v;//implode(",", $data);
			echo $testmode==1?$this->writeToFile($sql1):$sql1;
		}
		return;
	}

	if (count($data) > 0) {
		foreach ($data as $i => $v) {
			$sql1 = $sql.$v;//implode(",", $data);
			$db->setQuery($sql1);
			$db->query();
			echo mysql_error();
		}
	}
	echo "ACL group map processed:".@mysql_num_rows();

}



function convert_acl_grp() {

	global $database;	global $j1015;
	$db = $database;

	$data = array();
	$sql = "select * from ".$j1015.".#__core_acl_aro_groups";
	$db->setQuery($sql);
	$acl_old = $db->loadObjectList();
	$acldata = array();

	$acldata['id'] = null;
	$acldata['parent_id'] = null;
	$acldata['name'] = null;
	$acldata['lft'] = null;
	$acldata['rgt'] = null;
	$acldata['value'] = null;

	$sql = "insert into #__core_acl_aro_groups(`".implode("`,`", array_keys($acldata))."`) values ";
	if(count($acl_old) > 0)
	foreach ($acl_old as $i => $v){
		foreach ($acldata as $i1 => $v1) {
			$acldata[$i1] = $v->$i1;
		}
		$data[] = "('".implode("','", $acldata)."')";
	}

	global $testmode;
	if ($testmode) {
		foreach ($data as $i => $v) {
			$sql1 = $sql.$v;//implode(",", $data);
			echo $testmode==1?$this->writeToFile($sql1):$sql1;
		}
		return;
	}

	if (count($data) > 0) {
		foreach ($data as $i => $v) {
			$sql1 = $sql.$v;//implode(",", $data);
			$db->setQuery($sql1);
			$db->query();
			echo mysql_error();
		}
	}
	echo "ACL group map processed:".@mysql_num_rows();

}


};

?>