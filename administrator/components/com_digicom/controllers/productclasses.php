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

class DigiComAdminControllerProductClasses extends DigiComAdminController {
	var $_model = null;

	function __construct () {

		parent::__construct();

		$this->registerTask ("add", "edit");
		$this->registerTask ("", "listClasses");
		$this->registerTask ("unpublish", "publish");
		$this->registerTask ("orderup", "shiftorder");
		$this->registerTask ("orderdown", "shiftorder");
		$this->_model = $this->getModel("ProductClass");
	}

	function listClasses() {
	   	JRequest::setVar ("view", "ProductClasses");
		$view = $this->getView("ProductClasses", "html");
		$view->setModel($this->_model, true);
		$view->display();
	}


	function edit () {
		JRequest::setVar ("hidemainmenu", 1);
		$view = $this->getView("ProductClasses", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);
		$view->editForm();

	}


	function save(){
		if($this->_model->store()){
			$msg = JText::_('DSPRODUCTCLASSSAVED');
		}
		else{
			$msg = JText::_('DSPRODUCTCLASSSAVEFAILED');
		}
		$link = "index.php?option=com_digicom&controller=productclasses";
		$this->setRedirect($link, $msg);

	}

	function apply(){
		$id = JRequest::getVar("id", "0");
		if($this->_model->store()){
			$msg = JText::_('DSPRODUCTCLASSSAVED');
		}
		else{
			$msg = JText::_('DSPRODUCTCLASSSAVEFAILED');
		}
		$link = "index.php?option=com_digicom&controller=productclasses&task=edit&cid[]=".intval($id);
		$this->setRedirect($link, $msg);
	}


	function remove () {
		if (!$this->_model->delete()) {
			$msg = JText::_('DSPRODUCTCLASSREMOVEFAIL');
		} else {
		 	$msg = JText::_('DSPRODUCTCLASSREMOVESUCCESS');
		}

		$link = "index.php?option=com_digicom&controller=productclasses";
		$this->setRedirect($link, $msg);

	}

	function cancel () {
	 	$msg = JText::_('DSPRODUCTCLASSCANCELED');
		$link = "index.php?option=com_digicom&controller=productclasses";
		$this->setRedirect($link, $msg);


	}

	function publish () {
		$res = $this->_model->publish();

		if (!$res) {
			$msg = JText::_('DSPRODUCTCLASSPUBLISHINGERROR');
		} elseif ($res == -1) {
		 	$msg = JText::_('DSPRODUCTCLASSUNPUBLISHINGSUCC');
		} elseif ($res == 1) {
			$msg = JText::_('DSPRODUCTCLASSPUBLISHINGSUCC');
		} else {
				 	$msg = JText::_('DSPRODUCTCLASSUNSPECERROR');
		}

		$link = "index.php?option=com_digicom&controller=productclasses";
		$this->setRedirect($link, $msg);


	}

	function saveorder () {
		$res = $this->_model->reorder();

		if (!$res) {
			$msg = JText::_('DSPRODUCTCLASSORDERINGERROR');
		} else {
			$msg = JText::_('DSPRODUCTCLASSORDERINGSUCCESS');
		}
		$link = "index.php?option=com_digicom&controller=productclasses";
		$this->setRedirect($link, $msg);


	}

	function shiftorder () {
		$task = JRequest::getVar("task", "orderup", "request");

		$direct = ($task == "orderup")?(-1):(1);
		$res = $this->_model->shiftorder($direct);

		if (!$res) {
			$msg = JText::_('DSPRODUCTCLASSORDERINGERROR');
		} else {
			$msg = JText::_('DSPRODUCTCLASSORDERINGSUCCESS');
		}
		$link = "index.php?option=com_digicom&controller=productclasses";
		$this->setRedirect($link, $msg);


	}


};

?>