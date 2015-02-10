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