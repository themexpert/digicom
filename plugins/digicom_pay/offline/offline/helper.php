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
	public static function Storelog($name,$data)
	{
		$my = JFactory::getUser();
		jimport('joomla.log.log');
		JLog::addLogger(
			 array(
						// Sets file name
						'text_file' => 'com_digicom.offline.errors.php'
			 ),
			 // Sets messages of all log levels to be sent to the file
			 JLog::ALL,
			 // The log category/categories which should be recorded in this file
			 // In this case, it's just the one category from our extension, still
			 // we need to put it inside an array
			 array('com_digicom.offline')
		 );
		 $msg = 'StoreLog >>  user:'.$my->name.'('.$my->id.'), desc: ' . json_encode($data['raw_data']);
		 JLog::add($msg, JLog::WARNING, 'com_digicom.offline');

	}



}
