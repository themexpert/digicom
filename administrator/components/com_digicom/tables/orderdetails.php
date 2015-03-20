<?php
/**
 * @package		DigiCom
 * @copyright	Copyright (c)2010-2015 ThemeXpert
 * @license 	GNU General Public License version 3, or later
 * @author 		ThemeXpert http://www.themexpert.com
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class TableOrderDetails extends JTable
{

	function TableOrderDetails( &$db )
	{
		parent::__construct( '#__digicom_orders_details', 'id', $db );
	}


}