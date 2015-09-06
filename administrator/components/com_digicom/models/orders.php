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
 * Methods supporting a list of product records.
 *
 * @since  1.6
 */
class DigiComModelOrders extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array  An optional associative array of configuration settings.
	 *
	 * @see     JControllerLegacy
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'name', 'p.name',
				'amount,	a.amount',
				'amount_paid,	a.amount_paid',
				'status', 'a.status',
				'order_date', 'a.order_date',
				'ordering','order_type',
				'startdate','enddate'
			);

		}

		parent::__construct($config);
	}

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
		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$status = $this->getUserStateFromRequest($this->context . '.filter.status', 'filter_status', null, 'string');
		$this->setState('filter.status', $status);

		$order_type = $this->getUserStateFromRequest($this->context . '.filter.order_type', 'filter_order_type', null, 'string');
		$this->setState('filter.order_type', $order_type);

		$startdate = $this->getUserStateFromRequest($this->context . '.filter.startdate', 'filter_startdate', '','string');
		$this->setState('filter.startdate', $startdate);

		$enddate = $this->getUserStateFromRequest($this->context . '.filter.enddate', 'filter_enddate', '','string');
		$this->setState('filter.enddate', $enddate);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_digicom');
		$this->setState('params', $params);

		$filter = $this->getUserStateFromRequest($this->context . '.filter', 'filter', array(), 'array');
		//print_r($filter);die;
		// List state information.
		parent::populateState('a.id', 'desc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return  string  A store id.
	 *
	 * @since   1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.status');
		$id .= ':' . $this->getState('filter.order_type');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
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
		$user = JFactory::getUser();

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.*'
			)
		);
		$query->from($db->quoteName('#__digicom_orders') . ' AS a');

		// Join over the users for the checked out user.
		$query->select(array('c.name','c.email'))
			->join('LEFT', '#__digicom_customers AS c ON c.id=a.userid');

		// Join over the users for the checked out user.
		$query->select('ju.username')
			->join('LEFT', '#__users AS ju ON ju.id=a.userid');

		// Filter by status
		if ($status = $this->getState('filter.status'))
		{
			$query->where('a.status = ' . $db->quote($status));
		}

		// Filter by status
		if ($order_type = $this->getState('filter.order_type'))
		{
			if($order_type == 'free'){
				$query->where('a.amount <= 0');
			}else{
				$query->where('a.amount > 0');
			}

		}

		// Filter by order create date
		$startdate = $this->getState('filter.startdate');
		$enddate = $this->getState('filter.enddate');

		if($startdate && empty($enddate)){
			$enddate = date('Y-m-d');
		}

		if($enddate && empty($startdate)){
			$startdate = date('Y-m-d');
		}
		if($startdate && $enddate){
			$startdate = $startdate . ' 00:00:00';
			$enddate = $enddate . ' 23:59:59';

			$query->where('a.order_date BETWEEN ' . $db->quote($startdate) . ' AND ' . $db->quote($enddate));
		}

		// Filter by search in title.
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			elseif (stripos($search, 'user:') === 0)
			{
				$search = $db->quote('%' . $db->escape(substr($search, 5), true) . '%');
				$query->where('(ju.username LIKE ' . $search . ' OR c.name LIKE ' . $search . ')');
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

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'a.id');
		$orderDirn = $this->state->get('list.direction', 'desc');

		$query->order($db->escape($orderCol . ' ' . $orderDirn));
		//echo $query->__toString();jexit();
		return $query;
	}

}
