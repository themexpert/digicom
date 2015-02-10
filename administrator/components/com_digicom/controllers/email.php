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

class DigiComAdminControllerEmail extends DigiComAdminController {

	var $_model = null;

	function __construct(){
		parent::__construct();
		require_once( JPATH_COMPONENT.DS.'helpers'.DS.'helper.php' );
		$this->registerTask ("", "EmailList");
		$this->registerTask ("apply", "save");
		
		$this->_model = $this->getModel("Email");
	}

	function EmailList(){
		
		$view = $this->getView("Email", "html");
		
		$view->setModel($this->_model, true);
		
		$view->display();
	}
	
	function save () {
		if ($this->_model->store() ) {

			$msg = JText::_('COM_DIGICOM_EMAIL_TEMPLATE_SAVED');
		} else {
			$msg = JText::_('COM_DIGICOM_EMAIL_TEMPLATE_FAILED');
		}

		if ( JRequest::getVar('task','') == 'save' ) {
			$link = "index.php?option=com_digicom";
		} else {
			$type = JRequest::getVar('type','');
			$link = "index.php?option=com_digicom&controller=email&type=" . $type;
		}

		$this->setRedirect($link, $msg);
	}
	
}
