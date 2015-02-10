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

jimport ("joomla.aplication.component.model");


class DigiComModelTax extends DigiComModel
{

	function __construct () {
		parent::__construct();
	}

	function getShipping(&$tax, &$items, $configs, $cust_info){
		$tax['shipping'] = 0;

		for($i = 0; $i < count($items); $i++){
			$item = &$items[$i];
			$shipping = 0;

			if($item->domainrequired == 2 && $cust_info->_user->id > 0){
				if(($configs->get('country','') == $cust_info->_customer->shipcountry) && ($configs->get('state','') == $cust_info->_customer->shipstate)){
					//calc shipping cost for users from the same location as the shop
					if($item->shippingtype == 0){//if we adding flat shipping value
						$shipping += $item->shippingvalue0 * $item->quantity;
					}
					elseif($item->shippingtype == 1){//if shipping value is percentage  
						$shipping += $item->shippingvalue0 * $item->subtotal/100;
					}
				}
				elseif(($configs->get('country','') == $cust_info->_customer->shipcountry)){//customer if from the same country but not location
					//calc shipping cost for users from the same location as the shop
					if($item->shippingtype == 0){//if we adding flat shipping value
						$shipping += $item->shippingvalue1 * $item->quantity;
					}
					elseif($item->shippingtype == 1){//if shipping value is percentage
						$shipping += $item->shippingvalue1 * $item->subtotal/100;
					}
				}
				else{//customer is from other country
					//calc shipping cost for users from the same location as the shop
					if($item->shippingtype == 0){//if we adding flat shipping value
						$shipping += $item->shippingvalue2 * $item->quantity;
					}
					elseif($item->shippingtype == 1){//if shipping value is percentage  
						$shipping += $item->shippingvalue2 * $item->subtotal/100;
					}
				}
				$item->shipping = $shipping; 
			}
			else{
				$item->shipping = 0; 
			}
			$tax['shipping'] += $shipping;
		}
		return $tax['shipping'];
	}

	//returns tax rate for
	//product tax class
	//product class
	//customer tax class
	//rules set
	//from rates set
	//for customer
	function _getRate($ptc, $pc, $ctc, $rules, $rates, $cust){

		$configs =  $this->getInstance("Config", "digicomModel");
		$configs = $configs->getConfigs();
		$rate = array();
		$rateid = array();
		foreach ($rules as $rule) {
			$rule->pclass = is_array($rule->pclass)?$rule->pclass: explode ("\n", $rule->pclass);
			$rule->cclass = is_array($rule->cclass)?$rule->cclass:explode ("\n", $rule->cclass);
			$rule->ptype = is_array($rule->ptype)?$rule->ptype:explode ("\n", $rule->ptype);
			if (in_array($ptc, $rule->pclass) && in_array($ctc, $rule->cclass) && in_array($pc, $rule->ptype)) {
				$rateid[] = $rule->trate;
			}
		}
		$rateid = implode ("\n", $rateid);
		$rateid = explode ("\n", $rateid);
		if (count($rateid) > 0) {
			foreach ($rates as $rate1) {
				if (in_array($rate1->id, $rateid)) {
					$rate[] = $rate1;
				}
			}
		}

		$res = array();
		foreach($rate as $r){
			if($configs->get('tax_base',0) == 1){
				// PDG :: start
				if(isset($r->country) && isset($cust->country) && (trim($r->country) == trim($cust->country)) && (trim($r->state) == trim ($cust->state)) && trim($r->zip) == trim($cust->zipcode) && !$configs->get('tax_eumode',0)){//exact match
					$res[0] = $r->rate;
				}
				elseif(isset($r->country) && isset($cust->country) && trim($r->country) == trim($cust->country) && trim($r->state) == trim ($cust->state) && (trim($r->zip) == "All" || trim($r->zip) == "*" || strlen(trim($r->zip)) < 1 ) && !$configs->get('tax_eumode',0)){//any zip
					$res[1] = $r->rate;
				}
				elseif(isset($r->country) && isset($cust->country) && trim($r->country) == trim($cust->country) && (trim ($r->state) == "All" ) && (trim($r->zip) == "All" || trim($r->zip) == "*" || strlen(trim($r->zip)) < 1  ) && !$configs->get('tax_eumode',0)){//any state and zip
					$res[2] = $r->rate;
				}
				elseif(isset($r->country) && (trim($r->country) == "All" ) && (trim ($r->state) == "All" ) && (trim($r->zip) == "All" || trim($r->zip) == "*" || strlen(trim($r->zip)) < 1) && !$configs->get('tax_eumode',0)){//anywhere or general match
					$res[3] = $r->rate;
				}
				elseif(isset($r->country) && isset($cust->country) && (trim($r->country) == trim($cust->country)) && (trim($cust->person) == 1) && $configs->get('tax_eumode',0)){// EU VAT
					$res[0] = $r->rate;
				}
				// PDG :: end
			}
			else{

				if (isset($r->country) && trim($r->country) == trim($cust->shipcountry) && trim($r->state) == trim ($cust->shipstate) 
					&& trim($r->zip) == trim($cust->shipzipcode)
				){//exact match
					$res[0] = $r->rate;
				} elseif(isset($r->country) && trim($r->country) == trim($cust->shipcountry) && trim($r->state) == trim ($cust->shipstate) 
					&& (trim($r->zip) == "All" || trim($r->zip) == "*" || strlen(trim($r->zip)) < 1 )
				) {//any zip
					$res[1] = $r->rate;

				} elseif (isset($r->country) && trim($r->country) == trim($cust->shipcountry)
					&& (trim ($r->state) == "All" )
					&& (trim($r->zip) == "All" || trim($r->zip) == "*" || strlen(trim($r->zip)) < 1  )
				) {//any state and zip
					$res[2] = $r->rate;
				} elseif (isset($r->country) && (trim($r->country) == "All" )
					&& (trim ($r->state) == "All" )
					&& (trim($r->zip) == "All" || trim($r->zip) == "*" || strlen(trim($r->zip)) < 1  )
				) {//anywhere or general match
					$res[3] = $r->rate;
				}
			}
		}

		for($i = 0; $i <= 3; $i++){
			if(isset($res[$i]) && $res[$i] > 0){
				$res = $res[$i];
				break;
			}
		}
		if(is_array($res)){
			$res = 0;
		}
		return $res;
	}


