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

class DigiComAdminControllerPlugins extends DigiComAdminController {
	var $_model = null;

	function __construct () {

		parent::__construct();

		$this->registerTask ("add", "edit");
		$this->registerTask ("", "listPlugins");
		$this->_model = $this->getModel("Plugin");
		$this->registerTask ("unpublish", "publish");
	}

	function listPlugins() {

		$view = $this->getView("Plugins", "html");
		$view->setModel($this->_model, true);
		$view->display();


	}


	function edit () {
		JRequest::setVar ("hidemainmenu", 1);
		$view = $this->getView("Plugins", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);

		$model = $this->getModel("Config");
		$view->setModel($model);

		$view->editForm();

	}


	function save_default () {
		if ($this->_model->save_default() ) {

			$msg = JText::_('PLUGSAVED');
		} else {
			$msg = JText::_('PLUGFAILED');
		}
		$link = "index.php?option=com_digicom&controller=plugins";
		$this->setRedirect($link, $msg);

	}

	function save () {
		if ($this->_model->store() ) {

			$msg = JText::_('PLUGSAVED');
		} else {
			$msg = JText::_('PLUGFAILED');
		}
		$link = "index.php?option=com_digicom&controller=plugins";
		$this->setRedirect($link, $msg);

	}

	function upload () {
		$msg = $this->_model->upload();

		$link = "index.php?option=com_digicom&controller=plugins";
		$this->setRedirect($link, $msg);

	}

	function remove () {
		if (!$this->_model->delete()) {
			$msg = JText::_('PLUGREMERR');
		} else {
		 	$msg = JText::_('PLUGREMSUCC');
		}

		$link = "index.php?option=com_digicom&controller=plugins";
		$this->setRedirect($link, $msg);

	}

	function cancel () {
	 	$msg = JText::_('PLUGCANCEL');
		$link = "index.php?option=com_digicom&controller=plugins";
		$this->setRedirect($link, $msg);


	}

	function publish () {
		$res = $this->_model->publish();
		if (!$res) {
			$msg = JText::_('PLUGPUBERR');
		} elseif ($res == -1) {
		 	$msg = JText::_('PLUGUNPUB');
		} elseif ($res == 1) {
			$msg = JText::_('PLUGPUB');
		} else {
				 	$msg = JText::_('PLUGUNSPEC');
		}

		$link = "index.php?option=com_digicom&controller=plugins";
		$this->setRedirect($link, $msg);


	}

};

?>