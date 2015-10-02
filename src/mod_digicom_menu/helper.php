<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

// no direct access
defined('_JEXEC') or die;

abstract class ModDigiComMenuHelper {
	private $titles;
	private $links;
	private $getArrayClasses;

	public static function getDigiComComponent($authCheck = true) {

		if(!JComponentHelper::isEnabled('com_digicom')) return NULL;
		$menuitems = new StdClass();
		$menuitems->text = JText::_('COM_DIGICOM');
		$menuitems->submenu = new StdClass();
		$titles = self::getArraytitle();
		$links = self::getArrayLinks();

		for($i=0;$i<count(self::getArraytitle());$i++)
		{
			$menuitems->submenu->$i = new StdClass();
			$menuitems->submenu->$i->text = $titles[$i];
			$menuitems->submenu->$i->link = $links[$i];

			$child = self::checkChild($links[$i]);
			if($child){
				$menuitems->submenu->$i->child = true;
				$menuitems->submenu->$i->childitems = self::getChild($links[$i]);
			}else{
				$menuitems->submenu->$i->child = false;
			}
		}
		
		return $menuitems;
	}

	public static function checkChild($link){
		if($link == 'index.php?option=com_digicom&view=products') return true;
		return false;
	}
	
	public static function getChild($link){
		if($link != 'index.php?option=com_digicom&view=products') return true;;
		$child = array();
		
		$child[] = array(
			'title' => JText::_('MOD_DIGICOM_MENU_PRODUCTS_CHILD_ADD_PRODUCT'),
			'link'	=>	'index.php?option=com_digicom&task=product.add&product_type=reguler'
		);
		$child[] = array(
			'title' => JText::_('MOD_DIGICOM_MENU_PRODUCTS_CHILD_ADD_BUNDLE'),
			'link'	=>	'index.php?option=com_digicom&task=product.add&product_type=bundle'
		);

		return $child;
	}


	public static function getArraytitle(){
		return array(
			0 => JText::_('MOD_DIGICOM_MENU_DASHBOARD'),
			1 => JText::_('MOD_DIGICOM_MENU_PRODUCTS'),
			2 => JText::_('MOD_DIGICOM_MENU_CATEGORIES'),
			3 => JText::_('MOD_DIGICOM_MENU_FILE_MANAGER'),
			4 => JText::_('MOD_DIGICOM_MENU_CUSTOMERS'),
			5 => JText::_('MOD_DIGICOM_MENU_ORDERS'),
			6 => JText::_('MOD_DIGICOM_MENU_DISCOUNTS'),
			7 => JText::_('MOD_DIGICOM_MENU_REPORTS'),
			8 => JText::_('MOD_DIGICOM_MENU_SETTINGS')
		);
	}

	static public function getArrayLinks(){
		return array(
			0 => 'index.php?option=com_digicom',
			1 => 'index.php?option=com_digicom&view=products',
			2 => 'index.php?option=com_digicom&view=categories',
			3 => 'index.php?option=com_digicom&view=filemanager',
			4 => 'index.php?option=com_digicom&view=customers',
			5 => 'index.php?option=com_digicom&view=orders',
			6 => 'index.php?option=com_digicom&view=discounts',
			7 => 'index.php?option=com_digicom&view=reports',
			8 => 'index.php?option=com_digicom&view=configs'
		);
	}

}
