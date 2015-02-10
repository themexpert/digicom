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

class statetax {
	function selfile () {
		return "statetax_tax.php";
	}

	function type (){
		return "tax";
	}

	function getBEData (){


	}


	function getFEData() {

	}
	function getTax(&$tax, $configs, $cust_info, $total, $plugin_conf) {
		//tax calculations
//		$pconf = explode ("\n", $plugin_conf->value);
		$pconf = $plugin_conf->config->values;
//print_r($pconf);
		$tax_option = $pconf[0];
		$tax_rate = $pconf[1];
		//pconf[0] = tax option
		//tax options are 1 = "same state", 2 = "same country, not state", 3= "anywhere"
		//pconf[1] = tax rate, percentage of total cost
	   		//tax_option_1 - for customers from the same state 
		if ( $configs->tax_option == 'tax_option_1' ) {
			  if ( ($configs->get('country','') === $cust_info->_customer->country) && ($configs->get('state','') === $cust_info->_customer->state )) {
					  $pay_flag = true;
			  } 
		}
	  
		//tax option 2 - for custoemrs from the same country	  
		if ( $configs->tax_option == 'tax_option_2' ) {
				if ( $configs->get('country','') === $cust_info->_customer->country ) {
						$pay_flag = true;
				} 
		}

		//tax option 3 - for any customer
			if ( $pay_flag && isset($cust_info->_user->id) && ($cust_info->_user->id > 0) || $configs->tax_option == 'tax_option_3' ) {
		
			//customer should be taxed		
				if ( $configs->tax_type == 'tax_type_1' ) {//state tax
						$tax['type']= '' .  (JText::_("DSTAX")) . '<br />';
						$tax['type1']=  (JText::_("DSTAX")) ;
					}/*
				//vat tax - calculates in a different way.
				*/
		} else {//no tax option available
					$tax['type'] =  '';
					$tax['type1'] = '';
		 
			}
			$state_tax = 0;
			$vat_tax = 0;
			if ( $pay_flag && ($cust_info->_user->id > 0) || $configs->tax_option == 'tax_option_3' ) {
				   		if ($tax_rate != 0 ) {
								$state_tax = round($total*$tax_rate/100, 12); 
								$tax['value'] = DigiComHelper::format_price($state_tax, $tax['currency'], false, $configs);////sprintf($price_format,$state_tax) ;//. " " . $tax['currency'] . '<br>';
						}
			  /*
			//vat tax - calculates in a different way
			}*/
		}  else {//no tax option available
				$tax['value'] = 0;
			}


		}

	function get_info () {
		return "info";
	}

};

?>
