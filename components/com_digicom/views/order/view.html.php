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
		
		$input = JFactory::getApplication()->input;
		$order = $this->_models['order']->getOrder();
		$this->assign("order", $order);
		$configs = JComponentHelper::getComponent('com_digicom')->params;
		$this->assign("configs", $configs);
		$customer = new DigiComSiteHelperSession();
		$this->assign("customer", $customer);
		$layout = $input->get('layout','order');

		$template = new DigiComSiteHelperTemplate($this);
		$template->rander($layout);

		parent::display($tpl);
	}
}
