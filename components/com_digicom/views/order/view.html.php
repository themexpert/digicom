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

class DigiComViewOrder extends JViewLegacy {

	function display($tpl = null)
	{
		
		$customer = new DigiComSiteHelperSession();
		$app = JFactory::getApplication();
		$input = $app->input;
		
		$item = $app->getMenu()->getItems('link', 'index.php?option=com_digicom&view=orders', true);
		$Itemid = isset($item->id) ? '&Itemid=' . $item->id : '';

		$return = base64_encode( JURI::getInstance()->toString() );

		if($customer->_user->id < 1)
		{
			$app->Redirect(JRoute::_('index.php?option=com_users&view=login&return='.$return.$Itemid, false));
			return true;
		}

		$order = $this->_models['order']->getOrder();

		if($order->id < 1){
			return JError::raiseError(404, JText::_('COM_DIGICOM_ORDER_NOT_FOUND'));
		}elseif($order->userid != $customer->_user->id){
			return JError::raiseError(203, JText::_('COM_DIGICOM_ORDER_NOT_OWN'));
		}

		$this->assign("order", $order);
		$configs = JComponentHelper::getComponent('com_digicom')->params;
		$this->assign("configs", $configs);
		$customer = new DigiComSiteHelperSession();
		$this->assign("customer", $customer);
		$this->assign("Itemid", $Itemid);
		
		$layout = $input->get('layout','order');
		$template = new DigiComSiteHelperTemplate($this);
		$template->rander($layout);

		parent::display($tpl);
	}
}
