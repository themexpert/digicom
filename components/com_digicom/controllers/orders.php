<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 341 $
 * @lastmodified	$LastChangedDate: 2013-10-10 14:28:28 +0200 (Thu, 10 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

defined ('_JEXEC') or die ("Go away.");

jimport ('joomla.application.component.controller');

class DigiComControllerOrders extends DigiComController {

	var $_model = null;
	var $_config = null;
	var $_order = null;

	function __construct () {
		global $Itemid;
		parent::__construct();
		$this->registerTask ("", "listOrders");
		$this->registerTask ("list", "listOrders");
		$this->registerTask ("details", "showOrder");
		$this->registerTask ("showrec", "showOrderReceipt");
		$this->registerTask ("wait", "waitipn");
		
		$this->_model = $this->getModel("Order");
		$this->_config = $this->getModel("Config");
		$this->_license = $this->getModel("License");
		$this->_cart = $this->getModel("Cart");
		$this->_customers_model = $this->getModel("Customer");

		$this->log_link = JRoute::_("index.php?option=com_digicom&view=profile&task=login&returnpage=orders&Itemid=".$Itemid, false);
		$this->prof_link = JRoute::_("index.php?option=com_digicom&view=profile&task=edit&returnpage=orders&Itemid=".$Itemid, false);
		$this->order_link = JRoute::_("index.php?option=com_digicom&view=orders&Itemid=".$Itemid, false);
	}

	function listOrders()
	{
		global $Itemid;
		if($this->_customer->_user->id < 1)
		{
			$this->setRedirect(JRoute::_($this->log_link, false));
			return;
		}

		$res = DigiComHelper::checkProfileCompletion($this->_customer);
		if($res < 1)
		{
			$this->setRedirect($this->prof_link);
		}
		
		JRequest::setVar ("view", "Orders");
		$view = $this->getView("Orders", "html");
		$view->setModel($this->_model, true);
		$view->setModel($this->_config);
		$view->setModel($this->_license);
		$view->setModel($this->_cart);
		$view->setModel($this->_customers_model);
		$conf = $this->_config->getConfigs();
		$view->display();
	}

	function showOrder()
	{
		if ($this->_customer->_user->id < 1)
		{
			$this->setRedirect(JRoute::_($this->log_link) );
			return;
		}
		$res = DigiComHelper::checkProfileCompletion( $this->_customer );
		if ($res < 1)
		{
			$this->setRedirect( $this->prof_link );
		}

		$view = $this->getView("Orders", "html");
		$view->setModel($this->_model, true);
		$view->setModel($this->_config);
		$view->setModel($this->_license);
		$view->setLayout("showorder");
		$view->showOrder();
	}

	function showOrderReceipt()
	{
		if ($this->_customer->_user->id < 1)
		{
			$this->setRedirect(JRoute::_($this->log_link) );
			return;
		}

		$res = DigiComHelper::checkProfileCompletion( $this->_customer );
		if ($res < 1)
		{
			$this->setRedirect( $this->prof_link );
		}
		$view = $this->getView("Orders", "html");
		$view->setModel($this->_model, true);
		$view->setModel($this->_config);
		$view->setModel($this->_license);
		$view->setLayout("receipt");
		$view->showReceipt();
	}

}
