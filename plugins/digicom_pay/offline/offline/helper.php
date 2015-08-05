<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;


class plgDigiCom_PayOfflineHelper
{ 
	/*
	* get the payment submit url
	* usefull for thurdparty
	* @secure_post = if you want https
	* @sandbox = if you use sandbox or demo or dev mode
	*/
	function buildPaymentSubmitUrl($secure_post = true, $sandbox = false )
	{

		return '';

	}

	/*
	* method Storelog
	* from onTP_Storelog
	* used to store log for plugin debug payment
	* @data : the necessary info recieved from form about payment
	* @return null
	*/
	public static function Storelog($name,$logdata)
	{

		jimport('joomla.log.log');

		$my 	= JFactory::getUser();
		JLog::addLogger(
			array(
				'user' => $my->name.'('.$my->id.')',
				'desc' => json_encode($logdata['raw_data']),
				// Sets file name
				'text_file' => 'com_digicom.pay.paypal.php',
				'text_entry_format' => '{DATE} {TIME} {USER} {DESC}'
			),
			// Sets messages of all log levels to be sent to the file
			JLog::ALL,
			array('com_digicom')
		);

	}



}
