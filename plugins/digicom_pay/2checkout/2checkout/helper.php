<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;
class plgDigiCom_Pay2CheckoutHelper
{ 

	//gets the paypal URL
	public static function buildPaymentSubmitUrl($secure_post = true, $sandbox = false )
	{
		$url = $sandbox ? 'https://sandbox.2checkout.com/checkout/purchase' : 'https://www.2checkout.com/checkout/purchase';
		return $url;
	}

	public static function Storelog($name,$data)
	{
		jimport('joomla.error.log');
		$options = array('format' => "{DATE}\t{TIME}\t{USER}\t{DESC}");
		$path=JPATH_SITE.'/plugins/digicom_pay/'.$name.'/'.$name.'/';
		
		$my = JFactory::getUser();
		JLog::addLogger(array('user' => $my->name.'('.$my->id.')','desc'=>json_encode($logdata['raw_data'])));
	
	}
	
	public static function validateIPN($data,$secret,$sid,$order_id)
	{

		$hashSecretWord = $secret; //2Checkout Secret Word
		$hashSid = $sid; //2Checkout account number
		$hashTotal = $data['total']; //Sale total to validate against
		$hashOrder = $data['order_id']; //2Checkout Order Number
		$StringToHash = strtoupper(md5($hashSecretWord . $hashSid . $hashOrder . $hashTotal));
		//echo $hashOrder;die;
		if ($StringToHash != $data['key']) {
			//$result = 'Fail - Hash Mismatch'; 
			return false;
		} else { 
			//$result = 'Success - Hash Matched';
			return true;
		}
		$result;
	}

	public static function checkTotalToPay($order_id){
		$db = JFactory::getDbo();

		return '100.00';
	}
	
	function log_ipn_results($success) {
	   
		if (!$this->ipn_log) return; 

		// Timestamp
		$text = '['.date('m/d/Y g:i A').'] - '; 

		// Success or failure being logged?
		if ($success){
			$text .= "SUCCESS!\n";  
		}else{
			$text .= 'FAIL: '.$this->last_error."\n";
		} 

		// Log the POST variables
		$text .= "IPN POST Vars from Paypal:\n";
		foreach ($this->ipn_data as $key=>$value) {
			$text .= "$key=$value, ";
		}

		// Log the response from the paypal server
		$text .= "\nIPN Response from Paypal Server:\n ".$this->ipn_response;
		// Write to log
		$fp=fopen($this->ipn_log_file,'a');
		fwrite($fp, $text . "\n\n");
		fclose($fp);  // close file
	}

}
