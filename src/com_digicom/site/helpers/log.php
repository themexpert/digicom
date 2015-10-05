<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

JTable::addIncludePath(JPATH_SITE . '/components/com_digicom/tables', 'Table');
class DigiComSiteHelperLog {

    /*
     * ip, userip
     */
    protected $ip;

    /*
     * setLog method
     * use to trigger event to set log
     * @type = download,email,purchase,status,payment
     * type : status is any change event
     * type : payment is payment status active
     * @callback = from where event triggered or who did it
     * @message = short message about the event
     * @status = quick status about log event, if its complted or not
     * @info = log details or extra info encoded by json formet
     * setLog($type, $hook, $message, $info, $status = 'complete');
     * */
    public static function setLog($type, $hook, $callbackid, $message, $info, $status = 'complete')
    {
        $logTable = JTable::getInstance('log','Table');
        $logTable->type     = $type;
        $logTable->callback = $hook;
        $logTable->callbackid = $callbackid;
        $logTable->message  = $message;
        $logTable->params     = json_encode($info);
        $logTable->status   = $status;
        $logTable->ip       = DigiComSiteHelperLog::get_ip();

        //print_r($logTable);die;
        $logTable->store();

        return true;

    }

    /*
    * method getLog
    * check if any log for specific event with callback n callbackid match
    */

    public static function getLog($callback, $callbackid, $status = 'Active', $type = 'payment')
    {

      $db = JFactory::getDBO();
  		$query = $db->getQuery(true);
  		$query->select('*')
  		      ->from($db->quoteName('#__digicom_log'))
        		->where($db->quoteName('type') . ' = '. $db->quote($type))
        		->where($db->quoteName('callback') . ' = '. $db->quote($callback))
        		->where($db->quoteName('callbackid') . ' = '. $db->quote($callbackid))
        		->where($db->quoteName('status') . ' = '. $db->quote($status));

  		// Reset the query using our newly populated query object.
  		$db->setQuery($query);

  		return $db->loadObject();

    }

    /*
   * get the ip
   */
    public static function get_ip() {
        //Just get the headers if we can or else use the SERVER global
        if ( function_exists( 'apache_request_headers' ) ) {
            $headers = apache_request_headers();
        } else {
            $headers = $_SERVER;
        }
        //Get the forwarded IP if it exists
        if ( array_key_exists( 'X-Forwarded-For', $headers ) && filter_var( $headers['X-Forwarded-For'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
            $the_ip = $headers['X-Forwarded-For'];
        } elseif ( array_key_exists( 'HTTP_X_FORWARDED_FOR', $headers ) && filter_var( $headers['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 )
        ) {
            $the_ip = $headers['HTTP_X_FORWARDED_FOR'];
        } else {

            $the_ip = filter_var( $_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 );
        }
        return $the_ip;
    }

}
