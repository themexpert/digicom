<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;
//----------------------------------------------
// Processing Order Raw Template , text version
//----------------------------------------------
?>

= [EMAIL_HEADER] =


Hi, [CUSTOMER_USER_NAME]
This is an email to inform you about your order#[ORDER_ID] is in progress at [SITENAME]. The order is as follows

Order #[ORDER_ID] (<time datetime="[ORDER_DATE]">[ORDER_DATE]</time>)
=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=

[PRODUCTS]

=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=

Store address : [STORE_ADDRESS] [STORE_PHONE]

[FOOTER_TEXT]