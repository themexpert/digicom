<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

// TODO : Remove JRequest to JInput and php visibility

class DigiComModelDashboard extends JModelList
{

	/**
	 * Model context string.
	 *
	 * @var    string
	 * @since  3.1
	 */
	public $_context = 'com_digicom.dashboard';

	/**
	 * Method to auto-populate the model state.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @note Calling getState in this method will result in recursion.
	 *
	 * @since   3.1
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication('site');

		$offset = $app->input->get('limitstart', 0, 'uint');
		$this->setState('list.offset', $offset);
		$app = JFactory::getApplication();

		$params = $app->getParams();
		$this->setState('params', $params);

		$this->setState('list.limit', $params->get('maximum', 200));

		$this->setState('filter.published', 1);

		// Optional filter text
		$itemid = $app->input->getInt('Itemid', 0);
		$filterSearch = $app->getUserStateFromRequest('com_digicom.dashboard.list.' . $itemid . '.filter_search', 'filter-search', '', 'string');
		$this->setState('list.filter', $filterSearch);
	}

	/**
	 * Redefine the function and add some properties to make the styling more easy
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   3.1
	 */
	public function getItems()
	{
		// Invoke the parent getItems method to get the main list
		$items = parent::getItems();

		if (!count($items))
		{
			$app = JFactory::getApplication();
			$menu = $app->getMenu();
			$active = $menu->getActive();
			$params = new Registry;

			if ($active)
			{
				$params->loadString($active->params);
			}
		}

		return $items;
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return  string  An SQL query
	 *
	 * @since   1.6
	 */
	protected function getListQuery()
	{
		$app = JFactory::getApplication('site');
		$user	= JFactory::getUser();
		$groups	= implode(',', $user->getAuthorisedViewLevels());
		$orderby = $this->state->params->get('all_licenses_orderby', 'name');
		$published = $this->state->params->get('published', 1);
		$orderDirection = $this->state->params->get('all_licenses_orderby_direction', 'ASC');

		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		// Select required fields from the dashboard.
		$query->select('p.*')
			  ->select('l.*')
			  ->select(' DATEDIFF(expires, now()) as dayleft')
			  ->from($db->quoteName('#__digicom_products') . ' AS p');

		//JOIN employee ON employee.id=borrowed.employeeid
		$query->join('inner', '#__digicom_licenses AS l ON l.productid = p.id');

		if ($this->state->params->get('show_pagination_limit'))
		{
			$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->get('list_limit'), 'uint');
		}
		else
		{
			$limit = $this->state->params->get('maximum', 20);
		}

		$this->setState('list.limit', $limit);

		$offset = $app->input->get('limitstart', 0, 'uint');
		$this->setState('list.start', $offset);

		// Optionally filter on entered value
		//search
		if ($this->state->get('list.filter'))
		{
			$query->where(
				$db->quoteName('p.name') . ' LIKE '	 . $db->quote('%' . $this->state->get('list.filter') . '%')
				.
				' or '
				.
				$db->quoteName('l.licenseid') . ' = '	 . $db->quote( $this->state->get('list.filter') )
			);
		}

		$query->where($db->quoteName('l.active') . ' = ' . $published);
		$query->where($db->quoteName('l.userid') . ' = ' . $user->id);
		//$query->where('DATEDIFF(`expires`, now()) > -1 or DATEDIFF(`expires`, now()) IS NULL' );

		$query->order($db->quoteName($orderby) . ' ' . $orderDirection . ', p.name ASC');
		//echo $query->__tostring();die;
		return $query;
	}

}
