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

require_once JPATH_SITE . '/components/com_digicom/helpers/route.php';
JLoader::discover('DigiComSiteHelper', JPATH_SITE . '/components/com_digicom/helpers');
JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_digicom/models', 'DigiComModel');

$my	  			 	= JFactory::getUser();
$database			= JFactory :: getDBO();
$app				= JFactory :: getApplication();
$input				= $app->input;

$mosConfig_absolute_path =JPATH_BASE; 
$mosConfig_live_site	 =JURI::base();

$http_host = explode(':', $_SERVER['HTTP_HOST'] );

if( (!empty( $_SERVER['HTTPS'] ) && strtolower( $_SERVER['HTTPS'] ) != 'off' || isset( $http_host[1] ) && $http_host[1] == 443) && substr( $mosConfig_live_site, 0, 8 ) != 'https://' ) {
	$mosConfig_live_site1 = 'https://'.substr( $mosConfig_live_site, 7 );
} else {
	$mosConfig_live_site1 = $mosConfig_live_site;
}

$customer	= new DigiComSiteHelperSession();
$cart		= JModelLegacy::getInstance('Cart', 'DigiComModel', array('ignore_request' => true));
$helper		= new DigiComSiteHelperDigiCom();
$configs	= JComponentHelper::getComponent('com_digicom')->params;

$class_sfx = $params->get("moduleclass_sfx", '');
$price_format = '%'.$configs->get('totaldigits').'.'.$configs->get('decimaldigits').'f'; 
$categ_digicom = $params->get( 'digicom_category', '0' );
$Itemid = $input->get('Itemid',0);

if($categ_digicom > 0){
	$cat_url = DigiComHelperRoute::getCategoryRoute($categ_digicom);		
}else{
	$cat_url = JRoute::_('index.php?option=com_digicom&view=category&id=0&Itemid='.$Itemid);
}

$items = $cart->getCartItems ($customer, $configs);
$cart_itemid = $helper->getCartItemid();
$and_itemid = "";
if ($cart_itemid != "0") {
	$and_itemid = "&Itemid=".$cart_itemid;
}


$layout = $params->get('layout','default');
require JModuleHelper::getLayoutPath('mod_digicom_cart', $layout);