<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 341 $
 * @lastmodified	$LastChangedDate: 2013-10-10 14:28:28 +0200 (Thu, 10 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license
*/

defined ('_JEXEC') or die ("Go away.");

jimport ('joomla.application.component.controller');

class DigiComControllerDashboard extends DigiComController {

	var $_model = null;
	var $_config = null;
	var $_product = null;

	function __construct(){
		parent::__construct();
		$this->registerTask ("", "dashboard");
	}

	function dashboard() {

	   	JRequest::setVar ("view", "dashboard");
		$view = $this->getView("dashboard", "html");
		$view->display();

	}

}

