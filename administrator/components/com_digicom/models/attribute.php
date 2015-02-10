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


class DigiComAdminModelAttribute extends DigiComModel
{
	var $_attributes;
	var $_attribute;
	var $_id = null;

	function __construct () {
		parent::__construct();
		$cids = JRequest::getVar('cid', 0, '', 'array');

		$this->setId((int)$cids[0]);

	}


	function setId($id) {
		$this->_id = $id;

		$this->_attribute = null;
	}


	function getlistAttributes () {
		if (empty ($this->_attributes)) {
			$sql = "select * from #__digicom_customfields";
			$this->_attributes = $this->_getList($sql);

		}
		return $this->_attributes;

	}

	function getAttribute() {
		if (empty ($this->_attribute)) {
			$this->_attribute = $this->getTable("Attribute");
			$this->_attribute->load($this->_id);
		}
		return $this->_attribute;

	}

	function store () {
		$item = $this->getTable('Attribute');
		$data = JRequest::get('post');
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

	function delete () {
		$cids = JRequest::getVar('cid', array(0), 'post', 'array');
		$item = $this->getTable('Attribute');
		foreach ($cids as $cid) {
			if (!$item->delete($cid)) {
				$this->setError($item->getErrorMsg());
				return false;

			}
		}

		return true;
	}


	function publish () {
		$db = JFactory::getDBO();
		$cids = JRequest::getVar('cid', array(0), 'post', 'array');
		$task = JRequest::getVar('task', '', 'post');
		if ($task == 'publish'){
			$sql = "update #__digicom_customfields set published='1' where id in ('".implode("','", $cids)."')";
		} else {
			$sql = "update #__digicom_customfields set published='0' where id in ('".implode("','", $cids)."')";

		}
		$db->setQuery($sql);
		if (!$db->query() ){
			$this->setError($db->getErrorMsg());
			return false;
		}



	}

};
?>