<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

require_once(dirname(__FILE__) . '/paypal/helper.php');

class  plgDigiCom_PayPaypal extends JPlugin
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
	* initialized response status for quickr use
	*/
	protected $responseStatus;

	/*
	* construct method
	* default joomla plugin params
	* initialize responseStatus for payment use
	*/

	function __construct($subject, $config)
	{
		parent::__construct($subject, $config);

		//Define Payment Status codes in API  And Respective Alias in Framework
		$this->responseStatus= array (
			'Completed' => 'A',
			'Pending' 	=> 'P',
			'Failed' 		=> 'P',
			'Denied' 		=> 'P',
			'Refunded'	=> 'RF',
			'Canceled_Reversal'	=> 'CRV',
			'Reversed'	=> 'RV'
		);
		// $this->responseStatus = array(
		// 	'Completed' => 'C',
		// 	'Pending' => 'P',
		// 	'Failed' => 'E',
		// 	'Denied' => 'D',
		// 	'Refunded' => 'RF',
		// 	'Canceled_Reversal' => 'CRV',
		// 	'Reversed' => 'RV'
		// );
	}

	/*
	* method : onDigicomSidebarMenuItem
	* its been used to set a short edit menu link to digicom
	* right sidebar
	* return links to edit
	*/
	public function onDigicomSidebarMenuItem()
	{
		$pluginid = $this->getPluginId('paypal','digicom_pay','plugin');
		$params 	= $this->params;
		$link 		= JRoute::_("index.php?option=com_plugins&client_id=0&task=plugin.edit&extension_id=".$pluginid);

		return '<a target="_blank" href="' . $link . '" title="'.JText::_("PLG_DIGICOM_PAY_PAYPAL").'" id="plugin-'.$pluginid.'">' . JText::_("PLG_DIGICOM_PAY_PAYPAL_NICKNAME") . '</a>';

	}

	/*
	* method buildLayoutPath
	* @layout = ask for tmpl file name, default is default, but can be used others name
	* return propur file to take htmls
	*/
	function buildLayoutPath($layout)
	{
		if(empty($layout)) $layout = "default";

		// bootstrap2 check
		$bootstrap2 	= $this->params->get( 'bootstrap2' , 0);
		if($bootstrap2){
			$layout = "bootstrap2";
		}
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
		// Load the layout & push variables
		ob_start();
		$layout = $this->buildLayoutPath($layout);
		include($layout);
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	/*
	* method onDigicom_PayGetInfo
	* can be used Build List of Payment Gateway in the respective Components
	* for payment process its not used
	* @return   mixed  return plugin config object
	*/
	function onDigicom_PayGetInfo($config)
	{

		if (!in_array($this->_name, $config))
		{
			return;
		}

		$obj 				= new stdClass;
		$obj->name 	=	$this->params->get( 'plugin_name' );
		$obj->id		= $this->_name;
		return $obj;
	}

	/*
	* method onDigicom_PayGetHTML
	* on transection process this function is being used to get html from component
	* @dependent : self::buildLayout()
	* @return html for view
	* @vars : passed from component, all info regarding payment n order
	*/
	function onDigicom_PayGetHTML($vars, $pg_plugin)
	{
		if($pg_plugin != $this->_name) 
		{
			return;
		}

		$secure_post 			= $this->params->get('secure_post');
		$sandbox 				= $this->params->get('sandbox');
		$vars->sandbox 			= $sandbox;
		$vars->action_url 		= plgDigiCom_PayPaypalHelper::buildPaymentSubmitUrl($secure_post , $sandbox);

		// If component does not provide cmd
		if (empty($vars->cmd))
		{
			$vars->cmd = '_xclick';
		}

		//Take this receiver email address from plugin if component not provided it
		if(empty($vars->business)) $vars->business = $this->params->get('business');


		// @ get recurring layout Amol
		if (property_exists($vars, 'is_recurring') && $vars->is_recurring == 1)
		{
			// new since 1.3.9
			$html = $this->buildLayout($vars, 'recurring');
		}
		else
		{
			$html = $this->buildLayout($vars);
		}
		
		return $html;

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

		$sandbox 		= $this->params->get('sandbox', 0);
		$response 		= plgDigiCom_PayPaypalHelper::validateIPN($data, $sandbox, $this->params);
		$verify 		= $response[0];

		if (!$verify)
		{
			plgDigiCom_PayPaypalHelper::Storelog($this->_name, $response[1]);

			return false;
		}
		else
		{
			$this->onDigicom_PayStorelog($this->_name, $response[1]);		
		}


		$payment_status = $this->translateResponse( $data );

		$result = array(
			'order_id'			=> $data['custom'],
			'transaction_id'	=> $data['txn_id'],
			'subscriber_id' 	=> $data['subscr_id'],
			'buyer_email'		=> $data['payer_email'],
			'status'			=> $payment_status,
			'txn_type'			=> $data['txn_type'],
			'total_paid_amt'	=> $data['mc_gross'],
			'raw_data'			=> $data,
			'processor'			=> 'paypal'
		);

		return $result;
	}

	/*
	* method translateResponse
	* used to set proper sesponce for order status
	* @invoice_status : payment status recieved from payment site: processor
	* @return order status
	*/
	function translateResponse($data)
	{
		$payment_status = $data['payment_status'];

		if(array_key_exists($payment_status, $this->responseStatus))
		{
			return $this->responseStatus[$payment_status];
		}
		else
		{
			return "P";
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

		$log_write = $this->params->get('log_write', '0');

		if ($log_write)
		{
			plgDigiCom_PayPaypalHelper::Storelog($this->_name, $data);
		}
	}

	/*
	* method getPluginId
	* used to get plugin for use
	* @element : joomla plugin element name
	* @folder : joomla plugin folder name
	* @type : joomla plugin type
	* @return extension_id
	*/
	function getPluginId($element,$folder, $type)
	{
	    $db = JFactory::getDBO();
	    $query = $db->getQuery(true);
	    $query
	        ->select($db->quoteName('a.extension_id'))
	        ->from($db->quoteName('#__extensions', 'a'))
	        ->where($db->quoteName('a.element').' = '.$db->quote($element))
	        ->where($db->quoteName('a.folder').' = '.$db->quote($folder))
	        ->where($db->quoteName('a.type').' = '.$db->quote($type));

	    $db->setQuery($query);
	    $db->execute();
	    if($db->getNumRows()){
	        return $db->loadResult();
	    }
	    return false;
	}
}
