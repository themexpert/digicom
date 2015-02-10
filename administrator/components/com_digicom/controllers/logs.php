<?php

/**

 *
 * @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 341 $
 * @lastmodified	$LastChangedDate: 2013-10-10 14:28:28 +0200 (Thu, 10 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
 * @license			GNU/GPLv3 */
defined( '_JEXEC' ) or die( "Go away." );

jimport( 'joomla.application.component.controller' );

class DigiComAdminControllerLogs extends DigiComAdminController{

	var $_model = null;

	function __construct(){
		parent::__construct();
		$this->registerTask("systememails", "systememails");
		$this->registerTask("download", "download");
		$this->registerTask("editEmail", "editEmail");
		$this->registerTask("", "purchases");
		$this->registerTask("purchases", "purchases");
		$this->_model = $this->getModel("Logs");
		$this->_config = $this->getModel("Config");
		$this->_license = $this->getModel("License");
	}

	function systememails(){
		$view = $this->getView("Logs", "html" );
		$view->setModel($this->_model, true);
		$view->setModel($this->_config);
		$view->setModel($this->_license);
		$view->setLayout("systememails");
		$view->display();
	}

	function download(){
		$view = $this->getView("Logs", "html" );
		$view->setModel($this->_model, true);
		$view->setModel($this->_config);
		$view->setModel($this->_license);
		$view->setLayout("download");
		$view->display();
	}

	function purchases(){
		$view = $this->getView("Logs", "html" );
		$view->setModel($this->_model, true);
		$view->setModel($this->_config);
		$view->setModel($this->_license);
		$view->setLayout("purchases");
		$view->display();
	}

	function editEmail(){
		$view = $this->getView("Logs", "html" );
		$view->setModel($this->_model, true);
		$view->setModel($this->_config);
		$view->setModel($this->_license);
		$view->setLayout("editemail");
		$view->editEmail();
	}

};
?>