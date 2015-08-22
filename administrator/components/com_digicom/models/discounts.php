<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class DigiComModelDiscounts extends JModelList {

	protected $_context = 'com_digicom.discounts';
	protected $_valid_promos;
	protected $_promos;
	protected $_promo;
	protected $_id = null;
	protected $_pagination = null;

	public function __construct () {
		parent::__construct();
		$cids = JRequest::getVar('cid', 0, '', 'array');
		$this->setId((int)$cids[0]);
	}

	protected function populateState($ordering = NULL, $direction = NULL){
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

	function setId($id) {
		$this->_id = $id;
		$this->_promo = null;
	}

	protected function getListQuery()
	{
		$promosearch = JRequest::getVar("promosearch", "");
		$condition = JRequest::getVar("condition", '-1');
		$status = JRequest::getVar("status", '');
		$where = array();

		if(trim($promosearch) != "")
		{
			$where[] = " (title like '%".trim($promosearch)."%' or code like '%".trim($promosearch)."%') ";
		}

		if(trim($status) != "")
		{
			$where[] = "(published='$status')";
		}

		if($condition == 0) // expired
		{
			$where[] = "( `codestart` >= now() ) OR ( `codeend` <= now() ) OR ( `codelimit` = `used` AND `codelimit` > 0 )";
		}
		elseif ($condition == 1) // active
		{
			$where[] = "( `codestart` <= now()  AND  `codeend` >= now() ) AND ( codelimit=0 OR codelimit>used )";
		}

		$sql = "select *
				from #__digicom_promocodes " . (count($where) ? 'WHERE ' . implode(' AND ', $where) : '') . "
				order by id desc";

		return $sql;
	}

	function getItems(){
		$config = JFactory::getConfig();
		$app = JFactory::getApplication('administrator');
		$limistart = $app->getUserStateFromRequest($this->context.'.list.start', 'limitstart');
		$limit = $app->getUserStateFromRequest($this->context.'.list.limit', 'limit', $config->get('list_limit'));
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query = $this->getListQuery();

		$db->setQuery($query);
		$db->query();
		$result	= $db->loadObjectList();
		$this->total=count($result);
		$db->setQuery($query, $limistart, $limit);
		$db->query();
		$result	= $db->loadObjectList();
		return $result;
	}

	function getlistPromosValid_x () {
		if (empty ($this->_valid_promos)) {

			$sql = "select * from #__digicom_promocodes order by id desc";
			$this->_db->setQuery( $sql );
			$promos = $this->_db->loadObjectList();

			$nullDate = 0;

			$promos_valid = array();

			foreach($promos as $promo) {

				$published = $promo->published;
				$timestart = $promo->codestart;
				$timeend = $promo->codeend;
				$limit = $promo->codelimit;
				$used = $promo->used;
				$now = time();

				$promo_status = true;

				if ( $now <= $timestart && $published == "1") {
					$promo_status = true;
				} else if ($limit > 0 && $used >= $limit) {
					$promo_status = true;
				} else if ( ( $now <= $timeend || $timeend == $nullDate ) && $published == "1" ) {
					$promo_status = true;
				} else if ( $now > $timeend && $published == "1" && $timeend != $nullDate) {
					$promo_status = true;
				} elseif ( $published == "0" ) {
					$promo_status = false;
				} else {
					$promo_status = false;
				}

				if ($promo_status)
					$this->_valid_promos[] = $promo;
			}
		}

		return $this->_valid_promos;
	}

	function getPromo()
	{
		if (empty ($this->_promo))
		{
			$this->_promo = $this->getTable("Discount");
			$this->_promo->load($this->_id);
		}

		return $this->_promo;
	}

	function getPromoOrders()
	{
		$db = JFactory::getDBO();

		if (($this->_promo))
		{
			// Get previous orders restrictions
			$sql = "SELECT p.`name`, o.`productid`
					FROM `#__digicom_promocodes_orders` AS o
						 INNER JOIN `#__digicom_products` AS p ON p.`id`=o.`productid`
					WHERE o.`promoid`=" . (int) $this->_id . "
					ORDER BY p.`name`";
			$db->setQuery($sql);
			$promo_orders = $db->loadObjectList();

			return $promo_orders;
		}
	}

	function getPromoProducts()
	{
		$db = JFactory::getDBO();

		if (($this->_promo))
		{
			// Get previous orders restrictions
			$sql = "SELECT p.`name`, o.`productid`
					FROM `#__digicom_promocodes_products` AS o
						 INNER JOIN `#__digicom_products` AS p ON p.`id`=o.`productid`
					WHERE o.`promoid`=" . (int) $this->_id . "
					ORDER BY p.`name`";
			$db->setQuery($sql);
			$promo_products = $db->loadObjectList();

			return $promo_products;
		}
	}

	
	function delete () {
		$cids = JRequest::getVar('cid', array(0), 'post', 'array');
		$item = $this->getTable('Discount');
		foreach ($cids as $cid) {
			if (!$item->delete($cid)) {
				$this->setError($item->getErrorMsg());
				return false;
			}
		}
		return true;
	}

	function getConfigs() {
		$comInfo = JComponentHelper::getComponent('com_digicom');
		return $comInfo->params;
	}

}

?>
