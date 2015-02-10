<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 441 $
 * @lastmodified	$LastChangedDate: 2013-11-20 04:59:31 +0100 (Wed, 20 Nov 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license


*/

defined ('_JEXEC') or die ("Go away.");

jimport ("joomla.aplication.component.model");


class DigiComModelOrder extends DigiComModel
{
	var $_licenses;
	var $_license;
	var $_id = null;

	function __construct () {
		parent::__construct();
		$cids = JRequest::getVar('orderid', 0, '', 'array');
		$this->setId((int)$cids[0]);
	}

	function setId($id) {
		$this->_id = $id;
		$this->_order = null;
	}

	function getlistOrders(){
		$user = new DigiComSessionHelper();
		$db = JFactory::getDBO();

		if (empty ($this->_orders)) {
			$sql = "select l.*, o.order_date as date, p.name as productname, p.main_zip_file, p.domainrequired, u.username, p.hide_public "
					." from #__digicom_licenses l, #__digicom_products p, #__users u, #__digicom_orders o "
					." where l.productid=p.id and l.userid=u.id and l.userid=".$user->_user->id
					." and l.published=1 and l.orderid=o.id";

			$search = JRequest::getVar("search", "");
			if(trim($search) != "" && intval(trim($search)) != "0"){
				$and .= " and l.licenseid like '%".trim($search)."%' ";
			}

			$licid = JRequest::getVar("licid", "");
			$and = "";
			$sql = "";
			if(trim($licid) == ""){
				$sql = "select o.*, count(l.id) as lcount, u.username "
					." from #__digicom_licenses l, #__digicom_orders o, #__users u "
					." where l.orderid=o.id and o.userid=u.id and u.id=".$user->_user->id.$and." group by o.id order by o.order_date desc";
			}
			else{
				$sql = "select `orderid`, `old_orders`
						from #__digicom_licenses
						where `licenseid` = ".trim($licid);
				$db->setQuery($sql);
				$db->query();
				$oll_ord_ids = $db->loadAssocList();
				$ids_ord = array("0");
				if(isset($oll_ord_ids) && trim($oll_ord_ids["0"]["orderid"]) != ""){
					$ids_ord[] = $oll_ord_ids["0"]["orderid"];
				}

				if(isset($oll_ord_ids["0"]["old_orders"])){
					$temp_array = explode("|", $oll_ord_ids["0"]["old_orders"]);
					if(isset($temp_array) && count($temp_array) > 0){
						foreach($temp_array as $key=>$id){
							if(trim($id) != ""){
								$ids_ord[] = trim($id);
							}
						}
					}
				}

				$sql = "SELECT o.*, count(l.id) as lcount, u.username "
					." FROM #__digicom_licenses l, #__digicom_orders o, #__users u "
					." WHERE o.id in (".implode(",", $ids_ord).") and o.userid=u.id and u.id=".$user->_user->id.$and." group by o.id order by o.order_date desc";
			}
			$this->_orders = $this->_getList($sql);

			foreach ($this->_orders as $i => $v) {
				$and = "";
				if(trim($search) != "" && intval(trim($search)) == "0"){
					$and .= " and dp.name like '%".trim($search)."%' ";
				}

				$licid = JRequest::getVar("licid", "");
				$sql = "";
				$sql = "SELECT dp.id, dp.name, dl.licenseid, dp.hide_public
						FROM #__digicom_products dp
						INNER JOIN #__digicom_licenses dl ON ( dl.productid = dp.id )
						WHERE (dl.orderid='".$v->id."' OR (dl.old_orders like '".$v->id."|%' or dl.old_orders like '%|".$v->id."|%')) and dl.ltype <> 'package_item' ".$and;
				$db->setQuery($sql);
				$sql_result = $db->loadObjectList();

				//add packages to order
				$sql = "SELECT dp.id, dp.name, GROUP_CONCAT(dl.licenseid) as licenseid, dp.hide_public
						FROM #__digicom_products dp
						INNER JOIN #__digicom_licenses dl ON (dl.package_id = dp.id)
						WHERE (dl.orderid='".$v->id."' or dl.old_orders like '".$v->id."|%' or dl.old_orders like '%|".$v->id."|%') ".$and." group by dp.id";
				$db->setQuery($sql);
				$sql_result2 = $db->loadObjectList();
				$sql_result = array_merge($sql_result, $sql_result2);
				//add packages to order

				if(is_array($sql_result) && count($sql_result) > 0){
					$this->_orders[$i]->products = $sql_result;
				}
				else{
					unset($this->_orders[$i]);
					continue;
				}

				if(is_array($this->_orders[$i]->products)){
					foreach ($this->_orders[$i]->products as $j => $p) {
						$sql = "SELECT catid FROM #__digicom_product_categories WHERE productid=".$p->id;
						$db->setQuery($sql);
						$this->_orders[$i]->products[$j]->catid = $db->loadResult();
					}
				}
			}
		}
		return $this->_orders;
	}

	function getOrder($id = 0) {
		if (empty ($this->_order)) {
			$db = JFactory::getDBO();
			if ($id > 0) $this->_id = $id;
			else $id = $this->_id;
			$sql = "SELECT o.*, COUNT(l.id) as lcount, u.username "
					." FROM #__digicom_licenses l, #__digicom_orders o, #__users u "
					." WHERE l.orderid=o.id AND o.userid=u.id AND o.status='Active' AND o.id='".intval($id)."' group by o.id order by o.order_date desc"
			;
			$this->_order = $this->_getList($sql);//->load($this->_id);
			$this->_order = $this->_order[0];

			/*$sql = "select p.*, l.amount_paid as price, l.licenseid, l.id as lid, l.amount_paid from #__digicom_products p, #__digicom_licenses l "
					." where p.id=l.productid and l.orderid='".intval($id)."'";*/

			$sql = "SELECT dp.*, dl.amount_paid AS price, dl.licenseid AS lid, dl.amount_paid
						FROM #__digicom_products dp
						INNER JOIN #__digicom_licenses dl ON ( dl.productid = dp.id )
						WHERE (dl.orderid='".intval($id)."' OR (dl.old_orders like '".intval($id)."|%' or dl.old_orders like '%|".intval($id)."|%')) and dl.ltype <> 'package_item' ";
			$db->setQuery($sql);
			$prods = $db->loadObjectList();

			//add packages to order
			$sql = "SELECT p.* FROM #__digicom_products p WHERE id IN (SELECT package_id FROM `#__digicom_licenses` WHERE `orderid`=".$id." and `ltype`='package_item' group by package_id)";
			$db->setQuery($sql);
			$prods2 = $db->loadObjectList();
			if(isset($prods2)){
				foreach($prods2 as $key=>$product){
					$sql = "SELECT p.price AS total FROM #__digicom_licenses l, #__digicom_products_plans p where l.`orderid`=".intval($id)." and l.package_id=".intval($product->id)." and l.package_id=p.product_id";
					$db->setQuery($sql);
					$db->query();
					$result = $db->loadAssocList();
					if(isset($result)){
						$prods2[$key]->amount_paid = $result["0"]["total"];
						$prods2[$key]->price = $result["0"]["total"];
					}
				}
			}

			$prods = array_merge($prods, $prods2);
			//add packages to order

			$distinct = array();
			$prods1 = array();
			foreach ($prods as $i => $v) {
				if (!in_array($v->id, $distinct)) {
					$distinct[] = $v->id;
					$prods1[$v->id] = $v;
					$prods1[$v->id]->count = 1;
				} else {
					$prods1[$v->id]->count++;

				}
			}

			foreach($prods as $i => $v){
				$sql = "SELECT * FROM #__digicom_licensefields WHERE licenseid='".$v->lid."'";
				$db->setQuery($sql);
				$prods[$i]->orig_fields = $db->loadObjectList();
			}

			$this->_order->products = $prods;

		}
		return $this->_order;
	}

}

