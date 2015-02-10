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

class DigiComAdminModelPlain extends JModelList {

	protected $_context = 'com_digicom.Plain';
	var $_plains;
	var $_plain;
	var $_id = null;
	var $_total = 0;
	var $_pagination = null;

	function __construct () {
		parent::__construct();
		$cids = JRequest::getVar('cid', 0, '', 'array');
		$this->setId((int)$cids[0]);
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

	function setId($id) {
		$this->_id = $id;
		$this->_plain = null;
	}


	function getPlanitemNewSelect() {
		$product_id = JRequest::getVar('pid','0');
		$sql = "SELECT * FROM #__digicom_plans pl inner join #__digicom_products_plans pp on (pl.id = pp.plan_id) where pp.product_id = ".$product_id;
		$this->_db->setQuery($sql);
		$plains = $this->_db->loadObjectlist();

		return $plains;
	}

	function getPlanitemNew( $product_id, $plan_id ) {

		$sql = "SELECT * FROM #__digicom_plans pl
				inner join #__digicom_products_plans pp on (pl.id = pp.plan_id)
				where pp.product_id = ".$product_id." and pl.id = ".$plan_id;
		$this->_db->setQuery($sql);
		$plains = $this->_db->loadObject();

		return $plains;
	}

	function getPlanitemRenewalSelect() {

		$product_id = JRequest::getVar('pid','0');
		$sql = "SELECT * FROM #__digicom_plans pl 
			inner join #__digicom_products_renewals pp on (pl.id = pp.plan_id)
			where pp.product_id = ".$product_id;
		$this->_db->setQuery($sql);
		$plains = $this->_db->loadObjectlist();

		return $plains;
	}


	function getPlanitemRenewal( $product_id, $plan_id ) {

		$sql = "SELECT * FROM #__digicom_plans pl
				inner join #__digicom_products_renewals pp on (pl.id = pp.plan_id)
				where pp.product_id = ".$product_id." and pl.id = ".$plan_id;
		$this->_db->setQuery($sql);
		$plains = $this->_db->loadObject();

		return $plains;
	}

	function getlistPlainsForPack(){
		return $this->getlistPlains(true);
	}

	protected function getListQuery($pack = false){
		$db = JFactory::getDBO();
		$session = JFactory::getSession();
		$c = $this->getInstance("Config", "DigiComAdminModel");
		$configs = $c->getConfigs();
		$sql = "";

		if($pack){
			$sql = "SELECT p.* FROM #__digicom_plans p 
					WHERE p.duration_count = '-1' AND p.duration_type = '0'
					ORDER BY p.ordering ";
		}
		else{
			$sql = "SELECT p.* FROM #__digicom_plans p "
					." ORDER BY p.ordering"; 
		}
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

	/*function getlistPlains ($pack = false) {
				
		if (empty ($this->_plains)) {

			$c = $this->getInstance("Config", "DigiComAdminModel");
			$configs = $c->getConfigs();

			$db = JFactory::getDBO();
			
			if ($pack) { 
				$sql = "SELECT p.* FROM #__digicom_plans p 
					WHERE p.duration_count = '-1' AND p.duration_type = '0'
					ORDER BY p.ordering ";
					//echo $sql; die();
			} else {
				$sql = "SELECT p.* FROM #__digicom_plans p "
					//." where u.id=o.userid and c.id=u.id "
					." ORDER BY p.ordering"
					;
					//echo $sql;			
			}
			
			$this->_total = $this->_getListCount($sql);
			if ($this->getState('limitstart') > $this->_total) $this->setState('limitstart', 0);
			if ($this->getState('limitstart') > 0 & $this->getState('limit') == 0)  $this->setState('limitstart', 0);

			$this->_plains = $this->_getList($sql, $this->getState('limitstart'), $this->getState('limit'));

		}

		return $this->_plains;
	}*/

	function getPlain($id = 0) {

		if (empty ($this->_plain)) {

			$db = JFactory::getDBO();
			if ($id > 0) $this->_id = $id;
			else $id = $this->_id;

			$sql = "select p.* from #__digicom_plans p where p.id=".$id;
			$this->_plain = $this->_getList($sql); // ->load($this->_id); //

			if ($this->_plain) {
				$this->_plain = $this->_plain[0];
			} else {
			
				$this->_plain = new stdClass();
				$this->_plain->id = 0;
				$this->_plain->name = "";
				$this->_plain->duration_count = 0;
				$this->_plain->duration_type = 0;
				$this->_plain->ordering = 0;
				$this->_plain->published = 1;
			}
		}

		return $this->_plain;		
	}

	function store () {

		$item = $this->getTable('Plain');

		$data = JRequest::get('post');
		if (!$item->bind($data)){
			$this->setError($item->getErrorMsg());
			return false;
		}

		if (!$item->check()) {
			$this->setError($item->getErrorMsg());
			return false;
		}

		if (!$item->store()) {
			$this->setError($item->getErrorMsg());
			return false;
		}

		$item->reorder();
		
		return true;
	}

	function listPlainsToProduct($product_id) {
		echo $product_id;
		//die;
	}

	function storeSelectedPlains($product_id, $plains, $prices, $default) {
		$db = JFactory::getDBO();
		$db->setQuery("DELETE FROM #__digicom_products_plans WHERE product_id = " . $product_id);
		$db->query();

		foreach($plains as $key => $plain){
			if($plains[$key] == $default){
				$set_default = 1;
			}
			else{
				$set_default = 0;
			}

			$sql = "INSERT INTO #__digicom_products_plans(`product_id`, `plan_id`, `price`, `default`) "
				."VALUES('".$product_id."', '".$plains[$key]."', '".$prices[$plain]."', '".$set_default."');";

			$db->setQuery($sql);
			$db->query();
		}
	}

	function storeSelectedRenewals($product_id, $plains, $prices, $default) {

		$db = JFactory::getDBO();
		$db->setQuery("DELETE FROM #__digicom_products_renewals WHERE product_id = " . $product_id);
		$db->query();

		foreach($plains as $key => $plain) {

			if ($plains[$key] == $default) 
				$set_default = 1;
			else
				$set_default = 0;

			$sql = "INSERT INTO #__digicom_products_renewals(`product_id`, `plan_id`, `price`, `default`) "
				."VALUES('".$product_id."', '".$plains[$key]."', '".$prices[$plain]."', '".$set_default."');";

			$db->setQuery($sql);
			$db->query();
		}
	}

	function delete () {
		$cids = JRequest::getVar('cid', array(0), 'post', 'array');
		$item = $this->getTable('Plain');
		foreach ($cids as $cid) {
			if (!$item->delete($cid)) {
				$this->setError($item->getErrorMsg());
				return false;

			}
		}

		return true;
	}


	function publish () {
		$db = JFactory::getDBO();
		$cids = JRequest::getVar('cid', array(0), 'post', 'array');
		$task = JRequest::getVar('task', '', 'post');
		$item = $this->getTable('Plain');
		if ($task == 'publish'){
			$sql = "update #__digicom_plans set published='1' where id in ('".implode("','", $cids)."')";
		} else {
			$sql = "update #__digicom_plans set published='0' where id in ('".implode("','", $cids)."')";

		}
		$db->setQuery($sql);
		if (!$db->query()){
			$this->setError($db->getErrorMsg());
			return false;
		}
	}


/* NEW function */

	function orderField( $uid, $inc )
	{
		// Initialize variables
		//$db		= JFactory::getDBO();
		$row	= $this->getTable();
		$row->load( $uid );
		$row->move( $inc ); // , '`group` = '.$db->Quote($row->group)
		$row->reorder();

		return true;
	}


	function saveorder() {

		// Initialize variables
		$db			= JFactory::getDBO();

		$cid		= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$order		= JRequest::getVar( 'order', array (0), 'post', 'array' );
		$total		= count($cid);
		$conditions	= array();

		JArrayHelper::toInteger($cid, array(0));
		JArrayHelper::toInteger($order, array(0));

		$row = $this->getTable();
		// update ordering values
		for( $i=0; $i < $total; $i++ ) {
			$row->load( (int) $cid[$i] );
			// track sections
			//$groupings[] = $row->group;
			if ($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				if (!$row->store()) {
					JError::raiseError(500, $db->getErrorMsg());
				}
			}
		}

		$row->reorder();
		return true;
	}

	function getConfigs() {
		$comInfo = JComponentHelper::getComponent('com_digicom');
		return $comInfo->params;
	}
/* /END NEW function */

}


?>