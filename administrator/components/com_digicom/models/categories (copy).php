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
				'level', 'a.level',
				'parent_id', 'a.parent_id',
				'tag'
			);
		}
		
		parent::__construct($config);
		$cids = JRequest::getVar('cid', 0, '', 'array');
	 	$this->setId((int)$cids[0]);
		
	}

	function populateState($ordering = NULL, $direction = NULL){
		$app = JFactory::getApplication('administrator');
		$this->setState('list.start', $app->getUserStateFromRequest($this->_context . '.list.start', 'limitstart', 0, 'int'));
		$this->setState('list.limit', $app->getUserStateFromRequest($this->_context . '.list.limit', 'limit', $app->getCfg('list_limit', 25) , 'int'));
		$this->setState('selected', JRequest::getVar('cid', array()));

		$app = JFactory::getApplication();
		$context = $this->_context;

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
		parent::populateState('a.ordering', 'asc');

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

	protected function getListQuery() {
		/*
		$db = JFactory::getDBO();
		$where = "1=1";

		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__digicom_categories');
		$query->order("parent_id, ordering asc");
		$query->where($where);
		return $query;
		*/
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.id, a.name, a.alias, a.published, a.access,a.image' .
				', a.checked_out, a.checked_out_time, a.created_user_id' .
				', a.parent_id, a.level, a.ordering' .
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
		$listOrdering = $this->getState('list.ordering', 'a.ordering');
		$listDirn = $db->escape($this->getState('list.direction', 'ASC'));

		if ($listOrdering == 'a.access')
		{
			$query->order('a.access ' . $listDirn . ', a.ordering ' . $listDirn);
		}
		else
		{
			$query->order($db->escape($listOrdering) . ' ' . $listDirn);
		}

		return $query;
	}
	/*
	function getItems(){
		jimport('joomla.html.html.menu');
		$config = JFactory::getConfig();
		$app = JFactory::getApplication('administrator');
		$limistart = $app->getUserStateFromRequest($this->context.'.list.start', 'limitstart');
		$limit = $app->getUserStateFromRequest($this->context.'.list.limit', 'limit', $config->get('list_limit'));
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query = $this->getListQuery();

		$db->setQuery($query);
		$db->query();
		$result	= $db->loadObjectList();
		$this->total=count($result);

		$children = array();
		$citems = $result;

		if($citems){
			foreach($citems as $v){
				$pt 	= $v->parent_id;
				$list 	= @$children[$pt] ? $children[$pt] : array();
				array_push($list, $v);
				$children[$pt] = $list;
			}
		}
		
		foreach($children as $i => $v){
			foreach($children[$i] as $j => $vv){
				$children[$i][$j]->parent = $vv->parent_id;
				$children[$i][$j]->title = $vv->name;
			}
		}		
		
		$lists = JHTML::_('menu.treerecurse', 0, "", array(), $children, 20, 0, 0);
		$categories = $lists;
		if($limit != "0"){
			$categories = array_slice($categories, $limistart, $limit);
		}
		return $categories;
	}
	*/
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

			$sql = "select name from #__digicom_categories where id in (" . $cid . ")";
			$database->setQuery($sql);
			$names = $database->loadObjectList();
			$n = array ();
			foreach ($names as $name)
				$n[] = $name->name;
			$sql = "delete from #__menu where title in ('" . implode("','", $n) . "') and menutype='digicats'";
			$database->setQuery($sql);
			$database->query();

			$query = "DELETE FROM #__digicom_categories" . "\n WHERE id IN ( $cid )";
			$database->setQuery($query);
			if (!$database->query()) {
				echo "<script> alert('" . $database->getErrorMsg() . "'); window.history.go(-1); </script>\n";
			}
		}


		$item = $this->getTable('Category');
		foreach ($cids as $cid) {
			if (!$item->delete($cid)) {
				$this->setError($item->getErrorMsg());
				return false;

			}
		}
		$cid = implode(',', $cids);
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
			$this->setError($db->getErrorMsg());
			return false;
		}

		//print_r($names);die;
		/*
		if ($task == "publish") {
			foreach ($names as $i => $v) {

				$parent_link = 'index.php?option=com_digicom&controller=products&task=list&cid[]=' . $v->parent_id;
				$sql = "select id from #__menu where menutype='digicats' and link='" . $parent_link . "'";
				$database->setQuery($sql);
				$parent_id = $database->loadResult();

				$link = 'index.php?option=com_digicom&controller=products&task=list&cid[]=' . $v->id;
				$link2 = 'index.php?option=com_digicom&controller=categories&task=view&cid[]=' . $v->id;

				$sql = "select count(*) from #__digicom_categories where parent_id='" . $v->id . "' and published='1'";
				$database->setQuery($sql);
				$sibs = $database->loadResult();
				if ($sibs > 0) {
					//		$link = $link2;
					$sql = "update #__menu set published='1', link='".$link2."' where menutype='digicats' and name='" . $v->name . "' and (link='" . $link . "' or link='" . $link2 . "')";
				} else {
					$sql = "update #__menu set published='1' where menutype='digicats' and name='" . $v->name . "' and (link='" . $link . "' or link='" . $link2 . "')";
				}

				//echo $sql;
				$database->setQuery($sql);
				$database->query();
			}

		} else {
			foreach ($names as $i => $v) {
				$link = 'index.php?option=com_digicom&controller=products&task=list&cid[]=' . $v->id;
				$link2 = 'index.php?option=com_digicom&controller=categories&task=view&cid[]=' . $v->id;
				$sql = "update #__menu set published='0' where menutype='digicats' and name='" . $v->name . "' and (link='" . $link . "' or link='" . $link2 . "')";
				$database->setQuery($sql);
				$database->query();

			}
		}
		//			   die;
		*/

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


	function saveorder($pcid = 'params', $porder = 'params') {

		// Initialize variables
		$db			= JFactory::getDBO();


		if ( ($pcid != 'params') && ($porder != 'params')) {
			$cid		= $pcid;
			$order		= $porder;
		} else {
			$cid		= JRequest::getVar( 'cid', array(0), 'post', 'array' );
			$order		= JRequest::getVar( 'order', array (0), 'post', 'array' );
		}

		$total		= count($cid);
		$conditions	= array ();

		//debug($cid); debug($order); die();

		JArrayHelper::toInteger($cid, array(0));
		JArrayHelper::toInteger($order, array(0));

		$row = & JTable::getInstance('Category','Table');
		// update ordering values
		for( $i=0; $i < $total; $i++ ) {
			$row->load( (int) $cid[$i] );
			// track sections
			$groupings[] = $row->parent_id;
			if ($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				if (!$row->store()) {
					$msg = JText::_('CATEGORYORDERINGERROR');
					JError::raiseError(500, $db->getErrorMsg());
				}
			}
		}

		// execute updateOrder for each parent group
		$groupings = array_unique( $groupings );

		foreach ($groupings as $group) {
			$row->reorder('`parent_id` = '.$db->Quote($group));
		}

		$msg = JText::_('CATEGORYORDERINGSUCCESS');
		return $msg;
	}

	function getCatAndProductLisenceId( $id, $indent, $list, &$children, $prod, &$html, $selected, $level=0, $type=1) {

		if (@$children[$id]) {

			foreach ($children[$id] as $v) {

				$id = $v->id;

				if ( $type ) {
					$pre	 = '|_ &nbsp;';
					$spacer = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
				} else {
					$pre	 = '- ';
					$spacer = '&nbsp;&nbsp;';
				}

				if ( $v->parent == 0 ) {
					$txt	 = $v->name;
				} else {
					$txt	 = $pre . $v->name;
				}

				$pt = $v->parent;
				$list[$id] = $v;
				$list[$id]->treename = "$indent$txt";
				$list[$id]->prods = (isset($prod[$id])) ? $prod[$id] : array();
				$list[$id]->children = count( @$children[$id] );

				$html .= "<OPTGROUP LABEL='".$list[$id]->treename."'>\n";
				if (isset($prod[$id])) {
					foreach($prod[$id] as $key => $proditem) {
						if ($selected != $key)
							$html .= "<OPTION VALUE='".$key."'>".$indent.$proditem."</OPTION>";
						else
							$html .= "<OPTION SELECTED='SELECTED' VALUE='".$key."'>".$indent.$proditem."</OPTION>";
					}
				}
				$list = $this->getCatAndProductLisenceId( $id, $indent . $spacer, $list, $children, $prod, $html, $selected, $level+1, $type);
				$html .= "</OPTGROUP>\n";
			}
		}
		return $list;
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
