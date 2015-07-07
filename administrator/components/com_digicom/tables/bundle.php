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
 * Bundle Table class
 *
 * @package     Digicom
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

    public function removeUnmatchBundle($bundle_items,$product_id,$bundle='category'){
        $items = implode(',', $bundle_items);
				if(empty($items)) return true;
        $db = $this->getDbo();
        $query = $db->getQuery(true)
            ->select('GROUP_CONCAT(id) as id')
            ->from($db->quoteName('#__digicom_products_bundle'))
            ->where($db->quoteName('product_id').'='.$product_id)
            ->where($db->quoteName('bundle_type').'='.$db->quote($bundle))
            ->where($db->quoteName('bundle_id').' NOT IN ('.$items.')');
        $db->setQuery($query);
        $found = $db->loadObject();

        if(!empty($found->id) > 0){
            //TODO: delete them
            $query = $db->getQuery(true)
                    ->delete('#__digicom_products_bundle')
                    ->where('id IN (' . $found->id . ')');
                $db->setQuery($query);
                $db->execute();

                if ($error = $db->getErrorMsg())
                {
                    $this->setError($error);
                    return false;
                }
                return true;
        }else{
            return true;
        }

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

    public function getList($field = 'product_id',$value){
        $db = $this->getDbo();
        $query = $db->getQuery(true)
            ->select('b.bundle_id as id')
            ->select('p.name')
            ->select('p.price')
            ->from($db->quoteName('#__digicom_products_bundle').' as b')
            ->from($db->quoteName('#__digicom_products').' as p')
            ->where($db->quoteName('b.'.$field).'='.$value)
            ->where($db->quoteName('p.id').'=b.bundle_id')
            ->where($db->quoteName('b.bundle_type').'="product"');
        $db->setQuery($query);
        $items = $db->loadObjectList();
        //print_r($items);die;
        return $items;

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
