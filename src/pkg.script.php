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
class pkg_DigiComInstallerScript
{
	/**
	 * Function to perform changes during install
	 *
	 * @param   JInstallerAdapterComponent  $parent  The class calling this method
	 *
	 * @return  void
	 *
	 * @since   3.4
	 */
	public function postflight( $type, $parent )
	{
		if ( $type == 'install' ) {
			self::enablePlugins();
		}
	}
	
	/**
	* enable necessary plugins to avoid bad experience
	*/
	function enablePlugins(){
		$db = JFactory::getDBO();
		$sql = "SELECT `element`,`folder` from `#__extensions` WHERE `type` = 'plugin' AND `folder` in ('finder', 'system', 'digicom_pay', 'editors-xtd') AND `name` like '%digicom%' AND `enabled` = '0'";
		$db->setQuery($sql);
		$plugins = $db->loadObjectList();
		if(!count($plugins)) return false;
		foreach ($plugins as $key => $value) {
			if($value->folder == 'finder' or $value->folder == 'system' or $value->folder == 'editors-xtd' or ($value->folder=='digicom_pay' && $value->element=='offline'))
			{
		    	$query = $db->getQuery(true);
		    	$query->update($db->quoteName('#__extensions'));
		    	$query->set($db->quoteName('enabled') . ' = '.$db->quote('1'));
		    	$query->where($db->quoteName('type') . ' = '.$db->quote('plugin'));
		    	$query->where($db->quoteName('element') . ' = '.$db->quote($value->element));
		    	$query->where($db->quoteName('folder') . ' = '.$db->quote($value->folder));
		    	//$query = "UPDATE `#__extensions` SET `enabled`='1' WHERE `type`='plugin' AND `element`='".$value->element."' AND `folder`='".$value->folder."'";
	        	$db->setQuery($query);
	        	$db->execute();
			}
			
		}

		return true;

	}

}
