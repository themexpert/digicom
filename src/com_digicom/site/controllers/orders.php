<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

//TODO : PHP property need to be added eg : public, private

class DigiComControllerOrders extends JControllerLegacy
{
	var $_model = null;

	function __construct()
	{

		parent::__construct();

		$this->_model = $this->getModel( "Orders" );
	}


	function cycleStatus()
	{
		$app	= JFactory::getApplication();
		$res = $this->_model->cycleStatus();
		$msg = "";
		if(!$res){
			$msg = JText::_('COM_DIGICOM_ORDERS_NOTICE_ORDER_STATUS_CHANGE_ERR');
		}else{
			$msg = JText::_('COM_DIGICOM_ORDERS_NOTICE_ORDER_STATUS_CHANGED');
		}

		$app->enqueueMessage($msg);
		
		$link_orders = "index.php?option=com_digicom&view=manager";
		$this->setRedirect($link_orders, $msg);
	}

}
