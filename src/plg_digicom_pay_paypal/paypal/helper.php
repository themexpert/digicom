<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class plgDigiCom_PayPaypalHelper
{
	public static $ipn_data = array();
	public static $last_error = null;
	public static $ipn_response = null;
	public static $ipn_log = null;
	public static $ipn_log_file = null;

	/*
	* get the payment submit url
	* usefull for thurdparty
	* @secure_post = if you want https
	* @sandbox = if you use sandbox or demo or dev mode
	*/
	public static function buildPaymentSubmitUrl($secure_post = true, $sandbox = false )
	{
		$url = $sandbox? 'www.sandbox.paypal.com' : 'www.paypal.com';
		if ( $secure_post ){
			$url = 'https://' . $url . '/cgi-bin/webscr';
		}else{
			$url = 'http://' . $url . '/cgi-bin/webscr';
		}

		return $url;
	}

	/*
 	* According to https://www.paypal-knowledge.com/infocenter/index?page=content&id=FAQ1914&expand=true&locale=en_US		
 	* we are supposed to use www.paypal.com before June 30th, 2017.		
 	* As of October 20th, 2016 PayPal recommends using the ipnpb.paypal.com domain name
 	* @see 	https://www.paypal.com/au/webapps/mpp/ipn-verification-https			 
 	* @see  https://developer.paypal.com/docs/classic/ipn/integration-guide/IPNImplementation/#specs
 	* @see  https://github.com/paypal/ipn-code-samples/blob/master/php/PaypalIPN.php
 	*/
	public static function buildIPNPaymentUrl($secure_post = true, $sandbox = false )
	{
		$url = $sandbox? 'www.ipnpb.sandbox.paypal.com' : 'www.ipnpb.paypal.com';
		if ( $secure_post ){
			$url = 'https://' . $url . '/cgi-bin/webscr';
		}else{
			$url = 'http://' . $url . '/cgi-bin/webscr';
		}

		return $url;
	}
	

	/*
	* method Storelog
	* from onDigicom_PayStorelog
	* used to store log for plugin debug payment
	* @data : the necessary info recieved from form about payment
	* @return null
	*/
	public static function Storelog($name,$data)
	{
		$my = JFactory::getUser();
		jimport('joomla.log.log');
		JLog::addLogger(
			 array(
						// Sets file name
						'text_file' => 'com_digicom.paypal.errors.php'
			 ),
			 // Sets messages of all log levels to be sent to the file
			 JLog::ALL,
			 // The log category/categories which should be recorded in this file
			 // In this case, it's just the one category from our extension, still
			 // we need to put it inside an array
			 array('com_digicom.paypal')
		 );
		 $msg = 'StoreLog >>  user:'.$my->name.'('.$my->id.'), desc: ' . json_encode($data['raw_data']);
		 JLog::add($msg, JLog::WARNING, 'com_digicom.paypal');

	}

	/*
	* method validateIPN
	* from onDigicom_PayProcesspayment
	* used to validate the data recieved from payment is untouched
	* @data : the necessary info recieved from form about payment
	* @return null
	*/
	public static function validateIPN( $data, $paypal_url = '')
	{
		// parse the paypal URL
		if(!$paypal_url){
			$paypal_url	=plgDigiCom_PayPaypalHelper::buildIPNPaymentUrl();
		}
		$url_parsed = parse_url($paypal_url);

		// generate the post string from the _POST vars as-well-as load the
		// _POST vars into an arry so we can play with them from the calling
		// script.
		// append ipn command
		// open the connection to paypal
		$fp = fsockopen($url_parsed["host"],"80",$err_num,$err_str,30);
		// $fp = fsockopen ($this->paypal_url, 80, $errno, $errstr, 30);

		if(!$fp) {
			// could not open the connection.  If loggin is on, the error message
			// will be in the log.
			self::$last_error = 'fsockopen error no. '.$err_num.': '.$err_str;
			plgDigiCom_PayPaypalHelper::log_ipn_results(false);
			return false;
		} else {

			$post_string = '';
			foreach ($data as $field=>$value) {
				self::$ipn_data["$field"] = $value;
				$post_string .= $field.'='.urlencode(stripslashes($value)).'&';
			}
			$post_string.="cmd=_notify-validate";

			// Post the data back to paypal
			fputs($fp, "POST $url_parsed[path] HTTP/1.1\r\n");
			fputs($fp, "Host: $url_parsed[host]\r\n");
			fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
			fputs($fp, "Content-length: ".strlen($post_string)."\r\n");
			fputs($fp, "User-Agent: Digicom\r\n");
			fputs($fp, "Connection: close\r\n\r\n");
			fputs($fp, $post_string . "\r\n\r\n");

			// loop through the response from the server and append to variable
			while(!feof($fp)) {
				self::$ipn_response .= fgets($fp, 1024);
			}
			fclose($fp); // close connection
		}
		if (preg_match("/verified/",$post_string)) {
			// Valid IPN transaction.
			plgDigiCom_PayPaypalHelper::log_ipn_results(true);
			return true;
		} else {
			// Invalid IPN transaction.  Check the log for details.
			self::$last_error = 'IPN Validation Failed.';
			plgDigiCom_PayPaypalHelper::log_ipn_results(false);
			return false;
		}

	}

	/*
	 * method to log the result
	 * @success : responce
	 * */
	public static function log_ipn_results($success) {
		if (!self::$ipn_log) return;
		// Timestamp
		$text = '['.date('m/d/Y g:i A').'] - ';
		// Success or failure being logged?
		if ($success){
			$text .= "SUCCESS!\n";
		}else{
			$text .= 'FAIL: '.self::$last_error."\n";
		}

		// Log the POST variables
		$text .= "IPN POST Vars from Paypal:\n";
		foreach (self::$ipn_data as $key=>$value) {
			$text .= "$key=$value, ";
		}

		// Log the response from the paypal server
		$text .= "\nIPN Response from Paypal Server:\n ".self::$ipn_response;

		// Write to log
		//$fp=fopen(self::$ipn_log_file,'a');
		//fwrite($fp, $text . "\n\n");
		//fclose($fp);  // close file

		plgDigiCom_PayPaypalHelper::Storelog('paypal',$text . "\n\n");

	}
}
