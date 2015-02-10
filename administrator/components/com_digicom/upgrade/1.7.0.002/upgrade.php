<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 341 $
 * @lastmodified	$LastChangedDate: 2013-10-10 14:28:28 +0200 (Thu, 10 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

	// Store Description
	$this->addField('#__digicom_settings', 'storedesc', 'mediumtext', false, '');

	// Display store description
	$this->addField('#__digicom_settings', 'displaystoredesc', 'int(11)', false, '1');

	// Default for Store 
	$this->updateTable('#__digicom_settings', array('storedesc' => 'Welcome to our store', 'displaystoredesc' => '1'), " id=1 ");



?>