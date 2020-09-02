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

	public function getOrderItems(){
		$input = JFactory::getApplication()->input; 
		$order_id = $input->get('id','0');

		$db 	= JFactory::getDbo();
		$sql 	= 'SELECT `p`.*, `od`.quantity FROM
					`#__digicom_products` AS `p`
						INNER JOIN
					`#__digicom_orders_details` AS `od` ON (`od`.`productid` = `p`.`id`)
				WHERE `orderid` ='.$order_id;

		$db->setQuery($sql);
		$items = $db->loadObjectList();
		// print_r($items);die;

		$configs = $this->getConfigs();
		//change the price of items if needed
		for ( $i = 0; $i < count( $items ); $i++ )
		{
			$item = &$items[$i];
			$item->discount = 0;
			$item->currency = $configs->get('currency','USD');
			$item->price = DigiComSiteHelperPrice::format_price( $item->price, $item->currency, false, $configs ); //sprintf( $price_format, $item->product_price );
			$item->price_formated = $item->price;
			$item->subtotal = $item->price * $item->quantity;
		}

		return $items ;
	
	}

	function getConfigs() {
		$comInfo = JComponentHelper::getComponent('com_digicom');
		return $comInfo->params;
	}

}
