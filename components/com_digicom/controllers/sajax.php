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

class DigiComControllerSajax extends DigiComController {
	var $_model = null;

	function __construct () {

		parent::__construct();

		$this->registerTask ("", "phpchangeProvince");
		$this->registerTask ("edit", "phpchangeProvince");
		
	}
	function phpchangeProvince() {
		$view = $this->getView("Sajax", "html");
		$view->display(null);

	}


}
