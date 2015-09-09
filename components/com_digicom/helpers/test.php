<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;
JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_digicom/tables', 'Table');

/**
 * Digicom Component Session Helper
 *
 * @since  1.0.0
 */
class DigiComSiteHelperTest
{
  /**
	 * Class constructor
	 *
	 * @return  return customer object
	 *
	 * @since   1.0.0
	 */
	public function __construct ()
	{
      // Please always return; only comment when you test;
      // from url, pass the value ?debug=1
      // it will trigger this helper
      return;
      // // do some job man!
      // $dispatcher = JDispatcher::getInstance();
      // $data = $dispatcher->trigger('onDigicomDomainControlShowDomainList',array(JFactory::getUser()->id));
      // print_r($data);die;
  }
}
