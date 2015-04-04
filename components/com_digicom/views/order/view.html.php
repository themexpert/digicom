<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

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

		//print_r(json_decode($order->params));die;
		
		$layout = $input->get('layout','order');
		$template = new DigiComSiteHelperTemplate($this);
		$template->rander($layout);

		parent::display($tpl);
	}
}
