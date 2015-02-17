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

class TableCategory extends JTable {

	function TableCategory (&$db) {
		parent::__construct('#__digicom_categories', 'id', $db);
	}


	function store ($updateNulls = false) {
		return parent::store($updateNulls = false);
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

}