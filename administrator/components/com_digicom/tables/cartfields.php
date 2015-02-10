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

class TableCartFields extends JTable {
	var $fieldid = null;
	var $productid = null;
	var $sid = null;
	var $optionid = null;
	var $cid = null;

	function TableCartFields (&$db) {
		parent::__construct('#__digicom_cartfields', '', $db);
	}

};


?>