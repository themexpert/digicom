<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 341 $
 * @lastmodified	$LastChangedDate: 2013-10-10 14:28:28 +0200 (Thu, 10 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

	// add to config fields Images 
	$this->addField('#__digicom_settings', 'imagecatsizevalue', 'int(11)', false, '0');
	$this->addField('#__digicom_settings', 'imagecatsizetype', 'int(11)', false, '0');

	$this->addField('#__digicom_settings', 'imageprodsizefullvalue', 'int(11)', false, '0');
	$this->addField('#__digicom_settings', 'imageprodsizefulltype', 'int(11)', false, '0');

	$this->addField('#__digicom_settings', 'imageprodsizethumbvalue', 'int(11)', false, '0');
	$this->addField('#__digicom_settings', 'imageprodsizethumbtype', 'int(11)', false, '0');

	// add to config fields Desciptions 
	$this->addField('#__digicom_settings', 'imagecatdescvalue', 'int(11)', false, '0');
	$this->addField('#__digicom_settings', 'imagecatdesctype', 'int(11)', false, '0');

	$this->addField('#__digicom_settings', 'imageproddescvalue', 'int(11)', false, '0');
	$this->addField('#__digicom_settings', 'imageproddesctype', 'int(11)', false, '0');

?>