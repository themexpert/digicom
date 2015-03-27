<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class TableCustomer extends JTable {
	
	public function __construct(JDatabaseDriver $db)
	{
		parent::__construct('#__digicom_customers', 'id', $db);
	}
	
	public function hasPrimaryKey(){
		if(isset($this->id)&& $this->id>0){
			$db = JFactory::getDbo();
			$sql = 'SELECT * FROM `#__digicom_customers` WHERE `id`='.$this->id;
			$db->setQuery($sql);
			$res = $db->loadObject();
			if($res && $res->id>0){
				return true;
			} else {
				return false;
			}
		}
		return false;
	}
	
	public function store($updateNulls = false) {
		if(!$this->person){
			$this->person=0;
		}
		if($this->person){
			$this->shipfirstname	= $this->firstname;
			$this->shiplastname		= $this->lastname;
			$this->shipaddress		= $this->address;
			$this->shipcity			= $this->city;
			$this->shipstate		= $this->state;
			$this->shipprovince		= $this->province;
			$this->shipzipcode		= $this->zipcode;
			$this->shipcountry		= $this->country;
			$this->shipphone		= $this->phone;
		}
		return parent::store($updateNulls);
	}
}
?>