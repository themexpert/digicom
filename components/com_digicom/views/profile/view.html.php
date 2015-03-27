<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class DigiComViewProfile extends JViewLegacy {

	function display($tpl = null)
	{	

		$customer = new DigiComSiteHelperSession();
		$app = JFactory::getApplication();
		$input = $app->input;
		$Itemid = $input->get("Itemid", 0);
		$return = base64_encode( JURI::getInstance()->toString() );
		if($customer->_user->id < 1)
		{
			$app->Redirect(JRoute::_('index.php?option=com_users&view=login&return='.$return.'&Itemid='.$Itemid, false));
			return true;
		}

		$db = JFactory::getDBO();
		$configs = JComponentHelper::getComponent('com_digicom')->params;
	
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

		$template = new DigiComSiteHelperTemplate($this);
		$template->rander('profile');

		parent::display($tpl);
	}

	

}
