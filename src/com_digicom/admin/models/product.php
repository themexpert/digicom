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
 * Product model.
 *
 * @since  1.0.0
 */
class DigiComModelProduct extends JModelAdmin
{
	/**
	 * The type alias for this content type.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public $typeAlias = 'com_digicom.product';

	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $text_prefix = 'COM_DIGICOM_PRODUCTS';

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
			if ($record->published != -2)
			{
				return;
			}

			if(!$this->checkOrderExist($record->id)) {
				return;
			}

			if ($record->catid)
			{
				return JFactory::getUser()->authorise('core.delete', 'com_digicom.category.' . (int) $record->catid);
			}

			return parent::canDelete($record);
		}
	}


	function checkOrderExist($pid)
	{
		$app = JFactory::getApplication();

		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('id')
			  ->from('#__digicom_orders_details')
			  ->where('productid = '.$pid)
			  ->limit('1');
		$db->setQuery($query);
		$od = $db->loadObject();
		if($od->id > 0){
			$app->enqueueMessage(JText::sprintf('COM_DIGICOM_PRODUCT_EXIST_IN_ORDER',$pid), 'warning');
			return false;
		}else{
			return true;
		}
	}

	/**
	 * Method to delete one or more records.
	 *
	 * @param   array  &$pks  An array of record primary keys.
	 *
	 * @return  boolean  True if successful, false if an error occurs.
	 *
	 * @since   12.2
	 */
	public function delete(&$pks)
	{
		$dispatcher = JEventDispatcher::getInstance();
		$pks = (array) $pks;
		$table = $this->getTable();

		// Include the plugins for the delete events.
		JPluginHelper::importPlugin($this->events_map['delete']);

		// Iterate the items to delete each one.
		foreach ($pks as $i => $pk)
		{
			if ($table->load($pk))
			{
				if ($this->canDelete($table))
				{
					$context = $this->option . '.' . $this->name;

					// Trigger the before delete event.
					$result = $dispatcher->trigger($this->event_before_delete, array($context, $table));

					if (in_array(false, $result, true))
					{
						$this->setError($table->getError());

						return false;
					}

					if (!$table->delete($pk))
					{
						$this->setError($table->getError());

						return false;
					}

					// Trigger the after event.
					$dispatcher->trigger($this->event_after_delete, array($context, $table));
				}
				else
				{
					// Prune items that you can't change.
					unset($pks[$i]);
					$error = $this->getError();

					if ($error)
					{
						JLog::add($error, JLog::WARNING, 'jerror');

						return false;
					}
					else
					{
						JLog::add(JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'), JLog::WARNING, 'jerror');

						return false;
					}
				}
			}
			else
			{
				$this->setError($table->getError());

				return false;
			}
		}

		// Clear the component's cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to change the published of the record. Defaults to the permission for the component.
	 *
	 * @since   1.0.0
	 */
	protected function canEditState($record)
	{
		if (!empty($record->catid))
		{
			return JFactory::getUser()->authorise('core.edit.state', 'com_digicom.category.' . (int) $record->catid);
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
	 * @since   1.0.0
	 */
	public function getTable($type = 'Product', $prefix = 'Table', $config = array())
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
		$form = $this->loadForm('com_digicom.product', 'product', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		// Determine correct permissions to check.
		if ($this->getState('product.id'))
		{
			// Existing record. Can only edit in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.edit');
		}
		else
		{
			// New record. Can only create in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.create');
		}

		// Modify the form based on access controls.
		if (!$this->canEditState((object) $data))
		{
			// Disable fields for display.
			$form->setFieldAttribute('ordering', 'disabled', 'true');
			$form->setFieldAttribute('published', 'disabled', 'true');
			$form->setFieldAttribute('publish_up', 'disabled', 'true');
			$form->setFieldAttribute('publish_down', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is a record you can edit.
			$form->setFieldAttribute('ordering', 'filter', 'unset');
			$form->setFieldAttribute('published', 'filter', 'unset');
			$form->setFieldAttribute('publish_up', 'filter', 'unset');
			$form->setFieldAttribute('publish_down', 'filter', 'unset');
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
		$data = JFactory::getApplication()->getUserState('com_digicom.edit.product.data', array());

		if (empty($data))
		{
			$data = $this->getItem();

			// Prime some default values.
			if ($this->getState('product.id') == 0)
			{
				$app = JFactory::getApplication();
				$data->set('catid', $app->input->get('catid', $app->getUserState('com_digicom.product.filter.category_id'), 'int'));

				$data->set('product_type', $app->input->get('product_type', 'reguler'));

			}

		}else{
			if(isset($data['bundle_product']) && count($data['bundle_product'])){
				$i = 0;
				foreach($data['bundle_product'] as $product){
					$tmp = $this->getItem($product);
					$item = new stdClass();
					$item->id = $product;
					$item->name = $tmp->name;
					$item->price = $tmp->price;

					$data['bundle_product'][$i] = $item;
					$i++;
				}
			}
		}

		$this->preprocessData('com_digicom.product', $data);

		return $data;
	}

	/**
	 * Override preprocessForm to load custom form from templates
	 *
	 * @param   JForm   $form   A JForm object.
	 * @param   mixed   $data   The data expected for the form.
	 * @param   string  $group  The name of the plugin group to import (defaults to "content").
	 *
	 * @return  void
	 *
	 * @throws	Exception if there is an error in the form event.
	 *
	 * @since   1.6
	 */
	protected function preprocessForm(JForm $form, $data, $group = 'content')
	{
		$db = JFactory::getDBO();
		$query = "SELECT template FROM #__template_styles WHERE client_id = 0 AND home = 1";
		$db->setQuery($query);
		$defaultemplate = $db->loadResult();

		$params = JComponentHelper::getParams('com_digicom');
		JForm::addFormPath(JPATH_SITE . '/templates/' . $defaultemplate . '/html/com_digicom/templates/'.$params->get('template','default'));
		$form->loadFile('attribs', false);

		parent::preprocessForm($form, $data, $group);
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
			// Convert the params field to an array.
			$registry = new Registry;
			$registry->loadString($item->attribs);
			$item->attribs = $registry->toArray();

			// Convert the images field to an array.
			$registry = new Registry;
			$registry->loadString($item->images);
			$item->images = $registry->toArray();
			if(isset($item->images['image_intro'])){
				$item->image_intro	=	$item->images['image_intro'];
			}
			if(isset($item->images['image_full'])){
				$item->image_full		=	$item->images['image_full'];
			}

			// Convert the metadata field to an array.
			$registry = new Registry;
			$registry->loadString($item->metadata);
			$item->metadata = $registry->toArray();

			if (!empty($item->id))
			{
				$item->tags = new JHelperTags;
				$item->tags->getTagIds($item->id, 'com_digicom.product');
				$item->metadata['tags'] = $item->tags;

				$filesTable = $this->getTable('Files');
				$item->file = $filesTable->getList('product_id',$item->id);

				$bundleTable = $this->getTable('Bundle');
				$bundle_category = $bundleTable->getFieldValues('product_id',$item->id,'category');
				$item->bundle_category = explode(',', $bundle_category->bundle_id);

				$item->bundle_product = $bundleTable->getList('product_id',$item->id,'product');

			}

		}

		// Load associated content items
		$app = JFactory::getApplication();
		$assoc = JLanguageAssociations::isEnabled();

		if ($assoc)
		{
			$item->associations = array();

			if ($item->id != null)
			{
				$associations = JLanguageAssociations::getAssociations('com_digicom', '#__digicom_products', 'com_digicom.product', $item->id);

				foreach ($associations as $tag => $association)
				{
					$item->associations[$tag] = $association->id;
				}
			}
		}

		return $item;
	}

	/**
	 * Prepare and sanitise the table data prior to saving.
	 *
	 * @param   JTable  $table  A reference to a JTable object.
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	protected function prepareTable($table)
	{
		$date = JFactory::getDate();
		$user = JFactory::getUser();

		// Set the publish date to now
		$db = $this->getDbo();

		if ($table->published == 1 && (int) $table->publish_up == 0)
		{
			$table->publish_up = JFactory::getDate()->toSql();
		}

		if ($table->published == 1 && intval($table->publish_down) == 0)
		{
			$table->publish_down = $db->getNullDate();
		}


		$table->name = htmlspecialchars_decode($table->name, ENT_QUOTES);
		$table->alias = JApplicationHelper::stringURLSafe($table->alias);

		if (empty($table->alias))
		{
			$table->alias = JApplicationHelper::stringURLSafe($table->name);
		}

		if (empty($table->id))
		{
			// Set the values

			// Set ordering to the last item if not set
			if (empty($table->ordering))
			{
				$query = $db->getQuery(true)
					->select('MAX(ordering)')
					->from($db->quoteName('#__digicom_products'));

				$db->setQuery($query);
				$max = $db->loadResult();

				$table->ordering = $max + 1;
			}
			else
			{
				// Set the values
				$table->modified    = $date->toSql();
				$table->modified_by = $user->id;
			}
		}

		// Reorder the articles within the category so the new article is first
		if (empty($table->id))
		{
			$table->reorder('catid = ' . (int) $table->catid . ' AND published >= 0');
		}

		// Increment the product version number.
		$table->version++;
	}

	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param   JTable  $table  A JTable object.
	 *
	 * @return  array  An array of conditions to add to ordering queries.
	 *
	 * @since   1.0.0
	 */
	protected function getReorderConditions($table)
	{
		$condition = array();
		$condition[] = 'catid = ' . (int) $table->catid;

		return $condition;
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
		$filter  = JFilterInput::getInstance();

		if (isset($data['metadata']) && isset($data['metadata']['author']))
		{
			$data['metadata']['author'] = $filter->clean($data['metadata']['author'], 'TRIM');
		}
		
		// Alter the name for save as copy
		if ($app->input->get('task') == 'save2copy')
		{
			list($name, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['name']);
			$data['name']	= $name;
			$data['alias']	= $alias;
			$data['published']	= 0;
		}

		$images = array(
			'image_intro'	=> $data['image_intro'],
			'image_full'	=> $data['image_full']
		);

		$registry = new Registry;
		$registry->loadArray($images);
		$data['images'] = (string) $registry;

		if (isset($data['attribs']) && is_array($data['attribs']))
		{
			$registry = new Registry;
			$registry->loadArray($data['attribs']);
			$data['attribs'] = (string) $registry;
		}

		if(parent::save($data))
		{
			//hook the files here
			$recordId = $this->getState('product.id');

			if (isset($data['file']) && is_array($data['file']))
      {
          $files = $data['file'];
          foreach($files as $key => $file){
              $filesTable = $this->getTable('Files');
              $filesTable->id = $file['id'];
              $filesTable->product_id = $recordId;
              $filesTable->name = ($file['name'] ? $file['name'] : JText::sprintf('COM_DIGICOM_PRODUCT_FILE_NAME',$key));
              $filesTable->url = $file['url'];
              $filesTable->ordering = $file['ordering'];
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
              $bundleTable->id = '';
              $bundleTable->product_id = $recordId;
              $bundleTable->bundle_id = $bundle;
              $bundleTable->store();
          }
      }

      if (isset($data['bundle_product']) && is_array($data['bundle_product']))
      {

          $bTable = $this->getTable('Bundle');
          $bTable->removeUnmatchBundle($data['bundle_product'],$recordId,'product');

          $bundleTable = $this->getTable('Bundle');
          $bundle_product = $data['bundle_product'];
          $bundleTable->bundle_type = 'product';
          foreach($bundle_product as $bundle){
              $bundleTable->id = '';
              $bundleTable->product_id = $recordId;
              $bundleTable->bundle_id = $bundle;
              $bundleTable->store();
          }

      }

			$assoc = JLanguageAssociations::isEnabled();
			if ($assoc)
			{
				$id = (int) $this->getState($this->getName() . '.id');
				$item = $this->getItem($id);

				// Adding self to the association
				$associations = $data['associations'];

				foreach ($associations as $tag => $id)
				{
					if (empty($id))
					{
						unset($associations[$tag]);
					}
				}

				// Detecting all item menus
				$all_language = $item->language == '*';

				if ($all_language && !empty($associations))
				{
					JError::raiseNotice(403, JText::_('COM_DIGICOM_ERROR_ALL_LANGUAGE_ASSOCIATED'));
				}

				$associations[$item->language] = $item->id;

				// Deleting old association for these items
				$db = JFactory::getDbo();
				$query = $db->getQuery(true)
					->delete('#__associations')
					->where('context=' . $db->quote('com_digicom.product'))
					->where('id IN (' . implode(',', $associations) . ')');
				$db->setQuery($query);
				$db->execute();

				if ($error = $db->getErrorMsg())
				{
					$this->setError($error);

					return false;
				}

				if (!$all_language && count($associations))
				{
					// Adding new association for these items
					$key = md5(json_encode($associations));
					$query->clear()
						->insert('#__associations');

					foreach ($associations as $id)
					{
						$query->values($id . ',' . $db->quote('com_digicom.product') . ',' . $db->quote($key));
					}

					$db->setQuery($query);
					$db->execute();

					if ($error = $db->getErrorMsg())
					{
						$this->setError($error);
						return false;
					}
				}
			}

      return true;

		}

		return false;

	}


	/**
	 * Method to duplicate modules.
	 *
	 * @param   array  &$pks  An array of primary key IDs.
	 *
	 * @return  boolean  True if successful.
	 *
	 * @since   1.0.0
	 * @throws  Exception
	 */
	public function duplicate(&$pks)
	{
		$user	= JFactory::getUser();
		$db		= $this->getDbo();

		// Access checks.
		if (!$user->authorise('core.create', 'com_digicom.product'))
		{
			throw new Exception(JText::_('JERROR_CORE_CREATE_NOT_PERMITTED'));
		}

		foreach ($pks as $pk)
		{
			$data = (array) $this->getItem($pk);
			list($name, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['name']);

			$data['id']	= '';
			$data['name']	= $name;
			$data['alias']	= $alias;
			$data['published']	= 0;
			$data['published']	= 0;
			$data['tags']	= '';
			$data['file'] = '';
			$data['bundle_category'] = '';
			$data['bundle_product'] = '';

			if(!$this->save($data)){
				return false;
			}
		}

		// Clear modules cache
		$this->cleanCache();

		return true;
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

	/**
	 * Custom clean the cache of com_content and content modules
	 *
	 * @param   string   $group      The cache group
	 * @param   integer  $client_id  The ID of the client
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function cleanCache($group = null, $client_id = 0)
	{
		parent::cleanCache('com_digicom');
		parent::cleanCache('mod_digicom_categories');
		parent::cleanCache('mod_digicom_cart');
	}

	function getConfigs() {
		$comInfo = JComponentHelper::getComponent('com_digicom');
		return $comInfo->params;
	}
}
