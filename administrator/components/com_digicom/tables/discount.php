<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class TableDiscount extends JTable
{

	public function __construct (&$db) {
		parent::__construct('#__digicom_promocodes', 'id', $db);
	}

	/**
	 * Overloaded check method to ensure data integrity.
	 *
	 * @return  boolean  True on success.
	 */
	public function check()
	{
		// check for valid name
		if (trim($this->title) == '')
		{
			$this->setError(JText::_('COM_DIGICOM_ERR_TABLES_TITLE'));
			return false;
		}
		// check for valid code
		if (trim($this->code) == '')
		{
			$this->setError(JText::_('COM_DIGICOM_ERR_TABLES_CODE'));
			return false;
		}
		
		// Check for existing name
		$query = $this->_db->getQuery(true)
			->select($this->_db->quoteName('id'))
			->from($this->_db->quoteName('#__digicom_promocodes'))
			->where($this->_db->quoteName('code') . ' = ' . $this->_db->quote($this->code));
		$this->_db->setQuery($query);

		$xid = (int) $this->_db->loadResult();
		if ($xid && $xid != (int) $this->id)
		{
			$this->setError(JText::_('COM_DIGICOM_ERR_DISCOUNT_TABLES_CODE_UNIQUE'));
			return false;
		}

		return true;

	}


	function store($updateNulls = false) {

		// Set codestart to null date if not set
		if (!$this->codestart)
		{
			$this->codestart = $this->_db->getNullDate();
		}

		// Set codeend to null date if not set
		if (!$this->codeend)
		{
			$this->codeend = $this->_db->getNullDate();
		}

		return parent::store($updateNulls = false);

	}

	function storeProducts($promoid)
	{
		$db = JFactory::getDBO();

		if ($promoid)
		{
			$sql = "DELETE FROM `#__digicom_promocodes_products`
					WHERE `promoid`=$promoid";
			$db->setQuery($sql);
			$db->query();

			foreach($_POST['items_product_id'] as $item)
			{
				if ((int) $item)
				{
					$sql = "INSERT INTO `#__digicom_promocodes_products`(`promoid`, `productid`)
							VALUES($promoid, $item)";
					$db->setQuery($sql);
					$db->query();
				}
			}
		}
	}

}
