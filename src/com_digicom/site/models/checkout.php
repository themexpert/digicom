<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class DigiComModelCheckout extends JModelItem
{
	public $orders 		= array();
	public $configs 	= array();
	public $customer 	= array();
	
	function __construct()
	{
		parent::__construct();
		$this->configs = JComponentHelper::getComponent('com_digicom')->params;
		$this->customer = new DigiComSiteHelperSession();

	}

	public function getOrder(){
		$input = JFactory::getApplication()->input; 
		$order_id = $input->get('id','0');
		if(!isset( $this->orders[$order_id] )) {
			$db 	= JFactory::getDbo();
			$sql 	= 'SELECT * FROM `#__digicom_orders` WHERE `id`='.$order_id;
			$db->setQuery($sql);
			$order 	= $db->loadObject();
			$this->orders[$order_id]=$order;
		}
		return $this->orders[$order_id];
	}

}
