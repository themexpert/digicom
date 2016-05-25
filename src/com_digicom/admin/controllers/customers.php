<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

jimport ('joomla.application.component.controller');

class DigiComControllerCustomers extends JControllerAdmin {
	var $model = null;

	function __construct () {
		
		parent::__construct();
		$this->registerTask ("edit", "editAuthor");
		$this->registerTask ("apply", "save");
		$this->_model = $this->getModel('Customer');
		
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