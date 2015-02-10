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

class DigiComAdminModelEmailreminder extends JModelList {

	protected $_context = 'com_digicom.Emailreminder';
	var $_emailreminders;
	var $_emailreminder;
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
		$this->_emailreminder = null;
	}

	protected function getListQuery(){
		$db = JFactory::getDBO();
		$session = JFactory::getSession();
		$c = $this->getInstance("Config", "DigiComAdminModel");
		$configs = $c->getConfigs();
		$sql = "select p.* from #__digicom_emailreminders p "." order by p.ordering";
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

	/*function getListEmailreminders () {
		if (empty ($this->_emailreminders)) {

			$c = $this->getInstance("Config", "DigiComAdminModel");
			$configs = $c->getConfigs();

			$db = JFactory::getDBO();
			
			$sql = "select p.* from #__digicom_emailreminders p "
//				." where u.id=o.userid and c.id=u.id "
				." order by p.ordering"
				;
//echo $sql;
//			die;
			$this->_total = $this->_getListCount($sql);
			if ($this->getState('limitstart') > $this->_total) $this->setState('limitstart', 0);
			if ($this->getState('limitstart') > 0 & $this->getState('limit') == 0)  $this->setState('limitstart', 0);

			$this->_emailreminders = $this->_getList($sql, $this->getState('limitstart'), $this->getState('limit'));

		}

		return $this->_emailreminders;

	}*/

	function getEmailreminder($id = 0) {

		if (empty ($this->_emailreminder)) {

			$db = JFactory::getDBO();
			if ($id > 0) $this->_id = $id;
			else $id = $this->_id;

			$sql = "select p.* from #__digicom_emailreminders p where p.id=".$id;
			$this->_emailreminder = $this->_getList($sql); // ->load($this->_id); //

			if ( $this->_emailreminder ){
				$this->_emailreminder = $this->_emailreminder[0];
			} else {
				$this->_emailreminder = new stdClass();
				$this->_emailreminder->id = 0;
				$this->_emailreminder->name = "";
				$this->_emailreminder->type = 0;
				$this->_emailreminder->subject = "";
				$this->_emailreminder->body = "";
				$this->_emailreminder->ordering = 0;
				$this->_emailreminder->published = 1;
				$this->_emailreminder->period = 'day';
				$this->_emailreminder->calc = 'after';
				$this->_emailreminder->date_calc = 'expiration';
			}
		}
		return $this->_emailreminder;
		
	}

	function store () {

		$item = $this->getTable('Emailreminder');
		$data = JRequest::get('post');

		$data['body'] = JRequest::getVar('body', '', 'post', 'string', JREQUEST_ALLOWRAW);

		if (!$item->bind($data)){
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		if (!$item->check()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		if (!$item->store()) {
  			$this->setError($item->_db->getErrorMsg());
			return false;
		}

		$item->reorder();
		
		return true;
	}

	function storeSelectedEmailreminders($product_id, $plains) {
		$db = JFactory::getDBO();
		$db->setQuery("DELETE FROM #__digicom_products_emailreminders WHERE product_id = " . $product_id);
		$db->query();

		foreach($plains as $key => $plain) {
			$sql = "INSERT INTO #__digicom_products_emailreminders(product_id, emailreminder_id) VALUES('".$product_id."', '".$plains[$key]."');";
			$db->setQuery($sql);
			$db->query();
		}
	}


	function delete () {
		$cids = JRequest::getVar('cid', array(0), 'post', 'array');
		$item = $this->getTable('Emailreminder');
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
		$item = $this->getTable('Emailreminder');
		$return = 0;
		if ($task == 'publish'){
			$sql = "update #__digicom_emailreminders set published='1' where id in ('".implode("','", $cids)."')";
			$return = 1;
		} else {
			$sql = "update #__digicom_emailreminders set published='0' where id in ('".implode("','", $cids)."')";
			$return = -1;
		}
		$db->setQuery($sql);
		if (!$db->query()){
			$this->setError($db->getErrorMsg());
			return 0;
		}
		return $return;
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

	function duplicate($id) {
		$row = $this->getTable();
		$row->load( $id );
		$row->id = 0;
		$row->name = $row->name . " - duplicate";
		if (!$row->store()) {
			JError::raiseError(500, $db->getErrorMsg());
		}
		$row->reorder();
	}

	function getConfigs() {
		$comInfo = JComponentHelper::getComponent('com_digicom');
		return $comInfo->params;
	}

}

?>