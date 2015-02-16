<?php

/**

 *
 * @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 341 $
 * @lastmodified	$LastChangedDate: 2013-10-10 19:28:28 +0700 (Thu, 10 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
 * @license			GNU/GPLv3 */
defined( '_JEXEC' ) or die( "Go away." );

jimport( 'joomla.application.component.controller' );

class DigiComAdminControllerTools extends DigiComAdminController
{
	public $packages = array();
	
	public $orders = array();

	public $_model = null;

	function __construct()
	{
		parent::__construct();
		$this->registerTask( "", "start" );
	}

	function start()
	{
		# Grabber DEV 				252
		# obSocialSubmit SI DEV 	258
		# obSocialSubmit MI DEV 	257
		$db = JFactory::getDbo();
		$orderid = JRequest::getVar('orderid');
		if(!$orderid){
			echo '<h2>orderid: null</h2>';
			exit();
		}
		$productid = JRequest::getVar('productid');
		if(!$productid) {
			exit('<h2>productid: null</h2>');
		}
		$planid = JRequest::getVar('planid','');
		
		$product = $this->getProduct($productid, $planid);
		$this->removeOldLicense( $orderid, $productid );
		$this->createLicense($orderid, $product, null, true );
	}
	
	public function getProduct( $productid, $planid=null ) {
		$db = JFactory::getDbo();
		if(!$productid) { return null; }
		$sql = 'SELECT * FROM #__digicom_products WHERE id='.$productid;
		$db->setQuery($sql);
		$res = $db->loadObject();
		$sql = '';
		if($res->product_type=='bundle'){
			$sql = 'SELECT `p`.`id` as `productid` ,
						`pp`.`price` , 
						`p`.`product_type` , 
						`pl`.`duration_count`, 
						`pl`.`duration_type`,
						`pl`.`id` AS `plan_id`
					FROM `#__digicom_products` AS `p` 
							INNER JOIN
						`#__digicom_products_plans` AS `pp` ON `p`.`id`=`pp`.`product_id`
							INNER JOIN
						`#__digicom_plans` AS `pl` ON `pp`.`plan_id` = `pl`.`id`
					WHERE `p`.`id`='.$productid.'
					LIMIT 1';
		} else {
			if(!$planid) { return null; }
			$sql = 'SELECT `p`.`id` as `productid` ,
						`pp`.`price` , 
						`p`.`product_type` , 
						`pl`.`duration_count`, 
						`pl`.`duration_type`,
						`pl`.`id` AS `plan_id`
					FROM `#__digicom_products` AS `p` 
							INNER JOIN
						`#__digicom_products_plans` AS `pp` ON `p`.`id`=`pp`.`product_id`
							INNER JOIN
						`#__digicom_plans` AS `pl` ON `pp`.`plan_id` = `pl`.`id`
					WHERE `p`.`id`='.$productid.' 
						AND `pp`.`plan_id`='.$planid.'
					LIMIT 1';
		}
		$db->setQuery($sql);
		$product = $db->loadObject();
		return $product;
	
	}
	
	public function removeOldLicense($order_id, $product_id) {
		$app = JFactory::getApplication();
		$db = JFactory::getDbo();
		$sql = 'DELETE FROM `#__digicom_licenses` WHERE `orderid`='.$order_id.' AND `productid`='.$product_id;
		$db->setQuery($sql);
		$db->query();
		if($db->getErrorNum()){
			$msg = $db->getErrorMsg();
			$app->enqueuemessage($msg, 'error');
		}
	}
	
	/**
	 * Create license for product or package
	 */
	public function createLicense( $order_id, $product4sell, $user_id=null, $package_item=0 ) {
		if( $product4sell->product_type=='bundle' ) {
			$items = $this->getSubProduct($product4sell->productid);
			if( $items && count($items) ) {
				foreach( $items as $item ) {
					$this->createLicense( $order_id, $item,  $user_id, $product4sell->productid );
				}
			}
		} else {
			$this->createLicense2( $order_id, $product4sell, $user_id, $package_item );
		}
	}
	/**
	 * Create license for end product (not package)
	 */
	public function createLicense2( $order_id, $product, $user_id=null, $package_item=0 ){
		$db 	= JFactory::getDbo();
		$app 	= JFactory::getApplication();
		$order 		= $this->getOrder($order_id);
		$order_date = $order->order_date;
		$licenseid = $this->getNewLicenseId();
		$ltype = ($package_item)?'package_item':'common';
		if(!$user_id){
			$user_id = $order->userid;
		}
		$expires = "";
		$time_unit = array( 1=>'HOUR', 2=>'DAY', 3=>'MONTH', 4=>'YEAR' );
		if( $product->duration_type!=0 && $product->duration_count!= -1 ) {
			$expires = ' DATE_ADD(FROM_UNIXTIME('.$order->order_date.'), INTERVAL '.$product->duration_count.' '.$time_unit[$product->duration_type].') ';
		} else {
			$expires = ' "0000-00-00 00:00:00" ';
		}
		$sql = 'INSERT INTO `#__digicom_licenses`
					( `licenseid`, `userid`, `productid`, `domain`, `amount_paid`, `orderid`, `dev_domain`, `hosting_service`, `published`, `ltype`, `package_id`, `purchase_date`, `expires`, `renew`, `download_count`, `plan_id`)
						VALUES
					("'.$licenseid.'", '.$user_id.', '.$product->productid.', "", '.$product->price.', '.$order_id.', "", "", 1, "'.$ltype.'",'.$package_item.', FROM_UNIXTIME('.$order->order_date.'), '.$expires.', 0, 0, '.$product->plan_id.')';
		$db->setQuery($sql);
		$db->query();
		if($db->getErrorNum()){
			$app->enqueuemessage($db->getErrorMsg(), 'error');
		}else {
			echo '<h1>'.$licenseid.'</h1>';
		}
	}

	public function getSubProduct( $package_id ) {
		if( !isset( $this->packages[$package_id] ) ) {
			$db = JFactory::getDbo();
			$sql = 'SELECT  
						`f`.`featuredid` AS `productid` , 
						`pp`.`price` , 
						`p`.`product_type` , 
						`pl`.`duration_count`, 
						`pl`.`duration_type`,
						`pl`.`id` AS `plan_id`
						
					FROM `#__digicom_featuredproducts` AS  `f` 
							INNER JOIN  
						`#__digicom_products` AS  `p` ON  `f`.`featuredid` =  `p`.`id` 
							LEFT JOIN  
						`#__digicom_products_plans` AS `pp` ON (`f`.`featuredid` =`pp`.`product_id` AND `f`.`planid` = `pp`.`plan_id` )
							LEFT JOIN 
						`#__digicom_plans` AS `pl` ON `f`.`planid` = `pl`.`id`
					WHERE 
						`f`.`productid`='.$package_id;
			$db->setQuery($sql);
			$res = $db->loadObjectList();
			$this->packages[$package_id] = $res;
		}
		return $this->packages[$package_id];
	}

	public function getOrder( $order_id ){
		if(!isset( $this->orders[$order_id] )) {
			$db 	= JFactory::getDbo();
			$sql 	= 'SELECT * FROM `#__digicom_orders` WHERE `id`='.$order_id;
			$db->setQuery($sql);
			$order 	= $db->loadObject();
			$this->orders[$order_id]=$order;
		}
		return $this->orders[$order_id];
	}

	public function getNewLicenseId(){
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
}


?>