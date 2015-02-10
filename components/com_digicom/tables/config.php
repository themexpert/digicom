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

class TableConfig extends JTable {
	var $id = null;
	var $currency = null;
	var $store_name = null;
	var $store_url = null;
	var $store_email = null;
	var $product_per_page = null;
	var $google_account = null;
	var $country = null;
	var $state = null;
	var $city = null;
	var $tax_option = null;
	var $tax_rate = null;
	var $tax_type = null;
	var $totaldigits = null;
	var $decimaldigits = null;
	var $ftp_source_path = null;
	var $time_format = null;
	var $afteradditem = null;
	var $showreplic = null;
	var $idevaff = null;
	var $askterms = null;
	var $termsid = null;
	var $termsheight = null;
	var $termswidth = null;
	var $topcountries = null;
	var $usestoremail = null;
	var $catlayoutstyle = null;
	var $catlayoutcol = null;
	var $catlayoutrow = null;
	var $prodlayouttype = null;
	var $prodlayoutstyle = null;
	var $prodlayoutcol = null;
	var $prodlayoutrow = null;
	var $orderidvar = null;
	var $ordersubtotalvar = null;
	var $idevpath = null;
	var $askforship = null;
	var $person = null;
	var $taxnum = null;
	var $modbuynow = null;
	var $usecimg = null;
	var $showthumb = null;
	var $showsku = null;
	var $sendmailtoadmin = null;
	var $directfilelink = null;
	var $debugstore = null;
	var $dumptofile = null;
	var $dumpvars = null;
	var $thankshtml = null;
	var $ftranshtml = null;
	var $layout_template = null;
	var $showprodshort = 0;
	var $pendinghtml = 0;
	var $address  	= 0;
	var $zip 	= 0;
	var $phone 	= 0;
	var $fax 	= 0;
	var $afterpurchase	= null;
	var $showoid 	= 0;
	var $showoipurch	= 0;
	var $showolics 	= 0;
	var $showopaid 	= 0;
	var $showodate  = 0;
	var $showorec 	= 0;
	var $showlid	= 0;
	var $showlprod	= 0;
	var $showloid	= 0;
	var $showldate	= 0;
	var $showldown	= 0;
	var $showldomain= 0;
	var $showcam 	= 0;
	var $showcpromo	= 0;
	var $showcremove	= 0;
	var $showccont	= 0;
	var $tax_classes	= 0; 
	var $tax_base		   = 0;
	var $tax_catalog		= 0;
	var $tax_shipping	   = 0;
	var $tax_discount	   = 0;
	var $discount_tax	   = 0;
	var $tax_country		= null;
	var $tax_state		  = null;
	var $tax_zip			= null;
	var $tax_price		  = 0;
	var $tax_summary		= 0;
	var $shipping_price	 = 0;
	var $product_price	  = 0;
	var $tax_zero		   = 0;
	var $tax_apply		  = 0;
	var $usestorelocation 	= 0;
	var $allowcustomerchoseclass	= 0;
	var $takecheckout	= 0;
	var $continue_shopping_url	= null;

	function __construct (&$db) {
		parent::__construct('#__digicom_settings', 'id', $db);
		$sql = "SELECT COUNT(*) FROM #__digicom_settings WHERE id=1";
		$db->setQuery($sql);
		$c = $db->loadResult();
		if ($c < 1) {
			$sql = "INSERT INTO #__digicom_settings(`id`) VALUES (1)";
			$db->setQuery($sql);
			$db->query();
		}
	}

	function store ($updateNulls = false) {

		$this->thankshtml = stripslashes( $_REQUEST['thankshtml'] );
		$this->ftranshtml = stripslashes( $_REQUEST['ftranshtml'] );
		$this->pendinghtml = stripslashes( $_REQUEST['pendinghtml'] );

		$res = parent::store();
		if (!$res) return $res;
		$topcountries =  JRequest::getVar('topcountries', array(0), 'post', 'array');
		$db = JFactory::getDBO();

		$layout_template =  stripslashes( $_REQUEST['layout_template'] );
		$usecimg =  JRequest::getVar('usecimg', 0, 'post');
		$sql = "UPDATE #__digicom_settings SET `topcountries`='".implode(",", $topcountries)."', ".
				" `usecimg`='".$usecimg."', `layout_template`='".($layout_template)."' ".
			"WHERE id=1";

		$db->setQuery($sql);
		$res = $db->query();
		if (!$res) return $res;

		$regsubj = JRequest::getVar('registersubj', '', 'post');
		$regbody = stripslashes($_REQUEST['register_editor']);//JRequest::getVar('register_editor', '', 'post');

		$ordersubj = JRequest::getVar('ordersubj', '', 'post');
		$orderbody = stripslashes($_REQUEST['order_editor']);//JRequest::getVar('order_editor', '', 'post');

		$apprsubj = JRequest::getVar('approvedsubj', '', 'post');
		$apprbody = stripslashes($_REQUEST['approved_editor']);//JRequest::getVar('register_editor', '', 'post');

		$sql = "TRUNCATE TABLE #__digicom_mailtemplates";
		$db->setQuery($sql);
		$db->query();
		$sql = "INSERT INTO #__digicom_mailtemplates (`type`,`subject`,`body`) VALUES
			('register', '".mysql_escape_string($regsubj)."', '".mysql_escape_string($regbody)."'),
			('order', '".mysql_escape_string($ordersubj)."', '".mysql_escape_string($orderbody)."'),
			('approved', '".mysql_escape_string($apprsubj)."', '".mysql_escape_string($apprbody)."')
		";
		$db->setQuery($sql);
		$res = $db->query();
		if (!$res) return $res;

		return true;
	}
}
