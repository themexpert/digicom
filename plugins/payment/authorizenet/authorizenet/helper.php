<?php

jimport('joomla.html.html');
jimport( 'joomla.plugin.helper' );
class plgPaymentAuthorizenetHelper
{ 

	//gets the Authorizenet URL
	public static function buildAuthorizenetUrl($params = null)
	{
		$secure = $params->get('secure_post', true);
		$url	= '';
		$url_prefix = $params->get('sandbox') ? 'test.authorize.net' : 'secure.authorize.net';
		if ( $secure ) {
			$url .= 'https://'.$url_prefix.'/gateway/transact.dll' ;
		}
		else
		{
			$url .= 'http://'.$url_prefix.'/gateway/transact.dll' ;
		}
		return $url;	

	}

	public static function Storelog( $name, $logdata )
	{
		jimport('joomla.error.log');
		$options = array('format' => "{DATE}\t{TIME}\t{USER}\t{DESC}");
		if(JVERSION >='1.6.0')
		{
			$path=JPATH_SITE.'/plugins/payment/'.$name.'/'.$name.'/';
		} else {
			$path=JPATH_SITE.'/plugins/payment/'.$name.'/';
		}

		$my = JFactory::getUser();

		$log = 	array(
					'user' => $my->name.'('.$my->id.')',
					'desc'=>json_encode($logdata['raw_data'])
				);
		JLog::addLogger( $log );
	}
}