<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class DigiComSiteHelperPrice {

	public static $customer;

	/**
	* price formet helper
	*/
	public static function format_price( $amount, $ccode, $add_sym = false, $configs )
	{

		$currency_use = $configs->get('currency_use','symbol');
		$currency_symbol = $configs->get('currency_symbol','$');
		if($currency_use == 'symbol'){
			$ccode = $currency_symbol;
		}
		
		$price = number_format( (float)$amount, $configs->get('decimaldigits','2') , $configs->get('dec_group_symbol','.') , $configs->get('thousands_group_symbol',',') );
		if ( $add_sym ) {
			if ( $configs->get('currency_position','1') ) {
				$price = $price . $ccode;
			} else {
				$price = $ccode . $price;
			}
		}

		return $price;
	}


	/**
	* price formet helper
	*/
	public static function tax_price($price, $configs, $rateonly = false, $textonly = false)
	{
		if($price <= 0) return;
		// check the tax info
		$tax_enable 						= $configs->get('enable_taxes', 0);
		$display_tax_with_price = $configs->get('display_tax_with_price', 0);
		$price_with_tax 				= $configs->get('price_with_tax', 0);
		$fallback_tax_rate 			= $configs->get('fallback_tax_rate', 0);

		$tax_rate = self::get_tax_rate($configs);
		$tax_rate = $tax_rate * 100;

		if(!$tax_enable)
		{
			return 0;
		}
		elseif ($rateonly)
		{
			return $tax_rate;
		}
		elseif ($textonly)
		{
			return JText::sprintf('COM_DIGICOM_TAX_INCLUDE', $tax_rate);
		}
		elseif($display_tax_with_price && $price_with_tax)
		{
			$price = JText::sprintf('COM_DIGICOM_PRICE_WITH_TAX_INCLUDE', $tax_rate);
			return $price;
		}
		elseif($display_tax_with_price && !$price_with_tax)
		{
			$price = JText::sprintf('COM_DIGICOM_PRICE_WITH_TAX_EXCLUDE', $tax_rate);
			return $price;
		}
	}



	/**
	 * Get taxation rate
	 *
	 * @since 1.3.3
	 * @param bool $country
	 * @param bool $state
	 * @return mixed|void
	 */
	public static function get_tax_rate($configs, $country = false, $state = false )
	{
		if(!self::$customer){
			self::$customer = new DigiComSiteHelperSession();
		}
		$customer = self::$customer;
		$rate = (float) $configs->get('fallback_tax_rate', 0 );


		// print_r($user_address);die;
		// return;
		if( empty( $country ) ) {
			if( $customer->_user->id > 0 && !empty( $customer ) ) {
				$country = $customer->_customer->country;
			}

			$country = ! empty( $country ) ? $country : $configs->get('base_country','');
		}


		if( empty( $state ) ) {
			if( $customer->_user->id > 0 && ! empty( $customer ) ) {
				$state = $customer->_customer->state;
			}
			$state = ! empty( $state ) ? $state : $configs->get('base_state','');
		}

		if( ! empty( $country ) ) {
			$tax_rates   = $configs->get('tax_rates', '');
			$json = json_decode($tax_rates, true);
			if(is_array($json)){
				$tax_rates = self::group_by_key($json);
			}else{
				$tax_rates = '';
			}
			
			if( ! empty( $tax_rates ) ) {
			// print_r($tax_rates);die;
				// Locate the tax rate for this country / state, if it exists
				foreach( $tax_rates as $key => $tax_rate ) {

					if( $country != $tax_rate['country'] )
						continue;

					if( ! empty( $tax_rate['all_states'] ) ) {
						if( ! empty( $tax_rate['rate'] ) ) {
							$rate = number_format( $tax_rate['rate'], 4 );
						}
					} else {

						if( empty( $tax_rate['state'] ) || strtolower( $state ) != strtolower( $tax_rate['state'] ) )
							continue;

						$state_rate = $tax_rate['rate'];
						if( 0 !== $state_rate || ! empty( $state_rate ) ) {
							$rate = number_format( $state_rate, 4 );
						}
					}
				}
			}
		}

		// echo $rate;die;
		if( $rate > 1 ) {
			// Convert to a number we can use
			$rate = $rate / 100;
		}

		return $rate;
	}

	public static function group_by_key($array)
	{
	    $result = array();

	    foreach ($array as $key=>$sub)
	    {
	        // print_r($key);die;
					foreach ($sub as $k => $v)
	        {
	            $result[$k][$key] = $v;
	        }
	    }
	    return $result;
	}

	/*
	* $item = product object
	* show product subscription or validity period for purchase
	*/
	public static function getProductValidityPeriod($item){

		$dispatcher	= JEventDispatcher::getInstance();

		// Confirm that we have a price type
		if(!isset( $item->price_type )) $item->price_type = 0;

		// now switch by type to show message
		switch ($item->price_type) {
			case '0':
				// forever
				$result = JText::_('COM_DIGICOM_PRODUCT_EXPIRATION_NEVER');
				break;

			case '1':
				// has expiration
				$result = DigiComSiteHelperPrice::calculateExpirationLength($item->expiration_type, $item->expiration_length);
				break;
			
			default:
				// we dont know, lets trigger plugin
				$result = DigiComSiteHelperPrice::calculateExpirationLength($item->expiration_type, $item->expiration_length);
				$dispatcher->trigger('onDigicomCalculateProductValidity', array('com_digicom.common', &$item, &$result));
				
				break;
		}

		return $result;
	}

	public static function calculateExpirationLength($expiration_type, $expiration_length){

		
		switch ($expiration_type) {
			case 'year':
				if($expiration_length > 1){
					return $expiration_length . ' ' . JText::_('COM_DIGICOM_YEARS');
				}else {
					return $expiration_length . ' ' . JText::_('COM_DIGICOM_YEAR');
				}
				break;

			case 'day':
				if($expiration_length > 1){
					return $expiration_length . ' ' . JText::_('COM_DIGICOM_DAYS');
				}else {
					return $expiration_length . ' ' . JText::_('COM_DIGICOM_DAY');
				}
				break;

			case 'month':
				if($expiration_length > 1){
					return $expiration_length . ' ' . JText::_('COM_DIGICOM_MONTHS');
				}else {
					return $expiration_length . ' ' . JText::_('COM_DIGICOM_MONTH');
				}
				break;
			default:
				return JText::_('COM_DIGICOM_PRODUCT_EXPIRATION_NEVER');
				break;
		}

	}

}
