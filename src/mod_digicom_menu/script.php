<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

/**
 * Installation class to perform additional changes during install/uninstall/update
 *
 * @package     Joomla.Administrator
 * @subpackage  com_digicom
 * @since       3.4
 */
class mod_digicom_menuInstallerScript
{

	/**
	 * Method to install the extension
	 * $parent is the class calling this method
	 *
	 * @return void
	 */
	function install($parent) 
	{
		//echo '<p>The module has been installed</p>';
	}
 
	/**
	 * Method to uninstall the extension
	 * $parent is the class calling this method
	 *
	 * @return void
	 */
	function uninstall($parent) 
	{
		//echo '<p>The module has been uninstalled</p>';
	}
 
	/**
	 * Method to update the extension
	 * $parent is the class calling this method
	 *
	 * @return void
	 */
	function update($parent) 
	{
		//echo '<p>The module has been updated to version' . $parent->get('manifest')->version . '</p>';
	}
 
	/**
	 * Method to run before an install/update/uninstall method
	 * $parent is the class calling this method
	 * $type is the type of change (install, update or discover_install)
	 *
	 * @return void
	 */
	function preflight($type, $parent) 
	{
		//echo '<p>Anything here happens before the installation/update/uninstallation of the module</p>';
	}
 
	/**
	 * Method to run after an install/update/uninstall method
	 * $parent is the class calling this method
	 * $type is the type of change (install, update or discover_install)
	 *
	 * @return void
	 */
	function postflight($type, $parent) 
	{
		$module = JTable::getInstance('Module', 'JTable');
		$module->load(array('module'=>'mod_digicom_menu'));
		$module->position = 'menu';
		$module->published = 1;
		$module->ordering = 1;
		$module->access = 3;
		$module->params = '{"show_digicom_menu":"0"}';
		
		if (!$module->check())
		{
			JFactory::getApplication()->enqueueMessage(JText::sprintf('MOD_DIGICOM_MENU_ERROR_PUBLISH_MODULE', $module->getError()));
		}

		// Now store the module
		if (!$module->store())
		{
			JFactory::getApplication()->enqueueMessage(JText::sprintf('MOD_DIGICOM_MENU_ERROR_PUBLISH_MODULE', $module->getError()));
		}

		// Now we need to handle the module assignments
		self::assignMenu($module->id);
		
	}

	public static function assignMenu($pk){
		// Now we need to handle the module assignments
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($db->quoteName('moduleid'))
			->from($db->quoteName('#__modules_menu'))
			->where($db->quoteName('moduleid') . ' = ' . $pk);
		$db->setQuery($query);
		$menus = $db->loadObject();

		// Insert the new records into the table
		if( !isset($menus->moduleid) )
		{
			$query->clear()
				->insert($db->quoteName('#__modules_menu'))
				->columns(array($db->quoteName('moduleid'), $db->quoteName('menuid')))
				->values($pk . ', ' . 0);
			$db->setQuery($query);
			$db->execute();
		}

		return true;
	}
 	
}
