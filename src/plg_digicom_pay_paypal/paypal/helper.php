<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;
jimport('joomla.html.html');
jimport('joomla.plugin.helper');

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
		$url = $sandbox? 'ipnpb.sandbox.paypal.com' : 'ipnpb.paypal.com';
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
	public static function Storelog($name, $data)
	{
		$my = JFactory::getUser();
		
		jimport('joomla.error.log');
		JLog::addLogger(
			array(
					// Sets file name
					'text_file' => 'com_digicom.paypal.errors.php'
			),
			// Sets messages of all log levels to be sent to the file
			JLog::INFO,
			// The log category/categories which should be recorded in this file
			// In this case, it's just the one category from our extension, still
			// we need to put it inside an array
			array('com_digicom.paypal')
		);

		$message = 'Transaction added for ' . $my->name . '(' . $my->id . ')' . '. RawData: ' . json_encode($data['raw_data']);
		$logEntry       = new JLogEntry($message , JLog::INFO, 'com_digicom.paypal');

		JLog::add($logEntry);
	}


	/**
	 * ValidateIPN - Validate the payment detail. (We are thankful to Akeeba Subscriptions Team,
	 * while modifing the plugin according to paypal security update. https://github.com/paypal/TLS-update#php
	 * Security update links: https://devblog.paypal.com/upcoming-security-changes-notice/
	 * https://developer.paypal.com/docs/classic/ipn/ht_ipn/
	 * 
	 * @param   string  $data           data
	 * @param   string  $componentName  Component Name
	 *
	 * @since   2.2
	 *
	 * @return   string  data
	 */
	public function validateIPN($data, $sandbox = false, $params, $componentName = 'digicom')
	{

		$url              = plgDigiCom_PayPaypalHelper::buildIPNPaymentUrl(true, $sandbox);
		$newData = array(
			'cmd'	=> '_notify-validate'
		);
		$newData = array_merge($newData, $data);

		$options = array(
			CURLOPT_SSL_VERIFYPEER => true,
			CURLOPT_SSL_VERIFYHOST => 2,
			CURLOPT_VERBOSE        => false,
			CURLOPT_HEADER         => false,
			CURLINFO_HEADER_OUT    => false,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_CAINFO         => dirname(__FILE__) . '/cacert.pem',
			CURLOPT_HTTPHEADER     => array('User-Agent: Digicom','Connection: Close'),
			CURLOPT_POST           => true,
			CURLOPT_POSTFIELDS     => $newData,
			CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
			CURLOPT_CONNECTTIMEOUT  => 30,
			// Force the use of TLS (therefore SSLv3 is not used, mitigating POODLE; see https://github.com/paypal/merchant-sdk-php)
			CURLOPT_SSL_CIPHER_LIST => 'TLSv1',
			// This forces the use of TLS 1.x
			CURLOPT_SSLVERSION      => CURL_SSLVERSION_TLSv1,

		);

		/*
		 TLS 1.2 is only supported in OpenSSL 1.0.1c and later AND cURL 7.34.0 and later running on PHP 5.5.19+ or
		 PHP 5.6.3+. If these conditions are met we can use PayPal's minimum requirement of TLS 1.2 which is mandatory
		 since June 2016.
		*/
		$curlVersionInfo   = curl_version();
		$curlVersion       = $curlVersionInfo['version'];
		$openSSLVersionRaw = $curlVersionInfo['ssl_version'];

		// OpenSSL version typically reported as "OpenSSL/1.0.1e", I need to convert it to 1.0.1.5
		$parts             = explode('/', $openSSLVersionRaw, 2);
		$openSSLVersionRaw = (count($parts) > 1) ? $parts[1] : $openSSLVersionRaw;
		$openSSLVersion    = substr($openSSLVersionRaw, 0, -1) . '.' . (ord(substr($openSSLVersionRaw, -1)) - 96);

		// PHP version required for TLS 1.2 is 5.5.19+ or 5.6.3+
		$minPHPVersion = version_compare(PHP_VERSION, '5.6.0', 'ge') ? '5.6.3' : '5.5.19';

		$curlVerStatus = version_compare($curlVersion, '7.34.0', 'ge');

		if (!$curlVerStatus ||  ! version_compare($openSSLVersion, '1.0.1.3', 'ge') || 	! version_compare(PHP_VERSION, $minPHPVersion, 'ge'))
		{
			$phpVersion = PHP_VERSION;
			$data['ipncheck_envoirnmen_warning'] = "WARNING! PayPal demands that connections be made with TLS 1.2.
				This requires PHP $minPHPVersion+
				(you have $phpVersion), libcurl 7.34.0+ (you have $curlVersion) and OpenSSL 1.0.1c+ (you have
				$openSSLVersionRaw) on your server's PHP. Please upgrade these requirements to meet the stated
				minimum or the PayPal integration will cease working.";
		}

		$ch = curl_init($url);
		curl_setopt_array($ch, $options);
		@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

		$response = curl_exec($ch);
		$errNo = curl_errno($ch);
		$error = curl_error($ch);
		$lastHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		curl_close($ch);
		$status = false;
		
		if (($errNo > 0) && !empty($error))
		{
			$data['ipncheck_failure_got_error'] = "Could not open SSL connection to $hostname:443, cURL error $errNo: $error";

			$status = false;
		}

		if ($lastHttpCode >= 400)
		{
			$data['ipncheck_failure'] = "Invalid HTTP status $lastHttpCode verifying PayPal's IPN";

			$status = false;
		}

		// now verify response 
		if (strcmp ($response, "VERIFIED") == 0)
		{
			// The IPN is verified, process it
			$status = true;
		}
		else if (strcmp ($response, "INVALID") == 0)
		{
			if($params->get('bypass_validation', false))
			{
				// 7'th march 2018
				// tmp solution
				if($data['payment_status'] == 'Completed'){
					$status = true;
				}else{
					$status = false;				
				}

			}
			else
			{
				// IPN invalid, log for manual investigation
				$status = false; // commented for now
			}
		}
		
		$data['CURL_response'] = $response;
		$data['digicom_status'] = $status;

		$logData = array();
		$logData["JT_CLIENT"] = $componentNamel;
		$logData["raw_data"] = $data;

		self::Storelog("paypal", $logData);
		
		return [$status, $logData];
	}

}
