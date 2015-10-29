<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class DigiComModelOrder extends JModelItem
{

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since   1.0.0
	 *
	 * @return void
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication('site');

		// Load state from the request.
		$pk = $app->input->getInt('id');
		$this->setState('order.id', $pk);

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);

	}

	/**
	 * Method to get product data.
	 *
	 * @param   integer  $pk  The id of the product.
	 *
	 * @return  mixed  Menu item data object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('order.id');
		if ($this->_item === null)
		{
			$this->_item = array();
		}

		if (!isset($this->_item[$pk]))
		{
			try
			{
				$db = JFactory::getDBO();
				$query = $db->getQuery(true);
				$query->select('o.*')
					  ->from($db->quoteName('#__digicom_orders','o'))
					  ->where($db->quoteName('o.id').'='.intval($pk))
					  ->where($db->quoteName('o.published').'='.'1');

				$db->setQuery($query);
				$data = $db->loadObject();

				$db->clear();
				$query = $db->getQuery(true);
				$query->select($db->quoteName('p').'.*')
					  ->select($db->quoteName('od.quantity'))
					  ->select($db->quoteName('od.package_type'))
					  ->select($db->quoteName('od.price', 'price'))
					  ->select($db->quoteName('od.userid'))
					  ->from($db->quoteName('#__digicom_products','p'))
					  ->from($db->quoteName('#__digicom_orders_details','od'))
					  ->where($db->quoteName('p.id').'='.$db->quoteName('od.productid'))
					  ->where($db->quoteName('od.orderid').'='.$db->quote($data->id));
				$db->setQuery($query);
				$prods = $db->loadObjectList();

				$data->products = $prods;

				$this->_item[$pk] = $data;
			}
			catch (Exception $e)
			{
				if ($e->getCode() == 404)
				{
					// Need to go thru the error handler to allow Redirect to work.
					JError::raiseError(404, $e->getMessage());
				}
				else
				{
					$this->setError($e);
					$this->_item[$pk] = false;
				}
			}
		}

		return $this->_item[$pk];

	}

}
