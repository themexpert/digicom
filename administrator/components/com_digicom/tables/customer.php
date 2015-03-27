<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class TableCustomer extends JTable
{
	function TableCustomer( &$db )
	{
		parent::__construct( '#__digicom_customers', 'id', $db );
	}

	function loadCustommer( $id = NULL, $reset = true )
	{
		parent::load( $id );
	}
	function load( $id = NULL, $reset = true )
	{
		parent::load( $id );
		$db = JFactory::getDBO();
		$sql = "select username, email from #__users where id='" . $id . "'";
		$db->setQuery( $sql );
		$r = $db->loadObjectList();
		if ( count( $r ) > 0 ) {
			$this->email = $r[0]->email;
			$this->username = $r[0]->username;
		} else {
			$this->email = null;
			$this->username = null;
			$this->address = (JRequest::getVar('address','') != '') ? JRequest::getVar('address','') : null;
			$this->city = (JRequest::getVar('city','') != '') ? JRequest::getVar('city','') : null;
			$this->state = (JRequest::getVar('state','') != '') ? JRequest::getVar('state','') : null;
			$this->province = (JRequest::getVar('province','') != '') ? JRequest::getVar('province','') : null;
			$this->zipcode = (JRequest::getVar('zipcode','') != '') ? JRequest::getVar('zipcode','') : null;
			$this->country = (JRequest::getVar('country','') != '') ? JRequest::getVar('country','') : null;
			$this->payment_type = (JRequest::getVar('payment_type','') != '') ? JRequest::getVar('payment_type','') : null;
			$this->company = (JRequest::getVar('company','') != '') ? JRequest::getVar('company','') : null;
			$this->firstname = (JRequest::getVar('firstname','') != '') ? JRequest::getVar('firstname','') : null;
			$this->lastname = (JRequest::getVar('lastname','') != '') ? JRequest::getVar('lastname','') : null;
			$this->shipaddress = (JRequest::getVar('shipaddress','') != '') ? JRequest::getVar('shipaddress','') : null;
			$this->shipcity = (JRequest::getVar('shipcity','') != '') ? JRequest::getVar('shipcity','') : null;
			$this->shipstate = (JRequest::getVar('shipstate','') != '') ? JRequest::getVar('shipstate','') : null;
			$this->shipzipcode = (JRequest::getVar('shipzipcode','') != '') ? JRequest::getVar('shipzipcode','') : null;
			$this->shipcountry = (JRequest::getVar('shipcountry','') != '') ? JRequest::getVar('shipcountry','') : null;
			$this->person = (JRequest::getVar('person','') != '') ? JRequest::getVar('person','') : null;
			$this->taxnum = (JRequest::getVar('taxnum','') != '') ? JRequest::getVar('taxnum','') : null;
			$this->taxclass = (JRequest::getVar('taxclass','') != '') ? JRequest::getVar('taxclass','') : null;
		}
	}

	function store($updateNulls = false)
	{
		
		$db = JFactory::getDBO();
		$sql = "select count(*) from #__digicom_customers where id='" . $this->id . "'";
		$db->setQuery( $sql );
		$n = $db->loadResult();

		if ( $n < 1 & $this->id > 0 ) {
			$sql = "insert into #__digicom_customers(`id`) values ('" . $this->id . "')";
			$db->setQuery( $sql );
			$db->query();
		} else if ( $n < 1 & $this->id < 1 ) {
			return false;
		}

		return parent::store($updateNulls = false);
	}

}
