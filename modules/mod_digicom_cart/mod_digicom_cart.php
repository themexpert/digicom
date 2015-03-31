<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

require_once JPATH_SITE . '/components/com_digicom/helpers/route.php';

JLoader::discover('DigiComSiteHelper', JPATH_SITE . '/components/com_digicom/helpers');
JTable::addIncludePath(JPATH_SITE . '/components/com_digicom/tables', 'Table');
JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_digicom/models', 'DigiComModel');

// Lets cache some variable
$app				= JFactory :: getApplication();
$input				= $app->input;
$doc 				= JFactory::getDocument();

$customer	= new DigiComSiteHelperSession();
$cart		= JModelLegacy::getInstance('Cart', 'DigiComModel', array('ignore_request' => true));
$configs	= JComponentHelper::getComponent('com_digicom')->params;

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
$price_format = '%'.$configs->get('totaldigits').'.'.$configs->get('decimaldigits').'f';

$list = $cart->getCartItems ($customer, $configs);

$item = $app->getMenu()->getItems('link', 'index.php?option=com_digicom&view=cart', true);
$Itemid = isset($item->id) ? '&Itemid=' . $item->id : '';

// Load style file
$doc->addStyleSheet( JUri::root(true). '/modules/mod_digicom_cart/assets/css/mod_digicom_cart.css');

require JModuleHelper::getLayoutPath('mod_digicom_cart', $params->get('layout', 'default'));