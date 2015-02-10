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

jimport ('joomla.application.component.controller');

class DigiComAdminControllerFieldtypes extends DigiComAdminController {
	var $_model = null;

	function __construct () {

		parent::__construct();

		$this->registerTask ("add", "edit");
		$this->registerTask ("", "listFieldtypes");
		$this->_model = $this->getModel("Fieldtype");
		$this->registerTask ("unpublish", "publish");
	}

	function listFieldtypes() {

		$view = $this->getView("Fieldtypes", "html");
		$view->setModel($this->_model, true);

		$view->display();


	}


	function edit () {
		JRequest::setVar ("hidemainmenu", 1);
		$view = $this->getView("Fieldtypes", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);

		$model = $this->getModel("Config");
		$view->setModel($model);

		$view->editForm();

	}


	function save () {
		if ($this->_model->store() ) {
			$msg = JText::_('DS_SAVEFIELDTYPE_SUCC');
		} else {
			$msg = JText::_('DS_SAVEFIELDTYPE_FAILED');
		}
		$link = "index.php?option=com_digicom&controller=fieldtypes";
		$this->setRedirect($link, $msg);

	}


	function remove () {
		if (!$this->_model->delete()) {
			$msg = JText::_('DS_REMOFEFIELDTYPE_FAIL');
		} else {
		 	$msg = JText::_('DS_REMOFEFIELDTYPE_SUCC');
		}

		$link = "index.php?option=com_digicom&controller=fieldtypes";
		$this->setRedirect($link, $msg);

	}

	function cancel () {
	 	$msg = JText::_('DS_FIELDTYPE_OPERATIONCANCELED');
		$link = "index.php?option=com_digicom&controller=fieldtypes";
		$this->setRedirect($link, $msg);


	}

	function publish () {
		$res = $this->_model->publish();

		if (!$res) {
			$msg = JText::_('DS_FIELDTYPE_BLOCKIGERROR');
		} elseif ($res == -1) {
		 	$msg = JText::_('DS_FIELDTYPE_UNPUBSUCC');
		} elseif ($res == 1) {
			$msg = JText::_('DS_FIELDTYPE_PUBSUCC');
		} else {
				 	$msg = JText::_('DS_FIELDTYPE_UNSPECERROR');
		}

		$link = "index.php?option=com_digicom&controller=filedtypes";
		$this->setRedirect($link, $msg);


	}

};

?>