<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;
JHtml::_('jquery.framework');

JLoader::discover('DigiComSiteHelper', JPATH_COMPONENT_SITE . '/helpers');

require_once JPATH_COMPONENT . '/helpers/route.php';

$controller	= JControllerLegacy::getInstance('DigiCom');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
