<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 376 $
 * @lastmodified	$LastChangedDate: 2013-10-21 11:54:05 +0200 (Mon, 21 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license


*/

defined ('_JEXEC') or die ("Go away.");

jimport ("joomla.aplication.component.model");

class DigiComModelProduct extends DigiComModel
{

	var $_products;
	var $_product;
	var $_id = null;
	var $_total = 0;
	var $_pagination = null;

	function __construct () {
		parent::__construct();
		$cids = JRequest::getVar('cid', 0, '', 'array');
		$this->setId((int)$cids[0]);
	}


	function setId($id) {
		$this->_id = $id;
		$this->_product = null;
	}

	function getListProducts(){
		$my = JFactory::getUser();
		if(empty ($this->_products)){
			$user = JFactory::getUser();
			$where[] = " published = 1 ";
			$where[] = " hide_public = 0 ";

			$sql = "select * from #__digicom_products".
				(count($where) > 0 ? " where ": "").
				implode (" and ", $where);

			$order = " order by ordering asc  ";

			$this->_total = @$this->_getListCount( $sql . $order );

			
			$this->_products = $this->_getList( $sql . $order, $this->getState('limitstart'), $this->getState('limit'));
			$this->_pagination = new JPagination($this->_total, $this->getState('limitstart'), $this->getState('limit')); 

			if (count($this->_products) > 0)
			foreach ($this->_products as $i => $v) {
				if ($v->usestock) {
					if ($v->used >= $v->stock) {

						if ($v->emptystockact == 1){
						}
						elseif($v->emptystockact == 2){
							unset($this->_products[$i]);
						}
					}

				}

			}

		}
		$this->_addProductTax();
		return $this->_products;
	}

	function getProduct($id = 0){
		$my = JFactory::getUser();
		if(empty($this->_product)){
			$this->_product = $this->getTable("Product");
			if ($id) $this->_id = $id;
			$this->_product->load($this->_id);
		}
		
		$this->_products = $this->_product;
		
		return $this->_product;
	}

	function getPlansForProduct($id = 0) {
		$db = JFactory::getDBO();
	   	$id = intval($id);
		$sql = "SELECT p.id, p.name, r.price
				FROM `#__digicom_products_plans` AS r
				LEFT JOIN `#__digicom_plans` AS p ON r.`plan_id` = p.id
				WHERE r.`product_id` ={$id}
				AND p.published =1 ";
		$db->setQuery($sql);
		$res = $db->loadObjectList();
		return $res;
	}

	function getCategoryProducts($catid, &$totalprods)
	{
		$catids = $this->getCatIds($catid);
		$this->_products = null;
		$my = JFactory::getUser();

		$date_today = time();

		$where[] = " published=1 ";
		$where[] = " hide_public=0 ";
		$where[] = " (publish_up <= ".$date_today.") and (publish_down = 0 OR publish_down >= ".$date_today.") ";
		
		$configs =  $this->getInstance("Config", "digicomModel");
		$configs = $configs->getConfigs();
		$showfeatured_prod = $configs->get('showfeatured_prod',1);

		$sql = "select id from #__digicom_products where catid IN (".$catids.") " . (count($where) > 0 ? " and ": "") . implode (" and ", $where);
		$this->_total = $this->_getListCount($sql);
		$totalprods = $this->_total;
		$orderby = JRequest::getVar('orderby', 'default' );

		switch ( $orderby ) {
			case 'id' :
				$order_field = " id asc ";
				break;

			case 'latest' :
				$order_field = " id desc ";
				break;

			case 'name' :
				$order_field = " name asc ";
				break;

			case 'default' :
			default:
				$order_field = " ordering asc ";
				break;
		}
		
		$order = " order by " . $order_field;
		$site_config = JFactory::getConfig();
		$limit		= JRequest::getVar('limit', intval($site_config->get('list_limit',20)), '', 'int');
		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');
		$pids = array();
		
		$limit = $configs->get('prodlayoutrow',3)*$configs->get('prodlayoutcol',3);
		$pids = $this->_getList($sql.$order , $limitstart, $limit);
		
		if(isset($pids) && count($pids) > 0){
			foreach ($pids as $pid) {
				$this->_products[$pid->id] = $this->getAttributes($pid->id);
			}
		}
		
		
		// $pagination = new JPagination($totalprods,$limitstart,$limit);
			
		$return = array('items' => $this->_products, 'total' => $totalprods, 'limit' => $limit,  'limitstart' => $limitstart);
		return $return;
	}

	function getAttributes($pid) {
		$db = JFactory::getDBO();

		if ($pid < 1) return null;
		$this->_id = $pid;
		$this->_product = $this->getTable("Product");
		$this->_product->load($this->_id);
		return $this->_product;
	}

	function _addProductTax() {
		$configs =  $this->getInstance("Config", "digicomModel");
		$configs = $configs->getConfigs();
		if (count($this->_products) > 0) {
			foreach ($this->_products as $i => $v) {
				$item = &$this->_products[$i];
				$item->quantity = 1;
					$price = $item->price;
					$item->percent_discount = "N/A";
				$item->currency = $configs->get('currency','USD');

					$item->subtotal = $price * ($item->quantity);
					$item->price = DigiComHelper::format_price($item->price, $item->currency, false, $configs);//sprintf( $price_format, $item->product_price );
					$item->price_formated = DigiComHelper::format_price2($item->price, $item->currency, false, $configs);
					$item->subtotal = DigiComHelper::format_price($item->subtotal, $item->currency, false, $configs);//sprintf( $price_format, $item->subtotal );
					$item->subtotal_formated = DigiComHelper::format_price2($item->subtotal, $item->currency, false, $configs);
			}

		}
		$taxmodel = $this->getInstance("Tax", "digicomModel");

		$customer = new DigiComSessionHelper();
		$taxmodel->getTax($tax, $this->_products, $configs, $customer->_customer);

		if ($configs->get('product_price',1) == 1) {
			if ($configs->get('tax_catalog',0) == 0)
				if (isset($this->_products)) foreach ($this->_products as $i => $v)  $this->_products[$i]->price += $v->itemtax;
		} else 	if ($configs->get('product_price',1) == 0) {
			if ($configs->get('tax_catalog',0) == 1)
				foreach ($this->_products as $i => $v)  $this->_products[$i]->price -= $v->itemtax;
		}
	}

	function getCategory(){
		$db = JFactory::getDBO();
		$id = JFactory::getApplication()->input->get('cid',0);

		$sql = "select * from #__digicom_categories where id=".intval($id);
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadObject();
		return $result;
	}
	
	function getCatIds($catid){
		if($catid == 0){
			$db = JFactory::getDBO();
			$query = 'SELECT GROUP_CONCAT(id) as ids FROM `#__digicom_categories` WHERE `published` = 1 ORDER BY `ordering`';
			$db->setQuery($query);
			$catids = $db->loadObject();
			return $catids->ids;
		}else{
			$catidsArray = $this->getSubCategoriesId($catid);
			$catids = implode(',',$catidsArray);
			return (empty($catids) ? $catid : ($catid.','.$catids));
		}
	}
	
	public function getSubCategoriesId($catid){
		$db = JFactory::getDBO();
		$query = 'SELECT `id`, `parent_id` AS `parent`, `parent_id`, `title`, `title` as `name` FROM `#__digicom_categories` WHERE `published` = 1 ORDER BY `ordering`';
		$db->setQuery($query);
		$mitems = $db->loadObjectList();
		
		$children = array();
		if ($mitems)
		{
			foreach ($mitems as $v)
			{
				$v->title 		= $v->name;
				$v->parent_id 	= $v->parent;
				$pt = $v->parent;
				$list = @$children[$pt] ? $children[$pt] : array();
				array_push($list, $v);
				$children[$pt] = $list;
			}
		}
		$list = JHTML::_('menu.treerecurse', $catid, '', array(), $children, 9999, 0, 0);
		$subids = array();
		foreach ($list as $item){
			$subids[] = ($item->id);
		}
		return $subids;
	}

}
