<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 455 $
 * @lastmodified	$LastChangedDate: 2014-01-06 05:30:05 +0100 (Mon, 06 Jan 2014) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

defined ('_JEXEC') or die ("Go away.");

define ("myDC", "/");

global $isJ25;
$jv = new JVersion();
$isJ25 = $jv->RELEASE == '2.5';
if ($isJ25) {
	jimport('joomla.application.component.controller');
	jimport('joomla.application.component.model');
	jimport('joomla.application.component.view');
	class DigiComController	extends JController {}
	class DigiComModel		extends JModel {}
	class DigiComView		extends JView {}
}else{
	class DigiComController	extends JControllerLegacy {}
	class DigiComModel		extends JModelLegacy {}
	class DigiComView		extends JViewLegacy {}
}


JHtml::_( 'behavior.modal' );

// Get latest main helpers
include_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'helper.php');

JHtml::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'html');

require_once(JPATH_COMPONENT.DS.'helpers'.DS.'helper.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'log.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers' . DS . 'config.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'helpers' . DS . 'image.php');

// load core script
$document = JFactory::getDocument();
$document->addScript(JURI::root(true).'/media/digicom/assets/js/digicom.js?v=1.0.0&amp;sitepath='.JURI::root(true).'/');
