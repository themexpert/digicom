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

class TableCategory extends JTable {
	var $id = null;
	var $parent_id = null;
	var $title = null;
	var $name = null;
	var $section = null;
	var $image_position = null;
	var $description = null;
	var $published = null;
	var $checked_out = null;
	var $checked_out_time = null;
	var $editor = null;
	var $ordering = null;
	var $access = null;
	var $count = null;
	var $metakeywords = null;
	var $metadescription = null;
	var $images = null;
	var $image = null;
	var $thumb = null;
	var $params = null;

	function TableCategory (&$db) {
		parent::__construct('#__digicom_categories', 'id', $db);
	}


	function store ($updateNulls = false) {
		$res = parent::store($updateNulls = false);
		if (!$res) return $res;
		$topcountries =  JRequest::getVar('topcountries', array(0), 'post', 'array');
		$db = JFactory::getDBO();
		$descr = stripslashes(JRequest::getVar('description', '', 'request', 'string', JREQUEST_ALLOWRAW));
		$sql = "update #__digicom_categories set `description`='".$db->escape($descr)."' where id=".$this->id;
		$db->setQuery($sql);
		$res = $db->query();
		if (!$res) return $res;

		return true;
	}

};


?>