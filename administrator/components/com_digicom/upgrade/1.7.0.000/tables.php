<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 341 $
 * @lastmodified	$LastChangedDate: 2013-10-10 14:28:28 +0200 (Thu, 10 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

$table_cart_000 = "
CREATE TABLE IF NOT EXISTS `#__digicom_cart` (
  `cid` int(11) NOT NULL auto_increment,
  `item_id` int(11) NOT NULL default '0',
  `userid` int(11) NOT NULL default '0',
  `quantity` int(11) NOT NULL default '0',
  `sid` int(11) NOT NULL default '0',
  `plan_id` int(11) NOT NULL default '0',
  `renew` int(11) NOT NULL default '0',
  `renewlicid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`cid`),
  KEY `productid` (`item_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
";
$this->createTable($table_cart_000);

$table_cart_000 = "
CREATE TABLE IF NOT EXISTS `#__digicom_cartfields` (
  `fieldid` int(11) NOT NULL default '0',
  `productid` int(11) NOT NULL default '0',
  `sid` int(11) NOT NULL default '0',
  `optionid` int(11) NOT NULL default '-1',
  `cid` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
$this->createTable($table_cart_000);

$table_cart_000 = "
CREATE TABLE IF NOT EXISTS `#__digicom_categories` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) NOT NULL default '0',
  `title` varchar(50) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `image` varchar(500) NOT NULL default '',
  `thumb` varchar(500) NOT NULL default '',
  `section` varchar(50) NOT NULL default '',
  `image_position` varchar(10) NOT NULL default '',
  `description` text NOT NULL,
  `published` tinyint(1) NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `editor` varchar(50) default NULL,
  `ordering` int(11) NOT NULL default '0',
  `access` tinyint(3) unsigned NOT NULL default '0',
  `count` int(11) NOT NULL default '0',
  `metakeywords` text NOT NULL,
  `metadescription` text NOT NULL,
  `images` text NOT NULL,
  `params` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
";
$this->createTable($table_cart_000);

$table_cart_000 = "
CREATE TABLE IF NOT EXISTS `#__digicom_currencies` (
  `id` int(11) NOT NULL auto_increment,
  `pluginid` varchar(30) NOT NULL default '',
  `currency_name` varchar(20) NOT NULL default '',
  `currency_full` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
$this->createTable($table_cart_000);

$table_cart_000 = "
CREATE TABLE IF NOT EXISTS `#__digicom_currency_symbols` (
  `id` int(11) NOT NULL auto_increment,
  `ccode` varchar(5) NOT NULL default '',
  `csym` varchar(255) NOT NULL default '',
  `cimg` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
";
$this->createTable($table_cart_000);

$table_cart_000 = "
CREATE TABLE IF NOT EXISTS `#__digicom_customers` (
  `id` int(11) NOT NULL auto_increment,
  `address` varchar(200) NOT NULL default '',
  `city` varchar(100) default NULL,
  `state` varchar(100) default NULL,
  `province` varchar(100) default NULL,
  `zipcode` varchar(20) NOT NULL default '',
  `country` varchar(100) NOT NULL default '',
  `payment_type` varchar(20) NOT NULL default '',
  `company` varchar(100) NOT NULL default '',
  `firstname` varchar(50) NOT NULL default '',
  `lastname` varchar(50) NOT NULL default '',
  `shipaddress` varchar(200) NOT NULL default '',
  `shipcity` varchar(100) default NULL,
  `shipstate` varchar(100) default NULL,
  `shipzipcode` varchar(20) NOT NULL default '',
  `shipcountry` varchar(100) default NULL,
  `person` int(2) NOT NULL default '1',
  `taxnum` varchar(11) default '',
  `taxclass` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
";
$this->createTable($table_cart_000);

$table_cart_000 = "
CREATE TABLE IF NOT EXISTS `#__digicom_customfields` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(200) NOT NULL default '',
  `options` text NOT NULL,
  `published` int(2) NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL default '0',
  `size` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
$this->createTable($table_cart_000);

$table_cart_000 = "
CREATE TABLE IF NOT EXISTS `#__digicom_emailreminders` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `type` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `ordering` int(11) NOT NULL,
  `published` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
";
$this->createTable($table_cart_000);

$table_cart_000 = "
CREATE TABLE IF NOT EXISTS `#__digicom_featuredproducts` (
  `productid` int(11) NOT NULL default '0',
  `featuredid` int(11) NOT NULL default '0',
  `planid` INT NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
$this->createTable($table_cart_000);

$table_cart_000 = "
CREATE TABLE IF NOT EXISTS `#__digicom_languages` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(200) NOT NULL,
  `fefilename` text NOT NULL,
  `befilename` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
";
$this->createTable($table_cart_000);

$table_cart_000 = "
CREATE TABLE IF NOT EXISTS `#__digicom_licensefields` (
  `licenseid` int(11) NOT NULL default '0',
  `fieldname` varchar(200) NOT NULL default '',
  `optioname` varchar(200) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
$this->createTable($table_cart_000);

$table_cart_000 = "
CREATE TABLE IF NOT EXISTS `#__digicom_licenseprodfields` (
  `fieldid` int(11) NOT NULL default '0',
  `licenseid` int(11) NOT NULL default '0',
  `optionid` int(2) NOT NULL default '-1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
$this->createTable($table_cart_000);

$table_cart_000 = "
CREATE TABLE IF NOT EXISTS `#__digicom_licenses` (
  `id` int(11) NOT NULL auto_increment,
  `licenseid` varchar(20) NOT NULL default '',
  `userid` int(11) NOT NULL default '0',
  `productid` int(11) NOT NULL default '0',
  `domain` varchar(100) NOT NULL default '',
  `amount_paid` float NOT NULL default '0',
  `orderid` int(11) NOT NULL default '0',
  `dev_domain` varchar(100) NOT NULL default '',
  `hosting_service` varchar(50) NOT NULL default '',
  `published` int(2) NOT NULL default '1',
  `ltype` varchar(200) NOT NULL default 'common',
  `purchase_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `plan_id` int(11) NOT NULL default '0',
  `download_count` int(11) NOT NULL default '0',
  `renew` int(11) NOT NULL default '0',
  `renewlicid` int(11) NOT NULL default '0',  
  PRIMARY KEY  (`id`),
  UNIQUE KEY `licenseid` (`licenseid`),
  KEY `orderid` (`orderid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
$this->createTable($table_cart_000);

$table_cart_000 = "
CREATE TABLE IF NOT EXISTS `#__digicom_licenses_notes` (
  `id` int(11) NOT NULL auto_increment,
  `lic_id` int(11) NOT NULL,
  `notes` text NOT NULL,
  `expires` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
$this->createTable($table_cart_000);

$table_cart_000 = "
CREATE TABLE IF NOT EXISTS `#__digicom_licenses_payments` (
  `id` int(11) NOT NULL auto_increment,
  `lic_id` int(11) NOT NULL,
  `amount` float NOT NULL default '0',
  `expires` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
$this->createTable($table_cart_000);

$table_cart_000 = "
CREATE TABLE IF NOT EXISTS `#__digicom_mailtemplates` (
  `id` int(11) NOT NULL auto_increment,
  `type` varchar(50) NOT NULL default '',
  `subject` text NOT NULL,
  `body` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
";
$this->createTable($table_cart_000);

$table_cart_000 = "
CREATE TABLE IF NOT EXISTS `#__digicom_orders` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL default '0',
  `order_date` int(11) NOT NULL default '0',
  `amount` float NOT NULL default '0',
  `amount_paid` float NOT NULL default '0',
  `processor` varchar(100) NOT NULL,
  `number_of_licenses` int(11) NOT NULL default '0',
  `currency` varchar(10) NOT NULL,
  `status` varchar(10) NOT NULL default '',
  `tax` float NOT NULL default '0',
  `shipping` float NOT NULL default '0',
  `promocodeid` int(11) NOT NULL default '0',
  `promocode` varchar(255) NOT NULL default '',
  `promocodediscount` float NOT NULL default '0',
  `shipto` int(2) NOT NULL default '0',
  `fullshipto` text NOT NULL,
  `published` INT NOT NULL DEFAULT '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
$this->createTable($table_cart_000);

$table_cart_000 = "
CREATE TABLE IF NOT EXISTS `#__digicom_plans` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `duration_count` int(11) NOT NULL,
  `duration_type` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  `published` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
";
$this->createTable($table_cart_000);

$table_cart_000 = "
CREATE TABLE IF NOT EXISTS `#__digicom_prodfields` (
  `fieldid` int(11) NOT NULL default '0',
  `productid` int(11) NOT NULL default '0',
  `publishing` int(2) NOT NULL default '0',
  `mandatory` int(2) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
$this->createTable($table_cart_000);

$table_cart_000 = "
CREATE TABLE IF NOT EXISTS `#__digicom_productclass` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(200) NOT NULL,
  `published` tinyint(4) NOT NULL,
  `ordering` int(11) NOT NULL,
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
";
$this->createTable($table_cart_000);

$table_cart_000 = "
CREATE TABLE IF NOT EXISTS `#__digicom_products` (
  `id` int(11) NOT NULL auto_increment,
  `sku` varchar(100) NOT NULL default '',
  `name` varchar(150) NOT NULL default '',
  `images` text NOT NULL,
  `ordering` int(11) NOT NULL default '0',
  `file` varchar(150) NOT NULL default '',
  `description` text NOT NULL,
  `publish_up` int(11) NOT NULL default '0',
  `publish_down` int(11) NOT NULL default '0',
  `checked_out` int(11) NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `published` int(11) NOT NULL default '0',
  `passphrase` varchar(150) NOT NULL default '',
  `main_zip_file` mediumtext NOT NULL,
  `encoding_files` text NOT NULL,
  `domainrequired` int(11) NOT NULL default '0',
  `articlelink` text NOT NULL,
  `articlelinkid` int(11) NOT NULL default '0',
  `articlelinkuse` int(3) NOT NULL default '0',
  `shippingtype` int(11) NOT NULL default '0',
  `shippingvalue0` float NOT NULL default '0',
  `shippingvalue1` float NOT NULL default '0',
  `shippingvalue2` float NOT NULL default '0',
  `productemailsubject` text NOT NULL,
  `productemail` text NOT NULL,
  `sendmail` int(11) NOT NULL default '1',
  `popupwidth` int(11) NOT NULL default '800',
  `popupheight` int(11) NOT NULL default '600',
  `stock` int(11) NOT NULL default '0',
  `used` int(11) NOT NULL default '0',
  `usestock` int(11) NOT NULL default '0',
  `emptystockact` int(11) NOT NULL default '0',
  `showstockleft` int(11) NOT NULL default '0',
  `fulldescription` text NOT NULL,
  `metatitle` varchar(100) NOT NULL default '',
  `metakeywords` text NOT NULL,
  `metadescription` text NOT NULL,
  `access` tinyint(3) unsigned NOT NULL default '0',
  `prodtypeforplugin` text NOT NULL,
  `taxclass` int(11) NOT NULL,
  `class` int(11) NOT NULL,
  `showqtydropdown` int(11) NOT NULL default '0',
  `priceformat` int(11) NOT NULL default '1',
  `featured` INT NOT NULL DEFAULT '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `sku` (`sku`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
";
$this->createTable($table_cart_000);

$table_cart_000 = "
CREATE TABLE IF NOT EXISTS `#__digicom_products_emailreminders` (
  `id` int(11) NOT NULL auto_increment,
  `product_id` int(11) NOT NULL,
  `emailreminder_id` int(11) NOT NULL,
  `send` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
";
$this->createTable($table_cart_000);

$table_cart_000 = "
CREATE TABLE IF NOT EXISTS `#__digicom_products_plans` (
  `id` int(11) NOT NULL auto_increment,
  `product_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `price` float NOT NULL,
  `default` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
";
$this->createTable($table_cart_000);

$table_cart_000 = "
CREATE TABLE IF NOT EXISTS `#__digicom_products_renewals` (
  `id` int(11) NOT NULL auto_increment,
  `product_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `price` float NOT NULL,
  `default` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
";
$this->createTable($table_cart_000);

$table_cart_000 = "
CREATE TABLE IF NOT EXISTS `#__digicom_product_categories` (
  `productid` int(11) NOT NULL default '0',
  `catid` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
$this->createTable($table_cart_000);

$table_cart_000 = "
CREATE TABLE IF NOT EXISTS `#__digicom_promocodes` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(200) NOT NULL default '',
  `code` varchar(100) NOT NULL default '',
  `codelimit` int(11) NOT NULL default '0',
  `amount` float NOT NULL default '0',
  `codestart` int(11) NOT NULL default '0',
  `codeend` int(11) NOT NULL default '0',
  `forexisting` int(11) NOT NULL default '0',
  `published` int(11) NOT NULL default '0',
  `aftertax` int(11) NOT NULL default '0',
  `promotype` int(11) NOT NULL default '0',
  `used` int(11) NOT NULL default '0',
  `ordering` int(11) NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
";
$this->createTable($table_cart_000);

$table_cart_000 = "
CREATE TABLE IF NOT EXISTS `#__digicom_sendmails` (
  `id` int(11) NOT NULL auto_increment,
  `date` int(11) NOT NULL default '0',
  `email` varchar(40) NOT NULL default '',
  `body` text NOT NULL,
  `flag` varchar(30) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
$this->createTable($table_cart_000);

$table_cart_000 = "
CREATE TABLE IF NOT EXISTS `#__digicom_session` (
  `sid` int(11) NOT NULL auto_increment,
  `create_time` int(11) NOT NULL default '0',
  `cart_details` text NOT NULL,
  `transaction_details` text NOT NULL,
  `shipping_details` int(11) NOT NULL,
  `processor` varchar(250) NOT NULL,
  PRIMARY KEY  (`sid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
";
$this->createTable($table_cart_000);

$table_cart_000 = "
CREATE TABLE IF NOT EXISTS `#__digicom_settings` (
  `id` int(11) NOT NULL auto_increment,
  `currency` varchar(10) NOT NULL default 'USD',
  `currency_position` int(1) NOT NULL default '0',
  `store_name` varchar(200) NOT NULL default '',
  `store_url` varchar(200) NOT NULL default '',
  `store_email` varchar(200) NOT NULL default '',
  `product_per_page` int(11) NOT NULL default '10',
  `google_account` varchar(20) NOT NULL default '',
  `country` varchar(30) NOT NULL default '',
  `state` varchar(30) NOT NULL default '',
  `city` varchar(30) NOT NULL default '',
  `tax_option` varchar(20) NOT NULL default '',
  `tax_rate` float NOT NULL default '0',
  `tax_type` varchar(20) NOT NULL default '',
  `totaldigits` int(11) NOT NULL default '5',
  `decimaldigits` int(11) NOT NULL default '2',
  `ftp_source_path` varchar(100) NOT NULL default 'media',
  `time_format` varchar(150) NOT NULL default '',
  `afteradditem` int(11) NOT NULL default '0',
  `showreplic` int(11) NOT NULL default '0',
  `idevaff` varchar(100) NOT NULL default 'notapplied',
  `askterms` int(11) NOT NULL default '0',
  `termsid` int(11) NOT NULL default '-1',
  `termsheight` int(11) NOT NULL default '-1',
  `termswidth` int(11) NOT NULL default '-1',
  `topcountries` text NOT NULL,
  `usestoremail` int(11) NOT NULL default '1',
  `catlayoutstyle` int(11) NOT NULL default '1',
  `catlayoutcol` int(11) NOT NULL default '1',
  `catlayoutrow` int(11) NOT NULL default '1',
  `prodlayouttype` int(11) NOT NULL default '0',
  `prodlayoutstyle` int(11) NOT NULL default '1',
  `prodlayoutcol` int(11) NOT NULL default '1',
  `prodlayoutrow` int(11) NOT NULL default '1',
  `orderidvar` varchar(100) NOT NULL default 'order_id',
  `ordersubtotalvar` varchar(100) NOT NULL default 'order_subtotal',
  `idevpath` varchar(255) NOT NULL default 'aff',
  `askforship` int(2) NOT NULL default '1',
  `person` int(2) NOT NULL default '1',
  `taxnum` int(11) NOT NULL default '0',
  `modbuynow` int(11) NOT NULL default '0',
  `usecimg` int(2) NOT NULL default '0',
  `showthumb` int(2) NOT NULL default '0',
  `showsku` int(2) NOT NULL default '0',
  `sendmailtoadmin` int(2) NOT NULL default '1',
  `directfilelink` int(2) NOT NULL default '0',
  `debugstore` int(2) NOT NULL default '0',
  `dumptofile` int(2) NOT NULL default '0',
  `dumpvars` text NOT NULL,
  `ftranshtml` text NOT NULL,
  `thankshtml` text NOT NULL,
  `layout_template` text NOT NULL,
  `showprodshort` int(11) NOT NULL default '0',
  `pendinghtml` text NOT NULL,
  `address` varchar(255) NOT NULL,
  `zip` varchar(100) NOT NULL,
  `phone` varchar(200) NOT NULL,
  `fax` varchar(200) NOT NULL,
  `afterpurchase` int(2) NOT NULL default '1',
  `showoid` int(2) NOT NULL default '1',
  `showoipurch` int(2) NOT NULL default '1',
  `showolics` int(2) NOT NULL default '1',
  `showopaid` int(2) NOT NULL default '1',
  `showodate` int(2) NOT NULL default '1',
  `showorec` int(2) NOT NULL default '1',
  `showlid` int(2) NOT NULL default '1',
  `showlprod` int(2) NOT NULL default '1',
  `showloid` int(2) NOT NULL default '1',
  `showldate` int(2) NOT NULL default '1',
  `showldown` int(2) NOT NULL default '1',
  `showcam` int(2) NOT NULL default '1',
  `showcpromo` int(2) NOT NULL default '1',
  `showcremove` int(2) NOT NULL default '1',
  `showccont` int(2) NOT NULL default '1',
  `showldomain` int(2) NOT NULL default '1',
  `tax_classes` int(11) NOT NULL,
  `tax_base` int(11) NOT NULL default '1',
  `tax_catalog` int(11) NOT NULL,
  `tax_shipping` int(11) NOT NULL,
  `tax_discount` int(11) NOT NULL,
  `discount_tax` int(11) NOT NULL,
  `tax_country` varchar(200) NOT NULL,
  `tax_state` varchar(200) NOT NULL,
  `tax_zip` varchar(200) NOT NULL,
  `tax_price` int(11) NOT NULL,
  `tax_summary` int(11) NOT NULL,
  `shipping_price` int(11) NOT NULL,
  `product_price` int(11) NOT NULL,
  `tax_zero` int(11) NOT NULL,
  `tax_apply` varchar(200) NOT NULL,
  `continue_shopping_url` varchar(500) NOT NULL,
  `usestorelocation` int(11) NOT NULL,
  `allowcustomerchoseclass` int(11) NOT NULL default '2',
  `takecheckout` int(11) NOT NULL default '1',
  `showlterms` int(11) NOT NULL default '0',
  `showlexpires` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
";
$this->createTable($table_cart_000);

$table_cart_000 = "
CREATE TABLE IF NOT EXISTS `#__digicom_states` (
  `id` int(11) NOT NULL auto_increment,
  `state` varchar(200) NOT NULL default '',
  `country` varchar(200) NOT NULL default '',
  `eumember` int(2) NOT NULL default '0',
  `ccode` varchar(5) NOT NULL default '',
  `scode` varchar(5) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
";
$this->createTable($table_cart_000);

$table_cart_000 = "
CREATE TABLE IF NOT EXISTS `#__digicom_tax_customerclass` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(200) NOT NULL,
  `published` tinyint(4) NOT NULL,
  `ordering` int(11) NOT NULL,
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
";
$this->createTable($table_cart_000);

$table_cart_000 = "
CREATE TABLE IF NOT EXISTS `#__digicom_tax_productclass` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(200) NOT NULL,
  `published` tinyint(4) NOT NULL,
  `ordering` int(11) NOT NULL,
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
";
$this->createTable($table_cart_000);

$table_cart_000 = "
CREATE TABLE IF NOT EXISTS `#__digicom_tax_rate` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(250) NOT NULL,
  `country` varchar(200) NOT NULL,
  `state` varchar(200) NOT NULL,
  `zip` varchar(200) NOT NULL,
  `rate` double NOT NULL,
  `published` int(11) NOT NULL default '1',
  `ordering` int(11) NOT NULL,
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `name_2` (`name`),
  UNIQUE KEY `name_3` (`name`),
  UNIQUE KEY `name_4` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
";
$this->createTable($table_cart_000);

$table_cart_000 = "
CREATE TABLE IF NOT EXISTS `#__digicom_tax_rule` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(200) NOT NULL,
  `cclass` text NOT NULL,
  `pclass` text NOT NULL,
  `trate` text NOT NULL,
  `ptype` text NOT NULL,
  `published` int(11) NOT NULL default '1',
  `ordering` int(11) NOT NULL,
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
";
$this->createTable($table_cart_000);

$table_cart_000 = "
CREATE TABLE IF NOT EXISTS `#__digicom_topleveldomains` (
  `tld_id` mediumint(9) NOT NULL auto_increment,
  `tld` varchar(6) NOT NULL default '',
  `fullname` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`tld_id`),
  KEY `tld` (`tld`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
";
$this->createTable($table_cart_000);

?>