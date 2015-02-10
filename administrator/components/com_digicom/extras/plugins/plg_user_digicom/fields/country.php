<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  User.profile
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

/**
 * Provides input for TOS
 *
 * @package     Joomla.Plugin
 * @subpackage  User.profile
 * @since       2.5.5
 */
class JFormFieldCountry extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  2.5.5
	 */
	protected $type = 'Country';

	/**
	 * Method to get the field label markup.
	 *
	 * @return  string  The field label markup.
	 *
	 * @since   2.5.5
	 */
	protected function getOptions()
	{
		$db = JFactory::getDbo();
		$sql = "SELECT DISTINCT
					`country` as `text`, `ccode` as `value`
				FROM
					`#__digicom_states`";
		$db->setQuery($sql);
		$list = $db->loadObjectList();
		$mitems = array();
		@$mitems[] = JHTML::_('select.option', '', JText::_('PLG_USER_DIGICOM_SELECT_COUNTRY'));
		if($list){
			foreach ($list as $item)
			{
				@$mitems[] = JHTML::_('select.option', $item->value, $item->text);
			}
		}
		return $mitems;
	}
}
