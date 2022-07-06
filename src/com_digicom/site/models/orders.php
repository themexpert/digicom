<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class DigiComModelOrders extends JModelList
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
        $search = $app->input->get('search', '');
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
              ->from('#__digicom_orders as a')
              ->where('a.userid = ' . $db->quote($this->getState('filter.userid')));

        // Filter by search
        if ($this->getState('filter.search')) {
            $query->where('a.id = ' . $db->quote($this->getState('filter.search')));
        }

        // Add the list ordering clause.
        $query->order('a.id DESC');

        // echo $query->__toString();die;
        return $query;
    }

    /**
    * method to cycleStatus
    * quick action to change order status
    */
    public function cycleStatus()
    {
        $db = JFactory::getDBO();
        $input = JFactory::getApplication()->input;
        //orderstatus
        //print_r($_POST);die;
        $orderids = $input->get('cid', null, null);
        $statuses = $input->post->get('orderstatus', null, null);
        $status = $statuses['0'];
        $id = $orderids['0'];

        $table = $this->getTable('order');
        $table->load($id);
        $table->status = $status;

        if (empty($table->transaction_number)) {
            $table->transaction_number = DigiComSiteHelperDigicom::getUniqueTransactionId($table->id);
        }

        if ($status == 'Paid') {
            $table->amount_paid = $table->amount;
            $table->status = 'Active';
        } elseif ($status == 'Refund') {
            $table->amount_paid = 0;
        }

        if (!$table->store()) {
            return JFactory::getApplication()->enqueueMessage(JText::_('COM_DIGICOM_ORDER_STATUS_CHANGED_FAILED', $table->getErrorMsg()), 'error');
        }

        if ($status == 'Pending') {
            $sql = "update #__digicom_orders_details set published=0 where orderid in ('" . $id . "')";
            $type = 'process_order';
        } elseif ($status == 'Active' or $status == 'Paid') {
            $sql = "update #__digicom_orders_details set published=1 where orderid in ('" . $id . "')";
            $type = 'complete_order';
        } elseif ($status == 'Cancel') {
            $sql = "update #__digicom_orders_details set published='-1' where orderid in ('" . $id . "')";
            $type = 'cancel_order';
        } elseif ($status == 'Refund') {
            $sql = "update #__digicom_orders_details set published='-2' where orderid in ('" . $id . "')";
            $type = 'refund_order';
        }

        $db->setQuery($sql);
        $db->execute();

        // based on order status changes, we need to update license too :)
        $this->updateLicensesStatus($id, $type);

        // sent email as order status has changed
        DigiComHelperEmail::sendApprovedEmail($id, $type, $status);

        $dispatcher = JDispatcher::getInstance();
        if ($status == 'Active' or $status == 'Paid') {
            $orders = $this->getInstance('Order', 'DigiComModel');
            $items = $orders->getOrderItems($id);

            $dispatcher->trigger('onDigicomAfterPaymentComplete', [$id, $info = [], $table->processor, $items, $table->userid]);
        } else {
            $dispatcher->trigger('onDigicomAdminAfterOrderStatusChange', [$table]);
        }

        return true;
    }

    /*
    * create license as we are changng the status
    * $orderid = id of order
    * $type = order status; like:  complete_order;
    */
    public function updateLicensesStatus($orderid, $type)
    {
        $order = $this->getOrder($orderid);
        $items = $order->products;
        $customer_id = $order->userid;
        $number_of_products = count($items);
        DigiComSiteHelperLicense::updateLicenses($orderid, $number_of_products, $items, $customer_id, $type);
    }

    public function getOrder($id = 0)
    {
        $db = JFactory::getDBO();
        $sql = 'SELECT o.*'
                    . ' FROM #__digicom_orders o'
                    . " WHERE o.id='" . intval($id) . "' AND o.published='1'";
        $db->setQuery($sql);
        $order = $db->loadObject();

        $sql = "SELECT p.id, p.name, p.price,p.catid, od.package_type,od.quantity, od.amount_paid FROM #__digicom_products as p, #__digicom_orders_details as od WHERE p.id=od.productid AND od.orderid='" . $order->id . "'";
        $db->setQuery($sql);
        $prods = $db->loadObjectList();

        $order->products = $prods;

        return $order;
    }
}
