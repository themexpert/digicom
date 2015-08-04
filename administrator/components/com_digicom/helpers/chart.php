<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class DigiComHelperChart {

	/*
	 * method to get days labels on a month
	 * @return : 1st jan, 2nd jan
	 * */
	public static function getMonthLabelDay(){
		$days = '';
		$prefix = '';
		$currentDayOfMonth=date('j');
		for($i=1;$i<=$currentDayOfMonth;$i++){
			$days = $days . $prefix . '"' . DigiComHelperChart::addOrdinalNumberSuffix($i) . ' '.date('M').'"';
			$prefix = ', ';
		}

		return $days;
	}

	/*
	 * method to get date in position formet
	 * @return : 1st jan, 2nd jan
	 * */
	public static function addOrdinalNumberSuffix($num) {
		if (!in_array(($num % 100),array(11,12,13))){
			switch ($num % 10) {
				// Handle 1st, 2nd, 3rd
				case 1:  return $num.'st';
				case 2:  return $num.'nd';
				case 3:  return $num.'rd';
			}
		}
		return $num.'th';
	}

	/*
	 * method to get price of orders on selected dates
	 * @return : 1st jan, 2nd jan
	 * */
	public static function getMonthLabelPrice($monthlyDay, $byproduct = false){
		$date = new DateTime('now');
		$date->modify('first day of this month');
		$startdate = $date->format('Y-m-d') . ' 00:00:00';

		$date->modify('first day of next month');
		$enddate = $date->format('Y-m-d') . ' 00:00:00';

		$days = explode(', ', $monthlyDay);
		$price = '';
		$prefix = '';
		foreach($days as $day){
			$day = str_replace('"','',$day);
			//$day = str_replace(' Aug','',$day);
			//$day = substr($day,0,-2);
			$day = substr($day,0,-6);
			//echo $day;jexit();
			//"1st Aug"


			$dayPrice = ceil(DigiComHelperChart::getAmountDaily($day,$byproduct));
			$price = $price . $prefix . $dayPrice;
			$prefix = ', ';
		}
		
		return $price;
	}

	/*
	 * method to get daily amount for current month
	 * */
	public static function getAmountDaily($day, $byproduct){
		$db = JFactory::getDBO();
		$input = JFactory::getApplication()->input;
		$config = JComponentHelper::getComponent('com_digicom')->params;
		$session  = JFactory::getSession();
		if($byproduct){
			$productid = $input->get('productid','');
  
			  if(empty($productid)){
			    $productid = $session->get( 'productid', '' );  
			  }

			if(empty($productid)) return DigiComHelperDigiCom::format_price(0, $config->get('currency','USD'), true, $config);

		}

		$startdate = date("Y-m-".$day." 00:00:00");
		$start_date_int = strtotime($startdate);
		$enddate = date('Y-m-d 00:00:0', strtotime($startdate . ' + 1 day'));
		$end_date_int = strtotime($enddate);

		//$and = "";
		//$and .= " and `order_date` >= '".$start_date_int."'";
		//$and .= " and `order_date` < '".$end_date_int . "'";

		// set query
		$query = $db->getQuery(true);
		$query->select('SUM('.$db->quoteName('o.amount_paid').') as '.$db->quoteName('total'))
			  ->from($db->quoteName('#__digicom_orders', 'o'));

		if($byproduct){
			$query->join('inner',$db->quoteName('#__digicom_orders_details','od') . ' ON ('.$db->quoteName('od.orderid').'='.$db->quoteName('o.id').')');
		}

		$query->where($db->quoteName('o.order_date')." >= ".$db->quote($start_date_int));
		$query->where($db->quoteName('o.order_date')." < ".$db->quote($end_date_int));

		if($byproduct){
			$query->where($db->quoteName('od.productid')." = " . $db->quote($productid));
		}

		$db->setQuery($query);

		// $sql = "SELECT SUM(`amount_paid`) as total from #__digicom_orders where 1=1 ".$and;
		// //$sql = "SELECT SUM(CASE WHEN `amount_paid` = '1' THEN `amount` ELSE `amount_paid` END) as total from #__digicom_orders where 1=1 ".$and;
		// $db->setQuery($sql);
		// $db->query();
		$price = $db->loadResult();
		$result = DigiComHelperDigiCom::format_price($price, $config->get('currency','USD'), true, $config);
		return $result;
	}

	/*
	 * method to get daily amount for specific date
	 * */
	public static function getAmountByDate($day,$byproduct = false){

		$db = JFactory::getDBO();
		$input = JFactory::getApplication()->input;
		$config = JComponentHelper::getComponent('com_digicom')->params;
		$session  = JFactory::getSession();

		if($byproduct){
			$productid = $input->get('productid','');
  
			  if(empty($productid)){
			    $productid = $session->get( 'productid', '' );  
			  }

			if(empty($productid)) return DigiComHelperDigiCom::format_price(0, $config->get('currency','USD'), true, $config);

		}


		$startdate = date($day." 00:00:00");
		$start_date_int = strtotime($startdate);
		$enddate = date('Y-m-d 00:00:0', strtotime($startdate . ' + 1 day'));
		$end_date_int = strtotime($enddate);

		$query = $db->getQuery(true);
		$query->select('SUM('.$db->quoteName('o.amount_paid').') as '.$db->quoteName('total'))
			  ->from($db->quoteName('#__digicom_orders', 'o'));

		if($byproduct){
			$query->join('inner',$db->quoteName('#__digicom_orders_details','od') . ' ON ('.$db->quoteName('od.orderid').'='.$db->quoteName('o.id').')');
		}

		$query->where($db->quoteName('o.order_date')." >= ".$db->quote($start_date_int));
		$query->where($db->quoteName('o.order_date')." < ".$db->quote($end_date_int));

		if($byproduct){
			$query->where($db->quoteName('od.productid')." = " . $db->quote($productid));
		}

		//$sql = "SELECT SUM(`amount_paid`) as total from #__digicom_orders where 1=1 ".$and;
		$db->setQuery($query);
		//echo $query->__toString($query);jexit();
		//$db->execute();
		$price = $db->loadResult();
		$result = DigiComHelperDigiCom::format_price($price, $config->get('currency','USD'), true, $config);
		return $result;
	}
	

	/*
	* $month = Y-m ; 2015-1
	*/
	public static function getAmountByMonth($month,$byproduct){

		$db = JFactory::getDBO();
		$input = JFactory::getApplication()->input;
		$config = JComponentHelper::getComponent('com_digicom')->params;
		$session  = JFactory::getSession();

		if($byproduct){
			$productid = $input->get('productid','');
  
			  if(empty($productid)){
			    $productid = $session->get( 'productid', '' );  
			  }

			if(empty($productid)) return DigiComHelperDigiCom::format_price(0, $config->get('currency','USD'), true, $config);

		}

		$startdate = date($month."-1 00:00:00");
		$start_date_int = strtotime($startdate);
		$enddate = date('Y-m-d 00:00:0', strtotime($startdate . ' + 1 month'));
		//echo $enddate;die;
		$end_date_int = strtotime($enddate);

		// $and = "";
		// $and .= " and `order_date` >= '".$start_date_int."'";
		// $and .= " and `order_date` < '".$end_date_int . "'";

		// $sql = "SELECT SUM(`amount_paid`) as total from #__digicom_orders where 1=1 ".$and;
		
		// set make active query
		$query = $db->getQuery(true);
		$query->select('SUM('.$db->quoteName('o.amount_paid').') as '.$db->quoteName('total'))
			  ->from($db->quoteName('#__digicom_orders', 'o'));

		if($byproduct){
			$query->join('inner',$db->quoteName('#__digicom_orders_details','od') . ' ON ('.$db->quoteName('od.orderid').'='.$db->quoteName('o.id').')');
		}

		$query->where($db->quoteName('o.order_date')." >= ".$db->quote($start_date_int));
		$query->where($db->quoteName('o.order_date')." < ".$db->quote($end_date_int));

		if($byproduct){
			$query->where($db->quoteName('od.productid')." = " . $db->quote($productid));
		}
		$db->setQuery($query);


		// $db->setQuery($sql);
		// $db->query();
		$price = $db->loadResult();
		$result = DigiComHelperDigiCom::format_price($price, $config->get('currency','USD'), true, $config);
		return $result;
	}


	public static function getRangeDayLabel($range){
		$days = '';
		$prefix = '';
		switch($range){
			case "custom":
				$app      = JFactory::getApplication();
				$input    = $app->input;
				$start_date = $input->get('start_date','');
				$end_date = $input->get('end_date','');

				$daterange = DigiComHelperChart::createDateRangeArray($start_date, $end_date);
				//print_r($daterange);die;
				foreach ($daterange as $key => $value) {
					$date = new DateTime($value);
					$days = $days . $prefix . '"' . DigiComHelperChart::addOrdinalNumberSuffix($date->format('d')) . ' '.$date->format('M').'"';
					$prefix = ', ';
				}
				
				break;
			case "year":
				//all months of current year
				$date = new DateTime();
				$lastmonth = $date->format('m');

				for ($m=1; $m<=$lastmonth; $m++) {
				    $month = date('F', mktime(0,0,0,$m, 1, date('Y')));
				    $days = $days . $prefix . '"' . $month .'"';
					$prefix = ', ';
			    }

				break;
			case "last_month":
				//previous month
				
				$lastday = new DateTime('last day of last month');
				$lastdate = $lastday->format('j');
				for($i=0;$i<$lastdate;$i++){
					//$date = new DateTime($i.' days ago');
					$month = new DateTime('first day of last month');
					$date = $month->modify("+$i days");
					//echo $date->format('Y-m-j');die;
					$days = $days . $prefix . '"' . DigiComHelperChart::addOrdinalNumberSuffix($date->format('d')) . ' '.$date->format('M').'"';
					$prefix = ', ';
				}

				break;
			case "month":
				//current month
				return DigiComHelperChart::getMonthLabelDay();

				break;
			case "7day":
			default:
				//7day

				for($i=6;$i>=0;$i--){
					$date = new DateTime($i.' days ago');
					$days = $days . $prefix . '"' . DigiComHelperChart::addOrdinalNumberSuffix($date->format('d')) . ' '.$date->format('M').'"';
					$prefix = ', ';
				}

				

			break;
		}
		//echo $days;die;
		return $days;

	}
	
	public static function getRangePricesLabel($range,$rangeDays = null, $byproduct = false){

		$price = '';
		$prefix = '';
		switch($range){
			case "custom":
				$app      = JFactory::getApplication();
				$input    = $app->input;
				$start_date = $input->get('start_date','');
				$end_date = $input->get('end_date','');

				$daterange = DigiComHelperChart::createDateRangeArray($start_date, $end_date);
				//print_r($daterange);die;
				foreach ($daterange as $key => $value) {
					$date = new DateTime($value);
					//$days = $days . $prefix . '"' . DigiComHelperChart::addOrdinalNumberSuffix($date->format('d')) . ' '.$date->format('M').'"';
					//$prefix = ', ';

					$dayPrice = ceil(DigiComHelperChart::getAmountByDate($date->format('Y-m-d'),$byproduct));
					$price = $price . $prefix . $dayPrice;
					$prefix = ', ';

				}
				
				break;
			case "year":
				//all months of current year
				$date = new DateTime();
				$lastmonth = $date->format('m');

				for ($m=1; $m<=$lastmonth; $m++) {
				    $month = date('Y-m', mktime(0,0,0,$m, 1, date('Y')));
					$dayPrice = ceil(DigiComHelperChart::getAmountByMonth($month,$byproduct));
					$price = $price . $prefix . $dayPrice;
					$prefix = ', ';
			    }

				break;
			case "last_month":
				$lastday = new DateTime('last day of last month');
				$lastdate = $lastday->format('j');
				for($i=0;$i<$lastdate;$i++){
					//$date = new DateTime($i.' days ago');
					$month = new DateTime('first day of last month');
					$date = $month->modify("+$i days");
					//echo $date->format('Y-m-j');die;
					// $days = $days . $prefix . '"' . DigiComHelperChart::addOrdinalNumberSuffix($date->format('d')) . ' '.$date->format('M').'"';
					// $price = $price . $prefix . $dayPrice;
					// $prefix = ', ';

					$dayPrice = ceil(DigiComHelperChart::getAmountByDate($date->format('Y-m-d'),$byproduct));
					$price = $price . $prefix . $dayPrice;
					$prefix = ', ';
				}

				break;
			case "month":
				return DigiComHelperChart::getMonthLabelPrice($rangeDays,$byproduct);

				break;
			case "7day":
			default:
				//7day

				for($i=6;$i>=0;$i--){
					$date = new DateTime($i.' days ago');

					//$price = $price . $prefix . '"' . DigiComHelperChart::addOrdinalNumberSuffix($date->format('d')) . ' '.$date->format('M').'"';
					//$prefix = ', ';
					
					$dayPrice = ceil(DigiComHelperChart::getAmountByDate($date->format('Y-m-d'),$byproduct));
					$price = $price . $prefix . $dayPrice;
					$prefix = ', ';
				}

				break;
		}

		return $price;

	}


	/*
	* createDateRangeArray
	*/
	public static function createDateRangeArray($strDateFrom,$strDateTo)
	{
	    // takes two dates formatted as YYYY-MM-DD and creates an
	    // inclusive array of the dates between the from and to dates.

	    // could test validity of dates here but I'm already doing
	    // that in the main script

	    $aryRange=array();

	    $iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),     substr($strDateFrom,8,2),substr($strDateFrom,0,4));
	    $iDateTo=mktime(1,0,0,substr($strDateTo,5,2),     substr($strDateTo,8,2),substr($strDateTo,0,4));

	    if ($iDateTo>=$iDateFrom)
	    {
	        array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry
	        while ($iDateFrom<$iDateTo)
	        {
	            $iDateFrom+=86400; // add 24 hours
	            array_push($aryRange,date('Y-m-d',$iDateFrom));
	        }
	    }
	    return $aryRange;
	}

	/*
	public static function getSumScale($report, $max_value){
		$scale = array();
		if($report != "products"){
			$step = ceil($max_value / 4);
			if($max_value > 0){
				$scale[] = $max_value;
			}
			for($i=1; $i<4; $i++){
				$max_value = $max_value - $step;
				if($max_value > 0){
					$scale[] = $max_value;
				}
			}
		}
		else{
			$step = ceil($max_value / 4);
			$scale[] = "0";
			$temp = 0;
			for($i=1; $i<=4; $i++){
				$temp += $step;
				$scale[] = $temp;
			}
		}
		if(count($scale) == 0){
			$scale = array("4", "3", "2", "1");
		}
		return $scale;
	}

	public static function getEditLine($report, $startdate, $enddate, $configs){
		$edit_line = array();
		if($report == "weekly"){
			$start_date_int = strtotime($startdate);
			$edit_line[] = "Mon<br/>".date("m-d", $start_date_int);
			$edit_line[] = "Tues<br/>".date("m-d", strtotime("+1 days", $start_date_int));
			$edit_line[] = "Wed<br/>".date("m-d", strtotime("+2 days", $start_date_int));
			$edit_line[] = "Thurs<br/>".date("m-d", strtotime("+3 days", $start_date_int));
			$edit_line[] = "Fri<br/>".date("m-d", strtotime("+4 days", $start_date_int));
			$edit_line[] = "Sat<br/>".date("m-d", strtotime("+5 days", $start_date_int));
			$edit_line[] = "Sun<br/>".date("m-d", strtotime("+6 days", $start_date_int));
		}
		elseif($report == "monthly"){
			$start_date_int = strtotime($startdate);
			$edit_line[] = "Jan";
			$edit_line[] = "Feb";
			$edit_line[] = "Mar";
			$edit_line[] = "Apr";
			$edit_line[] = "May";
			$edit_line[] = "Jun";
			$edit_line[] = "July";
			$edit_line[] = "Aug";
			$edit_line[] = "Sep";
			$edit_line[] = "Oct";
			$edit_line[] = "Nov";
			$edit_line[] = "Dec";
		}
		elseif($report == "yearly"){
			$db = JFactory::getDBO();
			$sql = "select min(order_date) from #__digicom_orders";
			$db->setQuery($sql);
			$db->query();
			$min_date_int = $db->loadResult();
			$min_date = date("Y", $min_date_int);

			$sql = "select max(order_date) from #__digicom_orders";
			$db->setQuery($sql);
			$db->query();
			$max_date_int = $db->loadResult();
			$max_date = date("Y", $max_date_int);

			if(isset($min_date) && isset($max_date)){
				for($i=$min_date; $i<=$max_date; $i++){
					$edit_line[] = $i;
				}
			}
		}
		elseif($report == "custom"){
			$startdate = JRequest::getVar("startdate", "");
			$enddate = JRequest::getVar("enddate", "");

			$startdate_temp = strtotime($startdate);
			$startdate_temp = date($configs->get('time_format','DD-MM-YYYY'), $startdate_temp);

			$enddate_temp = strtotime($enddate);
			$enddate_temp = date($configs->get('time_format','DD-MM-YYYY'), $enddate_temp);

			$edit_line[] = $startdate_temp." ".JText::_("VIEWSTATTO")." ".$enddate_temp;
		}
		elseif($report == "daily"){
			$edit_line[] = JText::_("VIEWSTATTODAY");
		}
		return $edit_line;
	}
	
	public static function getAmount($report, $startdate, $enddate, $poz){
		$db = JFactory::getDBO();
		$start_date_int = "";
		$end_date_int = "";
		$return = "0";

		if($report == "weekly"){
			$start_date_int = strtotime("+".$poz." days", strtotime($startdate));
			$end_date_int = strtotime("+".($poz+1)." days", strtotime($startdate));
		}
		elseif($report == "monthly"){
			$start_date_int = strtotime("+".$poz." month", strtotime($startdate));
			$end_date_int = strtotime("+".($poz+1)." month", strtotime($startdate));
		}
		elseif($report == "yearly"){
			$start_date_int = strtotime("+".$poz." year", strtotime($startdate));
			$end_date_int = strtotime("+".($poz+1)." year", strtotime($startdate));
		}
		elseif($report == "custom"){
			$startdate = JRequest::getVar("startdate", "");
			$enddate = JRequest::getVar("enddate", "");
			$start_date_int = strtotime($startdate);

			if(trim($enddate) != ""){
				$end_date_int = strtotime($enddate);
				if($end_date_int === FALSE){
					$enddate = date("Y-M-d");
					$end_date_int = strtotime($enddate);
				}
				$end_date_int = strtotime("+1 days", $end_date_int);
			}
		}
		elseif($report == "daily"){
			$startdate_str = date("Y-m-d 00:00:00");
			$start_date_int = strtotime($startdate_str);
			$end_date_int = strtotime("+1 days", $start_date_int);
		}

		if($report == "daily" || $report == "weekly"){
			$pas = JRequest::getVar("pas", "0");
			$action = JRequest::getVar("action", "");
			if(trim($action) != ""){
				if(intval($pas) != "0"){
					if($report == "daily"){
						$startdate = date("Y-m-d", strtotime("-".intval($pas)." days", strtotime(date("Y-m-d"))));
						$enddate = date("Y-m-d", strtotime("+1 days", strtotime($startdate)));
						$start_date_int = strtotime($startdate);
						$end_date_int = strtotime($enddate);
					}
				}
			}
		}

		$and = "";
		if($start_date_int != ""){
			$and .= " and `order_date` >= ".$start_date_int;
		}
		if($end_date_int != ""){
			$and .= " and `order_date` < ".$end_date_int;
		}

		$sql = "SELECT SUM(CASE WHEN `amount_paid` = '-1' THEN `amount` ELSE `amount_paid` END) as total
				from #__digicom_orders
				where 1=1 ".$and;
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadResult();
		return $result;
	}

	public static function get10Products($report, $startdate, $enddate, $configs){
		$db = JFactory::getDBO();
		$start_date = "";
		$end_date = "";
		if(trim($startdate) != ""){
			$startdate = strtotime($startdate);
			$start_date = date("Y-m-d 00:00:00", $startdate);

			if($report != "custom"){
				$enddate = strtotime($enddate);
			}
			else{
				$enddate = strtotime("+1 days", strtotime($enddate));
			}
			$end_date = date("Y-m-d 00:00:00", $enddate);
		}

		$and = "";
		if(trim($start_date) != ""){
			$and .= " and od.purchase_date >= '".trim($start_date)."' ";
		}
		if(trim($end_date) != ""){
			$and .= " and od.purchase_date < '".trim($end_date)."' ";
		}

		$sql = "SELECT od.`productid`, p.name, count(*) total
				FROM `#__digicom_orders_details` od, #__digicom_products p
				where od.productid=p.id and od.cancelled=0 ".$and."
				group by `productid`
				order by total desc limit 0,10";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadAssocList();
		return $result;
	}

	public static function getColors(){
		$colors = array("#66FF66", "#FF0000", "#9933FF", "#FFCC00", "#33CC00", "#FFFF00", "#0000FF", "#FF9900", "#666600", "#FF00CC");
		return $colors;
	}

	public static function roundToMax($max_amout){
		if($max_amout == 0){
			return 0;
		}
		elseif($max_amout <= 1000){
			return 1000;
		}
		else{
			$max_head = 5000;
			while($max_amout > $max_head){
				$max_head += 5000;
			}
			return $max_head;
		}
	}

	public static function roundToMaxProducts($max_amout){
		if($max_amout == 0){
			return 0;
		}
		elseif($max_amout <= 10){
			return 10;
		}
		else{
			$max_head = 10;
			while($max_amout > $max_head){
				$max_head = ($max_head*10);
			}
			return $max_head;
		}
	}

	public static function createTotalDiagram($report, $startdate, $enddate, $configs){
		$diagram = "";
		$edit_line = DigiComDiagram::getEditLine($report, $startdate, $enddate, $configs);
		$max_amout = 0;
		foreach($edit_line as $key=>$value){
			$sum = ceil(DigiComDiagram::getAmount($report, $startdate, $enddate, $key));
			if($max_amout < $sum){
				$max_amout = $sum;
			}
		}
		$roud_max_amount = $max_amout;
		if($max_amout > 10){
			$roud_max_amount = DigiComDiagram::roundToMax($max_amout);
		}
		$scale = DigiComDiagram::getSumScale($report, $roud_max_amount);

		$padding = array("0"=>"0", "1"=>"50", "2"=>"50", "3"=>"50");
		$width = "40%";
		$margin_left = "";
		if($report == "monthly"){
			$width = "70%";
			$margin_left = "margin-left:35px !important;";
		}

		$diagram .= '<table style="width:'.$width.'">';
		$diagram .= 	'<tr>';
		$diagram .= 		'<td width="60px" align="right">';
		//start sum line ------------------------------------------
		$diagram .= 			'<div id="sum_line">';
		if(isset($scale) && count($scale) > 0){
			foreach($scale as $key=>$value){
				$diagram .= '<div style="padding-top: '.$padding[$key].'px;">'.$value.' -</div>';
			}
			$diagram .= '<div style="padding-top: 50px;">0</div>';
		}
		$diagram .= 			'</div>';
		//end sum line ------------------------------------------
		$diagram .= 		'</td>';
		$diagram .= 		'<td id="td_lines">';
		$diagram .= 			'<table style="margin-bottom:-5px;"><tr>';
		if(isset($edit_line) && count($edit_line) > 0){
			foreach($edit_line as $key=>$value){
				$diagram .=				'<td style="width:40px;" nowrap="nowrap">';
				$diagram .=				'<div id="all_element">';
				$diagram .=				'<div id="diagram_element" style="'.$margin_left.'">';
				$sum = DigiComDiagram::getAmount($report, $startdate, $enddate, $key);
				if(isset($sum)){
					$total = str_replace(",", "", $scale["0"]);
					$padding = ($sum * 270) / $total;
					$padding = intval(270 - $padding);
					$padding = $padding > 258 ? 258 : $padding;
					$sum = DigiComHelperDigiCom::format_price($sum, $configs->get('currency','USD'), true, $configs);
				}
				else{
					$padding = 270;
					$sum = "";
				}
				$diagram .=					'<div style="background-color: #FFFFFF; padding-top: '.$padding.'px;">';
				$diagram .=						'<span style="font-size: smaller; white-space: nowrap;">'.$sum."</span>";
				$diagram .=					'</div>';
				$diagram .=				'</div>';
				$diagram .=				'</div>';
				$diagram .=				'</td>';
			}
		}
		$diagram .= 			'</tr></table>';
		$diagram .= 		'</td>';
		$diagram .= 	'</tr>';
		//start edit line ------------------------------------------
		$diagram .= 	'<tr>';
		if(isset($edit_line) && count($edit_line) > 0){
			$diagram .= 	'<td>';
			$diagram .= 	'</td>';

			$diagram .= 	'<td>';
			$diagram .= 		'<table>';
			$diagram .= 			'<tr>';
			foreach($edit_line as $key=>$value){
				$width = " width: 30px; ";
				if($key != count($edit_line) -1){
					$width = " width: 50px; ";
				}
				$diagram .= 			'<td style="'.$width.' text-align:right;">';
				$diagram .= 				$value;
				$diagram .= 			'</td>';
			}
			$diagram .= 			'</tr>';
			$diagram .= 		'</table>';
			$diagram .= 	'</td>';
		}
		$diagram .= 	'<tr>';
		//end edit line ------------------------------------------
		$diagram .= '</table>';

		return $diagram;
	}

	public static function createProductsDiagram($report, $startdate, $enddate, $configs){
		$diagram = "";
		$all_products = DigiComDiagram::get10Products($report, $startdate, $enddate, $configs);
		$max_amout = 0;
		foreach($all_products as $key=>$value){
			if($max_amout < $value["total"]){
				$max_amout = $value["total"];
			}
		}
		$roud_max_amount = $max_amout;
		if($max_amout > 10){
			$roud_max_amount = DigiComDiagram::roundToMaxProducts($max_amout);
		}
		$scale = DigiComDiagram::getSumScale("products", $roud_max_amount);
		$total = "";
		$colors = DigiComDiagram::getColors();

		if(isset($all_products) && count($all_products) > 0){
			$diagram .= '<table cellpadding="0" cellspacing="0" style="width:600px;">';
			foreach($all_products as $key=>$value){
				$diagram .= '<tr>';
				$diagram .= 	'<td style="text-align:right; border-right:1px solid #000000; width:25%; padding-right: 5px;">';
				$diagram .= 		$value["name"];
				$diagram .= 	'</td>';
				$diagram .= 	'<td>';
				$total = str_replace(",", "", $scale[count($scale)-1]);
				$width = ($value["total"] * 500) / $total;
				$diagram .= 		'<div id="image_diagram_products">';
				$diagram .=				'<div style="background-color:'.$colors[$key].'; height:25px; width: '.$width.'px;">&nbsp;&nbsp;&nbsp;';
				$diagram .= 				'<div style="padding-left: '.($width+5).'px; line-height: 0px;">'.$value["total"].'</div>';
				$diagram .=				'</div>';
				$diagram .= 		'</div>';
				$diagram .= 	'</td>';
				$diagram .= '</tr>';
				//empty row
				$diagram .= '<tr>';
				$diagram .= 	'<td style="text-align:right; border-right:1px solid #000000; width:25%;">';
				$diagram .= 		'&nbsp;&nbsp;';
				$diagram .= 	'</td>';
				$diagram .= 	'<td>';
				$diagram .= 		'&nbsp;&nbsp;';
				$diagram .= 	'</td>';
				$diagram .= '</tr>';
				//empty row
			}
			//bottom line-------------
			$diagram .= 	'<tr>';
			$diagram .= 		'<td style="width:25%;">';
			$diagram .= 			'&nbsp;&nbsp;';
			$diagram .= 		'</td>';
			$diagram .= 		'<td style="border-top:1px solid #000000;">';
			$temp = 0;
			$diagram .= 			'<div style="float:left; width:120px;">'.$scale["0"].'</div>';
			$diagram .= 			'<div style="float:left; width:120px;">'.$scale["1"].'</div>';
			$diagram .= 			'<div style="float:left; width:120px;">'.$scale["2"].'</div>';
			$diagram .= 			'<div style="float:left; width:120px;">'.$scale["3"].'</div>';
			$diagram .= 			'<div style="float:left">'.$scale["4"].'</div>';
			$diagram .= 		'</td>';
			$diagram .= 	'</tr>';
			//bottom line-------------
			$diagram .= '</table>';
		}
		return $diagram;
	}

	*/

}
