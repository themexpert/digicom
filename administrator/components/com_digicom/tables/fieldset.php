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

class TableFieldset extends JTable {
	var $id = null;
	var $name = null;
	var $fields = null;
	var $core = null;
	var $editable = null;
	var $comment = null;
	var $published = null;
	var $access = null;
	var $checked_out = null;
	var $checked_out_time = null;
	var $ordering = null;

	function TableFieldset (&$db) {
		parent::__construct('#__digicom_fieldset', 'id', $db);
	}

};


?>