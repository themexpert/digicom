<?php
/**
 * @package     DigiCom
 * @author      ThemeXpert http://www.themexpert.com
 * @copyright   Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @since       1.0.0
 */

defined('_JEXEC') or die;

/**
 * Files Table class
 *
 * @package     DigiCom
 * @since       1.5
 */
class TableFiles extends JTable
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  &$db  A database connector object
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__digicom_products_files', 'id', $db);
	}
	
	
	/**
	 * Overload the store method for the Weblinks table.
	 *
	 * @param   boolean	Toggle whether null values should be updated.
	 * @return  boolean  True on success, false on failure.
	 * @since   1.6
	 */
	public function store($updateNulls = false)
	{
		$date	= JFactory::getDate();
        
		/*
        if ($this->id)
		{
			// Existing item
			$this->hits		= $this->hits+1;
		}
        */
        // New item. A item created and created_by field can be set by the user,
        // so we don't touch either of these if they are set.
        if (!(int) $this->creation_date)
        {
            $this->creation_date = $date->toSql();
        }
        
        // Verify that the file is
		$table = JTable::getInstance('Files', 'Table');
		if ($table->load(array('product_id' => $this->product_id, 'url' => $this->url)) && ($table->id != $this->id || $this->id == 0))
		{
			if(!($table->name == $this->name)){
				$table->name = $this->name;
				$table->store();
			}
			return true;
		}

		return parent::store($updateNulls);
	}
    
    /*
    * remove unmatch items from files list
    * item's files can be changed. so keep only current match and remove olds
    * run loop with existing db value and then check 
    * if that match with any submited value. if dont match remove it.
    * this way only new item will be stored and old will be removed
    */
    
    public function removeUnmatch($files_id,$product_id){
        $db = $this->getDbo();
        //DELETE from tablename WHERE id IN (1,2,3,...,254);
        $query = 'DELETE from '.$db->quoteName('#__digicom_products_files').' WHERE '.$db->quoteName('id') . ' in ('.$files_id.')';
        $db->setQuery($query);
        return $db->Query();
    }
    
    
    /*
    * get all files list based on req
    */
    
    public function getList($field = 'product_id',$value){
        $db = $this->getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__digicom_products_files'))
            ->where($db->quoteName($field).'='.$value)
            ->order($db->quoteName('ordering').' ASC');
        $db->setQuery($query);
        return $db->loadObjectList();
    }
    
}
