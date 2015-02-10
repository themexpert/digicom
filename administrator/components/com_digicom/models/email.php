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

jimport ("joomla.aplication.component.model");


class DigiComAdminModelEmail extends DigiComModel
{

	function getItems () {

		$type = JRequest::getVar('type','register','string');
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__digicom_mailtemplates'));
		$query->where($db->quoteName('type')." = ".$db->quote($type));
		$db->setQuery($query);
		$result	= $db->loadObject();		
		return $result;

	}


	function store () {
		$item = $this->getTable('MailTemplates');
		$data = JRequest::get('post');
		$item->load(array('type'=>$data['type']));
		if (!$item->bind($data)){
			$this->setError($item->getErrorMsg());
			return false;

		}

		if (!$item->check()) {
			$this->setError($item->getErrorMsg());
			return false;

		}

		if (!$item->store()) {
			$this->setError($item->getErrorMsg());
			return false;

		}
		return true;

	}

}