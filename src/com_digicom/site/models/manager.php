<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;
// include JPATH_COMPONENT_ADMINISTRATOR . '/models/orders.php';
class DigiComModelManager extends JModelList
{
	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	protected function populateState($ordering = 'ordering', $direction = 'ASC')
	{
		$app = JFactory::getApplication();

		// Get the parent id if defined.
		$search = $app->input->get('search', '', 'nohtml');
		$this->setState('filter.search', $search);

		$user = JFactory::getUser();
		$this->setState('filter.userid', $user->id);

		$params = $app->getParams();
		$this->setState('params', $params);

		parent::populateState($ordering, $direction);
	}
	/**
	 * Get the master query for retrieving a list of products subject to the model state.
	 *
	 * @return  JDatabaseQuery
	 *
	 * @since   1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('a.*')
			  ->from('#__digicom_orders as a');
			  // ->where('a.userid = ' . $db->quote($this->getState('filter.userid')));

		// Join over the users for the checked out user.
		$query->select(array('c.name','c.email'))
			->join('LEFT', '#__digicom_customers AS c ON c.id=a.userid');

		// Join over the users for the checked out user.
		$query->select('ju.username')
			->join('LEFT', '#__users AS ju ON ju.id=a.userid');


		// Filter by search
		if ($this->getState('filter.search'))
		{
			// $query->where('a.id = ' . $db->quote($this->getState('filter.search')));
		$search = $this->getState('filter.search');
		// print_r($search);die;
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			elseif (stripos($search, 'user:') === 0)
			{
				$search = $db->quote('%' . $db->escape(substr($search, 5), true) . '%');
				$query->where('(ju.username LIKE ' . $search . ' OR c.name LIKE ' . $search . ' OR ju.name LIKE ' . $search . ')');
			}
			elseif (stripos($search, 'email:') === 0)
			{
				$search = $db->quote('%' . $db->escape(substr($search, 6), true) . '%');
				$query->where('(c.email LIKE ' . $search . ')');
			}
			elseif (stripos($search, 'processor:') === 0)
			{
				$search = $db->quote('%' . $db->escape(substr($search, 10), true) . '%');
				$query->where('(a.processor LIKE ' . $search . ')');
			}
			elseif (stripos($search, 'promocode:') === 0)
			{
				$search = $db->quote('%' . $db->escape(substr($search, 10), true) . '%');
				$query->where('(a.promocode LIKE ' . $search . ')');
			}
			else
			{
				$query->where('a.id = ' . (int) $search);
			}

		}
		}

		$query->order('id desc');

		// echo $query->__toString();die;
		return $query;
	}
	
}
