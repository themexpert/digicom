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
// Admin Template for All types of orders
//----------------------------------------------
?>

= [EMAIL_HEADER] =
=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=

Hi [ADMIN_NAME],
This is an email to let you inform about your order # [ORDER_ID] at [SITENAME]. Here is the details:

=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=

[PRODUCTS]

=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=

Order ID # [ORDER_ID] (<time>[ORDER_DATE]</time>)
Order Amount : [ORDER_AMOUNT]
Order Status : [ORDER_STATUS]
Payment Method : [PAYMENT_METHOD]

Customer Name : [CUSTOMER_NAME]
Customer Username : [CUSTOMER_USER_NAME]
Customer Email : [CUSTOMER_EMAIL]

Thanks

[FOOTER_TEXT]
