<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class DigiComModelDigiCom extends JModelList
{
	protected $_configs = null;
	protected $_id = null;

	public function __construct () {
		parent::__construct();
		$this->_id = 1;
	}

	function getStartEndDate($report){
		$return = array();
		$date = new DateTime('now');
		$date->modify('first day of this month');
		$return[] = $date->format('Y-m-d') . ' 00:00:00';

		$date->modify('first day of next month');
		$return[] = $date->format('Y-m-d') . ' 00:00:00';

		return $return;
	}

	function getreportTotal($type = ''){
		//purchase_date
		$db = JFactory::getDBO();
		$report = JRequest::getVar("report", "monthly");
		$return = $this->getStartEndDate($report);
		// $start_date = "";
		// $end_date = "";


		// $return = $this->getStartEndDate($report);
		// //print_r($return );die;
		// $startdate_str = $return["0"];
		// $enddate_str = $return["1"];

		// if(trim($startdate_str) != ""){
		// 	$start_date = strtotime($startdate_str);
		// }

		// if(trim($enddate_str) != ""){
		// 	$end_date = strtotime($enddate_str);
		// 	if($end_date === FALSE){
		// 		$enddate_str = date("Y-M-d");
		// 		$end_date = strtotime($enddate_str);
		// 	}
		// 	$end_date = strtotime("+1 days", $end_date);
		// }

		// $and = "";
		// $and_products = "";
		// if(trim($start_date) != ""){
		// 	$start_date = date("Y-m-d H:i:s", $start_date);
		// 	$and_products .= " and od.purchase_date >= '".$start_date."'";
		// 	$start_date = strtotime($start_date);
		// 	$and .= " and o.order_date >= ".$start_date;
		// }
		// if(trim($end_date) != ""){
		// 	$end_date = date("Y-m-d H:i:s", $end_date);
		// 	$and_products .= " and od.purchase_date < '".$end_date."'";
		// 	$end_date = strtotime($end_date);
		// 	$and .= " and o.order_date < ".$end_date;
		// }
		/*
		$sql = "SELECT SUM(CASE WHEN o.`amount_paid` = '-1' THEN o.`amount` ELSE o.`amount_paid` END) as total
				FROM #__digicom_orders AS o
				WHERE 1=1 ".$and;

		*/
		$sql = "SELECT SUM(CASE WHEN `o`.`amount_paid` = '-1' THEN `o`.`amount` ELSE `o`.`amount_paid` END) as `total`
				FROM `#__digicom_orders` AS `o`
				WHERE `o`.`order_date` between '".$return[0] ."' AND curdate()" ;
		$db->setQuery($sql);
		$db->query();
		$total = $db->loadResult();
		//print_r($total);die;
		// Get chargebacks total
		// $sql = "SELECT SUM(od.`cancelled_amount`) as total
		// 		FROM #__digicom_orders_details AS od
		// 		WHERE 1=1 AND od.cancelled=1 ".$and_products;
		$sql = "SELECT SUM(od.`cancelled_amount`) as total
				FROM #__digicom_orders_details AS od
				WHERE 1=1 AND od.cancelled=1 AND `od`.`purchase_date` between '".$return[0] ."' AND curdate()";
		$db->setQuery($sql);
		$db->query();
		$chargebacks = $db->loadResult();

		// Get chargebacks total
		//$sql = "SELECT SUM(od.`cancelled_amount`) as total
		//		FROM #__digicom_orders_details AS od
		//		WHERE 1=1 AND od.cancelled=2 ".$and_products;
		$sql = "SELECT SUM(od.`cancelled_amount`) as total
				FROM #__digicom_orders_details AS od
				WHERE 1=1 AND od.cancelled=2 AND `od`.`purchase_date` between '".$return[0] ."' AND curdate()";
		$db->setQuery($sql);
		$db->query();
		$refunds = $db->loadResult();

		// Get deleted total
		// $sql = "SELECT SUM(od.`cancelled_amount`) as total
		// 		FROM #__digicom_orders_details AS od
		// 		WHERE 1=1 AND od.cancelled=3 ".$and_products;
		$sql = "SELECT SUM(od.`cancelled_amount`) as total
				FROM #__digicom_orders_details AS od
				WHERE 1=1 AND od.cancelled=3 AND `od`.`purchase_date` between '".$return[0] ."' AND curdate()";
		$db->setQuery($sql);
		$db->query();
		$deleted = $db->loadResult();

		return $total - $chargebacks - $refunds - $deleted;
	}

	function getreportOrders(){
		$db = JFactory::getDBO();
		$report = JRequest::getVar("report", "monthly");
		$return = $this->getStartEndDate($report);

		$sql = "select count(*)
				from #__digicom_orders o
				where 1=1 AND `o`.`order_date` between '".$return[0] ."' AND curdate()";
		$db->setQuery($sql);
		$db->query();
		$total = $db->loadResult();

		// $sql = "select count(*)
		// 		from #__digicom_orders o
		// 		where 1=1 and o.status='Pending' ".$and;
		$sql = "select count(*)
		 		from #__digicom_orders o
		 		where 1=1 and o.status='Pending' AND `o`.`order_date` between '".$return[0] ."' AND curdate()";
		$db->setQuery($sql);
		$db->query();
		$pending = $db->loadResult();


		$sql = "select count(*)
				from #__digicom_orders o
				where 1=1 AND `o`.`amount` = '0.000' AND `o`.`order_date` between '".$return[0] ."' AND curdate()";
		$db->setQuery($sql);
		$db->query();
		$free = $db->loadResult();

		$sql = "select count(*)
				from #__digicom_orders o
				where 1=1 AND `o`.`amount` != '0.000' AND `o`.`status` ='Active' AND`o`.`order_date` between '".$return[0] ."' AND curdate()";
		$db->setQuery($sql);
		$db->query();
		$paid = $db->loadResult();



		return array('total'=>$total,'pending'=>$pending, 'free'=> $free, 'paid'=>$paid);
	}

	function getreportCustomer(){
		$db = JFactory::getDBO();
		$report = JRequest::getVar("report", "monthly");
		$return = $this->getStartEndDate($report);
		$startdate_str = $return["0"];
		$enddate_str = $return["1"];
		$and = '';

		if(trim($startdate_str) != ""){
			$and .= " and c.registered >= '".$startdate_str."'";
		}
		if(trim($enddate_str) != ""){
			$and .= " and c.registered < '".$enddate_str."'";
		}

		$sql = "select count(*)
				from #__digicom_customers c
				where 1=1 ".$and;
				//echo $sql;die;
		$db->setQuery($sql);
		$db->query();
		$total = $db->loadResult();

		return $total;
	}

	/*function getreportLicenses($type){
		$db = JFactory::getDBO();
		$report = JRequest::getVar("report", "daily");
		$start_date = "";
		$end_date = "";

		if($report == "daily"){
			$return = $this->getStartEndDate($report);
			$start_date = strtotime($return["0"]);
			$end_date = strtotime($return["1"]);
			$start_date = date("Y-m-d H:i:s", $start_date);
			$end_date = date("Y-m-d H:i:s", $end_date);
		}
		elseif($report == "weekly" || $report == "monthly" || $report == "yearly"){
			$return = $this->getStartEndDate($report);
			$startdate_str = $return["0"];
			$enddate_str = $return["1"];

			if(trim($startdate_str) != ""){
				$start_date = strtotime($startdate_str);
				$start_date = date("Y-m-d H:i:s", $start_date);
			}

			if(trim($enddate_str) != ""){
				$end_date = strtotime($enddate_str);
				if($end_date === FALSE){
					$enddate_str = date("Y-M-d");
					$end_date = strtotime($enddate_str);
				}
				$end_date = strtotime("+1 days", $end_date);
				$end_date = date("Y-m-d H:i:s", $end_date);
			}
		}
		elseif($report == "custom"){
			$startdate_str = JRequest::getVar("startdate", "");
			$enddate_str = JRequest::getVar("enddate", "");

			if(trim($startdate_str) != ""){
				$start_date = strtotime($startdate_str);
				$start_date = date("Y-m-d H:i:s", $start_date);
			}

			if(trim($enddate_str) != ""){
				$end_date = strtotime($enddate_str);
				if($end_date === FALSE){
					$enddate_str = date("Y-M-d");
					$end_date = strtotime($enddate_str);
				}
				$end_date = strtotime("+1 days", $end_date);
				$end_date = date("Y-m-d H:i:s", $end_date);
			}
		}

		$and = "";
		if(trim($start_date) != ""){
			$and .= " and od.purchase_date >= '".$start_date."'";
		}
		if(trim($end_date) != ""){
			$and .= " and od.purchase_date < '".$end_date."'";
		}
		$sql = "select count(*)
				from #__digicom_orders_details od
				where 1=1 and od.published=1 ".$and;
		$db->setQuery($sql);
		$db->query();
		$total = $db->loadResult();
		// Get chargebacks total
		$sql = "SELECT count(*)
				FROM #__digicom_orders_details AS od
				WHERE 1=1 AND od.cancelled=1 ".$and;
		$db->setQuery($sql);
		$db->query();
		$chargebacks = $db->loadResult();
		// Get chargebacks total
		$sql = "SELECT count(*)
				FROM #__digicom_orders_details AS od
				WHERE 1=1 AND od.cancelled=2 ".$and;
		$db->setQuery($sql);
		$db->query();
		$refunds = $db->loadResult();
		// Get deleted total
		$sql = "SELECT count(*)
				FROM #__digicom_orders_details AS od
				WHERE 1=1 AND od.cancelled=3 ".$and;
		$db->setQuery($sql);
		$db->query();
		$deleted = $db->loadResult();
		if ($type == '')
		{
			return $total - $chargebacks - $refunds - $deleted;
		}
		elseif ($type == 'chargebacks')
		{
			return $chargebacks;
		}
		elseif ($type == 'refunds')
		{
			return $refunds;
		}
	}*/

	public function getConfigs(){
		$comInfo = JComponentHelper::getComponent('com_digicom');
		return $comInfo->params;
	}

	public function getMonthlyDay(){

			$db = JFactory::getDBO();
			$report = JRequest::getVar("report", "monthly");
			$return = $this->getStartEndDate($report);
			$startdate = $return[0];
			$enddate = $return[1];

			$start_date_int = strtotime($startdate);
			$end_date_int = strtotime($enddate);
			$dayChart = array();
			for($i=$start_date_int;$i<$end_date_int;$i=$nextday){
				$dayChart = '';
			}
			$edit_line[] = "Mon<br/>".date("m-d", $start_date_int);
			$edit_line[] = "Tues<br/>".date("m-d", strtotime("+1 days", $start_date_int));
			$edit_line[] = "Wed<br/>".date("m-d", strtotime("+2 days", $start_date_int));
			$edit_line[] = "Thurs<br/>".date("m-d", strtotime("+3 days", $start_date_int));
			$edit_line[] = "Fri<br/>".date("m-d", strtotime("+4 days", $start_date_int));
			$edit_line[] = "Sat<br/>".date("m-d", strtotime("+5 days", $start_date_int));
			$edit_line[] = "Sun<br/>".date("m-d", strtotime("+6 days", $start_date_int));
	}
}
