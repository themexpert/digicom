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

class TableTaxrule extends JTable {
	var $id = null;
	var $name = null;
	var $cclass = null;
	var $pclass = null;
	var $ptype = null;
	var $trate = null;
	var $published = null;
	var $checked_out = null;
	var $checked_out_time = null;
	var $ordering = null;

	function TableTaxrule (&$db) {
		parent::__construct('#__digicom_tax_rule', 'id', $db);
	}


	function store($updateNulls = false) {

		$x = JRequest::getVar('cclass', '', 'request');
		$this->cclass = implode("\n", $x);

		$x = JRequest::getVar('pclass', '', 'request');
		$this->pclass = implode("\n", $x);

		$x = JRequest::getVar('trate', '', 'request');
		$this->trate = implode("\n", $x);

		$x = JRequest::getVar('ptype', '', 'request');
		$this->ptype = implode("\n", $x);
 
		$res = parent::store($updateNulls = false);
		if (!$res) return $res;
		return true;
	}

};


?>