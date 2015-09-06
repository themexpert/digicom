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
	
}
