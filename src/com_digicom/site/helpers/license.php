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
		$query 	= $db->getQuery(true);

		if($type == 'complete_order'){
			$fields = array(
			    $db->quoteName('active') . ' = 1'
			);
		}else{
			$fields = array(
			    $db->quoteName('active') . ' = 0'
			);
		}
		$conditions = array(
		    $db->quoteName('orderid') . ' = '.$order_id,
		    $db->quoteName('userid') . ' = ' . $customer
		);
		$query->update($db->quoteName('#__digicom_licenses'))->set($fields)->where($conditions);

		$db->setQuery($query);

		return $db->execute();
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
		if(is_numeric($product)){
			$productTable = JModelAdmin::getInstance( "Product", "DigiComModel" );
			$product = $productTable->getItem($product);
		}
		$expires = "";
		$time_unit = array( 'day'=>'DAY', 'month'=>'MONTH', 'year'=>'YEAR');//HOUR
		//echo $product->price_type;die;
		if( $product->price_type!=0 ) {
			$expires = ' DATE_ADD('.$db->quote($order->order_date).', INTERVAL '.$product->expiration_length.' '.$time_unit[$product->expiration_type].') ';
		} else {
			$expires = $db->quote('0000-00-00 00:00:00');
		}

		if($published == 'complete_order'){
			$active = 1;
		}else{
			$active = 0;
		}

		// Create a new query object.
		$query = $db->getQuery(true);

		// Insert columns.
		$columns = array('licenseid', 'orderid', 'userid', 'productid', 'purchase', 'expires', 'active');

		// Insert values.
		$values = array($licenseid, $order_id, $user_id, $product->id, $db->quote($order->order_date), $expires, $active);

		// Prepare the insert query.
		$query
		    ->insert($db->quoteName('#__digicom_licenses'))
		    ->columns($db->quoteName($columns))
		    ->values(implode(',', $values));
		//echo $query->__toString();die;
		$db->setQuery($query);
		$db->execute();
		if($db->getErrorNum()){
			$app->enqueuemessage($db->getErrorMsg(), 'error');
		}
		return true;
	}

	public static function getNewLicenseId(){
		$db 	= JFactory::getDbo();
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->select('max('.$db->quoteName('licenseid').')')
			  ->from($db->quoteName('#__digicom_licenses'))
			  ->where("CONCAT('',".$db->quoteName('licenseid')."*1) = ".$db->quoteName('licenseid'));
		//$sql 	= "SELECT max(licenseid) FROM `#__digicom_licenses` WHERE CONCAT('',`licenseid`*1)=`licenseid`";

		$db->setQuery( $query );
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
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->select('*')
			  ->from($db->quoteName('#__digicom_orders'))
			  ->where($db->quoteName('id') . " = ".$db->quote($order_id));
		//$sql 	= 'SELECT * FROM `#__digicom_orders` WHERE `id`='.$order_id;
		$db->setQuery($query);

		return $db->loadObject();
	}

	public static function getProductsByUser($userid, $paid = true , $current = true){
		// Create a new query object.
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		// Select required fields from the dashboard.
		$query->select('DISTINCT l.productid as productid')
					->select(array('p.name,p.catid,p.bundle_source,p.product_type as type'))
					->select(array('l.*','DATEDIFF(expires, now()) as dayleft'))
			  	->from($db->quoteName('#__digicom_products') . ' AS p');
		$query->join('inner', '#__digicom_licenses AS l ON l.productid = p.id');

		if($current){
			$query->where($db->quoteName('l.active') . ' = ' . 1);
		}

		if($paid){
			$freeproduct = DigiComSiteHelperLicense::getFreeProduct();
			if(!empty($freeproduct)){
				$query->where($db->quoteName('l.productid') . ' NOT IN (' . $freeproduct.')');
			}
		}

		$query->where($db->quoteName('l.userid') . ' = ' . $userid);
		$query->order($db->quoteName('p.name') . ' ASC');
		$db->setQuery($query);
		return $db->loadObjectList();

	}

	public static function getFreeProduct()
	{
	    $db = JFactory::getDBO();
	    $query = $db->getQuery(true);
	    $query
	        ->select('GROUP_CONCAT('.$db->quoteName('id').')')
	        ->from($db->quoteName('#__digicom_products'))
	        ->where($db->quoteName('price').' = '.$db->quote('0.00'))
	        ->group($db->quoteName('price'));
	    $db->setQuery($query);
	    $db->execute();
	    if($db->getNumRows()){
					return $db->loadResult();
	    }
	    return false;
	}

}
