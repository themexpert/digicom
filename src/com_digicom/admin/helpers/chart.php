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
		$date->modify("first day of this month");
		$start_date = $date->format('Y-m-d 00:00:00');

		$date = new DateTime('now');
		//$date->modify("last day of this month");
		$end_date = $date->format('Y-m-d 23:59:59');
		//echo $end_date;die;
		$daterange = DigiComHelperChart::createDateRangeArray($start_date, $end_date);
		//print_r($daterange);die;
		$price = '';
		$prefix = '';
		foreach ($daterange as $key => $value) {

			$dayPrice = ceil(DigiComHelperChart::getAmountByDate($value,$byproduct));
			//echo $dayPrice . '::from line 69';die;
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

			if(empty($productid)) return 0;

		}

		$startdate = date("Y-m-".$day." 00:00:00");

		$date = new DateTime('now');
		$end_date = $date->format('Y-m-d 23:59:59');

		// set query
		$query = $db->getQuery(true);
		$query->select('SUM('.$db->quoteName('o.amount_paid').') as '.$db->quoteName('total'))
			  ->from($db->quoteName('#__digicom_orders', 'o'));

		if($byproduct){
			$query->join('inner',$db->quoteName('#__digicom_orders_details','od') . ' ON ('.$db->quoteName('od.orderid').'='.$db->quoteName('o.id').')');
		}

		// $query->where($db->quoteName('o.order_date')." >= ".$db->quote($start_date_int));
		// $query->where($db->quoteName('o.order_date')." < ".$db->quote($end_date_int));
		$query->where($db->quoteName('o.order_date')." BETWEEN '".$startdate."' AND '".$end_date."'");

		if($byproduct){
			$query->where($db->quoteName('od.productid')." = " . $db->quote($productid));
		}

		$db->setQuery($query);

		return $db->loadResult();

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

			if(empty($productid)) return 0;

		}

		$startdate 	= date($day." 00:00:00");
		$enddate 		= date($day.' 23:59:59');

		$query = $db->getQuery(true);
		$query->select('SUM('.$db->quoteName('o.amount_paid').') as '.$db->quoteName('total'))
			  ->from($db->quoteName('#__digicom_orders', 'o'));

		if($byproduct){
			$query->join('inner',$db->quoteName('#__digicom_orders_details','od') . ' ON ('.$db->quoteName('od.orderid').'='.$db->quoteName('o.id').')');
		}

		$query->where($db->quoteName('o.order_date')." BETWEEN '".$startdate."' AND '".$enddate."'");

		if($byproduct){
			$query->where($db->quoteName('od.productid')." = " . $db->quote($productid));
		}

		$db->setQuery($query);
		return $db->loadResult();

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

			if(empty($productid)) return 0;

		}

		$startdate = date($month."-1 00:00:00");
		$start_date_int = strtotime($startdate);
		$enddate = date('Y-m-d 23:59:59', strtotime($startdate . ' + 1 month'));
		//echo $enddate;die;
		$end_date_int = strtotime($enddate);

		// set make active query
		$query = $db->getQuery(true);
		$query->select('SUM('.$db->quoteName('o.amount_paid').') as '.$db->quoteName('total'))
			  ->from($db->quoteName('#__digicom_orders', 'o'));

		if($byproduct){
			$query->join('inner',$db->quoteName('#__digicom_orders_details','od') . ' ON ('.$db->quoteName('od.orderid').'='.$db->quoteName('o.id').')');
		}

		$query->where($db->quoteName('o.order_date')." BETWEEN ".$db->quote($startdate) ." AND ".$db->quote($enddate));

		if($byproduct){
			$query->where($db->quoteName('od.productid')." = " . $db->quote($productid));
		}

		$db->setQuery($query);
		return $db->loadResult();
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

					$month = new DateTime('first day of last month');
					$date = $month->modify("+$i days");

					$days = $days . $prefix . '"' . DigiComHelperChart::addOrdinalNumberSuffix($date->format('d')) . ' '.$date->format('M').'"';
					$prefix = ', ';
				}

				break;
			case "month":
				//current month
				$days = DigiComHelperChart::getMonthLabelDay();

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

				foreach ($daterange as $key => $value) {
					$date = new DateTime($value);

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

					$dayPrice = ceil(DigiComHelperChart::getAmountByDate($date->format('Y-m-d'),$byproduct));
					$price = $price . $prefix . $dayPrice;
					$prefix = ', ';
				}

				break;
			case "month":
				$date = new DateTime('now');
				$date->modify("first day of this month");
				$start_date = $date->format('Y-m-d 00:00:00');

				$date = new DateTime('now');
				$end_date = $date->format('Y-m-d 23:59:59');
				$daterange = DigiComHelperChart::createDateRangeArray($start_date, $end_date);
				foreach ($daterange as $key => $value) {
					$date = new DateTime($value);

					$dayPrice = ceil(DigiComHelperChart::getAmountByDate($date->format('Y-m-d'),$byproduct));
					$price = $price . $prefix . $dayPrice;
					$prefix = ', ';

				}
				break;
			case "7day":
			default:
				//7day

				for($i=6;$i>=0;$i--){
					$date = new DateTime($i.' days ago');

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


}
