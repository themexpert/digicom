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

class DigiComAdminControllerCustomers extends DigiComAdminController {
	var $model = null;

	function __construct () {
		
		parent::__construct();
		//$this->registerTask ("add", "edit");
		$this->registerTask ("add", "newAuthorStep1");
		$this->registerTask ("next", "newAuthorStep2");
		$this->registerTask ("edit", "editAuthor");

		$this->registerTask ("apply", "save");
		$this->registerTask ("", "listCustomers");
		$this->_model = $this->getModel('Customer');
		
	}

	function listCustomers() {
		$view = $this->getView("Customers", "html");
		$view->setModel($this->_model, true);
		$view->display();
	}

	function newAuthorStep1(){
		JRequest::setVar ("view", "Customers");
		$view = $this->getView("Customers", "html");
		$view->setLayout("settype");
		$view->setModel($this->_model, true);
		$username_value = JRequest::getVar("username", "","","string");
		$view->settypeform();
	}

	function newAuthorStep2(){
		$author_type 	= JRequest::getVar("author_type", 0,"","int");
		$username_value = JRequest::getVar("username", "", "", "string");

		if($author_type==1){
			if($this->_model->existUser($username_value)){
				if($this->_model->existNewAuthor($username_value)){
					$id = $this->_model->getCustomerId($username_value);
					$msg = JText::_("DIGI_EXIST_CUSTOMER_ERROR");
					$this->setRedirect('index.php?option=com_digicom&controller=customers&task=add', $msg, "notice");
				}
				else{
					$userid = $this->_model->getUserId($username_value);
					if(!empty($userid)){
						JRequest::setVar("id", $userid);
					}
					JRequest::setVar ("view", "Customers");
					$view = $this->getView("Customers", "html");
					$view->setLayout("editform");
					$view->setModel($this->_model, true);
					$view->editForm();
				}
			}
			else{
				$msg = JText::_("DIGI_NO_USER");
				$this->setRedirect( 'index.php?option=com_digicom&controller=customers&hidemainmenu=1&task=edit', $msg, "notice");
			}
		}
		else{
			JRequest::setVar ("view", "Customers");
			$view = $this->getView("Customers", "html");
			$view->setLayout("editform");
			$view->setModel($this->_model, true);
			$view->editForm();
		}
	}



	function editAuthor(){
		JRequest::setVar ("hidemainmenu", 1);
		$view = $this->getView("Customers", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);
		$model = $this->getModel("Config");
		$view->setModel($model);
		$view->editForm();
	}

	function save(){
		$error = "";
		$username = JRequest::getVar("username", "");
		if($this->_model->existUser($username) !== TRUE){
			if($this->_model->store($error)){
				$msg = JText::_('CUSTSAVED');
				$keyword = JRequest::getVar("keyword", "", "request");
				if(JRequest::getVar('task','') == 'save'){
					$link = "index.php?option=com_digicom&controller=customers".(strlen(trim($keyword)) > 0 ? "&keyword=".$keyword:"");
				}
				else{
					$cust_id = JRequest::getVar('id','');
					$link = "index.php?option=com_digicom&controller=customers&task=edit&cid[]=" . $cust_id;
				}
			}
			else{
				$msg = JText::_('CUSTSAVEFAILED');
				$msg .= " " . JText::_($error);
				$link = "index.php?option=com_digicom&controller=customers&task=add";
			}
			$this->setRedirect($link, $msg);
		}
		else{
			$link = "index.php?option=com_digicom&controller=customers&task=add";
			$msg = JText::_("DIGI_USER_IN_JOOMLA_EXIST");
			$this->setRedirect($link, $msg, "notice");
		}
	}


	function remove () {
		if (!$this->_model->delete()) {
			$msg = JText::_('CUSTENTREMODEFAILED');
		} else {
		 	$msg = JText::_('CUSTENTREMODESUCC');
		}

		$keyword = JRequest::getVar("keyword", "", "request");
		$link = "index.php?option=com_digicom&controller=customers".(strlen(trim($keyword)) > 0?"&keyword=".$keyword:"");

		$this->setRedirect($link, $msg);
	}

	function cancel () {
	 	$msg = JText::_('CUSTCANCELED');
		$keyword = JRequest::getVar("keyword", "", "request");
		$link = "index.php?option=com_digicom&controller=customers".(strlen(trim($keyword)) > 0?"&keyword=".$keyword:"");

		$this->setRedirect($link, $msg);
	}

	function publish () {

		$res = $this->_model->publish();

		if (!$res) {
			$msg = JText::_('CUSTBLOCKINGCANCELED');
		} elseif ($res == -1) {
		 	$msg = JText::_('CUSTBLOCKHSUCC');
		} elseif ($res == 1) {
			$msg = JText::_('CUSTUNBLOCKHSUCC');
		} else {
				 	$msg = JText::_('CUSTUNSPECERROR');
		}

		$link = "index.php?option=com_digicom&controller=customers";
		$this->setRedirect($link, $msg);
	}

}