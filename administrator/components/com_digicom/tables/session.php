<?php
/**
 * @package		DigiCom
 * @copyright	Copyright (c)2010-2015 ThemeXpert
 * @license 	GNU General Public License version 3, or later
 * @author 		ThemeXpert http://www.themexpert.com
 * @since 		1.0.0
 */

defined('_JEXEC') or die;


class TableSession extends JTable {
	var $sid = null;
	var $cart_details = null;
	var $transaction_details = null;
	var $create_time = null;

	function TableSession (&$db) {
		parent::__construct('#__digicom_session', 'sid', $db);
	}

};


?>