<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 341 $
 * @lastmodified	$LastChangedDate: 2013-10-10 14:28:28 +0200 (Thu, 10 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

	// Change permission for media folders

	@chmod( JPATH_ROOT . DS . 'images' . DS . 'stories' . DS . 'digicom' . DS . 'categories', 0755 );
	@chmod( JPATH_ROOT . DS . 'images' . DS . 'stories' . DS . 'digicom' . DS . 'categories' . DS . 'thumb', 0755 );

	@chmod( JPATH_ROOT . DS . 'images' . DS . 'stories' . DS . 'digicom' . DS . 'products', 0755  );
	@chmod( JPATH_ROOT . DS . 'images' . DS . 'stories' . DS . 'digicom' . DS . 'products' . DS . 'thumb', 0755 );

	// set default images setting and description words

	$this->changeFieldDefault('#__digicom_settings', 'imagecatsizevalue', '100');
	$this->changeFieldDefault('#__digicom_settings', 'imagecatsizetype', '1');

	$this->changeFieldDefault('#__digicom_settings', 'imageprodsizefullvalue', '300');
	$this->changeFieldDefault('#__digicom_settings', 'imageprodsizefulltype', '1');

	$this->changeFieldDefault('#__digicom_settings', 'imageprodsizethumbvalue', '100');
	$this->changeFieldDefault('#__digicom_settings', 'imageprodsizethumbtype', '1');

	$this->changeFieldDefault('#__digicom_settings', 'imagecatdescvalue', '10');
	$this->changeFieldDefault('#__digicom_settings', 'imagecatdesctype', '0');

	$this->changeFieldDefault('#__digicom_settings', 'imageproddescvalue', '10');
	$this->changeFieldDefault('#__digicom_settings', 'imageproddesctype', '0');

?>