<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class TableMail extends JTable {
	public $id 		= null;
	public $date 	= null;
	public $email 	= null;
	public $body 	= null;
	public $flag 	= null;

	function TableMail (&$db) {
		parent::__construct('#__digicom_sendmails', 'id', $db);
	}

};


?>