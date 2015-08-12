<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class DigiComSiteHelperEmail {

	//mail sending function

	public static function dispatchMail($orderid, $amount, $number_of_products, $timestamp, $items, $customer, $type = 'new_order', $status = '')
	{
		$db 	= JFactory::getDbo();
		$site_config = JFactory::getConfig();
		// get sid & uid
		if (is_object($customer)) $sid = $customer->_sid;
		if (is_array($customer)) $sid = $customer['sid'];

		if (is_object($customer) && isset($customer->_user->id))  $uid = $customer->_user->id;
		if (is_array($customer)) $uid = $customer['userid'];
		if(is_numeric($customer)) $uid = $customer;
		//echo $customer;jexit();

		if ( !$uid ) return;

		$my = JFactory::getUser($uid);

		$database = JFactory::getDBO();
		$configs = JModelLegacy::getInstance( "Config", "digicomModel" );
		$configs = $configs->getConfigs();
		$order = JTable::getInstance( "Order" ,"Table");
		$order->load( $orderid );

		//echo $type;die;
		$email_settings = $configs->get('email_settings');
		$email_header_image = $email_settings->email_header_image;//jform[email_settings][email_header_image]

		if(!empty($email_header_image)){
			$email_header_image = '<img src="'.JRoute::_(JURI::root().$email_header_image).'" />';
		}
		$phone = $configs->get('phone');
		$address = $configs->get('address');

		$email_footer = $email_settings->email_footer;

		$emailinfo = $configs->get($type,'new_order');
		$email_type = $emailinfo->email_type;
		$Subject = $emailinfo->Subject;
		$recipients = $emailinfo->recipients;
		$enable = $emailinfo->enable;
		$heading = $emailinfo->heading;//jform[email_settings][heading]
		if(!$enable) return;
		//print_r($emailinfo);die;
		//-----------------------------------------------------------------------
		$path = '/components/com_digicom/emails/';

		switch ($type) {
			case 'new_order':
				$emailType = JText::_('COM_DIGIOM_NEW_ORDER');
				$filename = 'new-order.'.$email_type.'.php';
				break;

			case 'process_order':
				$emailType = JText::_('COM_DIGIOM_PROCESS_ORDER');
				$filename = 'process-order.'.$email_type.'.php';
				break;

			case 'cancel_order':
				$emailType = JText::_('COM_DIGIOM_CANCEL_ORDER');
				$filename = 'cancel-order.'.$email_type.'.php';
				break;

			case 'complete_order':
				$emailType = JText::_('COM_DIGIOM_COMPLETE_ORDER');
				$filename = 'complete-order.'.$email_type.'.php';
				break;
		}

		$override = '/html/com_digicom/emails/';
		$template = DigiComSiteHelperEmail::getTemplate();
		$client   = JApplicationHelper::getClientInfo($template->client_id);
		$filePath = JPath::clean($client->path . '/templates/' . $template->template . $override.'/'.$filename);

		//echo $filePath;die;
		if (file_exists($filePath))
		{
			$emailbody = file_get_contents($filePath);
		}
		else
		{
			$filePath = JPath::clean($client->path . $path . '/'.$filename);
			$emailbody = file_get_contents($filePath);
		}
		//echo $emailbody;die;
		//-----------------------------------------------------------------------


		//$email = $configs->get('email');

		/*$message = $email->$type->body;
		$subject = $email->$type->subject;
		*/
		$message = $emailbody;
		$subject = $Subject;

		// Replace all variables in template
		$uri = JURI::getInstance();
		$sitename = (trim( $configs->get('store_name','DigiCom Store') ) != '') ? $configs->get('store_name','DigiCom Store') : $site_config->get( 'sitename' );
		$siteurl = (trim( $configs->get('store_url','') ) != '') ? $configs->get('store_url','') : $uri->base();

		$message = str_replace( "[SITENAME]", $sitename, $message );

		$message = str_replace( "../%5BSITEURL%5D", $siteurl, $message );
		$message = str_replace( "%5BSITEURL%5D", $siteurl, $message );
		$message = str_replace( "[SITEURL]", $siteurl, $message );

		$query = "select company from #__digicom_customers where id=" . $my->id;
		$db->setQuery( $query );
		$customer_database = $db->loadAssocList();
		$copany = (isset($customer_database["0"]["copany"]) ? $customer_database["0"]["copany"] : '');

		$message = str_replace("[EMAIL_TYPE]", $emailType, $message);
		$message = str_replace("[EMAIL_HEADER]", $heading, $message);
		$message = str_replace("[HEADER_IMAGE]", $email_header_image, $message);

		$message = str_replace("[CUSTOMER_COMPANY_NAME]", $copany, $message);
		$message = str_replace( "[CUSTOMER_USER_NAME]", $my->username, $message );
		$message = str_replace( "[CUSTOMER_NAME]", $my->name, $message );
		$message = str_replace( "[CUSTOMER_EMAIL]", $my->email, $message );

		$message = str_replace( "[ORDER_DATE]", date( $configs->get('time_format','d-m-Y'), $timestamp), $message );
		$message = str_replace( "[ORDER_ID]", $orderid, $message );
		$message = str_replace( "[ORDER_AMOUNT]", $amount, $message );
		$message = str_replace( "[NUMBER_OF_PRODUCTS]", $number_of_products, $message );
		$message = str_replace( "[DISCOUNT_AMOUNT]", $order->discount, $message );
		$message = str_replace( "[ORDER_STATUS]", $status, $message );

		$message = str_replace( "[STORE_ADDRESS]", $address, $message );
		$message = str_replace( "[STORE_PHONE]", $phone, $message );
		$message = str_replace( "[FOOTER_TEXT]", $email_footer, $message );

		$displayed = array();
		$product_list = '';


		$counter = array();
		foreach ( $items as $i => $item ) {
			if ( $i < 0 )
				continue;
			if ( !isset( $counter[$item->id] ) )
				$counter[$item->id] = 1;
			$counter[$item->id]++;
		}
		foreach ( $items as $i => $item ) {
			if ( $i < 0 )
				continue;
			$optionlist = '';
			if ( !in_array( $item->name, $displayed ) ) {
				//$product_list .= $counter[$item->id]." - ".$item->name.'<br />';
				$product_list .= $item->quantity . " - " . $item->name . '<br />';
			}
			$displayed[] = $item->name;
		}
		$message = str_replace( "[PRODUCTS]", $product_list, $message );
		$email = new stdClass();
		$email->body = $message;

		//subject
		$subject = str_replace( "[SITENAME]", $sitename, $subject );
		$subject = str_replace("[CUSTOMER_COMPANY_NAME]", $copany, $subject);
		$subject = str_replace( "../%5BSITEURL%5D", $siteurl, $subject );
		$subject = str_replace( "%5BSITEURL%5D", $siteurl, $subject );
		$subject = str_replace( "[SITEURL]", $siteurl, $subject );

		$subject = str_replace( "[CUSTOMER_USER_NAME]", $my->username, $subject );
		$subject = str_replace( "[CUSTOMER_NAME]", $my->name, $subject );
		$subject = str_replace( "[CUSTOMER_EMAIL]", $my->email, $subject );

		$subject = str_replace( "[ORDER_DATE]", date( $configs->get('time_format','d-m-Y'), $timestamp ), $subject );
		$subject = str_replace( "[ORDER_ID]", $orderid, $subject );
		$subject = str_replace( "[ORDER_AMOUNT]", $amount, $subject );
		$subject = str_replace( "[NUMBER_OF_PRODUCTS]", $number_of_products, $subject );
		$subject = str_replace( "[DISCOUNT_AMOUNT]", $order->discount, $subject );
		$subject = str_replace( "[ORDER_STATUS]", $status, $subject );

		$subject = str_replace( "{site_title}", $sitename, $subject );
		$subject = str_replace( "{order_number}", $orderid, $subject );
		$subject = str_replace( "{order_date}", date( $configs->get('time_format','d-m-Y'), $timestamp ), $subject );

		$message = str_replace( "{site_title}", $sitename, $message );
		$message = str_replace( "{order_number}", $orderid, $message );
		$message = str_replace( "{order_date}", date( $configs->get('time_format','d-m-Y'), $timestamp ), $message );



		$displayed = array();
		$product_list = '';
		foreach ( $items as $i => $item ) {
			if ( $i < 0 )
				continue;
			if ( !in_array( $item->name, $displayed ) )
				$product_list .= $item->name . '<br />';
			$displayed[] = $item->name;
		}
		$subject = str_replace( "[PRODUCTS]", $product_list, $subject );

		//replace styles
		$basecolor = $email_settings->email_base_color; //
		$basebgcolor = $email_settings->email_bg_color; //
		$tmplcolor = $email_settings->email_body_color; //
		$tmplbgcolor = $email_settings->email_body_bg_color; //
		$message = str_replace( "[BASE_COLOR]", $basecolor, $message );
		$message = str_replace( "[BASE_BG_COLOR]", $basebgcolor, $message );
		$message = str_replace( "[TMPL_COLOR]", $tmplcolor, $message );
		$message = str_replace( "[TMPL_BG_COLOR]", $tmplbgcolor, $message );


		// final email subject & message
		$subject = html_entity_decode( $subject, ENT_QUOTES );
		$message = html_entity_decode( $message, ENT_QUOTES );
		//echo $message;die;

		$app = JFactory::getApplication('site');

		$mosConfig_mailfrom = $app->getCfg("mailfrom");
		$mosConfig_fromname = $app->getCfg("fromname");

		if ( $configs->get('usestoremail',1) == '1' && strlen( trim( $configs->get('store_name','DigiCom Store') ) ) > 0 && strlen( trim( $configs->get('store_email','') ) ) > 0 ) {
			$adminName2 = $configs->get('store_name','DigiCom Store');
			$adminEmail2 = $configs->get('store_email','');
		} else{
			$adminName2 = $mosConfig_fromname;
			$adminEmail2 = $mosConfig_mailfrom;
		}

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

		// Log::write( $message );
		// $orderid, $amount, $number_of_products, $timestamp, $items, $customer,
		// $type = 'new_order', $status = ''

		$info = array(
			'orderid' => $orderid,
			'amount' => $amount,
			'customer' => $customer,
			'type' => $type,
			'status' => $status
		);
		$message = $type.' email for order#'.$orderid.', status: '.$status;
		////$type, $hook, $message, $info, $status = 'complete'
		if ( $mailSender->Send() !== true ) {
			DigiComSiteHelperLog::setLog('email', 'cart dispatch email', $orderid, $message, json_encode($info),'failed');
		}else{
			DigiComSiteHelperLog::setLog('email', 'cart dispatch email', $orderid, $message, json_encode($info),'success');
		}

		if ( $email_settings->sendmailtoadmin) {
			$message = 'Order email to Admin : '.$type.' email for order#'.$orderid.', status: '.$status;

			$recipients =  $adminEmail2 . (!empty($recipients) ? ', '.$recipients : '');
			$mailSender = JFactory::getMailer();
			$mailSender->isHTML( true );
			$mailSender->Encoding = 'base64';
			$mailSender->addRecipient( $recipients );
			$mailSender->setSender( array($adminEmail2, $adminName2) );
			$mailSender->setSubject( $subject );
			$mailSender->setBody( $message );

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
	* getTemplate
	* get the site template for frontend
	*/

	public static function getTemplate(){
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