<?php
/**

 *
 * @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 341 $
 * @lastmodified	$LastChangedDate: 2013-10-10 14:28:28 +0200 (Thu, 10 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
 * @license			GNU/GPLv3 */
defined( '_JEXEC' ) or die( "Go away." );

if(!defined('CRONDEBUGLOG')) define('CRONDEBUGLOG', '0');

function submitEmailFromBackend($order, $license)
{
	$db = JFactory::getDBO();
	$config = JFactory::getConfig();
	$products_id 	= JRequest::getVar("product_id", array(), "array");
	$user_details 	= getDigiUserDetails($order["userid"]);
	
	$from 		= $config->get('mailfrom');
	$fromname 	= $config->get('fromname');

	$recipient 	= array($user_details["0"]["email"]);
	$mode 		= true;
	$product_id_general = "";

	$tzoffset = $config->get('offset');
	$today = date('Y-m-d H:i:s', time() + $tzoffset);

	if (isset($products_id) && count($products_id) > 0)
	{
		foreach ($products_id as $key=>$product_id)
		{
			$product_id_general = $product_id;
			//start send email product -----------------------------------------------------------------------------------------
			$sql = "select `productemailsubject`, `productemail`
					from #__digicom_products
					where id=".intval($product_id);
			$db->setQuery($sql);
			$db->query();
			$result = $db->loadAssocList();
			if(isset($result) && trim($result["0"]["productemailsubject"]) != "" && trim($result["0"]["productemail"]) != "")
			{
				$plan = array();
				$email = array();
				$email["product_id"] = $product_id;

				$sql = "select u.*, c.*
						from #__users u, #__digicom_customers c
						where u.id=c.id and u.id=".intval($order["userid"]);
				$db->setQuery($sql);
				$db->query();
				$user_details = $db->loadAssocList();

				$sql = "select `name`
						from #__digicom_plans
						where id=".intval($license["plan_id"]);
				$db->setQuery($sql);
				$db->query();
				$plan["name"] = $db->loadResult();

				$email["subject"] = $subject;
				$email["body"] = $body;

				$subject = trim($result["0"]["productemailsubject"]);
				$subject = processDigiText($subject, $email, $license, $user_details, $plan);

				$body = trim($result["0"]["productemail"]);
				$body = processDigiText($body, $email, $license, $user_details, $plan);

// 				JUtility::sendMail($from, $fromname, $recipient, $subject, $body, $mode);
				$mail = JFactory::getMailer();
				$mail->sendMail($from, $fromname, $recipient, $subject, $body, $mode);
				$sql = "insert into #__digicom_logs(`userid`, `productid`, `emailname`, `to`, `subject`, `body`, `send_date`)
						values (".$user_details["0"]["id"].", ".$product_id.", 'Product Email', '".$user_details["0"]["email"]."', '".addslashes(trim($subject))."', '".addslashes($body)."', '".$today."')";
				$db->setQuery($sql);
				$db->query();
			}
			//stop send email product --------------------------------------------------------------------------------------------
		}//for each product

		//start send general email -------------------------------------------------------------------------------------------
		$sql = "select `subject`, `body` from #__digicom_mailtemplates where `type`='order'";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadAssocList();
		if(isset($result) && trim($result["0"]["body"]) != "")
		{
			$plan = array();
			$email = array();
			$email["product_id"] = $product_id_general;

			$sql = "select u.*, c.*
					from #__users u, #__digicom_customers c
					where u.id=c.id and u.id=".intval($order["userid"]);
			$db->setQuery($sql);
			$db->query();
			$user_details = $db->loadAssocList();

			$sql = "select `name`
					from #__digicom_plans
					where id=".intval($license["plan_id"]);
			$db->setQuery($sql);
			$db->query();
			$plan["name"] = $db->loadResult();

			$email["subject"] = $subject;
			$email["body"] = $body;

			$subject = trim($result["0"]["subject"]);
			$subject = processDigiText($subject, $email, $license, $user_details, $plan);

			$body = trim($result["0"]["body"]);
			$body = processDigiText($body, $email, $license, $user_details, $plan);
// 			JUtility::sendMail($from, $fromname, $recipient, $subject, $body, $mode);
			$mail = JFactory::getMailer();
			$mail->sendMail($from, $fromname, $recipient, $subject, $body, $mode);

			$sql = "insert into #__digicom_logs(`userid`, `productid`, `emailname`, `to`, `subject`, `body`, `send_date`)
					values (".$user_details["0"]["id"].", ".$product_id_general.", 'Order Email', '".$user_details["0"]["email"]."', '".addslashes(trim($subject))."', '".addslashes($body)."', '".$today."')";
			$db->setQuery($sql);
			$db->query();
		}
		//stop send general email --------------------------------------------------------------------------------------------

	}//if products on order
	return true;
}

