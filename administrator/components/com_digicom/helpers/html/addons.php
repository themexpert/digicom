<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_admin
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Utility class working with directory
 *
 * @since  1.6
 */
abstract class JHtmlAddons
{
	/**
	 * Method to generate a (un)writable message for directory
	 *
	 * @param   boolean  $name  is the directory writable?
	 *
	 * @return  string	html code
	 */
	public static function info($name,$type,$folder, $element,$client_id, $raw = false, $manifest_cache = '{}')
	{
		//name = item name
		//folder = plugin
		//type = module/plugin/component
		$manifest_cache = json_decode($manifest_cache);
		if(isset($manifest_cache->version) and !empty($manifest_cache->version)){
			$version = $manifest_cache->version;
		}else{
			$version = '';
		}

		switch ($type) {
			case 'component':
				if($element == 'com_digicom'){
					if($raw){
						return JText::_('COM_DIGICOM_ABOUT_SYSTEMINFO_CORE_COMPONENT') . ' ('.$version.')';
					}

					return '<span class="badge badge-info">' . JText::_('COM_DIGICOM_ABOUT_SYSTEMINFO_CORE_COMPONENT') . ' ('.$version.')</span>';
				}
				break;
			case 'module':
				if($client_id == 1){
					if($raw){
						return JText::_('COM_DIGICOM_ABOUT_SYSTEMINFO_ADMIN_EXTENSION') . ' ('.$version.')';
					}

					return '<span class="badge">' . JText::_('COM_DIGICOM_ABOUT_SYSTEMINFO_ADMIN_EXTENSION') . ' ('.$version.')</span>';
				}else{
					if($raw){
						return JText::_('COM_DIGICOM_ABOUT_SYSTEMINFO_FRONTEND_EXTENSION') . ' ('.$version.')';
					}

					return '<span class="badge badge-warning">' . JText::_('COM_DIGICOM_ABOUT_SYSTEMINFO_FRONTEND_EXTENSION') . ' ('.$version.')</span>';
				}
				break;

			case 'plugin':
			default:
					if($raw){
						return '(' . $folder . ')';
					}

					return '<span class="badge badge-success">' . $folder . ' ('.$version.')</span>';

				break;
		}

	}

	/**
	 * Method to generate a message for a directory
	 *
	 * @param   string   $name      the directory
	 * @param   boolean  $type  the message
	 * @param   boolean  $visible  is the $name visible?
	 *
	 * @return  string	html code
	 */
	public static function label($name, $type, $folder, $visible = true, $rawdata = false)
	{
		return $name;
	}
}
