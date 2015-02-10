<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 428 $
 * @lastmodified	$LastChangedDate: 2013-11-18 02:23:53 +0100 (Mon, 18 Nov 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

defined ('_JEXEC') or die ("Go away.");
defined ( 'DS' ) or define( 'DS', DIRECTORY_SEPARATOR );

global $isJ25;
$jv = new JVersion();
$isJ25 = $jv->RELEASE == '2.5';
if ($isJ25) {
	jimport( 'joomla.application.component.controller' );
	jimport('joomla.application.component.model');
	jimport( 'joomla.application.component.view');
	if (!class_exists('DigiComController')) {
		class DigiComController	extends JController {}
	}
	if (!class_exists('DigiComModel')) {
		class DigiComModel		extends JModel {}
	}
	if (!class_exists('DigiComView')) {
		class DigiComView		extends JView {}
	}
} else {
	if (!class_exists('DigiComController')) {
		class DigiComController	extends JControllerLegacy {}
	}
	if (!class_exists('DigiComModel')) {
		class DigiComModel		extends JModelLegacy {}
	}
	if (!class_exists('DigiComView')) {
		class DigiComView		extends JViewLegacy {}
	}
}
$class_sfx = $params->get("moduleclass_sfx", '');


$my	  			 		 = JFactory::getUser();
$mosConfig_absolute_path =JPATH_BASE; 
$mosConfig_live_site	 =JURI::base();
$database				= JFactory :: getDBO();

$http_host = explode(':', $_SERVER['HTTP_HOST'] );

if( (!empty( $_SERVER['HTTPS'] ) && strtolower( $_SERVER['HTTPS'] ) != 'off' || isset( $http_host[1] ) && $http_host[1] == 443) && substr( $mosConfig_live_site, 0, 8 ) != 'https://' ) {
	$mosConfig_live_site1 = 'https://'.substr( $mosConfig_live_site, 7 );
} else {
	$mosConfig_live_site1 = $mosConfig_live_site;
}

//show the shopping cart
jimport('joomla.application.component.model');
include_once JPATH_SITE.DS.'components'.DS.'com_digicom'.DS.'models'.DS.'cart.php';
include_once JPATH_SITE.DS.'components'.DS.'com_digicom'.DS.'models'.DS.'tax.php';

if(!class_exists("TableDigiComConfig")){
	include_once(JPATH_SITE.DS.'components'.DS.'com_digicom'.DS.'tables'.DS.'config.php');
}
if(!class_exists("TableDigiComPromo")){
	include_once(JPATH_SITE.DS.'components'.DS.'com_digicom'.DS.'tables'.DS.'promo.php');
}

include_once(JPATH_SITE.DS.'components'.DS.'com_digicom'.DS.'models'.DS.'config.php');
include_once(JPATH_SITE.DS.'components'.DS.'com_digicom'.DS.'helpers'.DS.'session.php');
include_once(JPATH_SITE.DS.'components'.DS.'com_digicom'.DS.'helpers'.DS.'helper.php');
$customer	= new DigiComSessionHelper();
$cart		= new DigiComModelCart();
$helper		= new DigiComHelper();
$config		= new DigiComModelConfig();
$configs	= $config->getConfigs();


$price_format = '%'.$configs->get('totaldigits',5).'.'.$configs->get('decimaldigits',2).'f'; 
$categ_digicom = $params->get( 'digicom_category', '' );

if (null !== $configs->get('continue_shopping_url','')){
	$continue_shopping_url = $configs->get('continue_shopping_url','');
}

if($categ_digicom != ''){
	$sql = "SELECT id FROM #__digicom_categories WHERE title LIKE '".$categ_digicom."' OR name LIKE '".$categ_digicom."'";
	$database->setQuery($sql);
	$id = $database->loadResult();	
	$cat_url = (isset($continue_shopping_url) && $continue_shopping_url != '') ? $continue_shopping_url : "index.php?option=com_digicom&controller=products&task=list&cid=" . $id;
} else {
	$cat_url = (isset($continue_shopping_url) && $continue_shopping_url != '') ? $continue_shopping_url : "index.php?option=com_digicom&controller=categories&task=listcategories";		
}

$items = $cart->getCartItems ($customer, $configs);

$cart_itemid = DigiComHelper::getCartItemid();
$and_itemid = "";
if ($cart_itemid != "0") {
	$and_itemid = "&Itemid=".$cart_itemid;
}


$layout = $params->get('layout','default');
require JModuleHelper::getLayoutPath('mod_digicom_cart', $layout);