function getProductEmails($product_id)
{
	$db = JFactory::getDBO();
	$sql = "select per.*, er.*
			from #__digicom_products_emailreminders per, #__digicom_emailreminders er
			where per.product_id=".intval($product_id)."
			  and per.emailreminder_id=er.id
			  and er.published=1";
	$db->setQuery($sql);
	$db->query();
	$result = $db->loadAssocList();
	return $result;
}

function getDigiPlanExpiration()
{
	$db = JFactory::getDBO();
	$sql = "select * from #__digicom_plans";
	$db->setQuery($sql);
	$db->query();
	$result = $db->loadAssocList("id");
	return $result;
}

function getTimeFormat($value)
{
	$time = "";
	switch($value){
		case "1" : 
			$time = "hour";
			break;
		case "2" :
			$time = "day";
			break;
		case "3" :
			$time = "month";
			break;
		case "4" :
			$time = "year";
			break;
	}
	return $time;
}

function getDigiUserDetails($id)
{
	$db = JFactory::getDBO();
	$sql = "select *
			from #__users
			where id=".intval($id);
	$db->setQuery($sql);
	$db->query();
	$result = $db->loadAssocList();
	return $result;
}

function getDigiCustomerDetails($id)
{
	$db = JFactory::getDBO();
	$sql = "select *
			from #__digicom_customers
			where id=".intval($id);
	$db->setQuery($sql);
	$db->query();
	$result = $db->loadAssocList();
	return $result;
}

function format_price($price, $ccode)
{
	$db = JFactory::getDBO();
	$code = 0;
	$sql = "select id, csym
			from #__digicom_currency_symbols
			where ccode='".strtoupper($ccode)."'";
	$db->setQuery($sql);
	$codea = $db->loadObjectList();

	if (count($codea) > 0){
		$code = $codea[0]->id;
	}
	else
	{
		$code = 0;
	}
	if ($code > 0)
	{
		$ccode = $codea["0"]->csym;
		$ccode = explode(",", $ccode);
		foreach ($ccode as $i => $code)
		{
			$ccode[$i] = "&#".trim($code).";";
		}
		$ccode = implode("", $ccode);
	}
	else
	{
		$ccode = "";
	}
	return $ccode.$price;
}

