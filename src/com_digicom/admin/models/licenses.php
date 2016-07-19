<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class DigiComModelLicenses extends JModelList {

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
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
				'licenseid', 'a.licenseid',
				'orderid', 'a.orderid',
				'userid', 'a.userid',
				'productid', 'a.productid',
				'purchase', 'a.purchase',
				'expires', 'a.expires',
				'active', 'a.active'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = 'a.licenseid', $direction = 'asc')
	{
		// Load the filter state.
		$this->setState('filter.search', $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search', '', 'string'));
		$this->setState('filter.published', $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '', 'string'));
		$this->setState('filter.userid', $this->getUserStateFromRequest($this->context . '.filter.userid', 'filter_userid', '', 'int'));
		$this->setState('filter.orderid', $this->getUserStateFromRequest($this->context . '.filter.orderid', 'filter_orderid', '', 'int'));
		$this->setState('filter.purchase', $this->getUserStateFromRequest($this->context . '.filter.purchase', 'filter_purchase', '', 'string'));
		$this->setState('filter.expires', $this->getUserStateFromRequest($this->context . '.filter.expires', 'filter_expires', '', 'string'));

		// Load the parameters.
		$this->setState('params', JComponentHelper::getParams('com_digicom'));

		// List state information.
		parent::populateState($ordering, $direction);
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
		$id .= ':' . $this->getState('filter.published');
		$id .= ':' . $this->getState('filter.userid');
		$id .= ':' . $this->getState('filter.orderid');
		$id .= ':' . $this->getState('filter.purchase');
		$id .= ':' . $this->getState('filter.expires');

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
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.id AS id,'
				. 'a.licenseid AS licenseid,'
				. 'a.orderid AS orderid,'
				. 'a.userid AS userid,'
				. 'a.productid AS productid,'
				. 'a.purchase AS purchase,'
				. 'a.expires AS expires,'
				. 'a.active AS active'
			)
		);
		$query->from($db->quoteName('#__digicom_licenses', 'a'));

		// Join over the users
		$query->select($db->quoteName('dc.name', 'client'))
			->join('LEFT', $db->quoteName('#__digicom_customers', 'dc') . ' ON dc.id = a.userid');
		
		// Join over the users
		$query->select($db->quoteName('dp.name', 'productname'))
			->join('LEFT', $db->quoteName('#__digicom_products', 'dp') . ' ON dp.id = a.productid');
		
		// Filter by published state
		$published = $this->getState('filter.published');

		if (is_numeric($published))
		{
			$query->where($db->quoteName('a.active') . ' = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where($db->quoteName('a.active') . ' IN (0, 1)');
		}

		// Filter by client.
		$clientId = $this->getState('filter.client_id');

		if (is_numeric($clientId))
		{
			$query->where($db->quoteName('a.userid') . ' = ' . (int) $clientId);
		}

		// // Filter by search in title
		// $search = $this->getState('filter.search');

		// if (!empty($search))
		// {
		// 	if (stripos($search, 'user:') === 0)
		// 	{
		// 		$query->where(
		// 			$db->quoteName('dc.id') . ' = ' . (int) substr($search, 5)
		// 			. ' OR ' .
		// 			$db->quoteName('dc.name') . ' = ' . (int) substr($search, 5)
		// 		);
		// 	}
		// 	elseif (stripos($search, 'id:') === 0)
		// 	{
		// 		$query->where($db->quoteName('a.id') . ' = ' . (int) substr($search, 3));
		// 	}
		// 	else
		// 	{
		// 		$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
		// 		$query->where('(dc.name LIKE ' . $search . ' OR a.alias LIKE ' . $search . ')');
		// 	}
		// }

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering', 'a.id');
		$orderDirn = $this->state->get('list.direction', 'ASC');

		$query->order($db->escape($orderCol . ' ' . $orderDirn));
		// print_r($query->__tostring());die;
		return $query;
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable  A JTable object
	 *
	 * @since   1.6
	 */
	public function getTable($type = 'License', $prefix = 'Table', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

}