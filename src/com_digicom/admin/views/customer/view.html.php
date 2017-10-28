<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class DigiComViewCustomer extends JViewLegacy {

	protected $form;

	function display ($tpl =  null )
	{
		$db = JFactory::getDBO();
		$customer = $this->get('customer');
		//print_r($customer);die;
		$user = $this->get('User');
		$isNew = ($customer->id < 1);
		$text = $isNew ? JText::_('COM_DIGICOM_CUSTOMER_NEW') : JText::_('COM_DIGICOM_CUSTOMER_EDIT');

		JToolBarHelper::title($text);

		$bar = JToolBar::getInstance('toolbar');
		$layout = new JLayoutFile('toolbar.title');

		$layout = new JLayoutFile('toolbar.settings');
		$bar->appendButton('Custom', $layout->render(array()), 'settings');

		JToolBarHelper::apply('customer.apply');
		JToolBarHelper::save('customer.save');

		JToolBarHelper::divider();
		JToolBarHelper::cancel ('customer.cancel');

		$this->assign("cust", $customer);
		$this->assign("user", $user);

		$configs = $this->get("Configs");

		$this->assign("configs", $configs);

		$this->form		= $this->get('Form');

		DigiComHelperDigiCom::addSubmenu('customers');
		$this->sidebar = DigiComHelperDigiCom::renderSidebar();
		JFactory::getApplication()->input->set('hidemainmenu', true);

		parent::display($tpl);
	}





}
