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

class DigiComAdminControllerPlans extends DigiComAdminController {
	
	var $_model = null;

	function __construct () {

		parent::__construct();
		$this->registerTask ("add", "edit");
		$this->registerTask ("apply", "save");
		$this->registerTask ("list", "listPlains");
		$this->registerTask ("", "listPlains");
		$this->registerTask ("unpublish", "publish");
		$this->registerTask ("orderup", "shiftorder");
		$this->registerTask ("orderdown", "shiftorder");

		$this->_model = $this->getModel("Plain");

	}

	function listPlains() {

		$view = $this->getView("Plans", "html");
		$view->setModel($this->_model, true);

		$model = $this->getModel("Config");
		$view->setModel($model);

		$view->display();

	}

	function planitem() {
		$view = $this->getView("Plans","html");
		$view->setModel($this->_model, true);
		$model = $this->getModel("Config");
		$view->setModel($model);
		$view->setLayout('planitem');
		$view->planitem();
	}

	function save () {
		if ($this->_model->store() ) {

			$msg = JText::_('LICSAVED');
		} else {
			$msg = JText::_('LICSAVEDFAILED');
		}

		if ( JRequest::getVar('task','') == 'save' ) {
			$link = "index.php?option=com_digicom&controller=plans";
		} else {
			$plan_id = JRequest::getVar('id','');
			$link = "index.php?option=com_digicom&controller=plans&task=edit&cid[]=" . $plan_id;
		}

		$this->setRedirect($link, $msg);
	}


	function remove () {
		if (!$this->_model->delete()) {
			$msg = JText::_('LICREMERR');
		} else {
		 	$msg = JText::_('LICREMSUCC');
		}

		$link = "index.php?option=com_digicom&controller=plans";
		$this->setRedirect($link, $msg);

	}

	function cancel () {
	 	$msg = JText::_('LICCANCELED');
		$link = "index.php?option=com_digicom&controller=plans";
		$this->setRedirect($link, $msg);


	}

	function publish () {
		$res = $this->_model->publish();
		if (!$res) {
			$msg = JText::_('LICBLOCKERR');
		} elseif ($res == -1) {
		 	$msg = JText::_('LICUNPUB');
		} elseif ($res == 1) {
			$msg = JText::_('LICPUB');
		} else {
				 	$msg = JText::_('LICUNSPEC');
		}

		$link = "index.php?option=com_digicom&controller=plans";
		$this->setRedirect($link, $msg);


	}
	

	function edit () {
		
		JRequest::setVar ("hidemainmenu", 1);
		$view = $this->getView("Plans", "html");

		$view->setLayout("editForm");
		$view->setModel($this->_model, true);

		$model = $this->getModel("Config");
		$view->setModel($model);

		$view->editForm();

	}


	function saveorder () {
		$res = $this->_model->saveorder();

		if (!$res) {
			$msg = JText::_('ERROR');
		} else {
			$msg = JText::_('SUCCESS');
		}
		$link = "index.php?option=com_digicom&controller=plans";
		$this->setRedirect($link, $msg);


	}

	function shiftorder () {
		$task = JRequest::getVar("task", "orderup", "request");
		$direct = ($task == "orderup")?(-1):(1);
/* */
		$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
		$res = $this->_model->orderField( $cid[0], $direct );
/* */
//		$res = $this->_model->shiftorder($direct);

		if (!$res) {
			$msg = JText::_('ERROR');
		} else {
			$msg = JText::_('SUCCESS');
		}
		$link = "index.php?option=com_digicom&controller=plans";
		$this->setRedirect($link, $msg);
	}


	function getPlainsByProductIDSelect() {
		$view = $this->getView("Plans", "html");

		$view->setLayout("editForm");
		$view->setModel($this->_model, true);

		$model = $this->getModel("Config");
		$view->setModel($model);

		$view->setLayout('selectnewplain');

		$view->getPlainsByProductIDSelectHTML();
	}

}

?>