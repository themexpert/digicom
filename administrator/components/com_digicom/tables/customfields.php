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

class TableCustomFields extends JTable {
	var $id = null;
	var $name = null;
	var $options = null;
	var $publishing = null;
	var $checked_out = null;
	var $checked_out_time = null;
	var $ordering = null;
	var $size = null;

	function TableCustomFields (&$db) {
		parent::__construct('#__digicom_customfields', 'id', $db);
	}

};


?>