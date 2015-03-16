<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 341 $
 * @lastmodified	$LastChangedDate: 2013-10-10 14:28:28 +0200 (Thu, 10 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

defined ('_JEXEC') or die ("Go away.");

class DigiComModelStats extends JModelLegacy
{
	var $_configs = null;
	var $_id = null;

	function __construct () {
		parent::__construct();
		$this->_id = 1;
	}

	function getPaginationDate($configs){
		$report = JRequest::getVar("report", "daily");
		$pas = JRequest::getVar("pas", "0");
		$action = JRequest::getVar("action", "");
		if(trim($action) == ""){
			if($report == "daily"){
				echo "(".date($configs->get('time_format','DD-MM-YYYY')).")";
			}
			elseif($report == "weekly"){
				$today = strtotime(date("Y-m-d"));
				$date = $this->getStartEndDate($report);
				echo "(".date($configs->get('time_format','DD-MM-YYYY'), strtotime($date["0"]))." ".JText::_("DSTO")." ".date($configs->get('time_format','DD-MM-YYYY'), strtotime($date["1"])).")";
			}
		}
		elseif($action != ""){
			if($report == "daily"){
				$today = strtotime(date("Y-m-d"));
				$today = strtotime("-".intval($pas)." days", $today);
				echo date($configs->get('time_format','DD-MM-YYYY'), $today);
			}
			elseif($report == "weekly"){
				$date = $this->getStartEndDate($report);
				$start_date = strtotime($date["0"]);
				$end_date = strtotime($date["1"]);
				echo "(".date($configs->get('time_format','DD-MM-YYYY'), $start_date)." ".JText::_("DSTO")." ".date($configs->get('time_format','DD-MM-YYYY'), $end_date).")";
			}
			elseif($report == "monthly"){
				$date = $this->getStartEndDate($report);
				$start_date = strtotime($date["0"]);
				$end_date = strtotime($date["1"]);
				echo "(".date("Y", $start_date).")";
			}
		}
	}

	function getStartEndDate($report){
		$return = array();
		$db = JFactory::getDBO();

		if($report == "daily"){
			$startdate = date("Y-m-d");
			$enddate = strtotime("+1 days", strtotime($startdate));
			$enddate = date("Y-m-d", $enddate);
			$return["0"] = $startdate;
			$return["1"] = $enddate;
		}
		elseif($report == "weekly"){
			$today_day = date("D");
			$today = date("Y-m-d");

			if($today_day == "Mon"){
				$return["0"] = $today;
				$return["1"] = date("Y-m-d", strtotime("+6 days", strtotime($today)));
			}
			elseif($today_day == "Tue"){
				$return["0"] = date("Y-m-d", strtotime("-1 days", strtotime($today)));
				$return["1"] = date("Y-m-d", strtotime("+5 days", strtotime($today)));
			}
			elseif($today_day == "Wed"){
				$return["0"] = date("Y-m-d", strtotime("-2 days", strtotime($today)));
				$return["1"] = date("Y-m-d", strtotime("+4 days", strtotime($today)));
			}
			elseif($today_day == "Thu"){
				$return["0"] = date("Y-m-d", strtotime("-3 days", strtotime($today)));
				$return["1"] = date("Y-m-d", strtotime("+3 days", strtotime($today)));
			}
			elseif($today_day == "Fri"){
				$return["0"] = date("Y-m-d", strtotime("-4 days", strtotime($today)));
				$return["1"] = date("Y-m-d", strtotime("+2 days", strtotime($today)));
			}
			elseif($today_day == "Sat"){
				$return["0"] = date("Y-m-d", strtotime("-5 days", strtotime($today)));
				$return["1"] = date("Y-m-d", strtotime("+1 days", strtotime($today)));
			}
			elseif($today_day == "Sun"){
				$return["0"] = date("Y-m-d", strtotime("-6 days", strtotime($today)));
				$return["1"] = $today;
			}
		}
		elseif($report == "monthly"){
			$year = date("Y");
			$return["0"] = $year."-01-01 00:00:00";
			$return["1"] = $year."-12-31 00:00:00";
		}
		elseif($report == "yearly"){
			$sql = "select min(order_date) as min_date, max(order_date) as max_date from #__digicom_orders";
			$db->setQuery($sql);
			$db->query();
			$result = $db->loadAssocList();

			$return["0"] = date("Y", $result["0"]["min_date"])."-01-01 00:00:00";
			$return["1"] = date("Y", $result["0"]["max_date"])."-12-31 00:00:00";
		}

		if($report == "daily" || $report == "weekly" || $report == "monthly"){
			$pas = JRequest::getVar("pas", "0");
			$action = JRequest::getVar("action", "");
			if(trim($action) != ""){
				if(intval($pas) != "0"){
					if($report == "daily"){
						$startdate = date("Y-m-d", strtotime("-".intval($pas)." days", strtotime($startdate)));
						$enddate = date("Y-m-d", strtotime("+1 days", strtotime($startdate)));
						$return["0"] = $startdate;
						$return["1"] = $enddate;
					}
					elseif($report == "weekly"){
						$startdate = strtotime("-".intval($pas*7)." days", strtotime($return["0"]));
						$enddate = strtotime("+6 days", $startdate);
						$return["0"] = date("Y-m-d", $startdate);
						$return["1"] = date("Y-m-d", $enddate);
					}
					elseif($report == "monthly"){
						$startyear = date("Y", strtotime("-".intval($pas)." years", strtotime(date("Y"))));
						$start_date = $startyear."-01-01 00:00:00";
						$endyear = date("Y", strtotime("+1 year", strtotime($start_date)));
						$return["0"] = $startyear."-01-01 00:00:00";
						$return["1"] = $endyear."-01-01 00:00:00";
					}
				}
			}
		}
		return $return;
	}

	function getreportTotal($type = ''){
		//purchase_date
		$db = JFactory::getDBO();
		$report = JRequest::getVar("report", "daily");
		$start_date = "";
		$end_date = "";

		if($report == "daily"){
			$return = $this->getStartEndDate($report);
			$start_date = strtotime($return["0"]);
			$end_date = strtotime($return["1"]);
		}
		elseif($report == "weekly" || $report == "monthly" || $report == "yearly"){
			$return = $this->getStartEndDate($report);
			$startdate_str = $return["0"];
			$enddate_str = $return["1"];

			if(trim($startdate_str) != ""){
				$start_date = strtotime($startdate_str);
			}

			if(trim($enddate_str) != ""){
				$end_date = strtotime($enddate_str);
				if($end_date === FALSE){
					$enddate_str = date("Y-M-d");
					$end_date = strtotime($enddate_str);
				}
				$end_date = strtotime("+1 days", $end_date);
			}
		}
		elseif($report == "custom"){
			$startdate_str = JRequest::getVar("startdate", "");
			$enddate_str = JRequest::getVar("enddate", "");

			if(trim($startdate_str) != ""){
				$start_date = strtotime($startdate_str);
			}

			if(trim($enddate_str) != ""){
				$end_date = strtotime($enddate_str);
				if($end_date === FALSE){
					$enddate_str = date("Y-M-d");
					$end_date = strtotime($enddate_str);
				}
				$end_date = strtotime("+1 days", $end_date);
			}
		}

		$and = "";
		$and_licenses = "";
		if(trim($start_date) != ""){
			$start_date = date("Y-m-d H:i:s", $start_date);
			$and_licenses .= " and od.purchase_date >= '".$start_date."'";
			$start_date = strtotime($start_date);
			$and .= " and o.order_date >= ".$start_date;
		}
		if(trim($end_date) != ""){
			$end_date = date("Y-m-d H:i:s", $end_date);
			$and_licenses .= " and od.purchase_date < '".$end_date."'";
			$end_date = strtotime($end_date);
			$and .= " and o.order_date < ".$end_date;
		}
		$sql = "SELECT SUM(CASE WHEN o.`amount_paid` = '-1' THEN o.`amount` ELSE o.`amount_paid` END) as total
				FROM #__digicom_orders AS o
				WHERE 1=1 ".$and;
		$db->setQuery($sql);
		$db->query();
		$total = $db->loadResult();
		// Get chargebacks total
		$sql = "SELECT SUM(od.`cancelled_amount`) as total
				FROM #__digicom_orders_details AS od
				WHERE 1=1 AND od.cancelled=1 ".$and_licenses;
		$db->setQuery($sql);
		$db->query();
		$chargebacks = $db->loadResult();
		// Get chargebacks total
		$sql = "SELECT SUM(od.`cancelled_amount`) as total
				FROM #__digicom_orders_details AS od
				WHERE 1=1 AND od.cancelled=2 ".$and_licenses;
		$db->setQuery($sql);
		$db->query();
		$refunds = $db->loadResult();
		// Get deleted total
		$sql = "SELECT SUM(od.`cancelled_amount`) as total
				FROM #__digicom_orders_details AS od
				WHERE 1=1 AND od.cancelled=3 ".$and_licenses;
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
	}

	function getreportOrders(){
		$db = JFactory::getDBO();
		$report = JRequest::getVar("report", "daily");
		$start_date = "";
		$end_date = "";

		if($report == "daily"){
			$return = $this->getStartEndDate($report);
			$start_date = strtotime($return["0"]);
			$end_date = strtotime($return["1"]);
		}
		elseif($report == "weekly" || $report == "monthly" || $report == "yearly"){
			$return = $this->getStartEndDate($report);
			$startdate_str = $return["0"];
			$enddate_str = $return["1"];

			if(trim($startdate_str) != ""){
				$start_date = strtotime($startdate_str);
			}

			if(trim($enddate_str) != ""){
				$end_date = strtotime($enddate_str);
				if($end_date === FALSE){
					$enddate_str = date("Y-M-d");
					$end_date = strtotime($enddate_str);
				}
				$end_date = strtotime("+1 days", $end_date);
			}
		}
		elseif($report == "custom"){
			$startdate_str = JRequest::getVar("startdate", "");
			$enddate_str = JRequest::getVar("enddate", "");

			if(trim($startdate_str) != ""){
				$start_date = strtotime($startdate_str);
			}

			if(trim($enddate_str) != ""){
				$end_date = strtotime($enddate_str);
				if($end_date === FALSE){
					$enddate_str = date("Y-M-d");
					$end_date = strtotime($enddate_str);
				}
				$end_date = strtotime("+1 days", $end_date);
			}
		}

		$and = "";
		if(trim($start_date) != ""){
			$start_date = date("Y-m-d H:i:s", $start_date);
			$start_date = strtotime($start_date);

			$and .= " and o.order_date >= ".$start_date;
		}
		if(trim($end_date) != ""){
			$end_date = date("Y-m-d H:i:s", $end_date);
			$end_date = strtotime($end_date);

			$and .= " and o.order_date < ".$end_date;
		}
		$sql = "select count(*)
				from #__digicom_orders o
				where 1=1 and o.status='Active' ".$and;
		$db->setQuery($sql);
		$db->query();
		$total = $db->loadResult();
		return $total;
	}

	function getreportLicenses($type){
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
	}

	function getConfigs() {
		$comInfo = JComponentHelper::getComponent('com_digicom');
		return $comInfo->params;
	}

}