function processDigiText($text, $email, $license_value, $user_details, $plan)
{
	$config = JFactory::getConfig();
	$customer_details = getDigiCustomerDetails($user_details["0"]["id"]);
	$site_name = $config->get('sitename');
	$user_email = $user_details["0"]["email"];
	$first_name = $customer_details["0"]["firstname"];
	$last_name = $customer_details["0"]["lastname"];
	$site_url = JURI::root();
	$user_name = $user_details["0"]["username"];

	$db = JFactory::getDBO();
	$sql = "select catid
			from #__digicom_product_categories
			where productid=".intval($email["product_id"]);
	$db->setQuery($sql);
	$db->query();
	$category_id = $db->loadResult();

	$product_url = JURI::root()."index.php?option=com_digicom&controller=products&task=view&pid=".intval($email["product_id"])."&cid=".intval($category_id);
	$product_url = '<a href="'.$product_url.'">'.$product_url.'</a>';
	$renew_url = JURI::root()."index.php?option=com_digicom&controller=cart&task=add&pid=".intval($email["product_id"])."&cid=".intval($category_id);
	$renew_url = '<a href="'.$renew_url.'">'.$renew_url.'</a>';
	$subscription_term = $plan["name"];
	$license_number = $license_value["licenseid"];
	$my_licenses = JURI::root()."index.php?option=com_digicom&view=licenses";
	$my_licenses = '<a href="'.$my_licenses.'">'.$my_licenses.'</a>';

	$sql = "select name
			from #__digicom_products
			where id=".intval($license_value["productid"]);
	$db->setQuery($sql);
	$db->query();
	$product_name = $db->loadResult();
	$expire_date = $license_value["expires"];
	$my_orders = JURI::root()."index.php?option=com_digicom&controller=orders&task=list";
	$my_orders = '<a href="'.$my_orders.'">'.$my_orders.'</a>';

	$sql = "select currency from #__digicom_settings";
	$db->setQuery($sql);
	$db->query();
	$currency = $db->loadResult();

	$product_id = intval($email["product_id"]);
	$sql = "select dp.name, dpr.price
			from #__digicom_plans dp, #__digicom_products_renewals dpr
			where dpr.plan_id = dp.id
			  and dpr.product_id=".$product_id;
	$db->setQuery($sql);
	$db->query();
	$product_plans = $db->loadAssocList();
	$renew_term = "";
	if(isset($product_plans) && count($product_plans) > 0){
		foreach($product_plans as $key=>$value){
			$price = format_price($value["price"], $currency);
			$renew_term .= $value["name"].": ".$price."\n";
		}
	}

	$order_id = $license_value["orderid"];
	$sql = "select `amount_paid`
			from #__digicom_orders
			where id=".intval($order_id);
	$db->setQuery($sql);
	$db->query();
	$order_amount = $db->loadResult();

	$tzoffset = $config->get('offset');
	$today = date('Y-m-d H:i:s', time() + $tzoffset);

	$text = str_replace("[SITENAME]", $site_name, $text);
	$text = str_replace("[CUSTOMER_EMAIL]", $user_email, $text);
	$text = str_replace("[CUSTOMER_FIRST_NAME]", $first_name, $text);
	$text = str_replace("[SITEURL]", $site_url, $text);
	$text = str_replace("[CUSTOMER_USER_NAME]", $user_name, $text);
	$text = str_replace("[RENEW_URL]", $renew_url, $text);
	$text = str_replace("[PRODUCT_URL]", $product_url, $text);
	$text = str_replace("[CUSTOMER_LAST_NAME]", $last_name, $text);
	$text = str_replace("[SUBSCRIPTION_TERM]", $subscription_term, $text);
	$text = str_replace("[LICENSE_NUMBER]", $license_number, $text);
	$text = str_replace("[MY_LICENSES]", $my_licenses, $text);
	$text = str_replace("[PRODUCT_NAME]", $product_name, $text);
	$text = str_replace("[EXPIRE_DATE]", $expire_date, $text);
	$text = str_replace("[MY_ORDERS]", $my_orders, $text);
	$text = str_replace("[RENEW_TERM]", $renew_term, $text);
	$text = str_replace("[TODAY_DATE]", $today, $text);
	$text = str_replace("[ORDER_AMOUNT]", $order_amount, $text);
	$text = str_replace("[ORDER_ID]", $order_id, $text);
	$text = str_replace("[CUSTOMER_COMPANY_NAME]", $user_details["0"]["copany"], $text);

	return $text;
}

function sendEmail($email, $license_value, $plan)
{
	$subject = $email["subject"];
	$body = $email["body"];

	$user_details = getDigiUserDetails($license_value["userid"]);
	$config 	= JFactory::getConfig();
	$from 		= $config->get('mailfrom');
	$fromname 	= $config->get('fromname');

	$recipient = array($user_details["0"]["email"]);
	$mode = true;
	$subject_procesed = processDigiText($subject, $email, $license_value, $user_details, $plan);
	$body_procesed = processDigiText($body, $email, $license_value, $user_details, $plan);

	$db = JFactory::getDBO();
	$sql = "select count(*)
			from #__digicom_logs
			where `userid`=".intval($user_details["0"]["id"])."
			  and `productid`=".intval($email["product_id"])."
			  and `emailid`=".intval($email["id"]);
	$db->setQuery($sql);
	$result = $db->loadResult();

	if(intval($result) == "0"){
		$tzoffset = $config->get('offset');
		$today = date('Y-m-d H:i:s', time() + $tzoffset);

		JUtility::sendMail($from, $fromname, $recipient, $subject_procesed, $body_procesed, $mode);
		$sql = "insert into #__digicom_logs(`userid`, `productid`, `emailname`, `emailid`, `to`, `subject`, `body`, `send_date`)
				values (".$user_details["0"]["id"].", ".$email["product_id"].", '".$email["name"]."', ".$email["id"].", '".$user_details["0"]["email"]."', '".addslashes(trim($subject_procesed))."', '".addslashes($body_procesed)."', '".$today."')";
		$db->setQuery($sql);
		$db->query();
	}
}

