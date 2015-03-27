<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class TableSession extends JTable {
	var $sid = null;
	var $cart_details = null;
	var $transaction_details = null;
	var $shipping_details = null;
	var $create_time = null;

	function TableSession (&$db) {
		parent::__construct('#__digicom_session', 'sid', $db);
	}

}
