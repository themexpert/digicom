
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

jimport('joomla.application.component.modellist');
jimport('joomla.utilities.date');

class DigiComAdminModelCategories extends JModelList {
	
	protected $_context = 'com_digicom.category';
	private $total=0;
	var $_categories;
	var $_category;
	var $_id = null;
	var $_total = 0;
	var $_pagination = null;

	function __construct () {
		
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'name', 'a.name',
				'alias', 'a.alias',
				'published', 'a.published',
				'access', 'a.access', 'access_level',
				'language', 'a.language',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'created_time', 'a.created_time',
				'created_user_id', 'a.created_user_id',
				'lft', 'a.lft',
				'rgt', 'a.rgt',
				'level', 'a.level',
				'path', 'a.path',
				'tag'
			);
		}
		
		parent::__construct($config);		
	}

	function populateState($ordering = NULL, $direction = NULL){
		$app = JFactory::getApplication();
		$context = $this->context;

		$extension = $app->getUserStateFromRequest('com_digicom.categories.filter.extension', 'extension', 'com_digicom', 'cmd');

		$this->setState('filter.extension', $extension);

		// Extract the component name
		$this->setState('filter.component', 'com_digicom');

		// Extract the optional section name
		$this->setState('filter.section', null);

		$search = $this->getUserStateFromRequest($context . '.search', 'filter_search');
		$this->setState('filter.search', $search);

		$level = $this->getUserStateFromRequest($context . '.filter.level', 'filter_level');
		$this->setState('filter.level', $level);

		$access = $this->getUserStateFromRequest($context . '.filter.access', 'filter_access');
		$this->setState('filter.access', $access);

		$published = $this->getUserStateFromRequest($context . '.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$language = $this->getUserStateFromRequest($context . '.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);

		$tag = $this->getUserStateFromRequest($this->context . '.filter.tag', 'filter_tag', '');
		$this->setState('filter.tag', $tag);
		
		// List state information.
		parent::populateState('a.lft', 'asc');

		// Force a language
		$forcedLanguage = $app->input->get('forcedLanguage');

		if (!empty($forcedLanguage))
		{
			$this->setState('filter.language', $forcedLanguage);
			$this->setState('filter.forcedLanguage', $forcedLanguage);
		}
	}

	function setId($id) {
		$this->_id = $id;
		$this->_category = null;
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return  string  A store id.
	 *
	 * @since   1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.extension');
		$id .= ':' . $this->getState('filter.published');
		$id .= ':' . $this->getState('filter.language');

		return parent::getStoreId($id);
	}

	/**
	 * Method to get a database query to list categories.
	 *
	 * @return  JDatabaseQuery object.
	 *
	 * @since   1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.id, a.name, a.alias, a.published, a.access' .
				', a.checked_out, a.checked_out_time, a.created_user_id' .
				', a.path, a.parent_id, a.level, a.lft, a.rgt' .
				', a.language'
			)
		);
		$query->from('#__digicom_categories AS a');

		// Join over the language
		$query->select('l.title AS language_title')
			->join('LEFT', $db->quoteName('#__languages') . ' AS l ON l.lang_code = a.language');

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor')
			->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		// Join over the asset groups.
		$query->select('ag.title AS access_level')
			->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

		// Join over the users for the author.
		$query->select('ua.name AS author_name')
			->join('LEFT', '#__users AS ua ON ua.id = a.created_user_id');

		// Join over the associations.
		$assoc = $this->getAssoc();

		if ($assoc)
		{
			$query->select('COUNT(asso2.id)>1 as association')
				->join('LEFT', '#__associations AS asso ON asso.id = a.id AND asso.context=' . $db->quote('com_categories.item'))
				->join('LEFT', '#__associations AS asso2 ON asso2.key = asso.key')
				->group('a.id, l.title, uc.name, ag.title, ua.name');
		}

		// Filter by extension
		if ($extension = $this->getState('filter.extension'))
		{
			$query->where('a.parent_id > 0');
		}

		// Filter on the level.
		if ($level = $this->getState('filter.level'))
		{
			$query->where('a.level <= ' . (int) $level);
		}

		// Filter by access level.
		if ($access = $this->getState('filter.access'))
		{
			$query->where('a.access = ' . (int) $access);
		}

		// Implement View Level Access
		if (!$user->authorise('core.admin'))
		{
			$groups = implode(',', $user->getAuthorisedViewLevels());
			$query->where('a.access IN (' . $groups . ')');
		}

		// Filter by published state
		$published = $this->getState('filter.published');

		if (is_numeric($published))
		{
			$query->where('a.published = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.published IN (0, 1))');
		}

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			elseif (stripos($search, 'author:') === 0)
			{
				$search = $db->quote('%' . $db->escape(substr($search, 7), true) . '%');
				$query->where('(ua.name LIKE ' . $search . ' OR ua.username LIKE ' . $search . ')');
			}
			else
			{
				$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
				$query->where('(a.name LIKE ' . $search . ' OR a.alias LIKE ' . $search . ' OR a.note LIKE ' . $search . ')');
			}
		}

		// Filter on the language.
		if ($language = $this->getState('filter.language'))
		{
			$query->where('a.language = ' . $db->quote($language));
		}

		// Filter by a single tag.
		$tagId = $this->getState('filter.tag');

		if (is_numeric($tagId))
		{
			$query->where($db->quoteName('tagmap.tag_id') . ' = ' . (int) $tagId)
				->join(
					'LEFT', $db->quoteName('#__contentitem_tag_map', 'tagmap')
					. ' ON ' . $db->quoteName('tagmap.content_item_id') . ' = ' . $db->quoteName('a.id')
					. ' AND ' . $db->quoteName('tagmap.type_alias') . ' = ' . $db->quote($extension . '.category')
				);
		}

		// Add the list ordering clause
		$listOrdering = $this->getState('list.ordering', 'a.lft');
		$listDirn = $db->escape($this->getState('list.direction', 'ASC'));

		if ($listOrdering == 'a.access')
		{
			$query->order('a.access ' . $listDirn . ', a.lft ' . $listDirn);
		}
		else
		{
			$query->order($db->escape($listOrdering) . ' ' . $listDirn);
		}

		return $query;
	}


	/**
	 * Method to determine if an association exists
	 *
	 * @return  boolean  True if the association exists
	 *
	 * @since  3.0
	 */

	public function getAssoc()
	{
		static $assoc = null;

		if (!is_null($assoc))
		{
			return $assoc;
		}

		$app = JFactory::getApplication();
		$extension = $this->getState('filter.extension');

		$assoc = JLanguageAssociations::isEnabled();
		$extension = explode('.', $extension);
		$component = array_shift($extension);
		$cname = str_replace('com_', '', $component);

		if (!$assoc || !$component || !$cname)
		{
			$assoc = false;
		}
		else
		{
			$hname = $cname . 'HelperAssociation';
			JLoader::register($hname, JPATH_SITE . '/components/' . $component . '/helpers/association.php');

			$assoc = class_exists($hname) && !empty($hname::$category_association);
		}

		return $assoc;
	}

	
	function getlistCategories(){
		if (empty ($this->_categories)) {
			$sql = "select * from #__digicom_categories order by parent_id, ordering asc";
			$this->_total = $this->_getListCount($sql);
			if ($this->getState('limitstart') > $this->_total) $this->setState('limitstart', 0);
			if ($this->getState('limitstart') > 0 & $this->getState('limit') == 0)  $this->setState('limitstart', 0);
			$this->_categories = $this->_getList($sql);
		}
		return $this->_categories;
	}

	function getCategory() {
		if (empty ($this->_category)) {
			$this->_category = $this->getTable("Category");
			$this->_category->load($this->_id);
		}
		return $this->_category;
	}

	function store(){
		
		$item = $this->getTable('Category');
		//file processing
		$data = JFactory::getApplication()->input->get('jform', array(), 'ARRAY');
		if(!$item->bind($data)){
			$this->setError($item->getError());
			return false;
		}

		if(!$item->check()){
			$this->setError($item->getError());
			return false;
		}
		if (!$item->store()){
			return false;
		}
		// Reorder categories
		$item->reorder('`parent_id` = ' . (int) $item->parent_id);		
		return true;
	}

	function delete () {
		$cids = JRequest::getVar('cid', array(0), 'post', 'array');
		$database = JFactory::getDBO();
		if (count($cids)) {
			$cid = implode(',', $cids);

			$sql = "select catid from #__digicom_products where catid in (" . $cid . ")";
			$database->setQuery($sql);
			$products = $database->loadObjectList();
			
			if(count($products) > 0){
				$this->setError(JText::_('COM_DIGICOM_ERROR_CAT_HAS_PRODUCTS'));
				return false;
			}

			$query = "DELETE FROM #__digicom_categories" . "\n WHERE id IN ( $cid )";
			$database->setQuery($query);
			if (!$database->query()) {
				$this->setError($database->getError());
				return false;
			}
		}

		return true;
	}

	
	function publish () {
		$db = JFactory::getDBO();
		$cids = JRequest::getVar('cid', array(0), 'post', 'array');
		$task = JRequest::getVar('task', '', 'post');
		$item = $this->getTable('Category');

		$database = JFactory::getDBO();
		$cid = implode(',', $cids);
		$sql = "select * from #__digicom_categories where id in (" . $cid . ")";
		$database->setQuery($sql);
		$names = $database->loadObjectList();

		if ($task == 'publish') {
			$sql = "update #__digicom_categories set published='1' where id in ('".implode("','", $cids)."')";
		} else {
			$sql = "update #__digicom_categories set published='0' where id in ('".implode("','", $cids)."')";

		}
		//echo $sql; die;
		$db->setQuery($sql);
		if (!$db->query() ) {
			$this->setError($db->getError());
			return false;
		}

		return true;

	}

	function orderField( $uid, $inc ) {
		// Initialize variables
		$db		= JFactory::getDBO();
		$row	=& JTable::getInstance('Category','Table');
		$row->load( $uid );
		$row->move( $inc, '`parent_id` = '.$db->Quote($row->parent_id) );
		$msg = JText::_('CATEGORYORDERINGSUCCESS');
		return $msg;
	}

	
	function saveorder($pks = array(), $order = array()) {

		
		$table = $this->getTable("Category");
		$tableClassName = get_class($table);
		$contentType = new JUcmType;
		$type = $contentType->getTypeByTable($tableClassName);
		$tagsObserver = $table->getObserverOfClass('JTableObserverTags');
		$conditions = array();
		//print_r($pks);die;
		if (empty($pks))
		{
			return JError::raiseWarning(500, JText::_('COM_DIGICOM_ERROR_NO_ITEMS_SELECTED'));
		}
		print_r($pks,$order);die;
		return $table->saveorder($pks,$order);

		/*
		// Update ordering values
		foreach ($pks as $i => $pk)
		{
			$table->load((int) $pk);
			//echo $pk ;die;
			//echo $order[$i] ;die;
			//echo $table->lft ;die;
			if ($table->lft != $order[$i])
			{
				$table->lft = $order[$i];

				if ($type)
				{
					$this->createTagsHelper($tagsObserver, $type, $pk, $type->type_alias, $table);
				}

				if (!$table->store())
				{
					$this->setError($table->getError());

					return false;
				}
				//print_r($table->store());die;
				// Remember to reorder within position and client_id
				$condition = $this->getReorderConditions($table);
				$found = false;

				foreach ($conditions as $cond)
				{
					if ($cond[1] == $condition)
					{
						$found = true;
						break;
					}
				}

				if (!$found)
				{
					$key = $table->getKeyName();
					$conditions[] = array($table->$key, $condition);
				}
			}
		}

		// Execute reorder for each category.
		foreach ($conditions as $cond)
		{
			$table->load($cond[0]);
			$table->reorder($cond[1]);
		}
		
		// Clear the component's cache
		$this->cleanCache();
		
		return true;
		*/

	}

	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param   JTable  $table  A JTable object.
	 *
	 * @return  array  An array of conditions to add to ordering queries.
	 *
	 * @since   12.2
	 */
	protected function getReorderConditions($table)
	{
		return array();
	}

	

	/**
	 * Method override to check-in a record or an array of record
	 *
	 * @param   mixed  $pks  The ID of the primary key or an array of IDs
	 *
	 * @return  mixed  Boolean false if there is an error, otherwise the count of records checked in.
	 *
	 * @since   12.2
	 */
	public function checkin($pks = array())
	{
		$pks = (array) $pks;
		$table = $this->getTable('Category');
		$count = 0;

		if (empty($pks))
		{
			$pks = array((int) $this->getState($this->getName() . '.id'));
		}

		$user = JFactory::getUser();

		// Check in all items.
		foreach ($pks as $pk)
		{
			if ($table->load($pk))
			{
				if ($table->checked_out > 0)
				{
					// If there is no checked_out or checked_out_time field, just return true.
					if (!property_exists($table, 'checked_out') || !property_exists($table, 'checked_out_time'))
					{
						//ok;
					}

					// Check if this is the user having previously checked out the row.
					if ($table->checked_out > 0 && $table->checked_out != $user->get('id') && !$user->authorise('core.admin', 'com_checkin'))
					{
						$this->setError(JText::_('JLIB_APPLICATION_ERROR_CHECKIN_USER_MISMATCH'));

						return false;
					}

					// Attempt to check the row in.
					if (!$table->checkin($pk))
					{
						$this->setError($table->getError());

						return false;
					}

					$count++;
				}
			}
			else
			{
				$this->setError($table->getError());

				return false;
			}
		}

		return $count;
	}

	/**
	 * Method override to check-out a record.
	 *
	 * @param   integer  $pk  The ID of the primary key.
	 *
	 * @return  boolean  True if successful, false if an error occurs.
	 *
	 * @since   12.2
	 */
	public function checkout($pk = null)
	{
		$pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');

		return parent::checkout($pk);
	}

	function getConfigs() {
		$comInfo = JComponentHelper::getComponent('com_digicom');
		return $comInfo->params;
	}

	/**
	 * Method to get a form object.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 *
	 * @since	3.2
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$form = $this->loadForm('com_digicom.category', 'category', array('control' => 'jform', 'load_data' => $loadData));
		
		if (empty($form))
		{
			return false;
		}

		return $form;
	}

}
