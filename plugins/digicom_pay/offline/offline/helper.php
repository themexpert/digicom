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
jimport( 'joomla.plugin.helper' );
class plgDigiCom_PayOfflineHelper
{ 

	function buildOfflineHelperUrl($secure = true)
	{

		return '';

	}

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
