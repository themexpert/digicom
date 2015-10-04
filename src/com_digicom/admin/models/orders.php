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


	/**
	* method delete orders
	* delete license,
	* delete order details
	* event call after delete items
	*/
	function delete()
	{
		$db = JFactory::getDBO();
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$item = $this->getTable( 'Order' );

		foreach ($cids as $cid)
		{
			if (!$item->delete($cid))
			{
				$this->setError($item->getErrorMsg());
				return false;
			}
		}

		// delete order details
		$db->setQuery('delete from #__digicom_orders_details where orderid in ('.implode(',', $cids).')');
		if (!$db->execute())
		{
			$this->setError($db->getErrorMsg());
			return false;
		}


		// delete License
		$db->clear();
		$db->setQuery('delete from #__digicom_licenses where orderid in ('.implode(',', $cids).')');
		if (!$db->execute())
		{
			$this->setError($db->getErrorMsg());
			return false;
		}

		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onDigicomAdminAfterOrderDelete', array($cids));

		return true;
	}

	/**
	* method to cycleStatus
	* quick action to change order status
	*/
	function cycleStatus(){

		$db = JFactory::getDBO();
		$input = JFactory::getApplication()->input;
		//orderstatus
		//print_r($_POST);die;
		$orderids = $input->get('cid', null, null);
		$statuses = $input->post->get('orderstatus',null,null);
		$status = $statuses['0'];
		$id = $orderids['0'];

		$table = $this->getTable('order');
		$table->load($id);
		$table->status = $status;

		if(empty($table->transaction_number)){
			$table->transaction_number = DigiComSiteHelperDigicom::getUniqueTransactionId($table->id);
		}

		if($status == 'Paid'){
			$table->amount_paid = $table->amount;
			$table->status = 'Active';
		}elseif ($status == 'Refund') {
			$table->amount_paid = 0;
		}

		if(!$table->store()){
			return JFactory::getApplication()->enqueueMessage(JText::_('COM_DIGICOM_ORDER_STATUS_CHANGED_FAILED',$table->getErrorMsg()),'error');
		}

		if($status == "Pending"){
			$sql = "update #__digicom_orders_details set published=0 where orderid in ('".$id."')";
			$type = 'process_order';
		}
		elseif($status == "Active" or $status == "Paid"){
			$sql = "update #__digicom_orders_details set published=1 where orderid in ('" . $id  . "')";
			$type = 'complete_order';
		}
		elseif($status == "Cancel"){
			$sql = "update #__digicom_orders_details set published='-1' where orderid in ('" . $id  . "')";
			$type = 'cancel_order';
		}
		elseif($status == "Refund"){
			$sql = "update #__digicom_orders_details set published='-2' where orderid in ('" . $id  . "')";
			$type = 'refund_order';
		}

		$db->setQuery($sql);
		if(!$db->query()){
			$res = false;
		}

		// based on order status changes, we need to update license too :)
		$this->updateLicensesStatus($id, $type);

		// sent email as order status has changed
		DigiComHelperEmail::sendApprovedEmail($id, $type, $status);

		$dispatcher = JDispatcher::getInstance();
		if($status == "Active" or $status == "Paid"){

			$orders = $this->getInstance( "Order", "DigiComModel" );
			$orders->getOrderItems($id);

			$dispatcher->trigger('onDigicomAfterPaymentComplete', array($id, $info = array(), $table->processor, $items, $table->userid));
		}else{
			$dispatcher->trigger('onDigicomAdminAfterOrderStatusChange', array($table));
		}


		return true;
	}

	/*
	* create license as we are changng the status
	* $orderid = id of order
	* $type = order status; like:  complete_order;
	*/
	public function updateLicensesStatus($orderid, $type){
		$order = $this->getOrder($orderid);
		$items = $order->products;
		$customer_id = $order->userid;
		$number_of_products = count($items);
		DigiComSiteHelperLicense::updateLicenses($orderid, $number_of_products, $items, $customer_id, $type);
	}

	function getOrder($id = 0){

			$db = JFactory::getDBO();
			$sql = "SELECT o.*"
					." FROM #__digicom_orders o"
					." WHERE o.id='".intval($id)."' AND o.published='1'"
			;
			$db->setQuery($sql);
			$order = $db->loadObject();

			$sql = "SELECT p.id, p.name, p.price,p.catid, od.package_type,od.quantity, od.amount_paid FROM #__digicom_products as p, #__digicom_orders_details as od WHERE p.id=od.productid AND od.orderid='". $order->id ."'";
			$db->setQuery($sql);
			$prods = $db->loadObjectList();

			$order->products = $prods;

		return $order;
	}
}
