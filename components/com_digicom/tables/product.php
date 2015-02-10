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

class TableProduct extends JTable {
	var $id = null;
	var $name = null;
	var $images = null;
	var $price = null;
	var $discount = null;
	var $ordering = null;
	var $file = null;
	var $description = null;
	var $publish_up = null;
	var $publish_down = null;
	var $checked_out = null;
	var $checked_out_time = null;
	var $published = null;
	var $passphrase = null;
	var $main_zip_file = null;
	var $encoding_files = null;
	var $domainrequired = null;
	var $articlelink = null;
	var $articlelinkid = null;
	var $articlelinkuse = null;
	var $shippingtype = null;
	var $shippingvalue0 = null;
	var $shippingvalue1 = null;
	var $shippingvalue2 = null;
	var $productemailsubject = null;
	var $productemail = null;
	var $sendmail = null;
	var $popupwidth = null;
	var $popupheight = null;
	var $stock = null;
	var $used = null;
	var $usestock = null;
	var $emptystockact = null;
	var $showstockleft = null;
	var $fulldescription = null;
	var $metatitle = null;
	var $metakeywords = null;
	var $metadescription = null;
	var $subtitle = null;

	function TableProduct (&$db) {
		parent::__construct('#__digicom_products', 'id', $db);
	}

	function load ($id = 0) {
		parent::load($id);
		$db = JFactory::getDBO();
		$sql = "SELECT catid FROM #__digicom_product_categories WHERE productid='".$this->id."'";
		$db->setQuery($sql);
		$this->selection = $db->loadColumn();

		$where = array();
		$where1[] = " f.published=1 ";
		$sql = "SELECT f.name, f.id, fp.publishing, fp.mandatory, f.options, f.size FROM #__digicom_customfields f LEFT JOIN 
			#__digicom_prodfields fp ON (f.id=fp.fieldid and fp.productid=".$this->id." )"
			.(count ($where1)>0? " WHERE ".implode (" and ", $where1):"");
		$db->setQuery($sql);
		$fields = $db->loadObjectList();
		$this->productfields = $fields;



	}

	function store () {
		$res = parent::store();
		if (!$res) return $res;

		$catid =  JRequest::getVar('catid', array(0), 'post', 'array');
		$catid = intval($catid);
		$db = JFactory::getDBO();
		$sql = "delete from #__digicom_product_categories where productid='".$this->id."'";
		$db->setQuery($sql);
		$res = $db->query();
		if (!$res) return $res;

		$sql = "insert into #__digicom_product_categories(productid, catid) values ";
		foreach ($catid as $id) {
			$sql_tmp[] = " ('".$this->id."', '".$id."' ) ";
		}
		$sql .= implode (",", $sql_tmp).";";
		$db->setQuery($sql);
		$res = $db->query();
		if (!$res) return $res;

		$fields =  JRequest::getVar('fieldid', array(), 'post', 'array');
		$sql = "delete from #__digicom_prodfields where productid='".$this->id."'";
		$db->setQuery($sql);
		$res = $db->query();
		if (!$res) return $res;


		$sqltmp = array();
		$sql = "insert into #__digicom_prodfields(productid, fieldid, publishing, mandatory) values ";
		foreach ($fields as $field) {
			$pub = JRequest::getVar('pub'.$field, 0, 'post');
			$mand = JRequest::getVar('mand'.$field, 0, 'post');
			$sqltmp[] = "('".$this->id."', '".$field."', '".$pub."', '".$mand."')";
		}

		$sql .= implode (",", $sqltmp).";";

		$db->setQuery($sql);
		$res = $db->query();
		if (!$res) return $res;

		$featured =  JRequest::getVar('featuredproducts', '', 'post');
		$featured = explode ("\n", $featured);
		$sql = "delete from #__digicom_featuredproducts where productid='".$this->id."'";
		$db->setQuery($sql);
		$res = $db->query();
		if (!$res) return $res;


		$sqltmp = array();
		$sql = "insert into #__digicom_featuredproducts(productid, featuredid) values ";
		foreach ($featured as $f) {
			$sqltmp[] = "('".$this->id."', '".$f."')";
		}

		$sql .= implode (",", $sqltmp).";";

		$db->setQuery($sql);
		$res = $db->query();
		if (!$res) return $res;
		return true;
	}

};


?>