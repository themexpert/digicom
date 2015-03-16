<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 341 $
 * @lastmodified	$LastChangedDate: 2013-10-10 14:28:28 +0200 (Thu, 10 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

defined ('_JEXEC') or die ("Go away.");
use Joomla\Registry\Registry;

class TableCategory extends JTableNested {

	function __construct (&$db) {

		//$this->extension = 'com_digicom';
		parent::__construct('#__digicom_categories', 'id', $db);

		JTableObserverTags::createObserver($this, array('typeAlias' => 'com_digicom.category'));
		JTableObserverContenthistory::createObserver($this, array('typeAlias' => 'com_digicom.category'));

		$this->access = (int) JFactory::getConfig()->get('access');
	}

	
	/**
	 * Overloaded check method to ensure data integrity.
	 *
	 * @return  boolean  True on success.
	 */
	public function check()
	{
		// check for valid name
		if (trim($this->name) == '')
		{
			$this->setError(JText::_('COM_DIGICOM_ERR_TABLES_TITLE'));
			return false;
		}
		
		// Check for existing name
		$query = $this->_db->getQuery(true)
			->select($this->_db->quoteName('id'))
			->from($this->_db->quoteName('#__digicom_categories'))
			->where($this->_db->quoteName('name') . ' = ' . $this->_db->quote($this->name));
		$this->_db->setQuery($query);

		$xid = (int) $this->_db->loadResult();
		if ($xid && $xid != (int) $this->id)
		{
			$this->setError(JText::_('COM_DIGICOM_ERR_TABLES_NAME'));
			return false;
		}
		
		if (empty($this->alias))
		{
			$this->alias = $this->name;
		}
		
		$this->alias = JApplication::stringURLSafe($this->alias);
		
		if (trim(str_replace('-', '', $this->alias)) == '')
		{
			$this->alias = JFactory::getDate()->format("Y-m-d-H-i-s");
		}
		
		// clean up keywords -- eliminate extra spaces between phrases
		// and cr (\r) and lf (\n) characters from string
		if (!empty($this->metakeywords))
		{
			// only process if not empty
			$bad_characters = array("\n", "\r", "\"", "<", ">"); // array of characters to remove
			$after_clean = JString::str_ireplace($bad_characters, "", $this->metakeywords); // remove bad characters
			$keys = explode(',', $after_clean); // create array using commas as delimiter
			$clean_keys = array();

			foreach ($keys as $key)
			{
				if (trim($key)) {  // ignore blank keywords
					$clean_keys[] = trim($key);
				}
			}
			$this->metakeywords = implode(", ", $clean_keys); // put array back together delimited by ", "
		}
		//set meta title
		if (trim($this->metatitle) == '')
		{
			$this->metatitle = $this->name;
		}

		return true;
	}


	/**
	 * Method to compute the default name of the asset.
	 * The default name is in the form table_name.id
	 * where id is the value of the primary key of the table.
	 *
	 * @return  string
	 *
	 * @since   11.1
	 */
	protected function _getAssetName()
	{
		$k = $this->_tbl_key;

		return 'com_digicom' . '.category.' . (int) $this->$k;
	}

	/**
	 * Method to return the title to use for the asset table.
	 *
	 * @return  string
	 *
	 * @since   11.1
	 */
	protected function _getAssetTitle()
	{
		return $this->name;
	}

	/**
	 * Get the parent asset id for the record
	 *
	 * @param   JTable   $table  A JTable object for the asset parent.
	 * @param   integer  $id     The id for the asset
	 *
	 * @return  integer  The id of the asset's parent
	 *
	 * @since   11.1
	 */
	protected function _getAssetParentId(JTable $table = null, $id = null)
	{
		$assetId = null;

		// This is a category under a category.
		if ($this->parent_id > 1)
		{
			// Build the query to get the asset id for the parent category.
			$query = $this->_db->getQuery(true)
				->select($this->_db->quoteName('asset_id'))
				->from($this->_db->quoteName('#__digicom_categories'))
				->where($this->_db->quoteName('id') . ' = ' . $this->parent_id);

			// Get the asset id from the database.
			$this->_db->setQuery($query);

			if ($result = $this->_db->loadResult())
			{
				$assetId = (int) $result;
			}
		}
		// This is a category that needs to parent with the extension.
		elseif ($assetId === null)
		{
			// Build the query to get the asset id for the parent category.
			$query = $this->_db->getQuery(true)
				->select($this->_db->quoteName('id'))
				->from($this->_db->quoteName('#__assets'))
				->where($this->_db->quoteName('name') . ' = ' . $this->_db->quote('com_digicom'));

			// Get the asset id from the database.
			$this->_db->setQuery($query);

			if ($result = $this->_db->loadResult())
			{
				$assetId = (int) $result;
			}
		}

		// Return the asset id.
		if ($assetId)
		{
			return $assetId;
		}
		else
		{
			return parent::_getAssetParentId($table, $id);
		}
	}

	

	/**
	 * Overloaded bind function.
	 *
	 * @param   array   $array   named array
	 * @param   string  $ignore  An optional array or space separated list of properties
	 *                           to ignore while binding.
	 *
	 * @return  mixed   Null if operation was satisfactory, otherwise returns an error
	 *
	 * @see     JTable::bind()
	 * @since   11.1
	 */
	public function bind($array, $ignore = '')
	{
		if (isset($array['params']) && is_array($array['params']))
		{
			$registry = new Registry;
			$registry->loadArray($array['params']);
			$array['params'] = (string) $registry;
		}

		if (isset($array['metadata']) && is_array($array['metadata']))
		{
			$registry = new Registry;
			$registry->loadArray($array['metadata']);
			$array['metadata'] = (string) $registry;
		}

		// Bind the rules.
		if (isset($array['rules']) && is_array($array['rules']))
		{
			$rules = new JAccessRules($array['rules']);
			$this->setRules($rules);
		}

		return parent::bind($array, $ignore);
	}

	/**
	 * Overridden JTable::store to set created/modified and user id.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   11.1
	 */
	public function store($updateNulls = false)
	{
		$date = JFactory::getDate();
		$user = JFactory::getUser();

		$this->modified_time = $date->toSql();

		if ($this->id)
		{
			// Existing category
			$this->modified_user_id = $user->get('id');
		}
		else
		{
			// New category
			$this->created_time = $date->toSql();
			$this->created_user_id = $user->get('id');
		}

		// Verify that the alias is unique
		$table = JTable::getInstance('Category', 'JTable', array('dbo' => $this->getDbo()));

		if ($table->load(array('alias' => $this->alias, 'parent_id' => $this->parent_id))
			&& ($table->id != $this->id || $this->id == 0))
		{
			$this->setError(JText::_('JLIB_DATABASE_ERROR_CATEGORY_UNIQUE_ALIAS'));

			return false;
		}

		return parent::store($updateNulls);
	}



}