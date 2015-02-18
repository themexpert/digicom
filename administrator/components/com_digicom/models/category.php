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

class DigiComAdminModelCategory extends JModelForm {
	
	protected $_context = 'com_digicom.Category';
	private $total=0;
	var $_categories;
	var $_category;
	var $_id = null;
	var $_total = 0;
	var $_pagination = null;

	function __construct(){
		parent::__construct();
		$cids = JRequest::getVar('cid', 0, '', 'array');
		$this->setId((int)$cids[0]);
	}

	function populateState($ordering = NULL, $direction = NULL){
		$app = JFactory::getApplication('administrator');
		$this->setState('list.start', $app->getUserStateFromRequest($this->_context . '.list.start', 'limitstart', 0, 'int'));
		$this->setState('list.limit', $app->getUserStateFromRequest($this->_context . '.list.limit', 'limit', $app->getCfg('list_limit', 25) , 'int'));
		$this->setState('selected', JRequest::getVar('cid', array()));
	}

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

	function setId($id) {
		$this->_id = $id;
		$this->_category = null;
	}

	protected function getListQuery() {
		$db = JFactory::getDBO();
		$where = "1=1";

		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__digicom_categories');
		$query->order("parent_id, ordering asc");
		$query->where($where);
		return $query;
	}

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
		$data = JRequest::get('post');

		if(!$item->bind($data)){
			$this->setError($item->getErrorMsg());
			return false;
		}

		if(!$item->check()){
			$this->setError($item->getErrorMsg());
			return false;
		}

		$item_old = $this->getTable('Category');
		if($item->id > 0){
			$item_old->load($item->id);
		}

		if(!$item->store()){
			$this->setError($item->getErrorMsg());
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