	function getTax(&$tax, &$items, $configs, $cust_info) {
		$db = JFactory::getDBO();

		$sql = "select * from #__digicom_tax_productclass order by ordering asc";
		$db->setQuery($sql);
		$taxpclass = $db->loadObjectList();

		$sql = "select * from #__digicom_tax_customerclass order by ordering asc";
		$db->setQuery($sql);
		$taxcclass = $db->loadObjectList();

		$sql = "select * from #__digicom_productclass order by ordering asc";
		$db->setQuery($sql);
		$pclass = $db->loadObjectList();

		$sql = "select * from #__digicom_tax_rule order by ordering asc";
		$db->setQuery($sql);
		$trule = $db->loadObjectList();

		$sql = "select * from #__digicom_tax_rate order by ordering asc";
		$db->setQuery($sql);
		$trate = $db->loadObjectList();

		$taxvalue = 0;

		//customer tax class
		$ctc = (isset($cust_info->taxclass) && $cust_info->taxclass > 0)?$cust_info->taxclass:$taxcclass[0]->id;

		require_once(JPATH_SITE.DS."components".DS."com_digicom".DS."models".DS."cart.php");
		$this->_customer = new DigiComSessionHelper();
		$digiComModelCart = new DigiComModelCart;
		$promo = $digiComModelCart->get_promo($this->_customer, 1);

		if(isset($items) && count($items) > 0){
			foreach($items as $i => $item){
				if($item->domainrequired == 2){
					if($configs->tax_classes > 0){
						$itemtax = 0;
						$ptc = $configs->tax_classes;
						$pc = $item->class > 0?$item->class:$pclass[0]->id;
						$rate = $this->_getRate($ptc, $pc, $ctc, $trule, $trate, $cust_info);

						if($promo->aftertax == 0){
							$price = $item->price - $tax["promo"];
						}
						else{
							$price = $item->price * $item->quantity;
						}

						if($configs->get('tax_catalog',0) == 0){
							$itemtax = $price * $rate/100;
						}
						else{
							$itemtax = $price /(1 + $rate/100);
						}
						$items[$i]->itemtax = $itemtax;
						$taxvalue += $itemtax;
					}
					else{
						$itemtax = 0;
						$items[$i]->itemtax = $itemtax;
						$taxvalue += $itemtax;
					}
				}
				else{
					$itemtax = 0;
					$ptc = $item->taxclass > 0 ? $item->taxclass : $taxpclass[0]->id;
					$pc = $item->class > 0 ? $item->class : $pclass[0]->id;
					$rate = $this->_getRate($ptc, $pc, $ctc, $trule, $trate, $cust_info);

					if($promo->aftertax == 0){
						$price = $item->subtotal - $tax["promo"];
					}
					else{
						$price = $item->price * $item->quantity;
					}

					if($configs->get('tax_catalog',0) == 0){
						$itemtax = $price * $rate/100;
					}
					else{
						$itemtax = $price /(1 + $rate/100);		
					}
					$items[$i]->itemtax = $itemtax;
					$taxvalue += $itemtax;
				}
			}
		}
		$tax['value'] = $taxvalue;
		return $taxvalue;
	}

}

