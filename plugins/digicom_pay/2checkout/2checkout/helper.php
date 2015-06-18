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
	
	function validateIPN($data,$secret)
	{

		$incoming_md5 = strtoupper($data['md5_hash']);
		$calculated_md5 = md5(
			$data['sale_id'].
			$data['vendor_id'].
			$data['invoice_id'].
			$secret
		);
		$calculated_md5 = strtoupper($calculated_md5);

		return ($calculated_md5 == $incoming_md5);

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
