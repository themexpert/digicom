<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

require_once JPATH_SITE . '/components/com_digicom/helpers/route.php';

/**
 * Helper for mod_digicom_categories
 *
 * @package     DigiCom
 * @subpackage  mod_articles_categories
 *
 * @since       1.0.0
 */
abstract class ModDigicomCategoriesHelper
{
	/**
	 * Get list of Categories
	 *
	 * @param   \Joomla\Registry\Registry  &$params  module parameters
	 *
	 * @return  array
	 *
	 * @since   1.5
	 */
	public static function getList(&$params)
	{
		$options               = array();
		$options['countItems'] = $params->get('numitems', 0);

		$categories = JCategories::getInstance('Digicom', $options);
		$category   = $categories->get($params->get('parent', 'root'));

		if ($category != null)
		{
			$items = $category->getChildren();

			if ($params->get('count', 0) > 0 && count($items) > $params->get('count', 0))
			{
				$items = array_slice($items, 0, $params->get('count', 0));
			}

			return $items;
		}
	}
}
