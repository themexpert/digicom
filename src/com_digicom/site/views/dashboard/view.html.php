<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;


class DigiComViewDashboard extends JViewLegacy
{
	public $items;
	public $configs;
	public $customer;

	function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$input = $app->input;
		$Itemid = $input->get("Itemid", 0);
		$this->customer = new DigiComSiteHelperSession();
		// $return = base64_encode( JURI::getInstance()->toString() );
		// if($this->customer->_user->id < 1)
		// {
		// 	$app->Redirect(JRoute::_('index.php?option=com_users&view=login&return='.$return.'&Itemid='.$Itemid, false));
		// 	return true;
		// }
		$this->items = $this->get('items');
		$this->configs = JComponentHelper::getComponent('com_digicom')->params;
		//print_r($items);die;

		$template = new DigiComSiteHelperTemplate($this);
		$template->rander('dashboard');

		parent::display($tpl);
	}


}
