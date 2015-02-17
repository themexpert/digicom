<?php

/**

 *
 * @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 341 $
 * @lastmodified	$LastChangedDate: 2013-10-10 14:28:28 +0200 (Thu, 10 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
 * @license			GNU/GPLv3 */
defined( '_JEXEC' ) or die( "Go away." );

jimport( 'joomla.application.component.controller' );

class DigiComAdminControllerOrders extends DigiComAdminController
{

	var $_model = null;

	function __construct()
	{

		parent::__construct();

//		$this->registerTask ("add", "edit");
		$this->registerTask( "", "listOrders" );
		$this->registerTask( "show", "showOrder" );
		$this->registerTask( "unpublish", "publish" );
		$this->_model = $this->getModel( "Order" );
		$this->_config = $this->getModel( "Config" );
	}

	function showOrder()
	{
		$view = $this->getView( "Orders", "html" );
		$view->setModel( $this->_model, true );
		$view->setModel( $this->_config );
		$view->setLayout( "showorder" );
		$view->showOrder();

	}

	function saveorder() {
		$this->_model->saveorder();
		$this->setRedirect('index.php?option=com_digicom&controller=orders');
	}

	function calc(){
		$json = new Services_JSON;
		//decode incoming JSON string
		$jsonRequest = JRequest::getVar("jsonString", "", "get");
		$jsonRequest = $json->decode($jsonRequest);
		$calc_result = $this->_model->calcPrice($jsonRequest);

		$jsonRequest->amount = $calc_result['amount'];
		$jsonRequest->amount_value = $calc_result['amount_value'];
		$jsonRequest->tax = $calc_result['tax'];
		$jsonRequest->tax_value = $calc_result['tax_value'];
		$jsonRequest->discount_sign = $calc_result['discount_sign'];
		$jsonRequest->discount = $calc_result['discount'];
		$jsonRequest->total = $calc_result['total'];
		$jsonRequest->total_value = $calc_result['total_value'];
		$jsonRequest->currency = $calc_result['currency'];

		echo $json->encode($jsonRequest);
		exit;
	}

	function listOrders()
	{
		$view = $this->getView( "Orders", "html" );
		$view->setModel( $this->_model, true );
		$model = $this->getModel( "Config" );
		$view->setModel( $model );
		$view->display();

	}

	function saveCustomer() {

		$error = "";
		$customer_model = $this->getModel('Customer');
		if ($result = $customer_model->store($error) ) {
			$id = $customer_model->_customer->id;
			if ($id > 0) {
				$this->setRedirect('index.php?option=com_digicom&controller=orders&task=prepereNewOrder&userid='.$id);
			}
		} else {
			$msg = JText::_('CUSTSAVEFAILED');
			echo $msg .= " " . JText::_($error);
			$this->setMessage($msg);
			$view = $this->getView( "Orders", "html" );
			$view->setModel( $this->_model, true );
			$model = $this->getModel( "Config" );
			$view->setModel( $model );
			$model = $this->getModel('Customer');
			$view->setModel( $model );
			$view->setLayout( "newcustomer" );
			$view->newCustomer();
		}
	}

	function newCreateCustomer() {
		$view = $this->getView( "Orders", "html" );
		$view->setModel( $this->_model, true );
		$model = $this->getModel( "Config" );
		$view->setModel( $model );
		$model = $this->getModel('Customer');
		$view->setModel( $model );
		$customer = $model;
		$view->setLayout( "newcustomer" );

		// define need redirect to create order or redirect to fill profile

		$usertype = JRequest::getVar('usertype', 3);
		$username = JRequest::getVar('username','');

		if(trim($username) == ""){
			$this->setRedirect('index.php?option=com_digicom&controller=orders&task=checkcreateuser&usertype=3', JText::_("DIGI_ENTER_VALID_USERNAME"), "notice");
		}

		$user = $customer->getUserByName($username);
		if (!empty($user->id)) {
			$cust =  $customer->getCustomerbyID($user->id);
			if (!empty($cust->firstname)) {
				$this->setRedirect('index.php?option=com_digicom&controller=orders&task=prepereNewOrder&userid='.$user->id);
			} else {
				$view->newCustomer();
			}
		} else {
			$cust =  $customer->getCustomerbyID(0);
			$view->newCustomer();
		}
	}

