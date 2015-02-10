<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 341 $
 * @lastmodified	$LastChangedDate: 2013-10-10 14:28:28 +0200 (Thu, 10 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

	// Hour Format / Settings
	$this->addField('#__digicom_settings', 'hour24format', 'int(11)', false, '0');

	// Default for hour format
	$this->updateTable('#__digicom_settings', array('hour24format' => '0'), " id=1 ");


?>