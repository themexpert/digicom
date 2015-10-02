<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

require_once(dirname(__FILE__) . '/offline/helper.php');

class plgDigiCom_PayOffline extends JPlugin
{
	/**
	 * Load the language file on instantiation. Note this is only available in Joomla 3.1 and higher.
	 * If you want to support 3.0 series you must override the constructor
	 *
	 * @var    boolean
	 * @since  3.1
	 */
	protected $autoloadLanguage = true;

	/*
	* construct method
	* default joomla plugin params
	* initialize responseStatus for payment use
	*/
	function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		//Define Payment Status codes in Authorise  And Respective Alias in Framework
		//1 = Approved, 2 = Declined, 3 = Error, 4 = Held for Review
		$this->responseStatus= array(
			'Success' =>'C',
			'Failure' =>'X',
			'Pending' =>'P'
		);
	}

	/*
	* method buildLayoutPath
	* @layout = ask for tmpl file name, default is default, but can be used others name
	* return propur file to take htmls
	*/
	function buildLayoutPath($layout)
	{
		if(empty($layout)) $layout = "default";

		$app = JFactory::getApplication();

		// core path
		$core_file 	= dirname(__FILE__) . '/' . $this->_name . '/tmpl/' . $layout . '.php';

		// override path from site active template
		$override	= JPATH_BASE .'/templates/' . $app->getTemplate() . '/html/plugins/' . $this->_type . '/' . $this->_name . '/' . $layout . '.php';

		if(JFile::exists($override))
		{
			$file = $override;
		}
		else
		{
	  		$file =  $core_file;
		}

		return $file;

	}

	/*
	* method buildLayout
	* @vars = object with product, order, user info
	* @layout = tmpl name
	* Builds the layout to be shown, along with hidden fields.
	* @return html
	*/

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

	/*
	* method onDigicom_PayGetHTML
	* on transection process this function is being used to get html from component
	* @dependent : self::buildLayout()
	* @return html for view
	* @vars : passed from component, all info regarding payment n order
	*/
	function onDigicom_PayGetHTML($vars,$pg_plugin)
	{
		if($pg_plugin != $this->_name) return;
		$vars->custom_name= $this->params->get( 'plugin_name' );
		$vars->custom_email=$this->params->get( 'plugin_mail' );
		$html = $this->buildLayout($vars);
		return $html;
	}

	/*
	* method onDigicom_PayGetInfo
	* can be used Build List of Payment Gateway in the respective Components
	* for payment process its not used
	*/

	function onDigicom_PayGetInfo($config)
	{

		if(!in_array($this->_name,$config)) return;
		$obj 		= new stdClass;
		$obj->name 	= $this->params->get( 'plugin_name' );
		$obj->id	= $this->_name;
		return $obj;
	}

	/*
	* method onDigicom_PayProcesspayment
	* used when we recieve payment from site or thurd party
	* @data : the necessary info recieved from form about payment
	* @return payment process final status
	*/
	function onDigicom_PayProcesspayment($data)
	{
		$processor = JFactory::getApplication()->input->get('processor','');
		if($processor != $this->_name) return;

		$payment_status = $this->translateResponse('Pending');
		$data['payment_status'] = $payment_status;
		
		if(!isset($data['payment_status']))
		{
			$info = array('raw_data'	=>	$data);
			$this->onDigicom_PayStorelog($this->_name, $info);
		}

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

	/*
	* method translateResponse
	* used to set proper sesponce for order status
	* @invoice_status : payment status recieved from payment site: processor
	* @return order status
	*/
	function translateResponse($invoice_status){

		foreach($this->responseStatus as $key=>$value)
		{
			if($key==$invoice_status)
			return $value;
		}
	}

	/*
	* method onDigicom_PayStorelog
	* used to store log for plugin debug payment
	* @data : the necessary info recieved from form about payment
	* @return null
	*/
	function onDigicom_PayStorelog($name, $data)
	{
		if($name != $this->_name) return;
		plgDigiCom_PayOfflineHelper::Storelog($this->_name,$data);
	}

	/*
	* method getUniqueTransactionId
	* used for local perpose to generate transection id
	* @order_id : order_id
	* @return long 15ch code
	*/
	function getUniqueTransactionId($order_id){
		$uniqueValue = $order_id.time();
		$long = md5(uniqid($uniqueValue, true));
		return substr($long, 0, 15);
	}
}
