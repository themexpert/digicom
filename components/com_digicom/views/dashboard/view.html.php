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


class DigiComViewDashboard extends JViewLegacy
{

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


		$this->customer = $customer ;

		$template = new DigiComSiteHelperTemplate($this);
		$template->rander('dashboard');

		parent::display($tpl);
	}
	

}

