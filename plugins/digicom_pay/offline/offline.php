<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;
//Import filesystem libraries. Perhaps not necessary, but does not hurt
jimport('joomla.filesystem.file');

$lang = JFactory::getLanguage();
$lang->load('plg_digicom_pay_offline', JPATH_ADMINISTRATOR);
require_once(JPATH_SITE.'/plugins/digicom_pay/offline/offline/helper.php');

class plgDigiCom_PayOffline extends JPlugin 
{
	var $_payment_gateway = 'offline';
	var $_log = null;

	function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		//Set the language in the class
		$config = JFactory::getConfig();


		//Define Payment Status codes in Authorise  And Respective Alias in Framework
		//1 = Approved, 2 = Declined, 3 = Error, 4 = Held for Review
		$this->responseStatus= array(
			'Success' =>'C',
			'Failure' =>'X',
			'Pending' =>'P'
		);
	}


	function buildLayoutPath($layout) {
		if(empty($layout))
		$layout="default";
		$app=JFactory::getApplication();
		$core_file 	= dirname(__FILE__) . '/' . $this->_name . '/tmpl/' . $layout . '.php';
		$override		= JPATH_BASE .'/templates/' . $app->getTemplate() . '/html/plugins/' . $this->_type . '/' . $this->_name . '/' . $layout . '.php';
		if(JFile::exists($override))
		{
			return $override;
		}
		else
		{
	  	return  $core_file;
		}
	}

	//Builds the layout to be shown, along with hidden fields.
	function buildLayout($vars, $layout = 'default' )
	{

		//Load the layout & push variables
		ob_start();
		$layout = $this->buildLayoutPath($layout);
		include($layout);
		$html = ob_get_contents(); 
		ob_end_clean();
		return $html;
	}

	function onTP_GetHTML($vars)
	{
		$vars->custom_name= $this->params->get( 'plugin_name' );
		$vars->custom_email=$this->params->get( 'plugin_mail' );
		$html = $this->buildLayout($vars);
		return $html;
	}

	function onTP_GetInfo($config)
	{

		if(!in_array($this->_name,$config))
		return;
		$obj 		= new stdClass;
		$obj->name 	=$this->params->get( 'plugin_name' );
		$obj->id	= $this->_name;
		return $obj;
	}
	//Adds a row for the first time in the db, calls the layout view
	function onTP_Processpayment($data)
	{
		
		$payment_status = $this->translateResponse('Pending');
		$data['payment_status'] = $payment_status;

		$result = array(
			'transaction_id'	=>	$this->getUniqueTransactionId($data['order_id']),
			'order_id'			=>	$data['order_id'],
			'status'			=>	$payment_status,
			'total_paid_amt'	=>	'',
			'raw_data'			=>	json_encode($data),
			'error'				=>	'',
			'return'			=>	$data['return'],
			'comment'			=>	$data['comment'],
			'processor'			=>	'offline'
		);
		return $result;
	}
	
	function translateResponse($invoice_status){

		foreach($this->responseStatus as $key=>$value)
		{
			if($key==$invoice_status)
			return $value;
		}
	}
	
	function onTP_Storelog($data)
	{
		$log = plgDigiCom_PayOfflineHelper::Storelog($this->_name,$data);
	}

	function getUniqueTransactionId($order_id){
		$uniqueValue = $order_id.time();
		$long = md5(uniqid($uniqueValue, true));
		return substr($long, 10);
	}
}


