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


	/**
	* price formet helper
	*/
	public static function format_price( $amount, $ccode, $add_sym = false, $configs )
	{

		$price = number_format( $amount, $configs->get('decimaldigits','2') , $configs->get('dec_group_symbol',',') , $configs->get('thousands_group_symbol',',') );
		if ( $add_sym ) {
			if ( $configs->get('currency_position','1') ) {
				$price = $price . " " . $ccode;
			} else {
				$price = $ccode . " " . $price;
			}
		}
		
		return $price;
	}

	/*
	* $item = product object
	* show product subscription or validity period for purchase
	*/
	public static function getProductValidityPeriod($item){
		//print_r($item);die;
		if(
			!isset( $item->price_type )
			or
			( $item->price_type == 0 )
		) return JText::_('COM_DIGICOM_PRODUCT_EXPIRATION_NEVER');

		$expiration_type = $item->expiration_type;

		switch ($expiration_type) {
			case 'year':
				if($item->expiration_length > 1){
					return $item->expiration_length . ' ' . JText::_('COM_DIGICOM_YEARS');
				}else {
					return $item->expiration_length . ' ' . JText::_('COM_DIGICOM_YEAR');
				}
				break;

			case 'day':
				if($item->expiration_length > 1){
					return $item->expiration_length . ' ' . JText::_('COM_DIGICOM_DAYS');
				}else {
					return $item->expiration_length . ' ' . JText::_('COM_DIGICOM_DAY');
				}
				break;

			case 'month':
				if($item->expiration_length > 1){
					return $item->expiration_length . ' ' . JText::_('COM_DIGICOM_MONTHS');
				}else {
					return $item->expiration_length . ' ' . JText::_('COM_DIGICOM_MONTH');
				}
				break;
			default:
				return JText::_('COM_DIGICOM_PRODUCT_EXPIRATION_NEVER');
				break;
		}

	}

}
