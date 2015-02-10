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

class DigiComAdminControllerTaxRates extends DigiComAdminController {
	var $_model = null;

	function __construct () {

		parent::__construct();

		$this->registerTask ("add", "edit");
		$this->registerTask ("", "listClasses");
		$this->registerTask ("search", "listClasses");
		$this->registerTask ("unpublish", "publish");
		$this->registerTask ("orderup", "shiftorder");
		$this->registerTask ("orderdown", "shiftorder");
		$this->_model = $this->getModel("TaxRate");
	}

	function listClasses() {
	   		JRequest::setVar ("view", "TaxRates");
		$view = $this->getView("TaxRates", "html");
		$view->setModel($this->_model, true);
		$view->display();
	}


	function edit () {
		JRequest::setVar ("hidemainmenu", 1);
		$view = $this->getView("TaxRates", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);
		$model = $this->getModel("Config");
		$view->setModel($model);

		$view->editForm();

	}


	function save () {
		if ($this->_model->store() ) {
			$msg = JText::_('DSTAXRATESAVED');
		} else {
			$msg = JText::_('DSTAXRATESAVEFAILED');
		}
		$link = "index.php?option=com_digicom&controller=taxrates";
		$this->setRedirect($link, $msg);

	}

	function apply(){
		$id = JRequest::getVar("id", array(), "array");
		if ($this->_model->store() ) {
			$msg = JText::_('DSTAXRATESAVED');
		} else {
			$msg = JText::_('DSTAXRATESAVEFAILED');
		}
		$link = "index.php?option=com_digicom&controller=taxrates&task=edit&cid[]=".$id["0"];
		$this->setRedirect($link, $msg);

	}


	function remove () {
		if (!$this->_model->delete()) {
			$msg = JText::_('DSTAXRATEREMOVEFAIL');
		} else {
		 	$msg = JText::_('DSTAXRATEREMOVESUCCESS');
		}

		$link = "index.php?option=com_digicom&controller=taxrates";
		$this->setRedirect($link, $msg);

	}

	function cancel () {
	 	$msg = JText::_('DSTAXRATECANCELED');
		$link = "index.php?option=com_digicom&controller=taxrates";
		$this->setRedirect($link, $msg);


	}

	function publish () {
		$res = $this->_model->publish();

		if (!$res) {
			$msg = JText::_('DSTAXRATEPUBLISHINGERROR');
		} elseif ($res == -1) {
		 	$msg = JText::_('DSTAXRATEUNPUBLISHINGSUCC');
		} elseif ($res == 1) {
			$msg = JText::_('DSTAXRATEPUBLISHINGSUCC');
		} else {
				 	$msg = JText::_('DSTAXRATEUNSPECERROR');
		}

		$link = "index.php?option=com_digicom&controller=taxrates";
		$this->setRedirect($link, $msg);


	}

	function saveorder () {
		$res = $this->_model->reorder();

		if (!$res) {
			$msg = JText::_('DSTAXRATEORDERINGERROR');
		} else {
			$msg = JText::_('DSTAXRATEORDERINGSUCCESS');
		}
		$link = "index.php?option=com_digicom&controller=taxrates";
		$this->setRedirect($link, $msg);


	}

	function shiftorder () {
		$task = JRequest::getVar("task", "orderup", "request");

		$direct = ($task == "orderup")?(-1):(1);
		$res = $this->_model->shiftorder($direct);

		if (!$res) {
			$msg = JText::_('DSTAXRATEORDERINGERROR');
		} else {
			$msg = JText::_('DSTAXRATEORDERINGSUCCESS');
		}
		$link = "index.php?option=com_digicom&controller=taxrates";
		$this->setRedirect($link, $msg);


	}

  	function upload () {
		if (!$this->_model->upload()) {
			$msg = JText::_('DSTAXRATEUPLOADFAIL');
		} else {
		 	$msg = JText::_('DSTAXRATEUPLOADSUCCESS');
		}

		$link = "index.php?option=com_digicom&controller=taxrates";
		$this->setRedirect($link, $msg);

	}

	function viewsample() {

		echo "sample rate 1, United-States , Alaska, * , 10.10<br />
			sample rate 2, United-States , California, 123456, 12.5<br />
			sample rate 3, United-States , *, *, 30.0<br />
			<input type='button' onclick='window.close();' value='Close page' />
		";
	}

};

?>