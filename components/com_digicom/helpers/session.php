<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

// TODO : PHP property visibility man.

class DigiComSiteHelperSession {
	var $_sid = null;
	var $user = null;
	var $_customer;
	var $_Itemid = null;

	function __construct () {

		global $Itemid;

		$db = JFactory::getDBO();
		$my = JFactory::getUser();
		$reg = JFactory::getSession();
		$time = time();
		$digicomid = 'digicomid';
		$sid = $reg->get($digicomid, 0);
		
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
		
		if (!$sid) {
			$sql = "select * from #__digicom_session where uid='".$my->id."'";
			$db->setQuery($sql);
			$digisession = $db->loadObject();
			if(isset($digisession->sid)){
				$sql = "UPDATE #__digicom_session SET `create_time`='".time()."'";
				$db->setQuery($sql);
				$db->query();
				$reg->set($digicomid, $digisession->sid);
			}else{
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
			//check if has userid
			if($my->id != 0){ //i'm loged in
				$sql = "select * from #__digicom_session where sid='".$sid."'";
				$db->setQuery($sql);
				$digisession = $db->loadObject();
				
				
				
				if($digisession->uid == 0){
					
					$sql = "delete from #__digicom_session where uid='".$my->id."'";
					$db->setQuery($sql);
					$db->query();
					
					$sql = "UPDATE #__digicom_session SET `uid`='".$my->id."' WHERE sid='".$sid."'";
					$db->setQuery($sql);
					$db->query();
				}
				
			}else{
				$sql = "select * from #__digicom_session where uid='".$my->id."'";
				$db->setQuery($sql);
				$digisession = $db->loadObject();				
			}
			
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

		$this->_sid = $sid;
		$this->_Itemid = $Itemid;
		//print_r($my->registerDate);die;
		$this->_user = $my;
		if ($this->_user->id > 0) {
			$sql ="select * from #__digicom_customers where email='".$this->_user->email."'";
			$db->setQuery($sql);
			$tmp = $db->loadObject();

			if($tmp->id != $this->_user->id){
				$query = "UPDATE `#__digicom_customers` SET `id`=".$this->_user->id." WHERE `email`='" . $this->_user->email."'";
				$db->setQuery( $query );
				$db->query();
			}

			if ( $tmp ) {
				$this->_customer = $tmp;
			} else {
				$this->_customer = new stdClass();
			}
		} else {
			$this->_customer = new stdClass();
		}
		
		if (!isset($this->_customer->firstname)) $this->_customer->firstname = '';
		if (!isset($this->_customer->lastname)) $this->_customer->lastname = '';
		if (!isset($this->_customer->email)) $this->_customer->email = '';
		if (!isset($this->_customer->country)) $this->_customer->country = '';
		if (!isset($this->_customer->state)) $this->_customer->state = '';
		if (!isset($this->_customer->zipcode)) $this->_customer->zipcode = '';
		if (!isset($this->_customer->registerDate)) $this->_customer->registerDate = $my->registerDate;
		
		if (!isset($this->_customer->id) && $my->id ) $this->_customer->id = $my->id;
		
		if($my->id > 0){
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
		
		}
		
		return true;
	}

	function getTransactionData() {
		if (empty($this->_sid) || $this->_sid < 1) return null;
		$db = JFactory::getDBO();
		$sql = "select transaction_details from #__digicom_session where sid=".$this->_sid;
		$db->setQuery($sql);
		$data = $db->loadResult();
		$data = unserialize(base64_decode($data));

		if (is_object($data) || !isset($data['cart']['orderid']) || empty($data['cart']['orderid'])){
			$data = array();
			$data['cart']['orderid'] = -1;
		}
		return $data;
	}
}
