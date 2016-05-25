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
abstract class JHtmlDirectory
{
	/**
	 * Method to generate a (un)writable message for directory
	 *
	 * @param   boolean  $writable  is the directory writable?
	 *
	 * @return  string	html code
	 */
	public static function writable($writable, $raw = false)
	{

		if ($writable)
		{
			if($raw){
				return JText::_('COM_DIGICOM_ABOUT_SYSTEMINFO_WRITABLE');
			}
			return '<span class="badge badge-success">' . JText::_('COM_DIGICOM_ABOUT_SYSTEMINFO_WRITABLE') . '</span>';
		}

		if($raw){
			return JText::_('COM_DIGICOM_ABOUT_SYSTEMINFO_UNWRITABLE');
		}
		return '<span class="badge badge-important">' . JText::_('COM_DIGICOM_ABOUT_SYSTEMINFO_UNWRITABLE') . '</span>';
	}

	/**
	 * Method to generate a message for a directory
	 *
	 * @param   string   $dir      the directory
	 * @param   boolean  $message  the message
	 * @param   boolean  $visible  is the $dir visible?
	 *
	 * @return  string	html code
	 */
	public static function message($dir, $message, $visible = true, $rawdata = false)
	{
		$output = $visible ? $dir : '';

		if (empty($message))
		{
			return $output;
		}

		if($rawdata){
			return $output . JText::_($message);
		}

		return $output . ' <strong>' . JText::_($message) . '</strong>';
	}
}
