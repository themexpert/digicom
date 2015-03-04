<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 398 $
 * @lastmodified	$LastChangedDate: 2013-11-04 05:07:10 +0100 (Mon, 04 Nov 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

defined ('_JEXEC') or die ("Go away.");

class DigiComAdminModelProducts extends JModelList {

	protected $_context = 'com_digicom.Product';
	var $_products;
	var $_product;
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
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'catid', 'a.catid', 'category_title',
				'access', 'a.access', 'access_level',
				'created', 'a.created',
				'created_by', 'a.created_by',
				'ordering', 'a.ordering',
				'featured', 'a.featured',
				'language', 'a.language',
				'hits', 'a.hits',
				'publish_up', 'a.publish_up',
				'publish_down', 'a.publish_down',
				'published', 'a.published',
				'author_id',
				'category_id',
				'level',
				'tag'
			);

			if (JLanguageAssociations::isEnabled())
			{
				$config['filter_fields'][] = 'association';
			}
		}
		
		parent::__construct($config);
		$cids = JRequest::getVar('cid', 0, '', 'array');
	 	$this->setId((int)$cids[0]);
		
		
		
	}

	function setId($id) {
		$this->_id = $id;
		$this->_product = null;
	}

	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();

		// Adjust the context to support modal layouts.
		if ($layout = $app->input->get('layout'))
		{
			$this->context .= '.' . $layout;
		}

		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$access = $this->getUserStateFromRequest($this->context . '.filter.access', 'filter_access');
		$this->setState('filter.access', $access);

		$authorId = $app->getUserStateFromRequest($this->context . '.filter.author_id', 'filter_author_id');
		$this->setState('filter.author_id', $authorId);

		$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$categoryId = $this->getUserStateFromRequest($this->context . '.filter.category_id', 'catid','');
		$this->setState('filter.category_id', $categoryId);

		$level = $this->getUserStateFromRequest($this->context . '.filter.level', 'filter_level');
		$this->setState('filter.level', $level);
		
		$limit = $this->getUserStateFromRequest($this->context . '.filter.limit', 'limit');
		$this->setState('filter.limit', $limit);

		$language = $this->getUserStateFromRequest($this->context . '.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);

		$tag = $this->getUserStateFromRequest($this->context . '.filter.tag', 'filter_tag', '');
		$this->setState('filter.tag', $tag);

		
		// List state information.
		parent::populateState();

		// Force a language
		$forcedLanguage = $app->input->get('forcedLanguage');

		if (!empty($forcedLanguage))
		{
			$this->setState('filter.language', $forcedLanguage);
			$this->setState('filter.forcedLanguage', $forcedLanguage);
		}
	}
	/*
	function getPagination(){
		$pagination=parent::getPagination();
		$pagination->total=$this->total;
		if($pagination->total%$pagination->limit>0){
			$nr_pages=intval($pagination->total/$pagination->limit)+1;
		}
		else{ 
			$nr_pages=intval($pagination->total/$pagination->limit);
		}
		$pagination->set('pages.total',$nr_pages);
		$pagination->set('pages.stop',$nr_pages);
		return $pagination;
	}
	*/
	function getCategories() {
		$db = JFactory::getDBO();
		$sql = "SELECT id,name FROM #__digicom_categories";
		$db->setQuery($sql);
		return $db->loadObjectList();
	}

	protected function getListQuery() {
		$input = JFactory::getApplication()->input;
        $db = JFactory::getDBO();
		$user = JFactory::getUser();
        //$catids = $input->get('catid','');
		
		//$session	= JFactory::getSession();
		//$category	= $session->get('dsproducategory', 0, 'digicom');
		//$prc		= JRequest::getVar("prc", $category, "request");
		//$search		= trim(JRequest::getVar("search", '', "post"));

		//$session_search = $session->get( 'digicom.product.search');
		//$state_filter	= JRequest::getVar("state_filter", '1');

        $query = $db->getQuery(true);
        $query->select(
			$this->getState(
				'list.select',
				'a.id, a.name, a.alias, a.checked_out, a.checked_out_time, a.catid' .
					', a.published, a.access, a.created, a.created_by, a.ordering, a.featured, a.hits' .
					', a.price, a.images, a.product_type, a.hide_public' .
					', a.publish_up, a.publish_down'
			)
		);
        $query->from('#__digicom_products a');
		
		// Join over the users for the checked out user.
		$query->select('uc.name AS editor')
			->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		// Join over the asset groups.
		$query->select('ag.title AS access_level')
			->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

		// Join over the categories.
		$query->select('c.name AS category_title')
			->join('LEFT', '#__digicom_categories AS c ON c.id = a.catid');

		// Join over the users for the author.
		$query->select('ua.name AS author_name')
			->join('LEFT', '#__users AS ua ON ua.id = a.created_by');
		
		/*
		if($prc > 0){
            $query->where($db->quoteName('catid') . " = '".$prc."'");
		}
		if($state_filter != "-1"){
            $query->where($db->quoteName('published') . " = '".$state_filter."'");
		}
		$session->set( 'digicom.product.search', $search );
		if (!empty($search)) {
            $query->where($db->quoteName('name') . " LIKE '%".$search."%'");
		}
		elseif (isset($_POST['submit_search'])) {
			$session->set('digicom.product.search', '');
		}
		elseif ($session_search) {
            $query->where($db->quoteName('name') . " LIKE '%".$session_search."%'");
		}
        $query->order($sort);
		//print_r($query->__toString());exit(''.__LINE__);
		*/
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
			$query->where('(a.published = 0 OR a.published = 1)');
		}

		// Filter by a single or group of categories.
		$baselevel = 1;
		$categoryId = $this->getState('filter.category_id');
		//echo ($categoryId);die;
		if (!empty($categoryId))
		{
			//JArrayHelper::toInteger($categoryId);
			//$categoryId = implode(',', $categoryId);
			$query->where('a.catid IN (' . $categoryId . ')');
		}
		
		// Filter on the level.
		if ($level = $this->getState('filter.level'))
		{
			$query->where('c.level <= ' . ((int) $level + (int) $baselevel - 1));
		}

		// Filter by author
		$authorId = $this->getState('filter.author_id');

		if (is_numeric($authorId))
		{
			$type = $this->getState('filter.author_id.include', true) ? '= ' : '<>';
			$query->where('a.created_by ' . $type . (int) $authorId);
		}

		// Filter by search in title.
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
				$query->where('(a.name LIKE ' . $search . ' OR a.alias LIKE ' . $search . ')');
			}
		}

		// Filter on the language.
		if ($language = $this->getState('filter.language'))
		{
			$query->where('a.language = ' . $db->quote($language));
		}

		// Filter by a single tag.
		/*
		$tagId = $this->getState('filter.tag');

		if (is_numeric($tagId))
		{
			$query->where($db->quoteName('tagmap.tag_id') . ' = ' . (int) $tagId)
				->join(
					'LEFT', $db->quoteName('#__contentitem_tag_map', 'tagmap')
					. ' ON ' . $db->quoteName('tagmap.content_item_id') . ' = ' . $db->quoteName('a.id')
					. ' AND ' . $db->quoteName('tagmap.type_alias') . ' = ' . $db->quote('com_content.article')
				);
		}
		*/
		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'a.id');
		$orderDirn = $this->state->get('list.direction', 'desc');

		if ($orderCol == 'a.ordering' || $orderCol == 'category_title')
		{
			$orderCol = 'c.name ' . $orderDirn . ', a.ordering';
		}

		if ($orderCol == 'access_level')
		{
			$orderCol = 'ag.title';
		}

		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}

	function getItems_x()
	{
		
		$config = JFactory::getConfig();
		$app	= JFactory::getApplication('administrator');
		$listOrder		=  $this->state->get('list.ordering', 'a.id');
		$listDirn		=  $this->state->get('list.direction', 'desc');
		$limistart	= $this->state->get('list.start', 'limitstart');
		$limit		= $this->state->get('list.limit', $config->get('list_limit'));
		
		echo// $limit;die;

		//$listOrder		= $app->getUserStateFromRequest('digicom.product.list.ordering','order','id','string');
		//$listDirn		= $app->getUserStateFromRequest('digicom.product.list.direction','order_Dir','desc','string');

		//$limistart	= $app->getUserStateFromRequest($this->context.'.list.start', 'limitstart');
		//$limit		= $app->getUserStateFromRequest($this->context.'.list.limit', 'limit', $config->get('list_limit'));

		$sort = $listOrder.' '.$listDirn;
		$db = JFactory::getDBO();

		//$query = $this->getListQuery($sort);
		$query = $this->getListQuery();
		$query = $this->getListQuery($sort);
		
		$db->setQuery($query);
		$db->query();
		$result	= $db->loadObjectList();
		$this->total=count($result);
		$db->setQuery($query, $limistart, $limit);
		$db->query();
		$result	= $db->loadObjectList();
		
		//print_r($result);die;
		
		foreach($result as $i => $v)
		{
			$sql = "SELECT id,name
					FROM #__digicom_categories
					WHERE id='".$v->catid."' and published=1";
			$db->setQuery($sql);
			$result[$i]->cats = $db->loadObjectList();
		}
		return $result;
	}

	/*function getListProducts ($sort = "ordering") {
		$session = JFactory::getSession();

		if (empty ($this->_products)) {
			$db = JFactory::getDBO();
			$prc = JRequest::getVar("prc", 0, "request");
			$search = trim(JRequest::getVar("search", '', "post"));
			$session_search = $session->get( 'digicom.product.search');
			$state_filter = JRequest::getVar("state_filter", '-1');

			$where = "WHERE 1=1 ";

			if($prc > 0){
				$where .= " and id IN (SELECT productid FROM #__digicom_product_categories WHERE catid='".$prc."' ) ";
			}
			if($state_filter != "-1"){
				$where .= " and published=".$state_filter;
			}
			$session->set( 'digicom.product.search', $search );
			if (!empty($search)) {
				$where .= " AND (name LIKE '%".$search."%') ";
			}
			elseif (isset($_POST['submit_search'])) {
				$session->set('digicom.product.search', '');
			}
			elseif ($session_search) {
				$where .= " AND (name LIKE '%".$session_search."%') ";
			}

			$sql = "SELECT * FROM #__digicom_products ".$where." ORDER BY ".$sort." ASC";
			$this->_total = $this->_getListCount($sql);

			if ($this->getState('limitstart') > $this->_total) $this->setState('limitstart', 0);
			if ($this->getState('limitstart') > 0 & $this->getState('limit') == 0)  $this->setState('limitstart', 0);
			if ($this->controller == "digicomProducts")
				$this->_products = $this->_getList($sql, $this->getState('limitstart'), $this->getState('limit'));
			else
				$this->_products = $this->_getList($sql);

			foreach ($this->_products as $i => $v) {
				$sql = "SELECT id,name FROM #__digicom_categories WHERE id in "
						." (SELECT catid FROM #__digicom_product_categories WHERE productid='".$v->id."') "
				;

				$db->setQuery($sql);
				$this->_products[$i]->cats = $db->loadObjectList();
			}

		}
		return $this->_products;
	}*/

	function getProduct($pid = 0) {
		if ($pid > 0 ) {
			$this->_id = $pid;
		}
		if (empty($this->_product)) {
			$this->_product = $this->getTable("Product");
			$this->_product->load($this->_id);
		}
		$db = JFactory::getDBO();
		
		if($this->_product->id){
			$filesTable = JTable::getInstance('Files', 'Table');
			$fileList = $filesTable->getList('product_id',$this->_product->id);
			$this->_product->file = $filesTable->getList('product_id',$this->_product->id);
			
			$filesTable = JTable::getInstance('Bundle', 'Table');
			$fileList = $filesTable->getList('product_id',$this->_product->id);
			$this->_product->bundle = $filesTable->getList('product_id',$this->_product->id);
			
		}
		return $this->_product;
	}

	function getFeatured() {
		$db = JFactory::getDBO();
		$sql = "SELECT p.* FROM #__digicom_products p WHERE p.featured = 1";
		$db->setQuery($sql);
		$featured_products = $db->loadObjectList();
		return $featured_products;
	}

	function _storeFile($file, $pid){
		jimport('joomla.filesystem.folder');
		$maindir = JPATH_ROOT.DS."administrator".DS."components".DS."digicom_product_uploads".DS;
		$tmpdir = $maindir."tmp".DS.$pid.DS;
		$resdir = $maindir.$pid.DS;

		DigiComAdminHelper::CreateIndexFile($maindir);
		DigiComAdminHelper::CreateIndexFile($resdir);
		$resdir_orig = $resdir."original".DS;
		DigiComAdminHelper::CreateIndexFile($resdir_orig);
		$resdir_encoded = $resdir."encoded".DS;
		DigiComAdminHelper::CreateIndexFile($resdir_encoded);

		$filename = $this->_getFilename ($file);
		$delete_file = JRequest::getVar('delete_file', 0, 'post');
		if($delete_file){
			if(file_exists($resdir)){
				JFolder::delete ($resdir);
			}

			if(file_exists($tmpdir)){
				JFolder::delete ($tmpdir);
			}

			if(file_exists($tmpdir)){
				JFolder::delete ($tmpdir);
			}
			JFolder::create($tmpdir);
			JFolder::create($tmpdir.DS."1".DS);
		}
		if(!file_exists($resdir_orig)){
			JFolder::create($resdir_orig);
		}

		if(!file_exists($file)){
			return true;
		}

		if(file_exists($file) && !JFile::copy ($file, $resdir_orig.$filename)){
			return false;
		}

		return true;
	}

	function _getFile ($pid = 0) {
		
		jimport('joomla.filesystem.file');
		$fullpath = '';

		$file = JRequest::getVar("ftpfile", '', "post");
		$c = $this->getInstance("Config", "DigiComAdminModel");
		$configs = $c->getConfigs();

		if( trim( strlen($file) ) > 0 ) {
			$fullpath = JPATH_SITE.DS.$configs->get('ftp_source_path','DigiCom').DS.$file;
		} else {
			$file = JRequest::getVar("file", array(), "files", "array");
			$fullpath = JPATH_ROOT.DS."tmp".DS.$file['name'];

			if(file_exists($file['tmp_name']) && JFile::copy($file['tmp_name'], $fullpath)){

			}
			elseif($pid > 0){
				$db = JFactory::getDBO();
				$sql = "select file from #__digicom_products where id='".$pid."'";
				$db->setQuery($sql);
				$file = $db->loadResult();
				$fullpath = JPATH_ROOT.DS."administrator".DS."components".DS."digicom_product_uploads".DS.$pid.DS.$file;
			}
			else{
				$fullpath = '';
			}
		}
		return $fullpath;
	}

	function _getFilename ($file) {
		$filename = explode(DS, $file);
		$filename = $filename[count($filename) - 1];
		return $filename;

	}

	function getImagesIds($product_id){
		$db = JFactory::getDBO();
		$sql = "select id from #__digicom_products_images where product_id=".intval($product_id);
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadColumn();
		return $result;
	}

	function existImage($path){
		$db = JFactory::getDBO();
		$sql = "select count(*) from #__digicom_products_images where `path`='".trim($path)."'";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadResult();
		if($result > 0){
			return true;
		}
		return false;
	}

	function store(){
		$task = JRequest::getVar('task');
		$item = $this->getTable('Product');
		$return = array();
		$db = JFactory::getDBO();
		$jconf = JFactory::getConfig();
		$dbprefix = $jconf->get('dbprefix');//$jconf->_registry['config']['data']->dbprefix;
		//file processing
		$data = JFactory::getApplication()->input->get('jform', array(), 'ARRAY');
		//print_r($data);die;
		$conf = $this->getInstance ("Config", "DigiComAdminModel");
		$configs = $conf->getConfigs();
		
		if(!$item->bind($data)){
		   	$this->setError($item->getError());
			$return["return"] = false;
			$return["id"] = "";
			return $return;
		}
		//print_r($item);die;
		if (!$item->check()) {
			//echo 'failed in checcking';die;
			$this->setError($item->getError());
			$return["return"] = false;
			$return["id"] = "";
			return $return;
		}
		
		if (!$item->store()){
			return false;
		}else{
			//hook the files here
			//-------------------------------------
            if (isset($data['file']) && is_array($data['file']))
            {
                $files = $data['file'];
                foreach($files as $key => $file){
                    $filesTable = $this->getTable('Files');
                    $filesTable->product_id = $item->id;
                    $filesTable->name = ($file['name'] ? $file['name'] : $file['url']);
                    $filesTable->url = $file['url'];
                    $filesTable->store();
                }
                if (isset($data['files_remove_id']) && !empty($data['files_remove_id'])){
                    $filesTable = JTable::getInstance('Files', 'Table');
                    $filesTable->removeUnmatch($data['files_remove_id'],$item->id);
                }
            }
			
			// hook bundle item
			//print_r($data['bundle']);die;
			if (isset($data['bundle']) && is_array($data['bundle'])){
				$filesTable = JTable::getInstance('Bundle', 'Table');
				$filesTable->removeSameTypes('product',$item->id);
				$filesTable->removeSameTypes('category',$item->id);
			}
			
			if (isset($data['bundle']['product']) && is_array($data['bundle']['product']))
            {
				$products_bundle = $data['bundle']['product'];
                foreach($products_bundle as $key => $bundle){
					
                    $filesTable = $this->getTable('Bundle');
                    $filesTable->product_id = $item->id;
                    $filesTable->bundle_id = $bundle;
                    $filesTable->bundle_type = 'product';
                    $filesTable->store();
                }
            }
			
			if (isset($data['bundle']['category']) && is_array($data['bundle']['category']))
            {
				$category_bundle = $data['bundle']['category'];
                foreach($category_bundle as $key2 => $bundle2){
					
                    $filesTable = $this->getTable('Bundle');
                    $filesTable->product_id = $item->id;
                    $filesTable->bundle_id = $bundle2;
                    $filesTable->bundle_type = 'category';
                    $filesTable->store();
                }
            }
			/*
			$bundle_remove_id = JFactory::getApplication()->input->get('bundle_remove_id','');
			if (isset($bundle_remove_id) && !empty($bundle_remove_id)){
				$filesTable = JTable::getInstance('Bundle', 'Table');
				$filesTable->removeUnmatch($bundle_remove_id,$item->id);
			}
			*/
            
		}
		
		if(!$item->id) {
			$query = "show table status";
			$db->setQuery($query);
			$suggested_id = $db->loadObjectList();
			foreach ($suggested_id as $res) {
				if ($res->Name == $dbprefix.'_digicom_products') {
					$pid = $res->Auto_increment;
					break;
				}
			}
		}
		else {
			$pid = $item->id;
		}
		
		$product_id = $pid;
		$return["return"] = true;
		$return["id"] = $product_id;
		// trigger plugin evens
		$jv = new JVersion();
		$isJ25 = $jv->RELEASE == '2.5';
		JPluginHelper::importPlugin('digicom');
		if($isJ25){
			$dispatcher = JDispatcher::getInstance();
		} else {
			$dispatcher	= JEventDispatcher::getInstance();
		}
		$dispatcher->trigger( 'onAfterDigiComProductSave', array( 'com_digicom.product_save' , $product_id, &$return ) );
		return $return;
	}

	function moveImageDown(){
		$db = JFactory::getDBO();
		$forchange = JRequest::getVar("forchange", "");
		$order = JRequest::getVar("order", array(), "array");
		$images_ids = JRequest::getVar("images_ids", array(), "array");
		$order1 = ""; //for find element;
		$order2 = ""; // for forchange element
		$find_id = "0";
		if(isset($images_ids) && count($images_ids) > 0){
			foreach($images_ids as $key=>$value){
				if($value == $forchange){
					$order1 = $order[$key];
					$order2 = $order[$key+1];
					$find_id = $images_ids[$key+1];
				}
			}
		}
		$sql = "update #__digicom_products_images set `order`=".intval($order2)." where id=".intval($forchange);
		$db->setQuery($sql);
		if($db->query()){
			$sql = "update #__digicom_products_images set `order`=".intval($order1)." where id=".intval($find_id);
			$db->setQuery($sql);
			$db->query();
		}
	}

	function moveImageUp(){
		$db = JFactory::getDBO();
		$forchange = JRequest::getVar("forchange", "");
		$order = JRequest::getVar("order", array(), "array");
		$images_ids = JRequest::getVar("images_ids", array(), "array");
		$order1 = ""; //for find element;
		$order2 = ""; // for forchange element
		$find_id = "0";
		if(isset($images_ids) && count($images_ids) > 0){
			foreach($images_ids as $key=>$value){
				if($value == $forchange){
					$order1 = $order[$key];
					$order2 = $order[$key-1];
					$find_id = $images_ids[$key-1];
				}
			}
		}
		$sql = "update #__digicom_products_images set `order`=".intval($order2)." where id=".intval($forchange);
		$db->setQuery($sql);
		if($db->query()){
			$sql = "update #__digicom_products_images set `order`=".intval($order1)." where id=".intval($find_id);
			$db->setQuery($sql);
			$db->query();
		}
	}

	function delete () {
		$cids = JRequest::getVar('cid', array(0), 'post', 'array');
		$db = JFactory::getDBO();
		$success = true;
		foreach ($cids as $cid) {
			$notExist = $this->checkOrderExist($cid);
			if($notExist){
				$sql = "DELETE FROM #__digicom_products WHERE id=".intval($cid);
				$db->setQuery($sql);
				$db->query();

				$sql = "DELETE FROM #__digicom_products_files WHERE product_id=".intval($cid);
				$db->setQuery($sql);
				$db->query();

				$sql = "DELETE FROM #__digicom_products_bundle WHERE product_id=".intval($cid);
				$db->setQuery($sql);
				$db->query();
			}else{
				$success = false;
			}
		}

		return $success;
	}
	
	function checkOrderExist($cid){
		$app = JFactory::getApplication();

		$db = JFactory::getDBO();
		$sql = "SELECT id FROM #__digicom_orders_details WHERE productid='".intval($cid)."' limit 1";
		$db->setQuery($sql);
		$od = $db->loadObject();
		if($od->id > 0){
			$app->enqueueMessage(JText::sprintf('COM_DIGICOM_PRODUCT_EXIST_IN_ORDER',$cid), 'warning');
			return false;
		}else{
			return true;
		}
	}
	
	function publish () {
		$db = JFactory::getDBO();
		$cids = JRequest::getVar('cid', array(0), 'post', 'array');
		$task = JRequest::getVar('task', '', 'post');
		$item = $this->getTable('Product');
		$res = 0;
		if ($task == 'publish') {
			$res = 1;
			$sql = "update #__digicom_products set published='1' where id in ('".implode("','", $cids)."')";
		} else {
			$res = -1;
			$sql = "update #__digicom_products set published='0' where id in ('".implode("','", $cids)."')";

		}
//die ($sql);
		$db->setQuery($sql);
		if (!$db->query() ) {
			$this->setError($db->getErrorMsg());
//			return false;
		}
		return $res;
	}

	function featured () {
		$db = JFactory::getDBO();
		$cids = JRequest::getVar('cid', array(0), 'post', 'array');
		$task = JRequest::getVar('task', '', 'post');
		$item = $this->getTable('Product');
		$res = 0;
		if ($task == 'featured') {
			$res = 1;
			$sql = "update #__digicom_products set featured='1' where id in ('".implode("','", $cids)."')";
		} else {
			$res = -1;
			$sql = "update #__digicom_products set featured='0' where id in ('".implode("','", $cids)."')";

		}
		$db->setQuery($sql);
		if (!$db->query() ) {
			$this->setError($db->getErrorMsg());
//			return false;
		}
		return $res;
	}



	function shiftorder($direction = 1) {
		$db = JFactory::getDBO();
		$cids = JRequest::getVar('cid', array(0), 'post', 'array');
		$order = JRequest::getVar('order', array(0), 'post', 'array');
		$sql = "update #__digicom_products set `ordering`=ordering".($direction == 1?"+1":"-1")." where id=".$cids[0];
		$db->setQuery($sql);
		$res = $db->query();
		if (!$res) return $res;
		return true;
	}

	function reorder () {
		$db = JFactory::getDBO();
		$cids = JRequest::getVar('cid', array(0), 'post', 'array');
		$order = JRequest::getVar('order', array(0), 'post', 'array');
		foreach ($cids as $i => $v) {
			$sql = "update #__digicom_products set `ordering`='".$order[$i]."' where id=".$v;
			$db->setQuery($sql);
			$res = $db->query();
			if (!$res) return $res;
		}
		/* */
		$table = $this->getTable();
		$table->reorder();
		/* */
		return true;
	}

	/* NEW function */
	function orderField( $uid, $inc )
	{
		// Initialize variables
		//$db		= JFactory::getDBO();
		$row	= $this->getTable();
		$row->load( $uid );
		$row->move( $inc ); // , '`group` = '.$db->Quote($row->group)
		$row->reorder();

		return true;
	}

	function saveorder() {

		// Initialize variables
		$db			= JFactory::getDBO();

		$cid		= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$order		= JRequest::getVar( 'order', array (0), 'post', 'array' );
		$total		= count($cid);
		$conditions	= array();


		JArrayHelper::toInteger($cid, array(0));
		JArrayHelper::toInteger($order, array(0));

		$row = $this->getTable();
		// update ordering values
		for( $i=0; $i < $total; $i++ ) {
			$row->load( (int) $cid[$i] );
			// track sections
			//$groupings[] = $row->group;
			if ($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				if (!$row->store()) {
					JError::raiseError(500, $db->getErrorMsg());
				}
			}
		}

		$row->reorder();
		/*
		// execute updateOrder for each parent group
		$groupings = array_unique( $groupings );
		foreach ($groupings as $group){
		//	echo 'group = '.$db->Quote($group)."<br/>";
			$row->reorder('`group` = '.$db->Quote($group));
		}
		*/
		$msg = JText::_('New ordering saved');
	}

	/* /END NEW function */
	function getlistProductTaxClasses () {
		$sql = "select * from #__digicom_tax_productclass order by ordering asc";
		$db = JFactory::getDBO();
		$db->setQuery($sql);
		return ($db->loadObjectList());
	}

	function getlistProductClasses () {
		$sql = "select * from #__digicom_productclass order by ordering asc";
		$db = JFactory::getDBO();
		$db->setQuery($sql);
		return ($db->loadObjectList());
	}

	function deleteImages(){
		$db = JFactory::getDBO();
		$task = JRequest::getVar("task", "");
		if($task == "delete_selected"){
			$selected_image = JRequest::getVar("selected_image", array(), "array");
			$product_id = JRequest::getVar("id", "0");
			if(isset($selected_image) && count($selected_image) > 0){
				foreach($selected_image as $key=>$path){
					$sql = "delete from #__digicom_products_images where `product_id`=".intval($product_id)." and `path`='".trim($path)."'";
					$db->setQuery($sql);
					$db->query();
				}
			}
		}
		elseif($task == "delete_all"){
			$product_id = JRequest::getVar("id", "0");
			$sql = "delete from #__digicom_products_images where `product_id`=".intval($product_id);
			$db->setQuery($sql);
			$db->query();
		}
		return true;
	}

	function copyProduct($ids=array()){
		
		if(!$ids||!count($ids)){
			$ids = JRequest::getVar("cid", array(), "array");
		}
        //print_r($ids);die;
		if(isset($ids) && count($ids) > 0){
			$db = JFactory::getDBO();
			foreach($ids as $key_id=>$id){
                $table  = $this->getTable("product");
				$table->load($id);
				//Set id empty, so we can store as new product
				$table->id = '';
				
				list($name, $alias) = $this->generateNewTitle($table->catid, $table->alias, $table->name);
                $table->name = $name;
				$table->alias = $alias;

				$table->store();
				$new_id = $table->id;
				
				$sql = "select * from #__digicom_products_files where `product_id`='".intval($id)."'";
				$db->setQuery($sql);
				$db->query();
				$renewals = $db->loadAssocList();
				if(isset($renewals) && count($renewals) > 0){
					foreach($renewals as $key=>$value){
						$sql = "insert into #__digicom_products_files (`product_id`, `name`, `url`, `creation_date`,`hits`) 
						values ('".intval($new_id)."', '".$value["name"]."', '".$value["url"]."', '".time()."','0')";
						$db->setQuery($sql);
						if(!$db->query()){
							return false;
						}
					}
				}
				
				$sql = "select * from #__digicom_products_bundle where product_id=".intval($id);
				$db->setQuery($sql);
				$db->query();
				$renewals = $db->loadAssocList();
				if(isset($renewals) && count($renewals) > 0){
					foreach($renewals as $key=>$value){
						$sql = "insert into #__digicom_products_bundle (`product_id`, `bundle_id`, `bundle_type`) 
						values ('".intval($new_id)."', '".$value["bundle_id"]."', '".$value["bundle_type"]."')";
						$db->setQuery($sql);
						if(!$db->query()){
							return false;
						}
					}
				}

			}

		}

		return true;
	}

	function getlistCategories(){
		$db = JFactory::getDBO();
		$sql = "select * from #__digicom_categories order by parent_id, ordering asc";
		$db->setQuery($sql);
		$result = $db->loadObjectList();
		return $result;
	}

	function getlistPlains ($pack = false) {	 
		if (empty ($this->_plains)) {

			$c = $this->getInstance("Config", "DigiComAdminModel");
			$configs = $c->getConfigs();

			$db = JFactory::getDBO();

			if ($pack) { 
				$sql = "SELECT p.* FROM #__digicom_plans p 
					WHERE p.duration_count = '-1' AND p.duration_type = '0'
					ORDER BY p.ordering ";
					//echo $sql; die();
			} else {
				$sql = "SELECT p.* FROM #__digicom_plans p "
					//." where u.id=o.userid and c.id=u.id "
					." ORDER BY p.ordering"
					;
					//echo $sql;
			}

			$this->_total = $this->_getListCount($sql);
			if ($this->getState('limitstart') > $this->_total) $this->setState('limitstart', 0);
			if ($this->getState('limitstart') > 0 & $this->getState('limit') == 0)  $this->setState('limitstart', 0);

			$this->_plains = $this->_getList($sql, $this->getState('limitstart'), $this->getState('limit'));

		}

		return $this->_plains;
	}

    protected function generateNewTitle($category_id, $alias, $title)
    {
        // Alter the title & alias
        $table = JTable::getInstance('Product', 'Table');
        $table->load(array('alias' => $alias, 'catid' => $category_id));
        if($table->id > 0)
        {
            $title = JString::increment($title);
            $alias = JString::increment($alias, 'dash');
        }
        return array($title, $alias);
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
		$form = $this->loadForm('com_digicom.product', 'product', array('control' => 'jform', 'load_data' => $loadData));
		
		if (empty($form))
		{
			return false;
		}

		return $form;
	}
	
	public function getfilterForm($data = array(), $loadData = true)
	{
		$form = $this->loadForm('com_digicom.filter_product', 'filter_product', array('control' => '', 'load_data' => $loadData));
		
		if (empty($form))
		{
			return false;
		}
		
		return $form;
	}
}
