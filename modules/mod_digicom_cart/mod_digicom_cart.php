<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

JLoader::discover('DigiComSiteHelper', JPATH_SITE . '/components/com_digicom/helpers');
JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_digicom/models', 'DigiComModel');

// Lets cache some variable
$app	   = JFactory :: getApplication();
$input	   = $app->input;

$customer  = new DigiComSiteHelperSession();
$cart	   = JModelLegacy::getInstance('Cart', 'DigiComModel', array('ignore_request' => true));
$configs   = JComponentHelper::getComponent('com_digicom')->params;
$list      = $cart->getCartItems ($customer, $configs);
$tax       = $cart->calc_price($list, $customer, $configs);
$item      = $app->getMenu()->getItems('link', 'index.php?option=com_digicom&view=cart', true);
$Itemid    = isset($item->id) ? '&Itemid=' . $item->id : '';

$moduleclass_sfx  = htmlspecialchars($params->get('moduleclass_sfx'));
$price_format     = '%'.$configs->get('totaldigits').'.'.$configs->get('decimaldigits').'f';

require JModuleHelper::getLayoutPath('mod_digicom_cart', $params->get('layout', 'default'));
