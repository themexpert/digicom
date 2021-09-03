<?php
/**
 * @package        DigiCom
 * @author         ThemeXpert http://www.themexpert.com
 * @copyright      Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license        GNU General Public License version 3 or later; see LICENSE.txt
 * @since          1.0.0
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
    public static function buildPaymentSubmitUrl($secure_post = true, $sandbox = false)
    {
        $url = $sandbox ? 'www.sandbox.paypal.com' : 'www.paypal.com';
        if ($secure_post) {
            $url = 'https://'.$url.'/cgi-bin/webscr';
        } else {
            $url = 'http://'.$url.'/cgi-bin/webscr';
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
    public static function buildIPNPaymentUrl($secure_post = true, $sandbox = false)
    {
        $url = $sandbox ? 'ipnpb.sandbox.paypal.com' : 'ipnpb.paypal.com';
        if ($secure_post) {
            $url = 'https://'.$url.'/cgi-bin/webscr';
        } else {
            $url = 'http://'.$url.'/cgi-bin/webscr';
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

        $message  = 'Transaction added for '.$my->name.'('.$my->id.')'.'. RawData: '.json_encode($data);
        $logEntry = new JLogEntry($message, JLog::INFO, 'com_digicom.paypal');

        JLog::add($logEntry);
    }

    /**
     * ValidateIPN - Validate the payment detail. (We are thankful to Akeeba Subscriptions Team,
     *
     * @param $data
     * @param  bool  $sandbox
     * @param $params
     * @param  string  $componentName
     *
     * @return bool True on success
     *
     * @params $params
     * @params $componentName
     *
     * @source https://developer.paypal.com/docs/api-basics/notifications/ipn/ht-ipn/
     *
     * @since   1.0.0
     */
    public static function validateIPN($data, $sandbox = false, $params, $componentName = 'digicom')
    {
        $url    = plgDigiCom_PayPaypalHelper::buildIPNPaymentUrl(true, $sandbox);

        // STEP 1: read POST data
        // Reading POSTed data directly from $_POST causes serialization issues with array data in the POST.
        // Instead, read raw POST data from the input stream.
        $raw_post_data  = file_get_contents('php://input');
        $raw_post_array = explode('&', $raw_post_data);
        $myPost         = array();
        foreach ($raw_post_array as $keyval) {
            $keyval = explode('=', $keyval);
            if (count($keyval) == 2) {
                $myPost[$keyval[0]] = urldecode($keyval[1]);
            }
        }
        // read the IPN message sent from PayPal and prepend 'cmd=_notify-validate'
        $req = 'cmd=_notify-validate';
        if (function_exists('get_magic_quotes_gpc')) {
            $get_magic_quotes_exists = true;
        }
        foreach ($myPost as $key => $value) {
            if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
                $value = urlencode(stripslashes($value));
            } else {
                $value = urlencode($value);
            }
            $req .= "&$key=$value";
        }

        self::Storelog("paypal", ['step' => 'validateIPN start', 'req' => $req, 'url' => $url]);

        try {
            // Step 2: POST IPN data back to PayPal to validate
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
            // In wamp-like environments that do not come bundled with root authority certificates,
            // please download 'cacert.pem' from "https://curl.haxx.se/docs/caextract.html" and set
            // the directory path of the certificate as shown below:
            // curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/cacert.pem');
            if ( ! ($response = curl_exec($ch))) {
                error_log("Got ".curl_error($ch)." when processing IPN data");
                self::Storelog("paypal", ['step' => 'curl_exec_error', 'message' => "Got ".curl_error($ch)." when processing IPN data"]);
                curl_close($ch);
                exit;
            }
            curl_close($ch);

            // inspect IPN validation result and act accordingly
            // now verify response
            if (strcmp($response, "VERIFIED") == 0) {
                // The IPN is verified, process it
                $status = true;
            } else {
                // if(strcmp ($response, "INVALID") == 0)
                if ($params->get('bypass_validation', 0) == '1') {
                    // 7'th march 2018
                    // tmp solution
                    if ($data['payment_status'] == 'Completed') {
                        $status = true;
                        self::Storelog("paypal", ['step' => 'force validation', 'payment_status' => $data['payment_status'], 'response' => $response]);
                    } else {
                        $status = false;
                    }
                } else {
                    // IPN invalid, log for manual investigation
                    $status = false; // commented for now
                }
            }

            $data['CURL_response']  = ['code' => $response['code'], 'raw_payment_status' => $data['payment_status'], 'response' => $response];
            $data['digicom_status'] = $status;

            $logData              = array();
            $logData["JT_CLIENT"] = $componentName;
            $logData["raw_data"]  = $data;

            self::Storelog("paypal", ['step' => 'validateIPN end', 'log' => $logData]);

            return [$status, $logData];

        } catch (\Exception $e) {
            // An unexpected error in processing; don't let this failure kill the site
            // throw new \RuntimeException('Unexpected error connecting to statistics server: ' . $e->getMessage(), 500);
            self::Storelog("paypal", ['step' => 'curl Exception', 'msg' => $e->getMessage()]);
            $status = false;

        }
    }


    /**
     * ValidateIPN - Validate the payment detail. (We are thankful to Akeeba Subscriptions Team,
     *
     * @param $data
     * @param $sandbox
     * @params $params
     * @params $componentName
     *
     * @return bool True on success
     *
     * @since   1.0.0
     */
    public function validateIPN_x($data, $sandbox = false, $params, $componentName = 'digicom')
    {
        $version    = new \JVersion;
        $httpOption = new \Joomla\Registry\Registry;
        $status     = true;
        $url        = plgDigiCom_PayPaypalHelper::buildIPNPaymentUrl(true, $sandbox);

        $myPost = $data;
        // read the IPN message sent from PayPal and prepend 'cmd=_notify-validate'
        $req = 'cmd=_notify-validate';
        if (function_exists('get_magic_quotes_gpc')) {
            $get_magic_quotes_exists = true;
        }
        foreach ($myPost as $key => $value) {
            if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
                $value = urlencode(stripslashes($value));
            } else {
                $value = urlencode($value);
            }
            $req .= "&$key=$value";
        }

        self::Storelog("paypal", ['step' => 'validateIPN start', 'req' => $req, 'url' => $url]);

        $headers = [
            'user-agent' => $componentName.';',
            'Accept'     => 'application/json',
        ];

        try {
            $http = \JHttpFactory::getHttp($httpOption);

            // limit the request timeout to 2 sec, to avoid server timeout issue
            $response = $http->post($url, [
                'body'    => $req,
                'cookies' => [],
            ], $headers, 2);

        } catch (\UnexpectedValueException $e) {
            // There was an error sending stats. Should we do anything?
            // throw new \RuntimeException('Could not send site statistics to remote server: ' . $e->getMessage(), 500);
            self::Storelog("paypal", ['step' => 'UnexpectedValueException', 'msg' => $e->getMessage()]);
            $status = false;

        } catch (\RuntimeException $e) {
            // There was an error connecting to the server or in the post request
            // throw new \RuntimeException('Could not connect to statistics server: ' . $e->getMessage(), 500);
            self::Storelog("paypal", ['step' => 'RuntimeException', 'msg' => $e->getMessage()]);
            $status = false;

        } catch (\Exception $e) {
            // An unexpected error in processing; don't let this failure kill the site
            // throw new \RuntimeException('Unexpected error connecting to statistics server: ' . $e->getMessage(), 500);
            self::Storelog("paypal", ['step' => 'Exception', 'msg' => $e->getMessage()]);
            $status = false;

        }

        if ($response === null || $response->code !== 200) {
            // TODO: Add a 'mark bad' setting here somehow
            // \JLog::add(\JText::_('Could not send site statistics to remote server.'), \JLog::WARNING, 'jerror');
            self::Storelog("paypal", ['step' => 'response null', 'details' => json_encode($response)]);
            $status = false;
        }

        // now verify response
        if (strcmp($response, "VERIFIED") == 0) {
            // The IPN is verified, process it
            $status = true;
        } elseif (strcmp($response, "INVALID") == 0) {
            if ($params->get('bypass_validation', false)) {
                // 7'th march 2018
                // tmp solution
                if ($data['payment_status'] == 'Completed') {
                    $status = true;
                } else {
                    $status = false;
                }
            } else {
                // IPN invalid, log for manual investigation
                $status = false; // commented for now
            }
        }

        $data['CURL_response']  = ['code' => $response['code'], 'body' => $response['raw_payment_status'], '' => $data['payment_status']];
        $data['digicom_status'] = $status;

        $logData              = array();
        $logData["JT_CLIENT"] = $componentName;
        $logData["raw_data"]  = $data;

        self::Storelog("paypal", ['step' => 'validateIPN end', 'log' => $logData]);

        return [$status, $logData];
    }

    public static function preparePostData()
    {
        // STEP 1: read POST data
        // Reading POSTed data directly from $_POST causes serialization issues with array data in the POST.
        // Instead, read raw POST data from the input stream.
        $raw_post_data  = file_get_contents('php://input');
        $raw_post_array = explode('&', $raw_post_data);
        $myPost         = array();
        foreach ($raw_post_array as $keyval) {
            $keyval = explode('=', $keyval);
            if (count($keyval) == 2) {
                $myPost[$keyval[0]] = urldecode($keyval[1]);
            }
        }

        return $myPost;
    }

}
