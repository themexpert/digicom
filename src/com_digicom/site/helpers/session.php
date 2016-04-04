<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;
JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_digicom/tables', 'Table');

/**
 * Digicom Component Session Helper
 *
 * @since  1.0.0
 */
class DigiComSiteHelperSession
{
	protected $oldsids;
	protected $digisession;
	/*
	* session id, int
	*/
	public $_sid = null;
	/*
	* joomla user, object
	*/
	public $user = null;
	/*
	* digicom customer, object
	*/
	public $_customer = array();

	/**
	 * Class constructor
	 *
	 * @return  return customer object
	 *
	 * @since   1.0.0
	 */

	public function __construct ()
	{
		$app 				= JFactory::getApplication();
		$db 				= JFactory::getDBO();
		$my 				= JFactory::getUser();
		$reg 				= JFactory::getSession();
		$sessionid  = $reg->getId();	
		$dispatcher	= JDispatcher::getInstance();
		$digicomid 	= 'digicomid';
		$sid 				= $reg->get($digicomid, 0);
		$debug 			= $app->input->get('debug');
		if($debug){
			new DigiComSiteHelperTest();
		}

		if(empty($this->oldsids) && !$sid){
			// first we will remove all session n cart info from db that passed 24 hours
			$sql = "SELECT GROUP_CONCAT(id) as sid from #__digicom_session where create_time< now() - INTERVAL 3 DAY";
			$db->setQuery($sql);

			$oldsids = $db->loadObject();
			$this->oldsids = rtrim($oldsids->sid, ',');
		}

		if(!empty($this->oldsids)){
			$sql = "delete from #__digicom_cart where `sid` in (".$this->oldsids.")";
			$db->setQuery($sql);
			$db->execute();

			$sql = "delete from #__digicom_session where `id` in (".$this->oldsids.")";
			$db->setQuery($sql);
			$db->execute();
		}
		
		//as we already removed all 24h old sessions, we need to check if we have current one or not
		if (!$sid) {
			//test code to verify old sessions
			//JSession::hasToken($tCheck, $forceExpire = true)

			// check with jsession id
			// first we will remove all session n cart info from db that passed 24 hours
			$sql = "SELECT * from #__digicom_session where `sid` = '" . $sessionid . "'";
			$db->setQuery($sql);
			$this->digisession = $db->loadObject();
			// so we dont have any digicomid, we need to create one
			// but before that lets checck in session table if we have with user id
			
			// no session no user
			if(!isset($this->digisession->id) && !$my->id)
			{
				// we dont have session id, userid
				$sql = "INSERT INTO #__digicom_session 
					(`sid`, `uid`,`create_time`, `cart_details`, `transaction_details`, `shipping_details`)
					VALUES ('".$sessionid."','".$my->id."',now(), '', '', '')";

				$db->setQuery($sql);
				$db->execute();
				$sid = $db->insertId();
				// $sid = $sessionid;
				$reg->set($digicomid, $sid);

				// $this->addLog();
			}
			// no session but has user
			elseif(!isset($this->digisession->id) && $my->id)
			{
				//we dont have session id but have userid
				$sql = "select * from #__digicom_session where uid='".$my->id."'";
				$db->setQuery($sql);
				$info = $db->loadObject();
				if($info->uid)
				{
					$this->digisession = $info;

					// user have previous session id
					$sid = $this->digisession->id;
					$reg->set($digicomid, $sid);

					// we have session id, so update the time
					$sql = "UPDATE #__digicom_session SET `create_time`= now()";
					$db->setQuery($sql);
					$db->execute();
				}
				else
				{
					// we dont have session id, userid
					$sql = "INSERT INTO #__digicom_session 
						(`sid`, `uid`,`create_time`, `cart_details`, `transaction_details`, `shipping_details`)
						VALUES ('".$sessionid."','".$my->id."',now(), '', '', '')";

					$db->setQuery($sql);
					$db->execute();
					$sid = $db->insertId();
					// $sid = $sessionid;
					$reg->set($digicomid, $sid);
				}
			}


		} elseif($my->id != 0) {
			// echo $my->id;die;
			// we have sessionid  $sid
			// check if has userid
			//i'm loged in with session id
			$sql = "select * from #__digicom_session where id='".$sid."'";
			$db->setQuery($sql);
			$this->digisession = $db->loadObject();
			// print_r($this->digisession);die;
			if(isset($this->digisession) && !$this->digisession->uid){
				//no userid
				$sql = "UPDATE #__digicom_session SET `uid`='".$my->id."' WHERE id='".$sid."'";
				$db->setQuery($sql);
				$db->execute();
				$this->digisession->uid = $my->id;

				$sql = "SELECT GROUP_CONCAT(id) as sid from #__digicom_session where uid='".$my->id."' and id != '".$sid."'";
				$db->setQuery($sql);
				$oldids = $db->loadObject();
				$oldidslist = rtrim($oldids->sid, ',');
				
				if(!empty($oldidslist))
				{
					$sql = "delete from #__digicom_cart where `sid` in (".$oldidslist.")";
					$db->setQuery($sql);
					$db->execute();

					$sql = "delete from #__digicom_session where `id` in (".$oldidslist.")";
					$db->setQuery($sql);
					$db->execute();
				}

			}

		}

		// set session id
		$this->_sid = $sid;
		// set user object
		$this->_user = $my;

		// set the customer info
		if ($this->_user->id > 0)
		{

			$table = JTable::getInstance('Customer','Table');
			$table->load(array('email'=>$this->_user->email));

			// update customer info if re-registered as customer
			if($table->id != $this->_user->id)
			{
				$query = "UPDATE `#__digicom_customers` SET `id`=".$this->_user->id." WHERE `email`='" . $this->_user->email."'";
				$db->setQuery( $query );
				$db->execute();

				$dispatcher->trigger('onDigicomSessionOnChangeCustomerID',array('com_digicom.session', $table->id, $this->_user->id));

				$table = JTable::getInstance('Customer','Table');
				$table->load(array('email'=>$this->_user->email));
			}



			if($my->id && (empty($this->_customer->name) or empty($this->_customer->email))){
				$table->name = $my->name;
				$table->email = $my->email;
				$table->store();
			}

			// as we have userlogedin, make use we fill info for customer table
			$this->_customer = $table;
		} else {
			// guest access
			$this->_customer = JTable::getInstance('Customer','Table');
		}

		//
		// // dont allow empty value, so define blank
		if (!isset($this->_customer->registerDate)) $this->_customer->registerDate = $my->registerDate;
		if (!isset($this->_customer->id) && $my->id ) $this->_customer->id = $my->id;

		// echo "<pre> return: ".print_r($this->_customer)."</pre>";
		return $this->_customer;
	}

	/**
	* get transaction data
	*
	* @return  data object for transsection from session
	*
	* @since   1.0.0
	*/
	function getTransactionData()
	{
		// return null of no session id $_sid
		if (empty($this->_sid) || $this->_sid < 1) return null;
		$db = JFactory::getDBO();
		$sql = "select transaction_details from #__digicom_session where id=".$this->_sid;
		$db->setQuery($sql);
		$data = $db->loadResult();
		$data = unserialize(base64_decode($data));

		//if no orderid, its value less, set default and return
		if (is_object($data) || !isset($data['cart']['orderid']) || empty($data['cart']['orderid'])){
			$data = array();
			$data['cart']['orderid'] = -1;
		}
		return $data;

	}
	function addLog(){
		JLog::addLogger(
       array(
            // Sets file name
            'text_file' => 'com_digicom.session.log.php'
       ),
       // Sets messages of all log levels to be sent to the file
       JLog::ALL,
       // The log category/categories which should be recorded in this file
       // In this case, it's just the one category from our extension, still
       // we need to put it inside an array
       array('com_digicom')
	   );
		$info = json_encode($_SERVER);
    JLog::add($info, JLog::INFO, 'com_digicom');
	}
}
