<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 341 $
 * @lastmodified	$LastChangedDate: 2013-10-10 14:28:28 +0200 (Thu, 10 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

defined ('_JEXEC') or die ("Go away.");

/*
 * Instantiating new object of our plugin class.
 */

/*
 * Adding our plugin to the plugins list.
 */
//HandleDigiComPlugins::registerPlugin ($plugin);

/*
 * Plugin class. Should contain all necessary methods for handling
 * payment routins for given paymnt system.
 * In general such class should have following methods:
 *  getBEData - retuns an array with be plugin data (header, payment option name 
 *				and its value).
 *  getFEData - returns html output for fe payment form.
 *  notify - processes responce from payment system to determine if
 *			 transaction was successfull.
 * selfile - required for proper plugin handling. In current version should return 
 *			 filename of plugin.
 *	  
 * 
 */
 
class offline {

	function selfile () {
			 return "offline_payment.php";	   
	}
	
	function type () {
		return "payment";
	}
	
	function getBEData ($plugin_conf) {   }

	function insert_currency() {
	
		global $database;
		
		$currencies = array (
			'A111AA' => 'TEST111'
		);
		return $currencies;		
		//echo $database->getQuery(); die;
	}	
	
	function deleteCurrency() {
	 
			
	   $database = JFactory::getDBO();
			
		$sql = "DELETE FROM #__digicom_currencies WHERE plugname='" . get_class($this) . "'";
		$database->setQuery($sql);
		$database->query();
	
		//echo $database->getQuery(); die;
		//echo $database->getQuery(); die;

	}

	function getFEData($items, $tax, $redir, $profile, $plugin, $configs) {

		$mosConfig_live_site = DigiComHelper::getLiveSite();
	   	$cust_info = $profile;	  

	  	$sid = $profile->_sid;
		$_Itemid = $profile->_Itemid;
		$uid = $profile->_user->id;	  

		$content =	'<br />
			<form action="'.$mosConfig_live_site.'/index.php?option=com_digicom&controller=cart&task=returnPayment&plugin=offline&sid='.$sid.'&Itemid='.$_Itemid.'" method="post" name="paymentForm1"></form>
			<script>
				document.paymentForm1.submit();
			</script>
		';
		@session_start();
		$_SESSION['offline_trans'] = 1;

		return $content;	
		
	}
	
	function notify () { }	

	function return1 ($plugin_conf, $cart, $configs, $plugin_handler) {

		$sid = JRequest::getVar("sid", "", 'request');

		$orderid = 0;
		
		if ($_SESSION['offline_trans'] == 1) {
			$customer = $plugin_handler->loadCustomer($sid);
			$items = $cart->getCartItems( $customer, $configs );
		 
			$tax = $cart->calc_price($items, $customer, $configs);

			$non_taxed = $tax['total'];//$total;
			$total = $tax['taxed'];
			$taxa = $tax['value'];
			$shipping = $tax['shipping'];
			$currency = $tax['currency'];
			$licenses = $tax['licenses'];
			
			$now = time();					
			$status = "Pending";

			$orderid = $plugin_handler->addOrder($items, $customer, $now, 'offline', $status);
			$plugin_handler->addLicenses($items, $orderid, $now, $customer, $status);				 
		}
		
		$_SESSION['offline_trans'] = 0;
		  
		$plugin_handler->goToSuccessURL($sid, 'Transaction Success', $orderid);
	}

	function return2 ($plugin_conf, $cart, $configs, $plugin_handler) {
		$sid = JRequest::getVar("sid", "", 'request');
		$plugin_handler->goToFailedURL($sid, 'Transaction Fail', $orderid);
	}

	function get_info () {
		$info = _PLUGIN_offline_PAYMENT;
		return $info;

	}
};
?>