<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class plgContentDigiComInstallerScript
{

	public function postflight($type, $parent)
	{
		if ( $type == 'install' )
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);

			$fields = array(
				$db->quoteName('enabled') . ' = ' . (int) 1,
				$db->quoteName('ordering') . ' = ' . (int) 9999
			);

			$conditions = array(
				$db->quoteName('element') . ' = ' . $db->quote('digicom'),
				$db->quoteName('type') . ' = ' . $db->quote('plugin')
			);

			$query->update($db->quoteName('#__extensions'))->set($fields)->where($conditions);

			$db->setQuery($query);
			$db->execute();
		}

		return true;
	}
}
