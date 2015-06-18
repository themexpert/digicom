<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

defined( 'DS' ) or define( 'DS', DIRECTORY_SEPARATOR);

jimport( 'joomla.plugin.plugin' );

require_once(dirname(__FILE__) . '/2checkout/helper.php');

class  plgDigiCom_Pay2checkout extends JPlugin
{
	public $responseStatus = array (
		'Completed' 		=> 'C',
		'Pending' 			=> 'P',
		'Failed' 			=> 'E',
		'Denied' 			=> 'D',
		'Refunded'			=> 'RF',
		'Canceled_Reversal' => 'CRV',
		'Reversed'			=> 'RV'
	);
	
	function __construct($subject, $config)
	{
		parent::__construct($subject, $config);

		//Define Payment Status codes in API  And Respective Alias in Framework
		$this->responseStatus= array(
 	 		'deposited'  		=> 'C',
			'pending'  			=> 'P',
			'approved'			=> 'p',
			'declined'			=> 'X',
 		 	'Refunded'			=> 'RF'
		);
		
		
	}

	/* 
	* Internal use functions 
	* to override the view or output styles
	*/
	function buildLayoutPath($layout) {
		jimport('joomla.filesystem.file');
		$app = JFactory::getApplication();
		$core_file 	= dirname(__FILE__) . '/' . $this->_name . '/tmpl/form.php';
		$override	= JPATH_BASE . '/templates/' . $app->getTemplate() . '/html/plugins/' . $this->_type . '/' . $this->_name . '/' . $layout . '.php';

		if(JFile::exists($override))
		{
			return $override;
		}
		else
		{
			return  $core_file;
		}
	}

	/*
	* Builds the layout to be shown, along with hidden fields.
	*/
	function buildLayout($vars, $layout = 'form' )
	{
		// Load the layout & push variables
		ob_start();
		$layout = $this->buildLayoutPath($layout);
		include($layout);
		$html = ob_get_contents(); 
		ob_end_clean();
		return $html;
	}
	
	/*
	* Used to Build List of Payment Gateway in the respective Components
	*/
	function onTP_GetInfo($config)
	{

		if(!in_array($this->_name,$config))
		return;
		$obj 		= new stdClass;
		$obj->name 	=$this->params->get( 'plugin_name' );
		$obj->id	= $this->_name;
		return $obj;
	}

	/*
	* Constructs the Payment form in case of On Site Payment gateways like 
	* Auth.net & constructs the Submit button in case of offsite ones like Paypal
	*/
	function onTP_GetHTML($vars)
	{
		$params 		= $this->params;
		$sandbox 		= $this->params->get('demo',0) ? 'Y' : 'N';
		$vars->action_url = plgDigiCom_Pay2CheckoutHelper::buildPaymentSubmitUrl($secure_post = true , $sandbox);
		
		$vars->sid = $this->params->get('sid','');
		$vars->demo = $this->params->get('demo',0) ? 'Y' : 'N';
		$vars->lang = $this->params->get('lang','en');
		$vars->pay_method = $this->params->get('pay_method','cc');


		$html = $this->buildLayout($vars);
		return $html;
	}



	function onTP_Processpayment($data) 
	{
		$secret = $this->params->get('secret','cc');
		/*
		$verify = plgDigiCom_Pay2checkoutHelper::validateIPN($data,$secret);
		if (!$verify) { 
			return false; 
		}	
		*/

		$id = array_key_exists('order_id', $data) ? (int)$data['order_id'] : -1;

		$message_type=$data['message_type'];
		//$payment_status=$this->translateResponse($data['invoice_status']);
		$payment_status=$this->translateResponse($data['credit_card_processed']);
		if($message_type == 'REFUND_ISSUED'){
			$payment_status='RF';
		}

		$result = array();
		if($id)
		{
			$result = array(
				'order_id'=>$id,
				'transaction_id'=>$data['order_number'],
				'buyer_email'=>$data['email'],
				'status'=>$payment_status,
				'subscribe_id'=>$data['subscr_id'],
				'txn_type'=>$data['pay_method'],
				'total_paid_amt'=>$data['total'],
				'raw_data'=>$data
			);
		}
		return $result;
	}

	function translateResponse($invoice_status){
		//credit_card_processed

		foreach($this->responseStatus as $key=>$value)
		{
			if($key==$invoice_status)
			return $value;
		}
	}
	function onTP_Storelog($data)
	{
			$log = plgDigiCom_Pay2checkoutHelper::Storelog($this->_name,$data);

	}
}
