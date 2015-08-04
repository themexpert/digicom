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
	* method onTP_Storelog
	* used to store log for plugin debug payment
	* @data : the necessary info recieved from form about payment
	* @return null
	*/

	public static function Storelog($name,$logdata)
	{
		jimport('joomla.log.log');
		
		$options = array('format' => "{DATE}\t{TIME}\t{USER}\t{DESC}");
		$path=JPATH_SITE.'/plugins/digicom_pay/'.$name.'/'.$name.'/';
		$my = JFactory::getUser();
		//$logs = &JLog::getInstance($logdata['JT_CLIENT'].'_'.$name.'.log',$options,$path);
		JLog::addLogger(array('user' => $my->name.'('.$my->id.')','desc'=>json_encode($logdata['raw_data'])));
	}



}
