<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class DigiComSiteHelperLicense {

	/*
	* prepare the licence area
	*/

	public static function addLicenceSubscription( $items, $user_id, $orderid, $type) {
		//print_r($items);die;
		if( $items && count($items) ) {
			foreach( $items as $key=>$item ) {
				self::createLicense( $orderid, $item, $user_id, $type );
			}
		}
	}

	/*
	* $order_id = orderid;
	* numof product
	* $items
	* customer
	* type, may complete_order by default
	*/
	public static function updateLicenses($order_id, $number_of_products = 0, $items, $customer , $type){

		$db 	= JFactory::getDbo();

		if($type == 'complete_order'){
			$sql = "UPDATE #__digicom_licenses SET active=1 WHERE orderid=" . $order_id . " and userid=" . $customer;
		}elseif($type == 'process_order'){
			$sql = "UPDATE #__digicom_licenses SET active=0 WHERE orderid=" . $order_id . " and userid=" . $customer;
		}else{
			$sql = "UPDATE #__digicom_licenses SET active='-1' WHERE orderid=" . $order_id . " and userid=" . $customer;
		}

		$db->setQuery($sql);

		return $db->query();
	}

	/**
	 * Create license for end product
	 */
	public static function createLicense( $order_id, $product, $user_id=null, $published ){

		$db 	= JFactory::getDbo();
		$app 	= JFactory::getApplication();
		$order 		= self::getOrder($order_id);
		$order_date = $order->order_date;
		$licenseid = self::getNewLicenseId();
		if(!$user_id){
			$user_id = $order->userid;
		}
		$expires = "";
		$time_unit = array( 'day'=>'DAY', 'month'=>'MONTH', 'year'=>'YEAR');//HOUR
		//echo $product->price_type;die;
		if( $product->price_type!=0 ) {
			$expires = ' DATE_ADD(FROM_UNIXTIME('.$order->order_date.'), INTERVAL '.$product->expiration_length.' '.$time_unit[$product->expiration_type].') ';
		} else {
			$expires = ' "0000-00-00 00:00:00" ';
		}

		if($published == 'complete_order'){
			$active = 1;
		}else{
			$active = 0;
		}

		$sql = 'INSERT INTO `#__digicom_licenses`
					( `licenseid`,`orderid`, `userid`, `productid`, `purchase`, `expires`, `active`)
						VALUES
					("'.$licenseid.'", '.$order_id.', '.$user_id.', '.$product->id.', FROM_UNIXTIME('.$order->order_date.'), '.$expires.', '.$active.')';
		$db->setQuery($sql);
		$db->query();
		if($db->getErrorNum()){
			$app->enqueuemessage($db->getErrorMsg(), 'error');
		}
	}

	public static function getNewLicenseId(){
		$db 	= JFactory::getDbo();
		$sql 	= "SELECT max(licenseid) FROM `#__digicom_licenses` WHERE CONCAT('',`licenseid`*1)=`licenseid`";
		$db->setQuery( $sql );
		$licenseid = $db->loadResult();
		if(isset($licenseid) && intval($licenseid) != "0"){
			$licenseid = intval($licenseid)+1;
		} else {
			$licenseid = 100000001;
		}
		return $licenseid;
	}
	public static function getOrder( $order_id ){
		$db 	= JFactory::getDbo();
		$sql 	= 'SELECT * FROM `#__digicom_orders` WHERE `id`='.$order_id;
		$db->setQuery($sql);

		return $db->loadObject();
	}
}
