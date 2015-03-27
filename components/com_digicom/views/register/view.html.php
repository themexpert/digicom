<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class DigiComViewRegister extends JViewLegacy {

	function display($tpl = null)
	{	

		$db = JFactory::getDBO();
		$configs = JComponentHelper::getComponent('com_digicom')->params;
		$app = JFactory::getApplication();
		$input = $app->input;

		$customer = new DigiComSiteHelperSession();
		
		if($customer->_user->id > 0){
			$Itemid = $input->get("Itemid", 0);
			$app->Redirect(JRoute::_('index.php?option=com_digicom&view=dashboard&Itemid='.$Itemid, false));
			return true;
		}

		$this->askforbilling = $configs->get('askforbilling',1);
		$this->askforcompany = $configs->get('askforcompany',1);
		
		$country_option = DigiComSiteHelperDigiCom::get_country_options($customer, false, $configs);
		$lists['country_option'] = $country_option;

		$profile = new StdClass();
		$profile->country = @$customer->country;
		$profile->state = @$customer->state;
		$shipcountry_option = DigiComSiteHelperDigiCom::get_country_options($profile, true, $configs);
		$lists['shipcountry_options'] = $shipcountry_option;

		$lists['customerlocation'] = DigiComSiteHelperDigiCom::get_store_province($profile, false);

		$profile = new StdClass();
		$profile->country = @$customer->country;
		$profile->state = @$customer->state;
		$lists['customershippinglocation'] = DigiComSiteHelperDigiCom::get_store_province($profile, true);

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

		//prepare default and tmp value
		$old_values = array();
		if(isset($_SESSION["new_customer"])){
			$old_values = $_SESSION["new_customer"];
		}
		$userinfo = new StdClass();
		$userinfo->firstname = "";
		$userinfo->lastname = "";
		$userinfo->company = "";
		$userinfo->email = "";
		$userinfo->username = "";
		$userinfo->password = "";
		$userinfo->password_confirm = "";
		$userinfo->address = "";
		$userinfo->city = "";
		$userinfo->zipcode = "";
		$userinfo->country = "";
		$userinfo->state = "";
		if(isset($old_values) && count($old_values) > 0){
			$userinfo->firstname = $old_values["firstname"];
			$userinfo->lastname = $old_values["lastname"];
			$userinfo->company = $old_values["company"];
			$userinfo->email = $old_values["email"];
			$userinfo->username = $old_values["username"];
			$userinfo->password = $old_values["password"];
			$userinfo->password_confirm = $old_values["password_confirm"];
			$userinfo->address = $old_values["address"];
			$userinfo->city = $old_values["city"];
			$userinfo->zipcode = $old_values["zipcode"];
			$userinfo->country = $old_values["country"];
			$userinfo->state = $old_values["state"];
			unset($_SESSION["new_customer"]);
		}
		$this->assign("userinfo", $userinfo);
		
		$layout = $input->get('layout','register');

		$template = new DigiComSiteHelperTemplate($this);
		$template->rander($layout);

		parent::display($tpl);
	}

	

}
