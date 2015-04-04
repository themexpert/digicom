<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

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