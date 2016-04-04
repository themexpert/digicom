<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;
use Joomla\Registry\Registry;

class DigiComSiteHelperEmail {

	/*
	* Method dispatchMail
	* mail sending function
	*/
	public static function dispatchMail($orderid, $amount, $number_of_products, $timestamp, $items, $customer, $type = 'new_order', $status = '')
	{
		$db 			= JFactory::getDbo();
		$jconfig 	= JFactory::getConfig();

		// get sid & uid
		if(is_object($customer)){
			$sid 		= $customer->_sid;

			if(isset($customer->_user->id)){
				$uid 	= $customer->_user->id;
			}
		}elseif(is_array($customer)){
			$sid 		= $customer['sid'];
			$uid 		= $customer['userid'];
		}elseif(is_numeric($customer)){
			$uid 		= $customer;
		}
		if ( !$uid ) return;

		$my 				= JFactory::getUser($uid);
		$database 	= JFactory::getDBO();

		$configs 		= JComponentHelper::getComponent('com_digicom')->params;
		$phone 			= $configs->get('phone');
		$address 		= $configs->get('address');

		$configinfo = $configs->get($type,'new_order');
		$emailinfo 	= new Registry;
		$emailinfo->loadObject($configinfo);

		$enable 		= $emailinfo->get('enable', 1);;
		if(!$enable) return;

		$email_type 	= $emailinfo->get('email_type', 'html');
		$Subject	 		= $emailinfo->get('Subject', 'Digicom system email');
		$recipients 	= $emailinfo->get('recipients', '');
		$heading 			= $emailinfo->get('heading', 'Digicom system email');//jform[email_settings][heading]

		$orderTable 			= JTable::getInstance( "Order" ,"Table");
		$orderTable->load( $orderid );

		$properties = $orderTable->getProperties(1);
		$order = JArrayHelper::toObject($properties, 'JObject');

		// Replace all variables in template
		$uri 			= JURI::getInstance();
		// site name n url
		$sitename = (trim( $configs->get('store_name','DigiCom Store') ) != '') ? $configs->get('store_name','DigiCom Store') : $jconfig->get( 'sitename' );
		$siteurl 	= (trim( $configs->get('store_url','') ) != '') ? $configs->get('store_url','') : $uri->base();

		//echo $type;die;
		$email_settings 		= $configs->get('email_settings');
		$email_header_image = $email_settings->email_header_image;//jform[email_settings][email_header_image]
		$email_footer = $email_settings->email_footer;

		//prepare styles
		$basecolor 		= $email_settings->email_base_color; //
		$basebgcolor 	= $email_settings->email_bg_color; //
		$tmplcolor 		= $email_settings->email_body_color; //
		$tmplbgcolor 	= $email_settings->email_body_bg_color; //

		if(!empty($email_header_image)){
			if(filter_var($email_header_image, FILTER_VALIDATE_URL)){
			  $imgLink = $email_header_image;
			}else{
				$imgLink = JURI::root() . $email_header_image;
			}

			$email_header_image = '<img src="'.JRoute::_($imgLink).'" />';
		}else{
			$email_header_image = $sitename;
		}


		//-----------------------------------------------------------------------
		$path = '/components/com_digicom/emails/';

		switch ($type) {
			case 'new_order':
				$emailType 	= JText::_('COM_DIGIOM_NEW_ORDER');
				$filename 	= 'new-order.'.$email_type.'.php';
				break;

			case 'process_order':
				$emailType 	= JText::_('COM_DIGIOM_PROCESS_ORDER');
				$filename 	= 'process-order.'.$email_type.'.php';
				break;

			case 'cancel_order':
				$emailType 	= JText::_('COM_DIGIOM_CANCEL_ORDER');
				$filename 	= 'cancel-order.'.$email_type.'.php';
				break;

			case 'refund_order':
				$emailType = JText::_('COM_DIGIOM_REFUND_ORDER');
				$filename = 'refund-order.'.$email_type.'.php';
				break;

			case 'complete_order':
				$emailType 	= JText::_('COM_DIGIOM_COMPLETE_ORDER');
				$filename 	= 'complete-order.'.$email_type.'.php';
				break;
		}

		$override = '/html/com_digicom/emails/';
		$template = DigiComSiteHelperEmail::getTemplate();
		$client   = JApplicationHelper::getClientInfo($template->client_id);
		$filePath = JPath::clean($client->path . '/templates/' . $template->template . $override.'/'.$filename);

		if (file_exists($filePath))
		{
			// $emailbody = file_get_contents($filePath);
			$emailbodypath = $filePath;
		}
		else
		{
			$filePath  = JPath::clean($client->path . $path . '/'.$filename);
			// $emailbody = file_get_contents($filePath);
			$emailbodypath = $filePath;
		}

		$query = "select * from #__digicom_customers where id=" . $my->id;
		$db->setQuery( $query );
		$customerinfo = $db->loadObject();
		$company = (isset($customerinfo->company) ? $customerinfo->company : '');

		// prepare product list
		// $layout = new JLayoutFile('email.product_list');
		// $product_list_new = $layout->render(array('products'=>$items));
		// print_r($product_list_new);die;
		// echo 4;die;
		$displayed = array();
		$product_list = '';
		foreach ( $items as $i => $item ) {
			if ( !in_array( $item->name, $displayed ) ) {
				$product_list .= $item->name . ' - (' . $item->quantity . ') <br />';
			}
			$displayed[] = $item->name;
		}
		$order_date = date( $configs->get('time_format','d-m-Y'), strtotime($timestamp) );
		
		// prepare the emailbody
    //-----------------------------------------------------------
    // accecable variables from email template:
    // $items = products object
    // $order
    // $customerinfo
    ob_start();
    include_once $emailbodypath;
    $emailbody = ob_get_contents();
    ob_end_clean();
    // print_r($emailbody);die;

		$message = $emailbody;
		$subject = $Subject;

		// prepare message body
		$message = str_replace( "[SITENAME]", $sitename, $message );
		$message = str_replace( "../%5BSITEURL%5D", $siteurl, $message );
		$message = str_replace( "%5BSITEURL%5D", $siteurl, $message );
		$message = str_replace( "[SITEURL]", $siteurl, $message );

		$message = str_replace("[EMAIL_TYPE]", $emailType, $message);
		$message = str_replace("[EMAIL_HEADER]", $heading, $message);
		$message = str_replace("[HEADER_IMAGE]", $email_header_image, $message);

		$message = str_replace("[CUSTOMER_COMPANY_NAME]", $company, $message);
		$message = str_replace( "[CUSTOMER_USER_NAME]", $my->username, $message );
		$message = str_replace( "[CUSTOMER_NAME]", $my->name, $message );
		$message = str_replace( "[CUSTOMER_EMAIL]", $my->email, $message );

		$message = str_replace( "[ORDER_DATE]", $order_date, $message );
		$message = str_replace( "[ORDER_ID]", $orderid, $message );
		$message = str_replace( "[ORDER_AMOUNT]", $amount, $message );
		$message = str_replace( "[NUMBER_OF_PRODUCTS]", $number_of_products, $message );
		$message = str_replace( "[DISCOUNT_AMOUNT]", $order->discount, $message );
		$message = str_replace( "[ORDER_STATUS]", $status, $message );

		$message = str_replace( "[STORE_ADDRESS]", $address, $message );
		$message = str_replace( "[STORE_PHONE]", $phone, $message );
		$message = str_replace( "[FOOTER_TEXT]", $email_footer, $message );

		$message = str_replace( "[PRODUCTS]", $product_list, $message );
		$message = str_replace( "[SITE_TITLE]", $sitename, $message );
		$message = str_replace( "[ORDER_NUMBER]", $orderid, $message );

		$message 			= str_replace( "[BASE_COLOR]", $basecolor, $message );
		$message 			= str_replace( "[BASE_BG_COLOR]", $basebgcolor, $message );
		$message 			= str_replace( "[TMPL_COLOR]", $tmplcolor, $message );
		$message 			= str_replace( "[TMPL_BG_COLOR]", $tmplbgcolor, $message );
		$message = str_replace("[EMAIL_TYPE]", $emailType, $message);
		
		//subject
		$subject = str_replace( "[SITENAME]", $sitename, $subject );
		$subject = str_replace("[CUSTOMER_COMPANY_NAME]", $company, $subject);
		$subject = str_replace( "../%5BSITEURL%5D", $siteurl, $subject );
		$subject = str_replace( "%5BSITEURL%5D", $siteurl, $subject );
		$subject = str_replace( "[SITEURL]", $siteurl, $subject );

		$subject = str_replace( "[CUSTOMER_USER_NAME]", $my->username, $subject );
		$subject = str_replace( "[CUSTOMER_NAME]", $my->name, $subject );
		$subject = str_replace( "[CUSTOMER_EMAIL]", $my->email, $subject );

		$subject = str_replace( "[ORDER_DATE]", $order_date, $subject );
		$subject = str_replace( "[ORDER_ID]", $orderid, $subject );
		$subject = str_replace( "[ORDER_AMOUNT]", $amount, $subject );
		$subject = str_replace( "[NUMBER_OF_PRODUCTS]", $number_of_products, $subject );
		$subject = str_replace( "[DISCOUNT_AMOUNT]", $order->discount, $subject );
		$subject = str_replace( "[ORDER_STATUS]", $status, $subject );

		$subject = str_replace( "[SITE_TITLE]", $sitename, $subject );
		$subject = str_replace( "[ORDER_NUMBER]", $orderid, $subject );
		$subject = str_replace( "[PRODUCTS]", $product_list, $subject );

		// final email subject & message
		$subject = html_entity_decode( $subject, ENT_QUOTES );
		$message = html_entity_decode( $message, ENT_QUOTES );

		$app = JFactory::getApplication('site');
		$jmailfrom = $app->getCfg("mailfrom");
		$jfromname = $app->getCfg("fromname");

		// admin email info
		if ( $jfromname != "" )
		{
			$adminName2 = $jfromname;
		}
		else
		{
			$adminName2 = $configs->get('store_name','DigiCom Store');
		}

		$adminEmail2 = $jmailfrom;

		// now override the value with digicom config
    if(!empty($email_settings->from_name)){
        $adminName2 = $email_settings->from_name;
    }
    if(!empty($email_settings->from_email)){
        $adminEmail2 = $email_settings->from_email;
    }

		$mailSender = JFactory::getMailer();
		$mailSender->isHTML( true );
		$mailSender->Encoding = 'base64';
		$mailSender->addRecipient( $my->email );
		$mailSender->setSender( array($adminEmail2, $adminName2) );
		$mailSender->setSubject( $subject );
		$mailSender->setBody( $message );

		$info = array(
			'orderid'	 	=> $orderid,
			'amount' 		=> $amount,
			'customer' 	=> $customer,
			'type' 			=> $type,
			'status' 		=> $status
		);

		$message = $type.' email for order#'.$orderid.', status: '.$status;

		if ( $mailSender->Send() !== true ) {
			DigiComSiteHelperLog::setLog('email', 'cart dispatch email', $orderid, $message, json_encode($info),'failed');
		}else{
			DigiComSiteHelperLog::setLog('email', 'cart dispatch email', $orderid, $message, json_encode($info),'success');
		}

		// Send email to admin if its enabled on email common settings
		if ( $email_settings->sendmailtoadmin)
		{
			$admin_name = 'Master';
			$payment_method = $order->processor;
			$emailbody = self::getAdminEmailBody();
			$emailbody = str_replace( "[EMAIL_HEADER]", $emailType, $emailbody );
			$emailbody = str_replace( "[ADMIN_NAME]", $admin_name, $emailbody );
			$emailbody = str_replace( "[ORDER_ID]", $orderid, $emailbody );
			$emailbody = str_replace( "[SITENAME]", $sitename, $emailbody );
			$emailbody = str_replace( "[PRODUCTS]", $product_list, $emailbody );
			$emailbody = str_replace( "[ORDER_DATE]", $order_date, $emailbody );
			$emailbody = str_replace( "[ORDER_AMOUNT]", $amount, $emailbody );
			$emailbody = str_replace( "[ORDER_STATUS]", $status, $emailbody );
			$emailbody = str_replace( "[PAYMENT_METHOD]", $payment_method, $emailbody );
			$emailbody = str_replace( "[CUSTOMER_NAME]", $my->name, $emailbody );
			$emailbody = str_replace( "[CUSTOMER_USER_NAME]", $my->username, $emailbody );
			$emailbody = str_replace( "[CUSTOMER_EMAIL]", $my->email, $emailbody );
			$emailbody = str_replace( "[FOOTER_TEXT]", $email_footer, $emailbody );

			// final email subject & message
			$message = html_entity_decode( $emailbody, ENT_QUOTES );
			// print_r($message);die;

			// admin email info
			$adminName2 = $jfromname;
			$adminEmail2 = $jmailfrom;


			$recipients =  $adminEmail2 . (!empty($recipients) ? ', '.$recipients : '');
			$mailSender = JFactory::getMailer();
			$mailSender->isHTML( true );
			$mailSender->Encoding = 'base64';
			$mailSender->addRecipient( $recipients );
			$mailSender->setSender( array($adminEmail2, $adminName2) );
			$mailSender->setSubject( $subject );
			$mailSender->setBody( $message );

			$message = $type.' email for order#'.$orderid.', status: '.$status;
			//Log::write( $message );
			if ( $mailSender->Send() !== true ) {
				DigiComSiteHelperLog::setLog('email', 'cart dispatch email', $orderid, $message, json_encode($info),'failed');
			}else{
				DigiComSiteHelperLog::setLog('email', 'cart dispatch email', $orderid, $message, json_encode($info),'success');
			}
		}

		return true;
	}

	/*
	* getAdminEmailBody
	* get the email content for admin
	*/
	public static function getAdminEmailBody()
	{
		$filename =	'admin-order.php';
		$path = '/components/com_digicom/emails/';
		$override = '/html/com_digicom/emails/';
		$template = DigiComSiteHelperEmail::getTemplate();
		$client   = JApplicationHelper::getClientInfo($template->client_id);
		$filePath = JPath::clean($client->path . '/templates/' . $template->template . $override.'/'.$filename);

		if (file_exists($filePath))
		{
			$emailbody = file_get_contents($filePath);
		}
		else
		{
			$filePath  = JPath::clean($client->path . $path . '/'.$filename);
			$emailbody = file_get_contents($filePath);
		}

		return $emailbody;
	}

	/*
	* getTemplate
	* get the site template for frontend
	*/
	public static function getTemplate()
	{
		// Get the database object.
		$db = JFactory::getDbo();
		// Build the query.
		$query = $db->getQuery(true)
			->select('*')
			->from('#__template_styles')
			->where('client_id = ' . $db->quote(0))
			->where('home = ' . $db->quote(1));

		// Check of the editor exists.
		$db->setQuery($query);
		return $db->loadObject();

	}

}
