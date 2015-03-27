<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class DigiComViewDownloads extends JViewLegacy {

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

		$mainframe=JFactory::getApplication();
		$Itemid = JRequest::getInt("Itemid", 0);
		$products = $this->get('listDownloads');
		
		$this->assign("products", $products);

		$template = new DigiComSiteHelperTemplate($this);
		$template->rander('downloads');
		
		parent::display($tpl);
	}

	
}
