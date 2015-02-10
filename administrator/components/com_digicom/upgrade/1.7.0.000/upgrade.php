<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 341 $
 * @lastmodified	$LastChangedDate: 2013-10-10 14:28:28 +0200 (Thu, 10 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

	// Upgrade 1.6.25.000

	include_once('tables.php');

	// Settings / Config

	$this->addField('#__digicom_settings', 'showprodshort', 'int(11)', false, '0');

	$this->addField('#__digicom_settings', 'pendinghtml', 'text', false);

	$this->addField('#__digicom_settings', 'address', 'varchar(255)', false);
	$this->addField('#__digicom_settings', 'zip', 'varchar(100)', false);
	$this->addField('#__digicom_settings', 'phone', 'varchar(200)', false);
	$this->addField('#__digicom_settings', 'fax', 'varchar(200)', false);

	$this->addField('#__digicom_settings', 'afterpurchase', 'int(2)', false, '1');

	$this->addField('#__digicom_settings', 'showoid', 'int(2)', false, '1');
	$this->addField('#__digicom_settings', 'showoipurch', 'int(2)', false, '1');
	$this->addField('#__digicom_settings', 'showolics', 'int(2)', false, '1');
	$this->addField('#__digicom_settings', 'showopaid', 'int(2)', false, '1');
	$this->addField('#__digicom_settings', 'showodate', 'int(2)', false, '1');
	$this->addField('#__digicom_settings', 'showorec', 'int(2)', false, '1');

	$this->addField('#__digicom_settings', 'showlid', 'int(2)', false, '1');
	$this->addField('#__digicom_settings', 'showlprod', 'int(2)', false, '1');
	$this->addField('#__digicom_settings', 'showloid', 'int(2)', false, '1');
	$this->addField('#__digicom_settings', 'showldate', 'int(2)', false, '1');
	$this->addField('#__digicom_settings', 'showldown', 'int(2)', false, '1');

	$this->addField('#__digicom_settings', 'showcam', 'int(2)', false, '1');
	$this->addField('#__digicom_settings', 'showcpromo', 'int(2)', false, '1');
	$this->addField('#__digicom_settings', 'showcremove', 'int(2)', false, '1');
	$this->addField('#__digicom_settings', 'showccont', 'int(2)', false, '1');

	$this->addField('#__digicom_settings', 'showldomain', 'int(2)', false, '1');

	$this->addField('#__digicom_settings', 'tax_classes', 'int(11)', false);
	$this->addField('#__digicom_settings', 'tax_base', 'int(11)', false, '1');
	$this->addField('#__digicom_settings', 'tax_catalog', 'int(11)', false);
	$this->addField('#__digicom_settings', 'tax_shipping', 'int(11)', false);
	$this->addField('#__digicom_settings', 'tax_discount', 'int(11)', false);
	$this->addField('#__digicom_settings', 'discount_tax', 'int(11)', false);
	$this->addField('#__digicom_settings', 'tax_country', 'varchar(200)', false);
	$this->addField('#__digicom_settings', 'tax_state', 'varchar(200)', false);
	$this->addField('#__digicom_settings', 'tax_zip', 'varchar(200)', false);
	$this->addField('#__digicom_settings', 'tax_price', 'int(11)', false);
	$this->addField('#__digicom_settings', 'tax_summary', 'int(11)', false);
	$this->addField('#__digicom_settings', 'shipping_price', 'int(11)', false);
	$this->addField('#__digicom_settings', 'product_price', 'int(11)', false);
	$this->addField('#__digicom_settings', 'tax_zero', 'int(11)', false);
	$this->addField('#__digicom_settings', 'tax_apply', 'varchar(200)', false);

	$this->addField('#__digicom_settings', 'tax_apply', 'varchar(200)', false);
	$this->addField('#__digicom_settings', 'continue_shopping_url', 'varchar(500)', false);

	$this->addField('#__digicom_settings', 'usestorelocation', 'int(11)', false);
	$this->addField('#__digicom_settings', 'allowcustomerchoseclass', 'int(11)', false, '2');
	$this->addField('#__digicom_settings', 'takecheckout', 'int(11)', false, '1');

	$this->updateTable('#__digicom_settings', array('tax_base' => 1), ' id = 1 ');

	// Products

	$this->addField('#__digicom_products', 'access', 'int(11) unsigned', false, '0');
	$this->addField('#__digicom_products', 'prodtypeforplugin', 'text', false, '');

	$this->addField('#__digicom_products', 'taxclass', 'int(11)', false);
	$this->addField('#__digicom_products', 'class', 'int(11)', false);

	// Customers

	$this->addField('#__digicom_customers', 'taxclass', 'int(11)', false);
	$this->changeFieldType('#__digicom_customers', 'city', 'city', 'varchar(100)');
	$this->changeFieldType('#__digicom_customers', 'state', 'state', 'varchar(100)');
	$this->changeFieldType('#__digicom_customers', 'province', 'province', 'varchar(100)');
	$this->changeFieldType('#__digicom_customers', 'shipcity', 'shipcity', 'varchar(100)');
	$this->changeFieldType('#__digicom_customers', 'shipstate', 'shipstate', 'varchar(100)');
	$this->changeFieldType('#__digicom_customers', 'shipcountry', 'shipcountry', 'varchar(100)');
	$this->changeFieldType('#__digicom_customers', 'taxnum', 'taxnum', 'varchar(11) DEFAULT "1"');

	// TaxRate

	$this->addKeyUnique('#__digicom_tax_rate', array('name'));

	// PluginSettings

	$this->addField('#__digicom_plugin_settings', 'id', 'int(11)', false, null, true);
	$this->addPrimaryKey('#__digicom_plugin_settings', array('id'));

	// Settings / Config

	$this->addField('#__digicom_settings', 'currency_position', 'int(1)', false, 0);

	/************************************************************************************************
	 *	Begin Subscription Upgrade scripts
	 */
	 
	$this->addField('#__digicom_settings', 'showlterms', 'int(1)', false, 0);
	$this->addField('#__digicom_settings', 'showlexpires', 'int(1)', false, 0);

	$this->addField('#__digicom_licenses', 'purchase_date', 'datetime', false, '0000-00-00 00:00:00');
	$this->addField('#__digicom_licenses', 'expires', 'datetime', false, '0000-00-00 00:00:00');

	$this->addField('#__digicom_cart', 'plan_id', 'int(11)', false, '0');
	$this->addField('#__digicom_cart', 'plugin_id', 'int(11)', false, '0');

	$this->updateTable('#__digicom_settings', array('tax_base' => 1), " id = 1 ");

	// Set Default dates to tables
	include_once('datas.php');

	// Update/Create digicats mainmenu in joomla
	include_once('digicats.php');

?>	