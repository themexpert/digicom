<?php
defined( '_JEXEC' ) or die( ';)' );
	jimport('joomla.html.html');
	jimport( 'joomla.plugin.helper' );
class plgDigiCom_PayBycheckHelper
{ 

	//gets the paypal URL
	function buildAuthorizenetUrl($secure = true)
	{

		$secure_post = $this->params->get('secure_post');
		$url = $this->params->get('sandbox') ? 'test.authorize.net' : 'secure.authorize.net';
		if ($secure_post) 
			$url = 'https://'.$url.'/gateway/transact.dll' ;
		else
			$url = 'http://'.$url.'/gateway/transact.dll' ;

		return $url;

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
