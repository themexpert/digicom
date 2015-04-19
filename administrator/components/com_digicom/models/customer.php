<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class DigiComModelCustomer extends JModelAdmin {

	protected $_context = 'com_digicom.customer';   
	protected $_customers;
	protected $_customer;
	protected $_id = null;
	protected $_total = 0;
	protected $_pagination = null;

	public function __construct () {
		parent::__construct();
		$cids = JRequest::getVar('id', 0);
		$this->setId((int)$cids);
	}

	function setId($id) {
		$this->_id = $id;
		$this->_customer = null;
	}

	public function getCustomer() {

		if (empty($this->_customer)) {
			$this->_customer = $this->getTable("Customer");
			$this->_customer->load($this->_id);
		}
		//print_r($this->_customer);die;
		if($this->getUser()){
			$user = JFactory::getUser($this->_id);
			if (!isset($this->_customer->registered)) $this->_customer->registered = $user->registerDate;
		}
		
		
		$this->_customer->orders = $this->getlistCustomerOrders($this->_id);

		return $this->_customer;
	}

	public function getCustomerByID($id) {
		$customer = $this->getTable("Customer");
		$customer->load($id);

		return $customer;
	}

	public function getCustomerIDbyUserName($username) {
		$this->_db->setQuery("SELECT dc.id FROM #__digicom_customers dc inner join #__users u on(u.id = dc.id) WHERE u.username = '".$username."'");
		$id = $this->_db->loadResult();
		return $id;
	}

	public function getUserByName($username) {
		$this->_db->setQuery("SELECT * FROM #__users u WHERE u.username = '".$username."'");
		$user = $this->_db->loadObject();
		return $user;
	}

	public function getUserByID($id) {
		$this->_db->setQuery("SELECT * FROM #__users u WHERE u.id = '".$id."'");
		$user = $this->_db->loadObject();
		return $user;
	}

	function store (&$error){
		
		jimport("joomla.database.table.user");
		
		$db = JFactory::getDBO();
		$user = new JUser();
		$my = new stdClass;
		$item = $this->getTable('Customer');
		$id = JRequest::getVar("id", "0");
		
		if($id != "0"){
			$data = JRequest::get('post');
			//$data['password2'] = $data['password_confirm'];
			//$data['name'] = $data['firstname'];
			$data['groups']= array(2);
			$data['block'] = 0;
			$user->bind($data);
			$user->gid = 18;
			$res = true;
			$my->id = $data['id'];

			if(!$my->id){
				if(!$user->save()){
					$error = $user->getError();
					$res = false;
				}
			}
			else{
				$user->id = $my->id;
			}
		}

		if(intval($id) == "0"){
			$sql = 'SELECT id FROM #__users ORDER BY id DESC LIMIT 1';
			$db->setQuery($sql);
			$data['id'] = intval($db->loadResult());
		}

		if (!$item->bind($data)) {
			$res = false;
		}

		if (!$item->check()) {
			$res = false;
		}
		
		if (!$item->store()) {
			$res = false;
		}
		//echo $res;die;

		$this->setId($item->id);
		$this->getCustomer();
		
		return $res;
	}
	/*
	function delete () {
		
		$cids = JRequest::getVar('cid', array(0), 'post', 'array');
		$item = $this->getTable('Customer');
		foreach ($cids as $cid) {
			if (!$item->delete($cid)) {
				$this->setError($item->getErrorMsg());
				return false;

			}
		}
		
		jimport("joomla.database.table.user");
		$db = JFactory::getDBO();
		$user = new JUser();

		foreach ($cids as $cid) {
			if (!$user->delete($cid)) {
				$this->setError($item->getErrorMsg());
				return false;

			}
		}

		return true;
	}

	*/
	/*
	function publish () {
		$db = JFactory::getDBO();
		$cids = JRequest::getVar('cid', array(0), 'post', 'array');
		$task = JRequest::getVar('task', '', 'post');
		$item = $this->getTable('Customer');
		if ($task == 'publish') {
			$sql = "update #__digicom_categories set published='1' where id in ('".implode("','", $cids)."')";
		} else {
			$sql = "update #__digicom_categories set published='0' where id in ('".implode("','", $cids)."')";

		}
		$db->setQuery($sql);
		if (!$db->query() ) {
			$this->setError($db->getErrorMsg());
			return false;
		}
	}
	*/
	public function getlistCustomerOrders ($userid) {
		$sql = "select * from #__digicom_orders where userid='".$userid."'order by id desc";
		$db = JFactory::getDBO();
		$db->setQuery($sql);
		return ($db->loadObjectList());
	}

	public function getCustomerId($username){
		$db = JFactory::getDBO();
		$sql = "select id from #__users where username='".trim($username)."'";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadResult();
		return $result;
	}

	public function existUser($username_value){
		$db = JFactory::getDBO();
		$sql = "select count(*) as total from #__users where username='".$username_value."'";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadObject();
		if($result->total == 0){
			return false;
		}
		return true;
	}

	public function existNewAuthor($username_value){
		$db = JFactory::getDBO();

		$sql = "select id from #__users u where u.username='".addslashes(trim($username_value))."'";
		$db->setQuery($sql);
		$db->query();
		$id = $db->loadResult();

		$sql = "select count(*) as total from #__digicom_customers a where a.id=".intval($id);
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadObject();
		if($result->total==0){
			return false;
		} 
		return true;
	}

	public function getUserId($username){
		$db = JFactory::getDBO();
		$sql = "select id from #__users where username='".$username."'";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadResult();
		return $result;
	}

	public function getConfigs() {
		$comInfo = JComponentHelper::getComponent('com_digicom');
		return $comInfo->params;
	}

	public function getUser(){
		$id = JRequest::getVar("id");
		if(intval($id) == "0"){
			$id = JRequest::getVar("cid", array(), "array");
			$id = $id["0"];
		}
		$db = JFactory::getDBO();
		$sql = "select * from #__users where id='".intval($id)."'";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadAssoc();
		return $result;
	}


	/**
	 * Method to get the row form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed    A JForm object on success, false on failure
	 *
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$form = $this->loadForm('com_digicom.customer', 'customer', array('control' => 'jform', 'load_data' => $loadData));
		
		if (empty($form))
		{
			return false;
		}
		
		return $form;
	}

}