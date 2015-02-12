<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 364 $
 * @lastmodified	$LastChangedDate: 2013-10-15 15:27:43 +0200 (Tue, 15 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

defined ('_JEXEC') or die ("Go away.");

jimport ("joomla.application.component.view");

class DigiComViewProfile extends DigiComView {

	function display ($tpl =  null ) {
		JToolBarHelper::title(JText::_('Customers Manager'), 'generic.png');
		JToolBarHelper::deleteList();
		JToolBarHelper::editListX();
		JToolBarHelper::addNewX();
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();

		$customers = $this->get('listCustomers');
		$this->assignRef('custs', $customers);
		parent::display($tpl);

	}

	function loginRegister($tpl = null)
	{	

		$db = JFactory::getDBO();
		
		require_once(JPATH_SITE.DS."components".DS."com_digicom".DS."models".DS."config.php");
		$conf_model = new DigiComModelConfig();
		$configs = $conf_model->getConfigs();
	
		$this->askforbilling = $configs->get('askforbilling',1);
		$this->askforcompany = $configs->get('askforcompany',1);
		
		$customer = new DigiComSessionHelper();
		
		if($customer->_user->id > 0){
			require_once(JPATH_SITE.DS."components".DS."com_digicom".DS."models".DS."customer.php");
			$customer_model = new DigiComModelCustomer();
			$customer = $customer_model->getCustomer($customer->_user->id);
		}
		
		$country_option = DigiComHelper::get_country_options($customer, false, $configs);
		$lists['country_option'] = $country_option;

		$profile = new StdClass();
		$profile->country = @$customer->country;
		$profile->state = @$customer->state;
		$shipcountry_option = DigiComHelper::get_country_options($profile, true, $configs);
		$lists['shipcountry_options'] = $shipcountry_option;

		$lists['customerlocation'] = DigiComHelper::get_store_province($profile, false);

		$profile = new StdClass();
		$profile->country = @$customer->country;
		$profile->state = @$customer->state;
		$lists['customershippinglocation'] = DigiComHelper::get_store_province($profile, true);

		$sql = "select * from #__digicom_states where eumember='1'";
		$db->setQuery($sql);
		$eucs = $db->loadObjectList();
		$eu = array();
		foreach ($eucs as $euc) $eu[] = $euc->country ;

		$this->assign("eu", $eu);
		$eulocated = (isset($customer->country) && in_array($customer->country, $eu));
		$this->assign("eulocated", $eulocated);


		$cclasses = @explode("\n", $customer->taxclass);
		$data = $this->get('listCustomerClasses');
		$select = '<select name="taxclass" >';
		if (count($data) > 0)
		foreach($data as $i => $v) {
			$select .= '<option value="'.$v->id.'" ';
			if (in_array($v->id, $cclasses)) {
				$select .= ' selected ' ;
			}
			$select .= ' > '.$v->name.'</option>';
		}
		$select .= '</select>';
		$lists['customer_class'] = $select;

		$this->assign("lists", $lists);

		$this->assign("customer", $customer);
		$this->assign("configs", $configs);

		parent::display($tpl);
	}

	function editForm($tpl = null)
	{
		require_once JPATH_COMPONENT . DS . 'helpers' . DS . 'sajax.php';

		$db = JFactory::getDBO();
		$uri		= JFactory::getURI();
		$customer = new DigiComSessionHelper();
		$customer = $this->_models['customer']->getCustomer($customer->_user->id);
		$isNew = ($customer->id < 1);

		$this->assign('action', 	$uri->toString());
	
		$user = JFactory::getUser();
		if($user->id != "" && $user->id != "0"){
			$customer->id = $user->id;
		}
		$uid = $customer->id;
		$this->assign("uid", $uid);

		$this->assign("cust", $customer);
		$configs = $this->_models['config']->getConfigs();
		$country_option = DigiComHelper::get_country_options($customer, false, $configs);
		$lists['country_option'] = $country_option;

		$profile = new StdClass();
		$profile->country = $customer->shipcountry;
		$profile->state = $customer->state;
		$shipcountry_option = DigiComHelper::get_country_options($customer, true, $configs);
		$lists['shipcountry_options'] = $shipcountry_option;

		$lists['customerlocation'] = DigiComHelper::get_store_province($customer, false);

		$profile = new StdClass();
		$profile->country = $customer->shipcountry;
		$profile->state = $customer->shipstate;
		$lists['customershippinglocation'] = DigiComHelper::get_store_province($profile, true);

//		global $plugin_handler;
//		$content = '<select name="payment_type" id="payment_type">'.$plugin_handler->getPluginOptions($profile->payment_type).'</select>';
		//$content = $this->_models['plugin']->getPluginOptions($customer->payment_type);
		//$lists['payment_type'] = $content;

		$sql = "SELECT * FROM #__digicom_states WHERE eumember='1'";
			$db->setQuery($sql);
			$eucs = $db->loadObjectList();
		$eu = array();
		foreach ($eucs as $euc) $eu[] = $euc->country ;

		$this->assign("eu", $eu);
		$eulocated = (isset($customer->country) && in_array($customer->country, $eu));
		$this->assign("eulocated", $eulocated);


		$cclasses = explode("\n", $customer->taxclass);
		$data = $this->get('listCustomerClasses');
		$select = '<select name="taxclass" >';
		if (count($data) > 0)
		foreach($data as $i => $v) {
			$select .= '<option value="'.$v->id.'" ';
			if (in_array($v->id, $cclasses)) {
				$select .= ' selected ' ;
			}
			$select .= ' > '.$v->name.'</option>';
		}
		$select .= '</select>';
		$lists['customer_class'] = $select;

		$this->assign("lists", $lists);

		$this->assign("configs", $configs);
		parent::display($tpl);

	}

	function login ($tpl = null) {
		$uri		= JFactory::getURI();
		$this->assign('action', $uri->root());
		parent::display($tpl);

	}

}

?>