<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 341 $
 * @lastmodified	$LastChangedDate: 2013-10-10 14:28:28 +0200 (Thu, 10 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

	// Show Featured / Settings
	$this->addField('#__digicom_settings', 'showfeatured', 'int(11)', false, '0');

	// Show Related / Settings
	$this->addField('#__digicom_settings', 'showrelated', 'int(11)', false, '0');

	// Default for related and featured
	$this->updateTable('#__digicom_settings', array('showfeatured' => '1', 'showrelated' => '1'), " id=1 ");

?>