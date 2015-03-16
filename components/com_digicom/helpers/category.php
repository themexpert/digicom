<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_weblinks
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Weblinks Component Category Tree
 *
 * @static
 * @package     Joomla.Site
 * @subpackage  com_weblinks
 * @since       1.6
 */
class DigiComCategories extends JCategories
{
	/**
	 * Name of the items state field
	 *
	 * @var    string
	 * @since  11.1
	 */

	public function __construct($options = array())
	{
		$options['statefield'] = 'published';
		$options['table'] = '#__digicom_products';
		$options['extension'] = 'com_digicom';
		parent::__construct($options);
	}
}
