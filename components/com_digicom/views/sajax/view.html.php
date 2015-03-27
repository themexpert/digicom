<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class DigiComViewSAjax extends JViewLegacy {

	function display ($tpl =  null) {
		require_once( JPATH_COMPONENT.DS.'helpers'.DS.'sajax.php' );

		parent::display($tpl);

	}


}