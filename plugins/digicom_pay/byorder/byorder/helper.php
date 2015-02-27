<?php
defined( '_JEXEC' ) or die( ';)' );
	jimport('joomla.html.html');
	jimport( 'joomla.plugin.helper' );
class plgplgDigiCom_PayByOrderHelper
{ 

	//gets the paypal URL
	function buildByorderHelperUrl($secure = true)
	{

		return '';

	}

	public static function Storelog($name,$logdata)
	{
		jimport('joomla.error.log');
	$options = array('format' => "{DATE}\t{TIME}\t{USER}\t{DESC}");
		if(JVERSION >='1.6.0')
		$path=JPATH_SITE.'/plugins/digicom_pay/'.$name.'/'.$name.'/';
		else
		$path=JPATH_SITE.'/plugins/digicom_pay/'.$name.'/';	  
	  $my = JFactory::getUser();		
		//$logs = &JLog::getInstance($logdata['JT_CLIENT'].'_'.$name.'.log',$options,$path);
	JLog::addLogger(array('user' => $my->name.'('.$my->id.')','desc'=>json_encode($logdata['raw_data'])));
	}



}
