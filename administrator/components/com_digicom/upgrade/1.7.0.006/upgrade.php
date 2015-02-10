<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 341 $
 * @lastmodified	$LastChangedDate: 2013-10-10 14:28:28 +0200 (Thu, 10 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

	// Default EmailReminders
	$emailreminders_006 = array(
		array(
			'id' => 1,
			'name' => 'Subscription expired!', 
			'type' =>  0,
			'subject' => 'Subscription to [PRODUCT_NAME] has expired!',
			'body' => '<p>Dear [CUSTOMER_FIRST_NAME], <br /> <br /> Your [SUBSCRIPTION_TERM] subscription to [PRODUCT_NAME] has expired! <br /> <br /> Please click on the link below to renew it: <br /> <br /> [RENEW_URL]	   <br /> <br /> Remember, you can always access your licenses here: <br /> <br /> [MY_LICENSES] <br /> <br /> Thank you! <br /> <br /> [SITEURL]</p>',
			'ordering' => 3,
			'published' => 1
		),
		array(
			'id' => 2,
			'name' => 'Subscription about to expired!',
			'type' =>  2,
			'subject' => 'Subscription to [PRODUCT_NAME] is about to expired!',
			'body' => '<p>Dear [CUSTOMER_FIRST_NAME], <br /> <br /> Your [SUBSCRIPTION_TERM] subscription to [PRODUCT_NAME] will expire in 2 days on [EXPIRE_DATE]  <br /> <br /> Please click on the link below to renew it: <br /> <br /> [RENEW_URL]	   <br /> <br /> Remember, you can always access your licenses here: <br /> <br /> [MY_LICENSES] <br /> <br /> Thank you! <br /> <br /> [SITEURL]</p>',
			'ordering' => 2,
			'published' => 1
		),
		array(
			'id' => 3,
			'name' => 'Subscription expired!', 
			'type' =>  7,
			'subject' => 'Subscription to [PRODUCT_NAME] is about to expired!',
			'body' => '<p>Dear [CUSTOMER_FIRST_NAME], <br /> <br /> Your [SUBSCRIPTION_TERM] subscription to [PRODUCT_NAME] has expired on [EXPIRE_DATE]! <br /> <br /> Please click on the link below to renew it: <br /> <br /> [RENEW_URL]	   <br /> <br /> Remember, you can always access your licenses here: <br /> <br /> [MY_LICENSES] <br /> <br /> Thank you! <br /> <br /> [SITEURL]</p>',
			'ordering' => 3,
			'published' => 1
		)
	);

	$this->insertTable( '#__digicom_emailreminders', $emailreminders_006 );

?>