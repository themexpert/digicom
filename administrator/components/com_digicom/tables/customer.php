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
		$sql = "select username from #__users where id='" . $id . "'";
		$db->setQuery( $sql );
		$r = $db->loadObjectList();
		if ( count( $r ) > 0 ) {
			$this->username = $r[0]->username;
		} else {
			$this->username = null;
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
