<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_weblinks
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * digicom_products_bundle Table class
 *
 * @package     Joomla.Administrator
 * @subpackage  com_weblinks
 * @since       1.5
 */
class TableBundle extends JTable
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  &$db  A database connector object
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__digicom_products_bundle', 'id', $db);
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
		// Verify that the bundle is added once per product
		$table = JTable::getInstance('Bundle', 'Table');

		if ($table->load(array('product_id' => $this->product_id, 'bundle_id' => $this->bundle_id,'bundle_type'=>$this->bundle_type)) && ($table->id != $this->id || $this->id == 0))
		{
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
    
    public function removeUnmatch($files_id,$product_id,$bundle_type){
        $db = $this->getDbo();
        //DELETE from tablename WHERE id IN (1,2,3,...,254);
        $query = "DELETE from ".$db->quoteName('#__digicom_products_bundle')." WHERE ".$db->quoteName('product_id') . "='".$product_id ."' and ".$db->quoteName('bundle_type') . "='".$bundle_type ."' and ".$db->quoteName('bundle_id') . " in ('".$files_id."')";
        //echo $query;die;
		$db->setQuery($query);
        return $db->Query();
    }
     
    public function removeSameTypes($bundle_type,$product_id){
        $db = $this->getDbo();
        //DELETE from tablename WHERE id IN (1,2,3,...,254);
        $query = "DELETE from #__digicom_products_bundle WHERE ".$db->quoteName('product_id') . "='".$product_id ."' and ".$db->quoteName('bundle_type') . "='".$bundle_type ."'";
		$db->setQuery($query);
        return $db->Query();
    }
    
    
    /*
    * get all files list based on req
    */
    
    public function getList($field = 'product_id',$value,$bundle_type=null){
        $db = $this->getDbo();
        $query = $db->getQuery(true)
            ->select('b.bundle_id as bundle_id')
            ->select('b.bundle_type')
            ->select('p.name')
            ->from($db->quoteName('#__digicom_products_bundle').' as b')
            ->from($db->quoteName('#__digicom_products').' as p')
            ->where($db->quoteName('b.'.$field).'='.$value)
            ->where($db->quoteName('p.id').'=b.bundle_id');
		if(!empty($bundle_type)){
			$query->where($db->quoteName('b.bundle_type').'="'.$bundle_type.'"');
		}
        $db->setQuery($query);
        return $db->loadObjectList();
    }
	
    /*
    * get all files list based on req
    */
    
    public function getFieldValues($field = 'product_id',$value,$bundle_type=null){
        $db = $this->getDbo();
        $query = $db->getQuery(true)
            ->select('GROUP_CONCAT(bundle_id) as bundle_id')
            ->from($db->quoteName('#__digicom_products_bundle'))
            ->where($db->quoteName($field).'='.$value);
		if(!empty($bundle_type)){
			$query->where($db->quoteName('bundle_type').'="'.$bundle_type.'"');
		}
        $db->setQuery($query);
        return $db->loadObject();
    }
    
}
