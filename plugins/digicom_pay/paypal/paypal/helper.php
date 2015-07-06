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
	
	//gets the paypal URL
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

	public static function Storelog($name,$logdata)
	{

		jimport('joomla.error.log');
		$options = array('format' => "{DATE}\t{TIME}\t{USER}\t{DESC}");
		$path=JPATH_SITE.'/plugins/digicom-pay/'.$name.'/'.$name.'/';
	
		$my 	= JFactory::getUser();  
		JLog::addLogger(
			array(
				'user' => $my->name.'('.$my->id.')', 
				'desc' => json_encode($logdata['raw_data'])
			)
		);
		
	}

	public static function validateIPN( $data, $paypal_url = '')
	{
		// parse the paypal URL
		if(!$paypal_url){
			$paypal_url	=plgDigiCom_PayPaypalHelper::buildPaymentSubmitUrl();
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
		$fp=fopen(self::$ipn_log_file,'a');
		fwrite($fp, $text . "\n\n");
		fclose($fp);  // close file
	}
}
