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

class DigiComAdminModelTaxproductclass extends JModelList{
	protected $_context = 'com_digicom.Taxproductclass';
	var $_pagination = null;
	var $_total = 0;
	var $_pclasses;
	var $_pclass;
	var $_id = null;

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

		$this->_pclass = null;
	}

	protected function getListQuery(){
		$db = JFactory::getDBO();
		$session = JFactory::getSession();
		$c = $this->getInstance( "Config", "DigiComAdminModel" );
		$configs = $c->getConfigs();
		$sql = "select * from #__digicom_tax_productclass";
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

	function getproductClass() {
		if (empty ($this->_pclass)) {
			$this->_pclass = $this->getTable("TaxProductClass");
			$this->_pclass->load($this->_id);
		}
		return $this->_pclass;

	}

	function store () {
		$item = $this->getTable('TaxProductClass');
		$data = JRequest::get('post');
		$conf = $this->getInstance ("config", "DigiComAdminModel");
		$configs = $conf->getConfigs();
		$return = array();

		if (!$item->bind($data)){
			$this->setError($item->getErrorMsg());
			$return["status"] = false;
		}

		if (!$item->check()) {
			$this->setError($item->getErrorMsg());
			$return["status"] = false;
		}

		if (!$item->store()) {
			$return["status"] = false;
		}
		$return["status"] = true;
		$return["id"] = $item->id;
		return $return;
	}

	function delete () {
		$cids = JRequest::getVar('cid', array(0), 'post', 'array');
		$item = $this->getTable('TaxProductClass');
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
		$item = $this->getTable('TaxProductClass');
		$res = 0;
		if ($task == 'publish'){
			$res = 1;
			$sql = "update #__digicom_tax_productclass set published='1' where id in ('".implode("','", $cids)."')";
		} else {
			$res = -1;
			$sql = "update #__digicom_tax_productclass set published='0' where id in ('".implode("','", $cids)."')";

		}
		$db->setQuery($sql);
		if (!$db->query() ){
			$this->setError($db->getErrorMsg());
//		return false;
		}
		return $res;


	}

	function shiftorder($direction = 1) {
		$db = JFactory::getDBO();
		$cids = JRequest::getVar('cid', array(0), 'post', 'array');
		$order = JRequest::getVar('order', array(0), 'post', 'array');
		$sql = "update #__digicom_tax_productclass set `ordering`=ordering".($direction == 1?"+1":"-1")." where id=".$cids[0];
		$db->setQuery($sql);
		$res = $db->query();
		if (!$res) return $res;
		return true;
	}

	function reorder () {
		$db = JFactory::getDBO();
		$cids = JRequest::getVar('cid', array(0), 'post', 'array');
		$order = JRequest::getVar('order', array(0), 'post', 'array');
		foreach ($cids as $i => $v) {
			$sql = "update #__digicom_tax_productclass set `ordering`='".$order[$i]."' where id=".$v;
			$db->setQuery($sql);
			$res = $db->query();
			if (!$res) return $res;
		}
		return true;
	}



};
?>