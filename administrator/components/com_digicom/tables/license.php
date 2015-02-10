<?php

/**

 *
 * @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 448 $
 * @lastmodified	$LastChangedDate: 2013-12-03 09:41:08 +0100 (Tue, 03 Dec 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
 * @license			GNU/GPLv3 */
defined( '_JEXEC' ) or die( "Go away." );

class TableLicense extends JTable
{

	var $id = null;
	var $licenseid = null;
	var $userid = null;
	var $productid = null;
	var $domain = null;
	var $amount_paid = null;
	var $orderid = null;
	var $dev_domain = null;
	var $hosting_service = null;
	var $published = null;
	var $ltype = null;
	var $package_id = null;
	var $purchase_date = null;
	var $expires = null;
	var $renew = null;
	var $renewlicid = null;
	var $download_count = null;
	var $plan_id = null;
	var $old_orders  = null;
	var $cancelled  = 0;
	var $cancelled_amount  = 0;

	function TableLicense( &$db )
	{
		parent::__construct( '#__digicom_licenses', 'id', $db );
	}

	function load( $id = 0, $reset = true )
	{
		parent::load( $id );
		$db = JFactory::getDBO();
		$sql = "select firstname, lastname from #__digicom_customers where id='" . $this->userid . "'";
		$db->setQuery( $sql );
		$res = $db->loadObjectList();
		if ( count( $res ) > 0 ) {
			$this->customer = $res[0]->fisrtname . " " . $res[0]->lastname;
		} else {
			$this->customer = "";
		}

		$sql = "select name from #__digicom_products where id='" . $this->productid . "'";
		$db->setQuery( $sql );
		$res = $db->loadObjectList();
		if ( count( $res ) > 0 ) {
			$this->product = $res[0]->name;
		} else {
			$this->product = "";
		}

	}

	function store($updateNulls = false)
	{
		$username = JRequest::getVar( "username", "", "request" );
		$db = JFactory::getDBO();
		$sql = "select id from #__users where username='" . $username . "'";
		$db->setQuery( $sql );
		$uid = $db->loadResult();

		if ( !$uid ) return false;

		$this->userid = $uid;

		$res = parent::store($updateNulls = false);

		if ( !$res ) return $res;

		return true;
	}

}


?>