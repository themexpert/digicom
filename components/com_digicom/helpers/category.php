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
 * DigiCom Component Category Tree
 *
 * @package     DigiCom
 * @since       1.0.0
 */
class DigiComCategories extends JCategories
{
	/**
	 * Name of the items state field
	 *
	 * @var    string
	 * @since  1.0.0
	 */

	public function __construct($options = array())
	{
		$options['statefield'] = 'published';
		$options['table'] = '#__digicom_products';
		$options['extension'] = 'com_digicom';
		parent::__construct($options);
	}
}
