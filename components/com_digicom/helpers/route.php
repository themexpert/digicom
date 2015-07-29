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
 * DigiCom Component Route Helper.
 *
 * @since  1.5
 */
abstract class DigiComSiteHelperRoute
{
	protected static $lookup = array();

	/**
	 * Get the product route.
	 *
	 * @param   integer  $id        The route of the DigiCom item.
	 * @param   integer  $catid     The category ID.
	 * @param   integer  $language  The language code.
	 *
	 * @return  string  The product route.
	 *
	 * @since   1.5
	 */
	public static function getProductRoute($id, $catid = 0, $language = 0)
	{
		$needles = array(
			'product'  => array((int) $id)
		);

		// Create the link
		$link = 'index.php?option=com_digicom&view=product&id=' . $id;

		if ((int) $catid > 1)
		{
			$categories = JCategories::getInstance('DigiCom');
			$category   = $categories->get((int) $catid);

			if ($category)
			{
				$needles['category']   = array_reverse($category->getPath());
				$needles['categories'] = $needles['category'];
				$link .= '&catid=' . $catid;
			}
		}

		if ($language && $language != "*" && JLanguageMultilang::isEnabled())
		{
			$link .= '&lang=' . $language;
			$needles['language'] = $language;
		}

		if ($item = self::_findItem($needles))
		{
			$link .= '&Itemid=' . $item;
		}

		return $link;
	}

	/**
	 * Get the category route.
	 *
	 * @param   integer  $catid     The category ID.
	 * @param   integer  $language  The language code.
	 *
	 * @return  string  The product route.
	 *
	 * @since   1.5
	 */
	public static function getCategoryRoute($catid, $language = 0)
	{
		if ($catid instanceof JCategoryNode)
		{
			$id       = $catid->id;
			$category = $catid;
		}
		else
		{
			$id       = (int) $catid;
			$category = JCategories::getInstance('DigiCom')->get($id);
		}

		if ($id < 1 || !($category instanceof JCategoryNode))
		{
			$link = '';
		}
		else
		{
			$needles               = array();
			$link                  = 'index.php?option=com_digicom&view=category&id=' . $id;
			$catids                = array_reverse($category->getPath());

			$needles['category']   = $catids;
			$needles['categories'] = $catids;

			if ($language && $language != "*" && JLanguageMultilang::isEnabled())
			{
				$link .= '&lang=' . $language;
				$needles['language'] = $language;
			}

			if ($item = self::_findItem($needles))
			{
				$link .= '&Itemid=' . $item;
			}

		}


		return $link;
	}

	/**
	 * Get the form route.
	 *
	 * @param   integer  $id  The form ID.
	 *
	 * @return  string  The product route.
	 *
	 * @since   1.5
	 */
	public static function getFormRoute($id)
	{
		// Create the link
		if ($id)
		{
			$link = 'index.php?option=com_digicom&task=product.edit&a_id=' . $id;
		}
		else
		{
			$link = 'index.php?option=com_digicom&task=product.edit&a_id=0';
		}

		return $link;
	}

	/**
	 * Find an item ID.
	 *
	 * @param   array  $needles  An array of language codes.
	 *
	 * @return  mixed  The ID found or null otherwise.
	 *
	 * @since   1.5
	 */
	protected static function _findItem($needles = null)
	{
		$app      = JFactory::getApplication();
		$menus    = $app->getMenu('site');
		$language = isset($needles['language']) ? $needles['language'] : '*';

		// Prepare the reverse lookup array.
		if (!isset(self::$lookup[$language])) {
			self::$lookup[$language] = array();

			$component = JComponentHelper::getComponent('com_digicom');
			$attributes = array('component_id');
			$values = array($component->id);

			if ($language != '*') {
				$attributes[] = 'language';
				$values[] = array($needles['language'], '*');
			}

			$items = $menus->getItems($attributes, $values);
			//print_r($items);die;
			foreach ($items as $item) {
				if (isset($item->query) && isset($item->query['view'])) {
					$view = $item->query['view'];
					if (!isset(self::$lookup[$language][$view])) {
						self::$lookup[$language][$view] = array();
					}

					if (isset($item->query['id'])) {
						/**
						 * Here it will become a bit tricky
						 * language != * can override existing entries
						 * language == * cannot override existing entries
						 */
						if (!isset(self::$lookup[$language][$view][$item->query['id']]) || $item->language != '*') {
							self::$lookup[$language][$view][$item->query['id']] = $item->id;
						}
					}
				}
			}
		}

		//print_r(self::$lookup[$language][$view]);die;
		//Array([0] => 110)
		//print_r($needles);die;//long array witt categorie=> 0=>10:uncategorised; categories prefix

		if ($needles)
		{
			foreach ($needles as $view => $ids)
			{

				if (isset(self::$lookup[$language][$view]))
				{

					foreach ($ids as $id)
					{

						if (isset(self::$lookup[$language][$view][(int) $id]))
						{
							return self::$lookup[$language][$view][(int) $id];
						}
					}
				}
			}
		}

		// Check if the active menuitem matches the requested language
//		$active = $menus->getActive();
//
//		if ($active
//			&& $active->component == 'com_digicom'
//			&& ($language == '*' || in_array($active->language, array('*', $language)) || !JLanguageMultilang::isEnabled()))
//		{
//			return $active->id;
//		}

		//print_r(self::$lookup[$language]);die;
		// lets search in big scal
		if (isset(self::$lookup[$language]['category']))
		{
			return self::$lookup[$language]['category'][0];
		}

		// If not found, return language specific home link
		$default = $menus->getDefault($language);

		return !empty($default->id) ? $default->id : null;
	}
}
