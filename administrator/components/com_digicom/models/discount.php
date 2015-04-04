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
 * Discount model.
 *
 * @since  1.0.0
 */
class DigiComModelDiscount extends JModelAdmin
{
	/**
	 * The type alias for this content type.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public $typeAlias = 'com_digicom.discount';

	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $text_prefix = 'COM_DIGICOM_DISCOUNTS';

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission for the component.
	 *
	 * @since   1.0.0
	 */
	protected function canDelete($record)
	{
		if (!empty($record->id))
		{
			
			if ($record->catid)
			{
				return JFactory::getUser()->authorise('core.delete', 'com_digicom.discount.' . (int) $record->catid);
			}

			return parent::canDelete($record);
		}
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission for the component.
	 *
	 * @since   1.0.0
	 */
	protected function canEditState($record)
	{
		if (!empty($record->catid))
		{
			return JFactory::getUser()->authorise('core.edit.state', 'com_digicom.discount.' . (int) $record->catid);
		}

		return parent::canEditState($record);
	}


	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed  Object on success, false on failure.
	 *
	 * @since   1.0.0
	 */
	public function getItem($pk = null)
	{
		
		if ($item = parent::getItem($pk))
		{
			
			$item->products = $this->getPromoProducts($item->id);

		}
		
		return $item;

	}

	public function getPromoProducts($id)
	{
		$db = JFactory::getDBO();

		
		// Get previous orders restrictions
		$sql = "SELECT p.`name`, o.`productid` as id
				FROM `#__digicom_promocodes_products` AS o
					 INNER JOIN `#__digicom_products` AS p ON p.`id`=o.`productid`
				WHERE o.`promoid`=" . (int) $id . "
				ORDER BY p.`name`";
		$db->setQuery($sql);
		$promo_products = $db->loadObjectList();

		return $promo_products;
		
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
	 * @since   1.0.0
	 */
	public function getTable($type = 'Discount', $prefix = 'Table', $config = array())
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
	 * @since   1.0.0
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_digicom.discount', 'discount', array('control' => 'jform', 'load_data' => $loadData));

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
	 * @since   1.0.0
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_digicom.edit.discount.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		$this->preprocessData('com_digicom.discount', $data);
		
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
		if(!isset($data['validfornew'])){
			$data['validfornew'] = 0;
		}
		if(!isset($data['validforrenewal'])){
			$data['validforrenewal'] = 0;
		}

		if(parent::save($data)){
			//hook the files here
			$recordId = $this->getState('discount.id');

			$this->storeProducts($data['products'],$recordId);

	        return true;

		}

		return false;
	
	}

	function storeProducts($items,$id)
	{
		$db = JFactory::getDBO();

		if ($id)
		{
			$sql = "DELETE FROM `#__digicom_promocodes_products` WHERE `promoid`=$id";
			$db->setQuery($sql);
			$db->query();

			foreach($items as $item)
			{
				if ((int) $item)
				{
					$sql = "INSERT INTO `#__digicom_promocodes_products`(`promoid`, `productid`) VALUES($id, $item)";
					$db->setQuery($sql);
					$db->query();
				}
			}
		}
	}

	function getConfigs() {
		$comInfo = JComponentHelper::getComponent('com_digicom');
		return $comInfo->params;
	}

}
