<?php
/**
 * @package		DigiCom
 * @copyright	Copyright (c)2010-2015 ThemeXpert
 * @license 	GNU General Public License version 3, or later
 * @author 		ThemeXpert http://www.themexpert.com
 * @since 		1.0.0
 */

defined('_JEXEC') or die;


use Joomla\Registry\Registry;
use Joomla\String\String;

/**
 * DigiCom Order Model class.
 *
 * @since  1.0.0
 */
class DigiComModelOrder extends JModelAdmin
{
	/**
	 * The type alias for the product type.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public $typeAlias = 'com_digicom.order';

	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $text_prefix = 'COM_DIGICOM_ORDER';

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission for the component.
	 *
	 * @since   1.6
	 */
	protected function canDelete($record)
	{
		if (!empty($record->id))
		{
			if ($record->state != -2)
			{
				return;
			}

			return JFactory::getUser()->authorise('core.delete', 'com_digicom.order.' . (int) $record->catid);

		}
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission for the component.
	 *
	 * @since   1.6
	 */
	protected function canEditState($record)
	{
		if (!empty($record->id))
		{
			return JFactory::getUser()->authorise('core.edit.state', 'com_digicom.order.' . (int) $record->id);
		}

		return parent::canEditState($record);
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
	 * @since   1.6
	 */
	public function getTable($type = 'Order', $prefix = 'Table', $config = array())
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
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_digicom.order', 'order', array('control' => 'jform', 'load_data' => $loadData));
		
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
	 * @since   1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_digicom.edit.order.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		$this->preprocessData('com_digicom.order', $data);

		return $data;
	}
	
	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed  Object on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function getItem($pk = null)
	{
		
		if ($item = parent::getItem($pk))
		{
			
			$item->products = $this->getProducts($item->id);

		}
		
		return $item;

	}

	public function getProducts($order)
	{
		$db = JFactory::getDBO();
		$sql = "SELECT p.id, p.name, p.catid, od.quantity,od.package_type, od.amount_paid, p.price FROM #__digicom_products as p, #__digicom_orders_details as od WHERE p.id=od.productid AND od.orderid='". $order ."'";
		$db->setQuery($sql);
		return $db->loadObjectList();
	}

	public static function getChargebacks($order)
	{
		$db = JFactory::getDBO();
		$sql = "SELECT SUM(`cancelled_amount`)
				FROM `#__digicom_orders_details`
				WHERE `cancelled`=1
				  AND `orderid`=" . (int) $order;
		$db->setQuery($sql);
		return $db->loadResult();
	}

	public static function getRefunds($order)
	{
		$db = JFactory::getDBO();
		$sql = "SELECT SUM(`cancelled_amount`)
				FROM `#__digicom_orders_details`
				WHERE `cancelled`=2
				  AND `orderid`=" . (int) $order;
		$db->setQuery($sql);
		return $db->loadResult();
	}

	public static function getDeleted($order, $license=0)
	{
		$db = JFactory::getDBO();
		$sql = "SELECT SUM(`amount_paid`)
				FROM `#__digicom_orders_details`
				WHERE `cancelled`=3
				  AND `orderid`=" . (int) $order;
		$db->setQuery($sql);
		return $db->loadResult();
	}

	public static function isLicenseDeleted($id)
	{
		$db = JFactory::getDBO();
		$sql = "SELECT `cancelled`
				FROM `#__digicom_orders_details`
				WHERE `id`='" . $id . "'";
		$db->setQuery($sql);
		return $db->loadResult();
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

		// Alter the name for save as copy
		if ($app->input->get('task') == 'save2copy')
		{
			list($name, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['name']);
			$data['name']	= $name;
			$data['alias']	= $alias;
			$data['state']	= 0;
		}
		if(parent::save($data)){
			//hook the files here
			$recordId = $this->getState('order.id');

			if (isset($data['file']) && is_array($data['file']))
	        {
	            $files = $data['file'];
	            foreach($files as $key => $file){
	                $filesTable = $this->getTable('Files');
	                $filesTable->product_id = $recordId;
	                $filesTable->name = ($file['name'] ? $file['name'] : $file['url']);
	                $filesTable->url = $file['url'];
	                $filesTable->store();
	            }
	            if (isset($data['files_remove_id']) && !empty($data['files_remove_id'])){
	                $filesTable = JTable::getInstance('Files', 'Table');
	                $filesTable->removeUnmatch($data['files_remove_id'],$recordId);
	            }
	        }

	        // hook bundle item
			if (isset($data['bundle_category']) && is_array($data['bundle_category']))
	        {
	            $bTable = $this->getTable('Bundle');
	            $bTable->removeUnmatchBundle($data['bundle_category'],$recordId);

	            $bundleTable = $this->getTable('Bundle');
	            $bundle_category = $data['bundle_category'];
	            $bundleTable->bundle_type = 'category';

	            foreach($bundle_category as $bundle){          
	                $bundleTable->product_id = $recordId;
	                $bundleTable->bundle_id = $bundle;
	                $bundleTable->store();
	            }
	        }

	        if (isset($data['bundle_product']) && is_array($data['bundle_product']))
	        {
	            
	            $bTable = $this->getTable('Bundle');
	            $bTable->removeUnmatchBundle($data['bundle_product'],$recordId,'order');

	            $bundleTable = $this->getTable('Bundle');
	            $bundle_product = $data['bundle_product'];
	            $bundleTable->bundle_type = 'order';
	            foreach($bundle_product as $bundle){          
	                $bundleTable->product_id = $recordId;
	                $bundleTable->bundle_id = $bundle;
	                $bundleTable->store();
	            }
		
	        }			

	        return true;

		}

		return false;
	
	}

	/**
	 * Method to change the name & alias.
	 *
	 * @param   integer  $category_id  The id of the parent.
	 * @param   string   $alias        The alias.
	 * @param   string   $name         The name.
	 *
	 * @return  array  Contains the modified name and alias.
	 *
	 * @since   3.1
	 */
	protected function generateNewTitle($category_id, $alias, $name)
	{
		// Alter the name & alias
		$table = $this->getTable();

		while ($table->load(array('alias' => $alias, 'catid' => $category_id)))
		{
			if ($name == $table->name)
			{
				$name = String::increment($name);
			}

			$alias = String::increment($alias, 'dash');
		}

		return array($name, $alias);
	}

	/**
	 * Method to toggle the featured setting of articles.
	 *
	 * @param   array    $pks    The ids of the items to toggle.
	 * @param   integer  $value  The value to toggle to.
	 *
	 * @return  boolean  True on success.
	 */
	public function featured($pks, $value = 0)
	{
		// Sanitize the ids.
		$pks = (array) $pks;
		JArrayHelper::toInteger($pks);

		if (empty($pks))
		{
			$this->setError(JText::_('COM_DIGICOM_NO_ITEM_SELECTED'));

			return false;
		}

		$table = $this->getTable();

		try
		{
			$db = $this->getDbo();
			$query = $db->getQuery(true)
						->update($db->quoteName('#__digicom_products'))
						->set('featured = ' . (int) $value)
						->where('id IN (' . implode(',', $pks) . ')');
			$db->setQuery($query);
			$db->execute();

		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());
			return false;
		}

		$table->reorder();

		$this->cleanCache();

		return true;
	}

	function getConfigs() {
		$comInfo = JComponentHelper::getComponent('com_digicom');
		return $comInfo->params;
	}
}
