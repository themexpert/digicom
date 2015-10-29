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
		$custommer = new DigiComSiteHelperSession();
		$input = JFactory::getApplication()->input;
		$search = $input->get('search','');

		$sql = "SELECT o.*"
				." FROM #__digicom_orders o, #__digicom_orders_details od"
				." WHERE ".($search ? 'o.id = "'.$search.'" and ' : '')."o.userid=".$custommer->_customer->id." group by o.id order by o.id desc";

		return $sql;
	}
	//
	// function getlistOrders(){
	// 	$input = JFactory::getApplication()->input;
	// 	$search = $input->get('search','');
	// 	$custommer = new DigiComSiteHelperSession();
	// 	//print_r($custommer);die;
	// 	$db = JFactory::getDBO();
	// 	if (empty ($this->_orders)) {
	//
	// 		$sql = "SELECT o.*"
	// 				." FROM #__digicom_orders o, #__digicom_orders_details od"
	// 				." WHERE ".($search ? 'o.id = "'.$search.'" and ' : '')."o.userid=".$custommer->_customer->id." group by o.id order by o.id desc";
	// 		$this->_orders = $this->_getList($sql);
	//
	// 		//print_r($this->_orders);die;
	//
	// 	}
	// 	return $this->_orders;
	// }


}
