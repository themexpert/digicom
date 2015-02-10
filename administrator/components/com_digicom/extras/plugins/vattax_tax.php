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

class vattax {
	function selfile () {
	return "vattax_tax.php";
	}

	function type (){
	return "tax";
	}

	function getBEData (){


	}


	function getFEData() {}
	function getTax(&$tax, $configs, $cust_info, $total, $plugin_conf) {
		//tax calculations
		$db = JFactory::getDBO();
		$pconf = explode ("\n", $plugin_conf->value);
		$pconf = $plugin_conf->config->values;
		$tax_rate = $pconf[0];
$x = ( unserialize(base64_decode($tax_rate)));
		$tax_rate = $x['00'];
		//if there was state tax to charge - it is charged now
		//lets consider that state tax is of higher priority than VAT
		if (isset($tax['type']) && strlen ($tax['type']) > 0){
			//do nothing - we got our tax already
		} else {//vat tax goes here
			if (isset($cust_info->country))
				$ccountry = $cust_info->country;//customer's country
			else
				$ccountry = "";//customer's country
			if (isset($cust_info->person))
				$cperson = $cust_info->person;//customer is a person or company
			else 
				$cperson = "";//customer is a person or company
			if (isset($cust_info->taxnum))
				$ctaxnum = $cust_info->taxnum;//tax number of the customer
			else 
				$ctaxnum = "";//tax number of the customer

			$scountry = $configs->get('country',''); //shop's location
			$sperson = $configs->person; //shop's owner a person?
			$staxnum = $configs->taxnum; //shop's owner tax number

			$sql = "select eumember from #__digicom_states where country='".$scountry."' limit 1";
			$db->setQuery($sql);
			$seumember = $db->loadResult();//is shop in eu?
			$sql = "select eumember from #__digicom_states where country='".$ccountry."' limit 1";
			$db->setQuery($sql);
			$ceumember = $db->loadResult();//is customer in eu?
			$cargevat = 0;
//echo $ceumember." ". $seumember;
			if ($ceumember != 1 && $seumember != 1) {//nothing to charge
//echo "A";
		   		   		   	$cargevat = 0;
			} else if ($ceumember != 1 && $seumember == 1) {//add full vat tax
//echo "B";
				$chargevat = 1;
			} else if ($ceumember == 1 && $seumember == 1) {//both from eu - specific rules apply
//echo "C";
				if ($cperson == 1) { //charge full vat tax
					$cargevat = 1;
				} else if ($cperson != 1 && intval($ctaxnum) < 1)   { //customer is a company with no tax number - add full vat tax
					$cargevat = 1;
				} else if ($cperson != 1 && intval ($ctaxnum) > 1 ) {//don'r charge vat
					$cargevat = 0;
				} else {//something weird happening - will add workaround and log write later
					$cargevat = 0;
				}
			} else {//something weird happening - will add workaround and log write later
//echo "D";
				$cargevat = 0;
			}

			//we got info if tax should be charged
			if ($cargevat == 0) {//charge nothing
				$tax['type'] =  '';
					$tax['type1'] = '';
					$tax['value'] = 0;
			} else { //charge vat 
					$tax['type'] =  '' .  (JText::_("DSVAT")) . '<br />';
					$tax['type1'] =   (JText::_("DSVAT"));
					$vat_tax = round( $total*$tax_rate/100, 12 ); 

					$tax['value'] = DigiComHelper::format_price($vat_tax, $tax['currency'], false, $configs);//sprintf($price_format,$vat_tax);// . " " . $tax['currency'] . '<br>';
			}

		}


//tax calculations end here

	}


};

?>