	function checkcreateuser() {

		$usertype = JRequest::getVar('usertype', 3);

		switch($usertype) {
			case '1':
				$view = $this->getView( "Orders", "html" );
				$view->setModel( $this->_model, true );
				$model = $this->getModel( "Config" );
				$view->setModel( $model );
				$model = $this->getModel('Customer');
				$view->setModel( $model );
				$view->setLayout( "newcustomer" );
				$view->newCustomer();
				break;
			case '2':
				$view = $this->getView( "Orders", "html" );
				$view->setModel( $this->_model, true );
				$model = $this->getModel( "Config" );
				$view->setModel( $model );
				$view->setLayout( "selectusername" );
				$view->selectUsername();
				break;
			case '3':
			default:
				$view = $this->getView( "Orders", "html" );
				$view->setModel( $this->_model, true );
				$model = $this->getModel( "Config" );
				$view->setModel( $model );
				$view->setLayout( "selectusername" );
				$view->selectUsername();
				break;
		}
	}


	function add() {
		$view = $this->getView( "Orders", "html" );
		$view->setModel( $this->_model, true );
		$model = $this->getModel( "Config" );
		$view->setModel( $model );
		$model = $this->getModel( "Customer" );
		$view->setModel( $model );
		$model = $this->getModel( "License" );
		$view->setModel( $model );
		$model = $this->getModel( "Plain" );
		$view->setModel( $model );
		$view->setLayout( "addneworder" );
		$view->addNewOrder();
	}


	function prepereNewOrder() {
		$view = $this->getView( "Orders", "html" );
		$view->setModel( $this->_model, true );
		$model = $this->getModel( "Config" );
		$view->setModel( $model );
		$model = $this->getModel('Customer');
		$view->setModel( $model );
		$view->setLayout( "prepereneworder" );
		$view->prepereNewOrder();
	}

	function edit()
	{
		JRequest::setVar( "hidemainmenu", 1 );
		$view = $this->getView( "Orders", "html" );
		$view->setModel( $this->_model, true );

		$model = $this->getModel( "Config" );
		$view->setModel( $model );

		$model = $this->getModel('Customer');
		$view->setModel( $model );

		$model = $this->getModel( "Product" );
		$view->setModel( $model );

		$view->setLayout( "editForm" );

		$view->editForm();
	}

	function save()
	{
		if ( $this->_model->store() ) {

			$msg = JText::_( 'ORDSAVED' );
		} else {
			$msg = JText::_( 'ORDFAILED' );
		}
		$link = "index.php?option=com_digicom&controller=orders";
		$this->setRedirect( $link, $msg );

	}

	function remove()
	{
		if ( !$this->_model->delete() ) {
			$msg = JText::_( 'ORDREMERR' );
		} else {
			$msg = JText::_( 'ORDREMSUCC' );
		}

		$link = "index.php?option=com_digicom&controller=orders";
		$this->setRedirect( $link, $msg );

	}

	function cancel()
	{
		$msg = JText::_( 'ORDCANCEL' );
		$link = "index.php?option=com_digicom&controller=orders";
		$this->setRedirect( $link, $msg );

	}

	function publish()
	{
		$res = $this->_model->publish();
		if ( !$res ) {
			$msg = JText::_( 'ORDBLOCKERR' );
		} elseif ( $res == -1 ) {
			$msg = JText::_( 'ORDUNPUB' );
		} elseif ( $res == 1 ) {
			$msg = JText::_( 'ORDPUB' );
		} else {
			$msg = JText::_( 'ORDUNSPEC' );
		}

		$link = "index.php?option=com_digicom&controller=orders";
		$this->setRedirect( $link, $msg );

	}

	function cycleStatus(){
		$res = $this->_model->cycleStatus();
		$msg = "";
		if(!$res){
			$msg = JText::_('ORDSTATUSCHANGEERR');
		}
		else{
			$msg = JText::_('ORDSTATUSCHANGED');
		}
		$link_orders = "index.php?option=com_digicom&controller=orders";
		$this->setRedirect($link_orders, $msg);
	}

	function productitem() {
		$view = $this->getView( "Orders", "html" );
		$view->setModel( $this->_model, true );
		$model = $this->getModel( "Config" );
		$view->setModel( $model );
		$model = $this->getModel('Customer');
		$view->setModel( $model );
		$model = $this->getModel('License');
		$view->setModel( $model );
		$view->setLayout( "productitem" );
		$view->productitem();
	}


}