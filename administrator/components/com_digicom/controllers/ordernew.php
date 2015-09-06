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
 * OrderNew controller class.
 *
 * @since  1.0.0
 */
class DigiComControllerOrderNew extends JControllerForm
{

	/**
	 * Method to check if you can add a new record.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key.
	 *
	 * @return  boolean
	 *
	 * @since   1.0.0
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		$recordId = (int) isset($data[$key]) ? $data[$key] : 0;
		$categoryId = 0;

		if ($recordId)
		{
			$categoryId = (int) $this->getModel()->getItem($recordId)->catid;
		}

		if ($categoryId)
		{
			// The category has been set. Check the category permissions.
			return JFactory::getUser()->authorise('core.edit', $this->option . '.order.' . $categoryId);
		}

		// Since there is no asset tracking, revert to the component permissions.
		return parent::allowEdit($data, $key);
	}

	/**
	 * Function that allows child controller access to model data after the data has been saved.
	 *
	 * @param   JModelLegacy  $model      The data model object.
	 * @param   array         $validData  The validated data.
	 *
	 * @return	void
	 *
	 * @since	1.0.0
	 */
	protected function postSaveHook(JModelLegacy $model, $validData = array())
	{
		$task = $this->getTask();

		if ($task == 'save')
		{
			$this->setRedirect(JRoute::_('index.php?option=com_digicom&view=orders', false));
		}
	}


	/**
	* Function Calc to surve json return for new order price calc value on the fly
	*/
	function calc()
	{
		$this->_model = $this->getModel( "OrderNew" );
		//decode incoming JSON string
		$jsonRequest = JRequest::getVar("jsonString", "", "get");
		$jsonRequest = json_decode($jsonRequest);
		$calc_result = $this->_model->calcPrice($jsonRequest);

		$data = new stdclass();
		$data->amount = $calc_result['amount'];
		$data->amount_value = $calc_result['amount_value'];
		$data->tax = $calc_result['tax'];
		$data->tax_value = $calc_result['tax_value'];
		$data->discount_sign = $calc_result['discount_sign'];
		$data->discount = $calc_result['discount'];
		$data->total = $calc_result['total'];
		$data->total_value = $calc_result['total_value'];
		$data->currency = $calc_result['currency'];

		// Get the document object.
		$document = JFactory::getDocument();

		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');

		// Change the suggested filename.
		JResponse::setHeader('Content-Disposition','attachment;filename="orders.json"');
		// Output the JSON data.
		echo json_encode($data);
		JFactory::getApplication()->close();

	}

}
