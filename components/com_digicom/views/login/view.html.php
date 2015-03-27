<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

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
