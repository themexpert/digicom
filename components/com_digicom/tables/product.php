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

	function TableProduct (&$db) {
		parent::__construct('#__digicom_products', 'id', $db);
	}

	function load ($id = 0) {
		parent::load($id);
		
		/*$db = JFactory::getDBO();
		$sql = "SELECT catid FROM #__digicom_product_categories WHERE productid='".$this->id."'";
		$db->setQuery($sql);
		$this->selection = $db->loadColumn();
		*/
		$this->selection = $this->catid;
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

}