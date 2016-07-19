<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

/*Cart Table Class*/

class TableCart extends JTable {
	var $cid = null;
	var $sid = null;
	var $item_id = null;
	var $userid = null;
	var $quantity = null;

	function __construct (&$db) {
		parent::__construct('#__digicom_cart', 'cid', $db);
	}

};


?>