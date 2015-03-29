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

		for($i=0;$i<9;$i++){
			$menuitems->submenu->$i = new StdClass();
			$menuitems->submenu->$i->text = $titles[$i];
			$menuitems->submenu->$i->link = $links[$i];
		}
		
		return $menuitems;
	}

	public static function getArraytitle(){
		return array(
			0 => JText::_('COM_DIGICOM_SIDEBAR_MENU_DASHBOARD'),
			1 => JText::_('COM_DIGICOM_SIDEBAR_MENU_CATEGORIES'),
			2 => JText::_('COM_DIGICOM_SIDEBAR_MENU_PRODUCTS'),
			3 => JText::_('COM_DIGICOM_SIDEBAR_MENU_FILE_MANAGER'),
			4 => JText::_('COM_DIGICOM_SIDEBAR_MENU_CUSTOMERS'),
			5 => JText::_('COM_DIGICOM_SIDEBAR_MENU_ORDERS'),
			6 => JText::_('COM_DIGICOM_SIDEBAR_MENU_DISCOUNTS'),
			7 => JText::_('COM_DIGICOM_SIDEBAR_MENU_REPORTS'),
			8 => JText::_('COM_DIGICOM_SIDEBAR_MENU_ABOUT')
		);
	}

	static public function getArrayLinks(){
		return array(
			0 => 'index.php?option=com_digicom',
			1 => 'index.php?option=com_digicom&view=categories',
			2 => 'index.php?option=com_digicom&view=products',
			3 => 'index.php?option=com_digicom&view=filemanager',
			4 => 'index.php?option=com_digicom&view=customers',
			5 => 'index.php?option=com_digicom&view=orders',
			6 => 'index.php?option=com_digicom&view=discounts',
			7 => 'index.php?option=com_digicom&view=stats',
			8 => 'index.php?option=com_digicom&view=about',
		);
	}

}
