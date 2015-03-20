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

class DigiComViewLogin extends JViewLegacy {

	function display($tpl = null)
	{	

		$uri = JFactory::getURI();
		$this->assign('action', $uri->root());
		
		$template = new DigiComSiteHelperTemplate($this);
		$template->rander('login');

		parent::display($tpl);
	}

	

}
