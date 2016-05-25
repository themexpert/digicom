<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

// no direct access
defined('_JEXEC') or die;

require_once dirname(__FILE__).'/helper.php';

// Initialise variables.
$lang		= JFactory::getLanguage();
$user		= JFactory::getUser();
$app		= JFactory::getApplication();
$hideMainmenu	= $app->input->get('hidemainmenu')  ;

$show_digicom_menu 	= $params->get('show_digicom_menu', 1);
if ($show_digicom_menu) {
	$hideMainmenu=false;
}

// Get the authorised components and sub-menus.
$menuItems = ModDigiComMenuHelper::getDigiComComponent(true);

// Render the module layout
require JModuleHelper::getLayoutPath('mod_digicom_menu', $params->get('layout', 'default'));
