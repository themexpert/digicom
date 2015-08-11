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
use Joomla\String\String;

/**
 * DigiCom Order Model class.
 *
 * @since  1.0.0
 */
class DigiComModelOrder extends JModelAdmin
{
	/**
	 * The type alias for the product type.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public $typeAlias = 'com_digicom.order';

	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $text_prefix = 'COM_DIGICOM_ORDER';

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission for the component.
	 *
	 * @since   1.6
	 */
	protected function canDelete($record)
	{
		if (!empty($record->id))
		{
			if ($record->state != -2)
			{
				return;
			}

			return JFactory::getUser()->authorise('core.delete', 'com_digicom.order.' . (int) $record->catid);

		}
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission for the component.
	 *
	 * @since   1.6
	 */
	protected function canEditState($record)
	{
		if (!empty($record->id))
		{
			return JFactory::getUser()->authorise('core.edit.state', 'com_digicom.order.' . (int) $record->id);
		}

		return parent::canEditState($record);
	}

	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $type    The table name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable  A JTable object
	 *
	 * @since   1.6
	 */
	public function getTable($type = 'Order', $prefix = 'Table', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Abstract method for getting the form from the model.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 *
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_digicom.order', 'order', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  array  The default data is an empty array.
	 *
	 * @since   1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_digicom.edit.order.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		$this->preprocessData('com_digicom.order', $data);

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed  Object on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function getItem($pk = null)
	{

		if ($item = parent::getItem($pk))
		{

			$item->products = $this->getProducts($item->id);

		}

		return $item;

	}

	public function getProducts($order)
	{
		$db = JFactory::getDBO();
		$sql = "SELECT p.id, p.name, p.catid, od.quantity,od.package_type, od.amount_paid, od.price FROM #__digicom_products as p, #__digicom_orders_details as od WHERE p.id=od.productid AND od.orderid='". $order ."'";
		$db->setQuery($sql);
		return $db->loadObjectList();
	}

	/**
	 * Method to validate the form data.
	 *
	 * @param   JForm   $form   The form to validate against.
	 * @param   array   $data   The data to validate.
	 * @param   string  $group  The name of the field group to validate.
	 *
	 * @return  mixed  Array of filtered data if valid, false otherwise.
	 *
	 * @see     JFormRule
	 * @see     JFilterInput
	 * @since   12.2
	 */
	public function validate($form, $data, $group = null)
	{

		return $data;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since	3.1
	 */

	public function save($data)
	{
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$table = $this->getTable();
		$table->load($data['id']);

		//print_r($data);die;
		$status = $data['status'];
		if($status == 'Paid'){
			$data['amount_paid'] = $table->amount;
			$data['status'] = 'Active';
		}

		if(empty($table->transaction_number)){
			$data['transaction_number'] = DigiComSiteHelperDigicom::getUniqueTransactionId($table->id);
		}

		if(parent::save($data)){

			if($status == "Pending"){
				$sql = "update #__digicom_orders_details set published=0 where orderid in ('".$table->id."')";
				$type = 'process_order';
			}
			elseif($status == "Active" or $status == "Paid"){
				$sql = "update #__digicom_orders_details set published=1 where orderid in ('" . $table->id  . "')";
				$type = 'complete_order';
			}
			elseif($status == "Cancel"){
				$sql = "update #__digicom_orders_details set published='-1' where orderid in ('" . $table->id  . "')";
				$type = 'cancel_order';
			}
			$db->setQuery($sql);
			$db->execute();

			$info = array(
				'orderid' => $table->id,
				'status' => $status,
				'now_paid' => $data['amount_paid'],
				'total_paid' => $table->amount_paid,
				'username' => JFactory::getUser()->username
			);

			DigiComSiteHelperLog::setLog('status', 'Admin order save', $table->id, 'Admin changed order#'.$table->id.', status: '.$status.', paid: '.$data['amount_paid'], json_encode($info),$status);

			$orders = $this->getInstance( "Orders", "DigiComModel" );
			$orders->updateLicensesStatus($data['id'], $type);

			if($status == "Active" or $status == "Paid"){
				DigiComHelperEmail::sendApprovedEmail($data['id'], $type, $status, $data['amount_paid']);
			}

		}

		return true;

	}

	function getConfigs() {
		$comInfo = JComponentHelper::getComponent('com_digicom');
		return $comInfo->params;
	}
}
