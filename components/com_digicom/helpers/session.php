<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

/**
 * Digicom Component Session Helper
 *
 * @since  1.0.0
 */
class DigiComSiteHelperSession
{
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
	public $_customer;

	/**
	 * Class constructor
	 *
	 * @return  return customer object
	 *
	 * @since   1.0.0
	 */
	public function __construct ()
	{

		$db = JFactory::getDBO();
		$my = JFactory::getUser();
		$reg = JFactory::getSession();
		$time = time();
		$digicomid = 'digicomid';
		$sid = $reg->get($digicomid, 0);

		// first we will remove all session n cart info from db that passed 24 hours
		$sql = "SELECT GROUP_CONCAT(sid) as sid from #__digicom_session where create_time<'".($time - 3600*24)."'";
		$db->setQuery($sql);
		$oldsids = $db->loadObject();
		if(!empty($oldsids->sid)){
			$sql = "delete from #__digicom_cart where `sid` in (".$oldsids->sid.")";
			$db->setQuery($sql);
			$db->query();

			$sql = "delete from #__digicom_session where `sid` in (".$oldsids->sid.")";
			$db->setQuery($sql);
			$db->query();
		}

		//as we already removed all 24h old sessions, we need to check if we have current one or not
		if (!$sid) {
			// so we dont have any digicomid, we need to create one
			// but before that lets checck in session table if we have with user id
			$sql = "select * from #__digicom_session where uid='".$my->id."'";
			$db->setQuery($sql);
			$digisession = $db->loadObject();
			if(isset($digisession->sid)){
				// we have session id, so update the time
				$sql = "UPDATE #__digicom_session SET `create_time`='".time()."'";
				$db->setQuery($sql);
				$db->query();
				$reg->set($digicomid, $digisession->sid);
			}else{
				// we dont have session id, create id
				$sql = "INSERT INTO #__digicom_session (`uid`,`create_time`, `cart_details`, `transaction_details`, `shipping_details`)
					VALUES
					('".$my->id."','".$time."', '', '', '')
				 ";
				$db->setQuery($sql);
				$db->query();
				$sid = $db->insertId();
				$reg->set($digicomid, $sid);
			}
		} else {
			// we have sessionid  $sid
			// check if has userid
			if($my->id != 0){
				//i'm loged in
				$sql = "select * from #__digicom_session where sid='".$sid."'";
				$db->setQuery($sql);
				$digisession = $db->loadObject();

				if($digisession->uid == 0){
					//no userid
					$sql = "UPDATE #__digicom_session SET `uid`='".$my->id."' WHERE sid='".$sid."'";
					$db->setQuery($sql);
					$db->query();
					$digisession->uid = $my->id;
				}

			}else{
				//we have userid
				$sql = "select * from #__digicom_session where uid='".$my->id."'";
				$db->setQuery($sql);
				$digisession = $db->loadObject();

			}

			// reset time of too old
			$sid_time = $digisession->create_time;
			if (!$sid_time || ($sid_time + 3600*24) < $time) {
				$sql = "delete from #__digicom_session where sid='".$sid."'";
				$db->setQuery($sql);
				$db->query();
				$sql = "INSERT INTO #__digicom_session (`uid`,`create_time`, `cart_details`, `transaction_details`, `shipping_details`)
					VALUES
					('".$my->id."','".$time."', '', '', '')
					 ";
				$db->setQuery($sql);
				$db->query();
				$sid = $db->insertId();
				$reg->set($digicomid, $sid);
			}
		}

		// set session id
		$this->_sid = $sid;
		// set user object
		$this->_user = $my;

		// set the customer info
		if ($this->_user->id > 0) {
			$sql ="select * from #__digicom_customers where email='".$this->_user->email."'";
			$db->setQuery($sql);
			$tmp = $db->loadObject();

			// update customer info if re-registered as customer
			if(isset($tmp) and $tmp->id != $this->_user->id){
				$query = "UPDATE `#__digicom_customers` SET `id`=".$this->_user->id." WHERE `email`='" . $this->_user->email."'";
				$db->setQuery( $query );
				$db->query();
			}

			if ( isset($tmp) ) {
				// as we have userlogedin, make use we fill info for customer table
				$this->_customer = $tmp;

				$name_array = explode(" ", $my->name);
				$first_name = "";
				$last_name = "";
				if(count($name_array) == 1){
					$name = $my->name;
					$first_name = $name;
					$last_name = $name;
				} else {
					$last_name = $name_array[count($name_array)-1];
					unset($name_array[count($name_array)-1]);
					$first_name = implode(" ", $name_array);
				}

				$email = $my->email;

				if (empty( $this->_customer->firstname )&& $my->id ) $this->_customer->firstname 	= $first_name;
				if (empty( $this->_customer->lastname )&& $my->id ) $this->_customer->lastname 	= $last_name;
				if (empty( $this->_customer->email )&& $my->id ) $this->_customer->email = $email;

			} else {
				//user but not customer
				$this->_customer = new stdClass();
			}

		} else {
			// guest access
			$this->_customer = new stdClass();
		}

		// dont allow empty value, so define blank
		if (!isset($this->_customer->firstname)) $this->_customer->firstname = '';
		if (!isset($this->_customer->lastname)) $this->_customer->lastname = '';
		if (!isset($this->_customer->email)) $this->_customer->email = '';
		if (!isset($this->_customer->country)) $this->_customer->country = '';
		if (!isset($this->_customer->state)) $this->_customer->state = '';
		if (!isset($this->_customer->zipcode)) $this->_customer->zipcode = '';
		if (!isset($this->_customer->registerDate)) $this->_customer->registerDate = $my->registerDate;
		if (!isset($this->_customer->id) && $my->id ) $this->_customer->id = $my->id;

		return true;
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
		$sql = "select transaction_details from #__digicom_session where sid=".$this->_sid;
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

}
