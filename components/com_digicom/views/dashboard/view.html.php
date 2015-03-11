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

jimport("joomla.application.component.view");

class DigiComViewDashboard extends DigiComView
{

	function display($tpl = null)
	{
		$template = new DigiComTemplateHelper($this);
		$template->rander('dashboard');

		parent::display($tpl);
	}
	

}

