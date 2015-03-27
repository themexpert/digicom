<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 418 $
 * @lastmodified	$LastChangedDate: 2013-11-16 09:20:18 +0100 (Sat, 16 Nov 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

defined ('_JEXEC') or die ("Go away.");

class TableMail extends JTable {
	public $id 		= null;
	public $date 	= null;
	public $email 	= null;
	public $body 	= null;
	public $flag 	= null;

	function TableMail (&$db) {
		parent::__construct('#__digicom_sendmails', 'id', $db);
	}

};


?>