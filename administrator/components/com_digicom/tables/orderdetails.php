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

class TableOrderDetails extends JTable
{

	function TableOrderDetails( &$db )
	{
		parent::__construct( '#__digicom_orders_details', 'id', $db );
	}


}