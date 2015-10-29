<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class DigiComModelCustomers extends JModelList {

	protected $_context = 'com_digicom.customers';

	/**
	 * Method to auto-populate the model state.
	 *
	 * @return  void
	 *
	 * @note    Calling getState in this method will result in recursion.
	 * @since   1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();

		$this->setState('filter.search', $app->getUserStateFromRequest($this->_context . '.filter.search', 'keyword', '', 'string'));

		// List state information.
		parent::populateState('a.id', 'desc');
	}

	protected function getListQuery()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('a.*')
					->from($db->quoteName('#__digicom_customers') . ' AS a');
					// Join over the language
		$query->select('u.username')
					->join('LEFT', $db->quoteName('#__users') . ' AS u ON u.id = a.id');

		$search = $this->getState('filter.search');

		if($search){
			$query->where("u.username like '%".$search."%' or a.name like '%".$search."%'");
		}
		$query->order('a.id desc');
		// echo $query->__tostring();die;
		return $query;

	}

}
