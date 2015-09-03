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
		
		$db 		= JFactory::getDBO();
		$my 		= JFactory::getUser();
		$reg 		= JFactory::getSession();
		$time 		= time();
		$digicomid 	= 'digicomid';
		$sid 		= $reg->get($digicomid, 0);
		//SELECT GROUP_CONCAT(sid) as sid from `fs62c_digicom_session` where `create_time` < NOW() - INTERVAL 1 DAY

		// first we will remove all session n cart info from db that passed 24 hours
		$sql = "SELECT GROUP_CONCAT(sid) as sid from #__digicom_session where create_time< now() - INTERVAL 7 DAY";
		$db->setQuery($sql);
		$oldsids = $db->loadObject();
		//echo $oldsids->sid;die;

		if(!empty($oldsids->sid)){
			$sql = "delete from #__digicom_cart where `sid` in (".$oldsids->sid.")";
			$db->setQuery($sql);
			$db->execute();

			$sql = "delete from #__digicom_session where `sid` in (".$oldsids->sid.")";
			$db->setQuery($sql);
			$db->execute();
		}

		//as we already removed all 24h old sessions, we need to check if we have current one or not
		if (!$sid) {
			// so we dont have any digicomid, we need to create one
			// but before that lets checck in session table if we have with user id

			if(!$my->id){
				// we dont have session id, userid
				$sql = "INSERT INTO #__digicom_session (`uid`,`create_time`, `cart_details`, `transaction_details`, `shipping_details`)
					VALUES
					('".$my->id."',now(), '', '', '')
				 ";

				$db->setQuery($sql);
				$db->execute();
				$sid = $db->insertId();
				$reg->set($digicomid, $sid);

			}
			else{
				//we dont have session id but have userid
				$sql = "select * from #__digicom_session where uid='".$my->id."'";
				$db->setQuery($sql);
				$digisession = $db->loadObject();

				if(isset($digisession->uid) &&  $digisession->uid != 0){
					// user have previous session id
					$sid = $digisession->sid;
					$reg->set($digicomid, $sid);

					// we have session id, so update the time
					$sql = "UPDATE #__digicom_session SET `create_time`= now()";
					$db->setQuery($sql);
					$db->execute();
				}else{

					//user dosent have anyting before pending
					$sql = "INSERT INTO #__digicom_session (`uid`,`create_time`, `cart_details`, `transaction_details`, `shipping_details`)
						VALUES
						('".$my->id."',now(), '', '', '')
					 ";

					$db->setQuery($sql);
					$db->execute();
					$sid = $db->insertId();
					$reg->set($digicomid, $sid);

				}


			}


		} elseif($my->id != 0) {
			// we have sessionid  $sid
			// check if has userid
			//i'm loged in with session id
			$sql = "select * from #__digicom_session where sid='".$sid."'";
			$db->setQuery($sql);
			$digisession = $db->loadObject();

			if(isset($digisession) && !$digisession->uid){
				//no userid
				$sql = "UPDATE #__digicom_session SET `uid`='".$my->id."' WHERE sid='".$sid."'";
				$db->setQuery($sql);
				$db->execute();
				$digisession->uid = $my->id;
			}

		}

		// set session id
		$this->_sid = $sid;
		// set user object
		$this->_user = $my;

		// set the customer info
		if ($this->_user->id > 0) {
			$table = JTable::getInstance('Customer','Table');
			$table->load(array('email'=>$this->_user->email));

			// update customer info if re-registered as customer
			if($table->id != $this->_user->id){
				$query = "UPDATE `#__digicom_customers` SET `id`=".$this->_user->id." WHERE `email`='" . $this->_user->email."'";
				$db->setQuery( $query );
				$db->execute();
				$table = JTable::getInstance('Customer','Table');
				$table->load(array('email'=>$this->_user->email));
			}


			// as we have userlogedin, make use we fill info for customer table
			$this->_customer = $table;

			if($my->id && (empty($this->_customer->name) or empty($this->_customer->email))){
				$table->name = $my->name;
				$table->email = $my->email;
				$table->store();
			}

		} else {
			// guest access
			$this->_customer = JTable::getInstance('Customer','Table');
		}
		//
		// // dont allow empty value, so define blank
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
