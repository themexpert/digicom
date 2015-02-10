<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 341 $
 * @lastmodified	$LastChangedDate: 2013-10-10 14:28:28 +0200 (Thu, 10 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

	$date_003 = JFactory::getDate();
	$date_time_now_003 = $date_003->toSql();

	$lisenses_update_003 = array(
		'purchase_date' => $date_time_now_003,
		'plan_id' => 1
	);

	$this->updateTable( '#__digicom_licenses', $lisenses_update_003, ' purchase_date = "0000-00-00 00:00:00" ' );

?>