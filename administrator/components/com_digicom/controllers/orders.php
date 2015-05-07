<?php
/**
 * @package		DigiCom
 * @copyright	Copyright (c)2010-2015 ThemeXpert
 * @license 	GNU General Public License version 3, or later
 * @author 		ThemeXpert http://www.themexpert.com
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class DigiComControllerOrders extends JControllerAdmin
{

	var $_model = null;

	function __construct()
	{

		parent::__construct();

		$this->_model = $this->getModel( "Orders" );
		$this->_config = $this->getModel( "Config" );
	}

	function remove()
	{
		if ( !$this->_model->delete() ) {
			$msg = JText::_( 'COM_DIGICOM_ORDERS_NOTICE_ORDER_REMOVE_ERROR' );
		} else {
			$msg = JText::_( 'COM_DIGICOM_ORDERS_NOTICE_ORDER_REMOVE_SUCCESS' );
		}

		$link = "index.php?option=com_digicom&view=orders";
		$this->setRedirect( $link, $msg );

	}

	function cancel()
	{
		$msg = JText::_( 'COM_DIGICOM_ORDERS_NOTICE_ORDER_CANCEL' );
		$link = "index.php?option=com_digicom&view=orders";
		$this->setRedirect( $link, $msg );

	}

	function publish()
	{
		$res = $this->_model->publish();
		if ( !$res ) {
			$msg = JText::_( 'COM_DIGICOM_ORDERS_NOTICE_ORDER_BLOCK_ERR' );
		} elseif ( $res == -1 ) {
			$msg = JText::_( 'COM_DIGICOM_ORDERS_NOTICE_ORDER_UNPUB' );
		} elseif ( $res == 1 ) {
			$msg = JText::_( 'COM_DIGICOM_ORDERS_NOTICE_ORDER_PUB' );
		} else {
			$msg = JText::_( 'COM_DIGICOM_ORDERS_NOTICE_ORDER_UNSPEC' );
		}

		$link = "index.php?option=com_digicom&view=orders";
		$this->setRedirect( $link, $msg );

	}

	function cycleStatus(){
		$res = $this->_model->cycleStatus();
		$msg = "";
		if(!$res){
			$msg = JText::_('COM_DIGICOM_ORDERS_NOTICE_ORDER_STATUS_CHANGE_ERR');
		}
		else{
			$msg = JText::_('COM_DIGICOM_ORDERS_NOTICE_ORDER_STATUS_CHANGED');
		}
		$link_orders = "index.php?option=com_digicom&view=orders";
		$this->setRedirect($link_orders, $msg);
	}

	function calc()
	{
		//decode incoming JSON string
		$jsonRequest = JRequest::getVar("jsonString", "", "get");
		$jsonRequest = json_decode($jsonRequest);
		$calc_result = $this->_model->calcPrice($jsonRequest);
		
		$data = new stdclass();
		$data->amount = $calc_result['amount'];
		$data->amount_value = $calc_result['amount_value'];
		$data->tax = $calc_result['tax'];
		$data->tax_value = $calc_result['tax_value'];
		$data->discount_sign = $calc_result['discount_sign'];
		$data->discount = $calc_result['discount'];
		$data->total = $calc_result['total'];
		$data->total_value = $calc_result['total_value'];
		$data->currency = $calc_result['currency'];
		
		// Get the document object.
		$document = JFactory::getDocument();
		
		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');
		
		// Change the suggested filename.
		JResponse::setHeader('Content-Disposition','attachment;filename="orders.json"');
		// Output the JSON data.
		echo json_encode($data);
		JFactory::getApplication()->close();

	}
}