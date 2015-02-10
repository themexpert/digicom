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

class DigiComAdminControllerTaxRules extends DigiComAdminController {
	var $_model = null;

	function __construct () {

		parent::__construct();

		$this->registerTask ("add", "edit");
		$this->registerTask ("", "listClasses");
		$this->registerTask ("unpublish", "publish");
		$this->registerTask ("orderup", "shiftorder");
		$this->registerTask ("orderdown", "shiftorder");
		$this->_model = $this->getModel("TaxRule");
	}

	function listClasses() {
	   		JRequest::setVar ("view", "TaxRules");
		$view = $this->getView("TaxRules", "html");
		$view->setModel($this->_model, true);
		$view->display();
	}


	function edit () {
		JRequest::setVar ("hidemainmenu", 1);
		$view = $this->getView("TaxRules", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);
		$view->editForm();

	}


	function save () {
		if ($this->_model->store() ) {
			$msg = JText::_('DSTAXRULESAVED');
		} else {
			$msg = JText::_('DSTAXRULESAVEFAILED');
		}
		$link = "index.php?option=com_digicom&controller=taxrules";
		$this->setRedirect($link, $msg);

	}

	function apply(){
		$id = JRequest::getVar("id", array(), "array");
		if ($this->_model->store() ) {
			$msg = JText::_('DSTAXRULESAVED');
		} else {
			$msg = JText::_('DSTAXRULESAVEFAILED');
		}
		$link = "index.php?option=com_digicom&controller=taxrules&task=edit&cid[]=".$id["0"];
		$this->setRedirect($link, $msg);
	}


	function remove () {
		if (!$this->_model->delete()) {
			$msg = JText::_('DSTAXRULEREMOVEFAIL');
		} else {
		 	$msg = JText::_('DSTAXRULEREMOVESUCCESS');
		}

		$link = "index.php?option=com_digicom&controller=taxrules";
		$this->setRedirect($link, $msg);

	}

	function cancel () {
	 	$msg = JText::_('DSTAXRULECANCELED');
		$link = "index.php?option=com_digicom&controller=taxrules";
		$this->setRedirect($link, $msg);


	}

	function publish () {
		$res = $this->_model->publish();

		if (!$res) {
			$msg = JText::_('DSTAXRULEPUBLISHINGERROR');
		} elseif ($res == -1) {
		 	$msg = JText::_('DSTAXRULEUNPUBLISHINGSUCC');
		} elseif ($res == 1) {
			$msg = JText::_('DSTAXRULEPUBLISHINGSUCC');
		} else {
				 	$msg = JText::_('DSTAXRULEUNSPECERROR');
		}

		$link = "index.php?option=com_digicom&controller=taxrules";
		$this->setRedirect($link, $msg);


	}

	function saveorder () {
		$res = $this->_model->reorder();

		if (!$res) {
			$msg = JText::_('DSTAXRULEORDERINGERROR');
		} else {
			$msg = JText::_('DSTAXRULEORDERINGSUCCESS');
		}
		$link = "index.php?option=com_digicom&controller=taxrules";
		$this->setRedirect($link, $msg);


	}

	function shiftorder () {
		$task = JRequest::getVar("task", "orderup", "request");

		$direct = ($task == "orderup")?(-1):(1);
		$res = $this->_model->shiftorder($direct);

		if (!$res) {
			$msg = JText::_('DSTAXRULEORDERINGERROR');
		} else {
			$msg = JText::_('DSTAXRULEORDERINGSUCCESS');
		}
		$link = "index.php?option=com_digicom&controller=taxrules";
		$this->setRedirect($link, $msg);


	}


};

?>