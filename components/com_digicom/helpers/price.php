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
			
			default:
				//month
				if($item->expiration_length > 1){
					return $item->expiration_length . ' ' . JText::_('COM_DIGICOM_MONTHS');
				}else {
					return $item->expiration_length . ' ' . JText::_('COM_DIGICOM_MONTH');
				}
				break;
				break;
		}

	} 

}