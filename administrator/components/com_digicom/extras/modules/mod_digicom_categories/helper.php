<?php
/**
  @version		$Id: helper.php 341 2013-10-10 12:28:28Z thongta $
 * @package		DigiCom - Shopping Cart for Joomla.
 * @copyright	(C) 2013 themexpert.com. All rights reserved.
 * @author		themexpert.com
 * @license		GNU/GPLv3, see LICENSE
 */
// no direct access
defined('_JEXEC') or die('Restricted access');
$mainframe = JFactory::getApplication();

class modDigiComCategoriesHelper
{
	
	public static function getCategories($parent_id=0, $lv='', $catsList=array())
	{
		$db	= JFactory::getDBO();
		$qr = '
			SELECT `id` AS `id`, `name` AS `title`, `parent_id` AS `parent_id`
			FROM
				`#__digicom_categories`
			WHERE
				`parent_id` = '.(int)$parent_id.' AND
				`published` = 1
			ORDER BY `ordering` ASC
		';
		$db->setQuery($qr);
		$cats	= $db->loadObjectList();
		if (!$cats) {
			return $catsList;
		}
		$nlv = ' - - '.($lv == '' ? '' : $lv);
		foreach ($cats as $c) {
			$cat = new stdClass();
			$cat->id 				= $c->id;
			$cat->title				= $lv.$c->title;
			$cat->parent_id			= $c->parent_id;
			$cArr	= array();
			$cArr[]	= $cat;
			$subCat	= self::getCategories($cat->id, $nlv, array());
			if ($subCat) {
				$cArr	= array_merge($cArr,$subCat);
			}
			if (is_array($catsList)) {
				$catsList	= array_merge($catsList,$cArr);
			} else {
				$catsList	= $cArr;
			}
		}
		return $catsList;
	}
}