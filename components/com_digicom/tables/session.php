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

class TableSession extends JTable {
	var $sid = null;
	var $cart_details = null;
	var $transaction_details = null;
	var $shipping_details = null;
	var $create_time = null;

	function TableSession (&$db) {
		parent::__construct('#__digicom_session', 'sid', $db);
	}

}
