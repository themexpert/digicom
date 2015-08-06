<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class DigiComHelperEmail {

    /*
	* $type = process_order, new_order, cancel_order;
	*/
    public static function sendApprovedEmail( $orderid = 0 , $type = 'complete_order', $status = 'Active', $paid = '')
    {
        if ( $orderid < 1 )
            return;
        $db = JFactory::getDBO();
        $order = JTable::getInstance( 'Order','table' );
        $order->load( $orderid );

        $configs = JComponentHelper::getComponent('com_digicom')->params;

        $cust_info = JTable::getInstance( 'Customer','table' );
        $cust_info->load( $order->userid );

        $my = $cust_info;
        //print_r($my);jexit();
        /*
        $emailinfo = $configs->get('email');
        $message = $emailinfo->$type->body;
        $subject = $emailinfo->$type->subject;
        */

        //echo $type;die;
        $email_settings = $configs->get('email_settings');
        $email_header_image = $email_settings->email_header_image;//jform[email_settings][email_header_image]
        if(!empty($email_header_image)){
            $email_header_image = '<img src="'.JRoute::_(JURI::root().$email_header_image).'" />';
        }
        $phone = $configs->get('phone');

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
        $template = DigiComHelperEmail::getTemplate();
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


        $mes = new stdClass();

        $promo = new stdClass(); //$cart->get_promo($cust_info);
        $promo->id = $order->promocodeid;
        $promo->code = $order->promocode;
        if ( $promo->id > 0 ) {
            $promoid = $promo->id;
            $promocode = $promo->code;
        } else {
            $promoid = '0';
            $promocode = '0';
        }

        $amount = DigiComHelperDigiCom::format_price( ($paid ? $paid : $order->amount), $configs->get('currency','USD'), true, $configs );

        $timestamp = time();

        $app = JFactory::getApplication('administrator');
        $sitename = (trim( $configs->get('store_name','DigiCom Store') ) != '') ? $configs->get('store_name','DigiCom Store') : $app->getCfg( 'sitename' );
        $siteurl = (trim( $configs->get('store_url',JURI::root()) ) != '') ? $configs->get('store_url',JURI::root()) : JURI::root();

        $message = str_replace( "[SITENAME]", $sitename, $message );

        $message = str_replace("[EMAIL_TYPE]", $emailType, $message);
        $message = str_replace("[EMAIL_HEADER]", $heading, $message);
        $message = str_replace("[HEADER_IMAGE]", $email_header_image, $message);

        $message = str_replace( "../%5BSITEURL%5D", $siteurl, $message );
        $message = str_replace( "%5BSITEURL%5D", $siteurl, $message );
        $message = str_replace( "[SITEURL]", $siteurl, $message );


        $message = str_replace( "[CUSTOMER_USER_NAME]", $my->username, $message );
        $message = str_replace( "[CUSTOMER_FIRST_NAME]", $my->firstname, $message );
        $message = str_replace( "[CUSTOMER_LAST_NAME]", $my->lastname, $message );
        $message = str_replace( "[CUSTOMER_EMAIL]", $my->email, $message );

        $message = str_replace( "[ORDER_DATE]", date( $configs->get('time_format','DD-MM-YYYY'), $timestamp ), $message );
        $message = str_replace( "[ORDER_ID]", $orderid, $message );
        $message = str_replace( "[ORDER_AMOUNT]", $amount, $message );
        $message = str_replace( "[NUMBER_OF_PRODUCTS]", $order->number_of_products, $message );
        $message = str_replace( "[DISCOUNT_AMOUNT]", $order->discount, $message );
        $message = str_replace( "[ORDER_STATUS]", $status, $message );

        $message = str_replace( "[STORE_PHONE]", $phone, $message );
        $message = str_replace( "[FOOTER_TEXT]", $email_footer, $message );

        $displayed = array();
        $product_list = '';

        $sql = "select od.*, p.name from #__digicom_orders_details od, #__digicom_products p where od.productid=p.id and od.orderid=" . $orderid;
        $db->setQuery( $sql );
        $items = $db->loadObjectList();

        $product_list = "";
        foreach ( $items as $item ) {
            $product_list .= $item->quantity . " - " . $item->name . '<br />';
        }

        $message = str_replace( "[PRODUCTS]", $product_list, $message );

        //subject
        $subject = str_replace( "[SITENAME]", $sitename, $subject );
        $subject = str_replace( "../%5BSITEURL%5D", $siteurl, $subject );
        $subject = str_replace( "%5BSITEURL%5D", $siteurl, $subject );
        $subject = str_replace( "[SITEURL]", $siteurl, $subject );

        $subject = str_replace( "[CUSTOMER_USER_NAME]", $my->username, $subject );
        $subject = str_replace( "[CUSTOMER_FIRST_NAME]", $my->firstname, $subject );
        $subject = str_replace( "[CUSTOMER_LAST_NAME]", $my->lastname, $subject );
        $subject = str_replace( "[CUSTOMER_EMAIL]", $my->email, $subject );


        $subject = str_replace( "[ORDER_DATE]", date( $configs->get('time_format','DD-MM-YYYY'), $timestamp ), $subject );
        $subject = str_replace( "[ORDER_ID]", $orderid, $subject );
        $subject = str_replace( "[ORDER_AMOUNT]", $amount, $subject );
        $subject = str_replace( "[NUMBER_OF_PRODUCTS]", $order->number_of_products, $subject );
        $subject = str_replace( "[DISCOUNT_AMOUNT]", $order->discount, $subject );
        $subject = str_replace( "[ORDER_STATUS]", $status, $subject );

        $subject = str_replace( "{site_title}", $sitename, $subject );
        $subject = str_replace( "{order_number}", $orderid, $subject );
        $subject = str_replace( "{order_date}", date( $configs->get('time_format','d-m-Y'), $timestamp ), $subject );

        $message = str_replace( "{site_title}", $sitename, $message );
        $message = str_replace( "{order_number}", $orderid, $message );
        $message = str_replace( "{order_date}", date( $configs->get('time_format','d-m-Y'), $timestamp ), $message );

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



        $subject = html_entity_decode( $subject, ENT_QUOTES );
        $message = html_entity_decode( $message, ENT_QUOTES );

        // Send email to user
        //global $mosConfig_mailfrom, $mosConfig_fromname, $configs;


        $mosConfig_mailfrom = $app->getCfg( "mailfrom" );
        $mosConfig_fromname = $app->getCfg( "fromname" );
        if ( $configs->get('usestoremail',0) == '1' && strlen( trim( $configs->get('store_name','DigiCom Store') ) ) > 0 && strlen( trim( $configs->get('store_email',JFactory::getConfig()->get('mailfrom')) ) ) > 0 ) {
            $adminName2 = $configs->get('store_name','DigiCom Store');
            $adminEmail2 = $configs->get('store_email',JFactory::getConfig()->get('mailfrom'));
        } else if ( $mosConfig_mailfrom != "" && $mosConfig_fromname != "" ) {
            $adminName2 = $mosConfig_fromname;
            $adminEmail2 = $mosConfig_mailfrom;
        } else {

            $query = "SELECT name, email"
                . "\n FROM #__users"
                . "\n WHERE LOWER( usertype ) = 'superadministrator'"
                . "\n OR LOWER( usertype ) = 'super administrator'"
            ;
            $database->setQuery( $query );
            $rows = $database->loadObjectList();
            $row2 = $rows[0];
            $adminName2 = $row2->name;
            $adminEmail2 = $row2->email;
        }

        // now override the value with digicom config
        if(!empty($email_settings->from_name)){
            $adminName2 = $email_settings->from_name;
        }
        if(!empty($email_settings->from_email)){
            $adminEmail2 = $email_settings->from_email;
        }
        

        $mailSender = JFactory::getMailer();
        $mailSender->IsHTML( true );
        $mailSender->addRecipient( $my->email );
        $mailSender->setSender( array($adminEmail2, $adminName2) );
        $mailSender->setSubject( $subject );
        $mailSender->setBody( $message );

        $info = array(
            'orderid' => $orderid,
            'amount' => $amount,
            'customer' => $cust_info,
            'type' => $type,
            'status' => $status
        );
        $message = 'admin: order#'.$orderid.', type:'.$type.', status: '.$status.', amount: '.$amount;

        if ( $mailSender->Send() !== true ) {
            // lets set the email log with fal
            DigiComSiteHelperLog::setLog('email', 'admin orders email', $orderid, $message, json_encode($info),'failed');
        }else{
            // lets set the email log with success
            DigiComSiteHelperLog::setLog('email', 'admin orders email', $orderid, $message, json_encode($info),'success');
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