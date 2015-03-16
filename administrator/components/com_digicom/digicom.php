<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 355 $
 * @lastmodified	$LastChangedDate: 2013-10-11 13:18:38 +0200 (Fri, 11 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
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