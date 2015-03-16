<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_categories
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Categories helper.
 *
 * @since  1.6
 */
class CategoriesHelper
{
	
	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param   string   $extension   The extension.
	 * @param   integer  $categoryId  The category ID.
	 *
	 * @return  JObject
	 *
	 * @since   1.6
	 * @deprecated  3.2  Use JHelperContent::getActions() instead
	 */
	public static function getActions($extension, $categoryId = 0)
	{
		// Log usage of deprecated function
		JLog::add(__METHOD__ . '() is deprecated, use JHelperContent::getActions() with new arguments order instead.', JLog::WARNING, 'deprecated');

		// Get list of actions
		$result = JHelperContent::getActions($extension, 'category', $categoryId);

		return $result;
	}

	/**
	 * Gets a list of associations for a given item.
	 *
	 * @param   integer  $pk         Content item key.
	 * @param   string   $extension  Optional extension name.
	 *
	 * @return  array of associations. 
	 */
	public static function getAssociations($pk, $extension = 'com_content')
	{
		$associations = array();
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->from('#__digicom_categories as c')
			->join('INNER', '#__associations as a ON a.id = c.id AND a.context=' . $db->quote('com_categories.item'))
			->join('INNER', '#__associations as a2 ON a.key = a2.key')
			->join('INNER', '#__digicom_categories as c2 ON a2.id = c2.id AND c2.extension = ' . $db->quote($extension))
			->where('c.id =' . (int) $pk)
			->where('c.extension = ' . $db->quote($extension));
		$select = array(
			'c2.language',
			$query->concatenate(array('c2.id', 'c2.alias'), ':') . ' AS id'
		);
		$query->select($select);
		$db->setQuery($query);
		$contentitems = $db->loadObjectList('language');

		// Check for a database error.
		if ($error = $db->getErrorMsg())
		{
			JError::raiseWarning(500, $error);

			return false;
		}

		foreach ($contentitems as $tag => $item)
		{
			// Do not return itself as result
			if ((int) $item->id != $pk)
			{
				$associations[$tag] = $item->id;
			}
		}

		return $associations;
	}
}