function cronjobs()
{
	$jnow = JFactory::getDate();
	$date_today = $jnow->toSQL();
	$date_today_int = strtotime($date_today);
	$db = JFactory::getDBO();
	$sql = "select last_check_date
			from #__digicom_settings
			where id=1";
	$db->setQuery($sql);
	$db->query();
	$last_check_date = $db->loadResult();
	$int_last_check = strtotime($last_check_date);
	$day_last_check = date('d', $int_last_check);
	$day_today = date('d');

	if($day_today != $day_last_check)
	{
		//if today not search
		$sql = "select id
				from #__digicom_plans
				where duration_count=-1
				  and duration_type=0";
		$db->setQuery($sql);
		$db->query();
		$unlimited_plan_id = intval($db->loadResult());

		$sql = "select l.licenseid, l.userid, l.productid, l.purchase_date, l.expires, l.plan_id
				from #__digicom_licenses l
				where l.plan_id <> ".$unlimited_plan_id."
				  and l.published = 1
				order by id asc";
		$db->setQuery($sql);
		$db->query();
		$all_licenses = $db->loadAssocList();
		$all_plans = getDigiPlanExpiration();

		if(isset($all_licenses) && count($all_licenses) > 0)
		{
			foreach($all_licenses as $license_key=>$license_value)
			{
				$date_today_int = strtotime($date_today);
				$product_id = $license_value["productid"];
				$plan_id = intval($license_value["plan_id"]);
				$emails_for_product = getProductEmails($product_id);

				if (isset($emails_for_product) && count($emails_for_product) > 0)
				{
					if ($all_plans[$plan_id]["duration_type"] != "0")
					{
						foreach ($emails_for_product as $key=>$email)
						{
							$send_email = false;
							$actual_date = $jnow->toSql();
							$actual_date_int = strtotime($actual_date);
							$actual_date_string = date("Y-m-d", $actual_date_int);
		
							$license_expiration = $license_value["expires"];
							$license_expiration_int = strtotime($license_expiration);
							$license_expiration_string = date("Y-m-d", $license_expiration_int);

							$license_purchase = $license_value["purchase_date"];
							$license_purchase_int = strtotime($license_purchase);
							$license_purchase_string = date("Y-m-d", $license_purchase_int);

							/*
							 * New email reminder trigger system
							 */
							if ($email["date_calc"] == 'expiration')
							{
								// Based on "Expiration date" -- $license_expiration_int
								$license_date_calc = $license_expiration_int;
							}
							else
							{
								// Based on "Purchase date" -- $license_purchase_int
								$license_date_calc = $license_purchase_int;
							}
							if ($email["calc"] == 'before')
							{
								// Based on "Before"
								$alert_date = strtotime("-" . $email["type"] . " " . $email["period"], $license_date_calc);
								$alert_date_string = date("Y-m-d", $alert_date);
								if ($actual_date_string == $alert_date_string)
								{
									$send_email = true;
								}
							}
							else
							{
								// Based on "After"
								$alert_date = strtotime("+" . $email["type"] . " " . $email["period"], $license_date_calc);
								$alert_date_string = date("Y-m-d", $alert_date);
								if ($actual_date_string == $alert_date_string)
								{
									$send_email = true;
								}
							}

							if ($send_email === true)
							{
								// We must to send email
								sendEmail($email, $license_value, $all_plans[$plan_id]);
								$sql = "update #__digicom_products_emailreminders
										set send=1
										where product_id=".intval($license_value["productid"])."
										  and emailreminder_id=".intval($email["id"]);
								$db->setQuery($sql);
								$db->query();
							}
						}
					} // Somethime this product will expire,  0 = unlimited
				} // We have emails for check to send or not
			} // For each license
			$sql = "update #__digicom_settings
					set last_check_date ='".$date_today."'";
			$db->setQuery($sql);
			$db->query();
		} // If we have licenses

		// Expire/Validate all user products
		require_once(JPATH_SITE.DS."administrator".DS."components".DS."com_digicom".DS."helpers".DS."helper.php");

		$sql = "SELECT DISTINCT(`userid`)
				FROM #__digicom_licenses
				WHERE DATE(`expires`) = CURDATE()
				AND `expires` < NOW()";

		$db->setQuery($sql);
		$db->query();
		$rows = $db->loadObjectList();

		foreach($rows as $row) {
			DigiComAdminHelper::expireUserProduct($row->userid);
		}
	} // If today not search
} // End function
