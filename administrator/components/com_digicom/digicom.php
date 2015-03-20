<?php
/**
 * @package		DigiCom
 * @copyright	Copyright (c)2010-2015 ThemeXpert
 * @license 	GNU General Public License version 3, or later
 * @author 		ThemeXpert http://www.themexpert.com
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

JLoader::discover('DigiComHelper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers');

JHtml::_('behavior.tabstate');

if (!JFactory::getUser()->authorise('core.manage', 'com_digicom'))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

$controller	= JControllerLegacy::getInstance('Digicom');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();

DigiComHelperDigiCom::setSidebarRight();