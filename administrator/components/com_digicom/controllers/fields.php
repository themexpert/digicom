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

class DigiComAdminControllerFields extends DigiComAdminController {
	var $_model = null;

	function __construct () {

		parent::__construct();

		$this->registerTask ("add", "edit");
		$this->registerTask ("", "listFields");
		$this->_model = $this->getModel("Field");
		$this->registerTask ("unpublish", "publish");
	}

	function listFields() {
		$view = $this->getView("Fields", "html");
		$view->setModel($this->_model, true);
		$view->display();


	}


	function edit () {
		JRequest::setVar ("hidemainmenu", 1);
		$view = $this->getView("Fields", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);

		$model = $this->getModel("Config");
		$view->setModel($model);

		$view->editForm();

	}


	function save () {
		if ($this->_model->store() ) {
			$msg = JText::_('DS_SAVEFIELD_SUCC');
		} else {
			$msg = JText::_('DS_SAVEFIELD_FAILED');
		}
		$link = "index.php?option=com_digicom&controller=fields";
		$this->setRedirect($link, $msg);

	}


	function remove () {
		if (!$this->_model->delete()) {
			$msg = JText::_('DS_REMOFEFIELD_FAIL');
		} else {
		 	$msg = JText::_('DS_REMOFIELDS_SUCC');
		}

		$link = "index.php?option=com_digicom&controller=fields";
		$this->setRedirect($link, $msg);

	}

	function cancel () {
	 	$msg = JText::_('DS_FIELD_OPERATIONCANCELED');
		$link = "index.php?option=com_digicom&controller=fields";
		$this->setRedirect($link, $msg);


	}

	function publish () {
		$res = $this->_model->publish();

		if (!$res) {
			$msg = JText::_('DS_FIELD_BLOCKIGERROR');
		} elseif ($res == -1) {
		 	$msg = JText::_('DS_FIELD_ATTRIBUNPUBSUCC');
		} elseif ($res == 1) {
			$msg = JText::_('DS_FIELD_ATTRIBPUBSUCC');
		} else {
				 	$msg = JText::_('DS_FIELD_ATTRIBUNSPECERROR');
		}

		$link = "index.php?option=com_digicom&controller=fields";
		$this->setRedirect($link, $msg);


	}

};

?>