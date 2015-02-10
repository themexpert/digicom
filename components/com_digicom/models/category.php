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

jimport ("joomla.aplication.component.model");

class DigiComModelCategory extends DigiComModel
{

	var $_categories;
	var $_category;
	var $_id = null;
	var $_total = 0;
	var $_pagination = null;
	var $_categorycats = null;
	var $_categoryprods = null;

	function __construct () {
		parent::__construct();
		$cids = JRequest::getVar('cid', 0, '', 'array');
		if (is_array($cids)) $cid = intval($cids[0]); else $cid = intval($cids);
		$this->setId((int)$cid);
		global $mainframe, $option;
		$configs = $this->getInstance("Config", "digicomModel");
		$configs = $configs->getConfigs();
		// Get the pagination request variables
		$limit = $configs->get('catlayoutcol',3) * $configs->get('catlayoutrow',3) ;//$mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart = JRequest::getVar("limitstart", 0, "request");//$mainframe->getUserStateFromRequest( $option.'limitstart', 'limitstart', 0, 'int' );

		$this->setState('limit', $limit); // Set the limit variable for query later on
		$this->setState('limitstart', $limitstart);
	}


	function setId($id) {
		$this->_id = $id;
		$this->_category = null;
	}

	function getPagination($catid = 0) {
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))	{
			jimport('joomla.html.pagination');
			if (!$this->_total) {
				if ($catid) {
					$this->getCategoryCategories();
				} else {
					$this->getlistCategories();
				}
			}
			$this->_pagination = new JPagination( $this->_total, $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}

	function getlistCategories(){
		$where = array();

		if(empty ($this->_categories)){
			$user = JFactory::getUser();
			$where[] = " c.published=1 ";
			//$where[] = " c.access <= ".$user->gid;
			$where[] = " c.parent_id=0 ";
			$where[] = "(
							(select count(p.id)
							 from #__digicom_products p
							 where p.hide_public = 0
							   and p.id IN (SELECT productid
											FROM #__digicom_product_categories
											WHERE catid = c.id)) > 0
							or
							(select count(cc.id)
							 from #__digicom_categories cc
							 where cc.parent_id=c.id and cc.published=1) > 0
						) ";

			$sql = "select c.* from #__digicom_categories c ".(count($where) > 0? " where ": "") . implode (" and ", $where);
			$order = " order by ordering asc ";
			$this->_total = @$this->_getListCount($sql);
			$this->_categories = $this->_getList($sql.$order, $this->getState('limitstart'), $this->getState('limit'));
		}
		//move images---------------------------------------
		if(isset($this->_categories) && count($this->_categories) > 0){
			$db = JFactory::getDBO();
			foreach($this->_categories as $key=>$category){
				if(trim($category->images) != ""){
					$category->images = str_replace("/", DS, trim($category->images));
					$category->images = str_replace("\\", DS, trim($category->images));
					$images = explode(DS, $category->images);
					$images = $images[count($images)-1];
					copy(JPATH_SITE.$category->images, JPATH_SITE.DS."images".DS."stories".DS."digicom".DS."categories".DS.$images);
					$this->_categories[$key]->image = $images;
					$this->_categories[$key]->images = "";
					$sql = "update #__digicom_categories set `image`='".trim($images)."', `images` = '' where id=".intval($category->id);
					$db->setQuery($sql);
					$db->query();
				}
			}
		}
		//move images---------------------------------------
		return $this->_categories;
	}


	function getCategory() {
		if (empty ($this->_category)) {
			$this->_category = $this->getTable("Category");
			$this->_category->load($this->_id);
		}
		return $this->_category;
	}


	function getCategoryProducts($id = 0) {

		$my = JFactory::getUser();

		if (empty ($this->_category) && !$id) {
			$this->getCategory();
		}

		if (!empty($this->_category) && !$this->_category->id && !$id) return null;

		if (!$id) $id = $this->_category->id;

		$where[] = " published=1 ";
		//$where[] = " access<=".$my->gid." ";

		$sql = "select id
				from #__digicom_products
				where id in (select productid from #__digicom_product_categories where catid='".intval($id)."')
				  and hide_public=0".
			(count($where) > 0? " and ": "") .
			implode (" and ", $where);
		;
		$this->_total = @$this->_getListCount($sql);

		$order = " order by ordering asc ";

		$products = $this->_getList($sql.$order);
		$this->_categoryprods = $products;
		return $this->_categoryprods;
	}

	function __showCategories(& $cats) {

		$output = '';
		foreach($cats as $cat) {
			if ( $cat->parent_id == 0 ) {
				$output .= $this->__showCategoriesItem(0, $cat, $cats ,$output);
			}
		}

		return $output;
	}

	function getConfigs(){
		$comInfo = JComponentHelper::getComponent('com_digicom');
		return $comInfo->params;
	}

	function __showCategoriesItem($level, $cat, & $cats, & $output)
	{
		$configs = $this->getConfigs();
		$Itemid = JRequest::getVar("Itemid", "0");

		$output .= "<ul class='level".$level."'>";
		$output .= "<li>";

		if ( $cat->catcount > 0 ) {
			$catlink = JRoute::_("index.php?option=com_digicom&controller=categories&task=view&cid=".$cat->id."&Itemid=".$Itemid);
		} else {
			$catlink = JRoute::_("index.php?option=com_digicom&controller=products&task=list&cid=".$cat->id."&Itemid=".$Itemid);
		}

		$output .= "<a href='".$catlink."'>" . $cat->name . "</a> (" . $cat->catcount . " categories / ".$cat->prodcount." products )" ;

		foreach($cats as $tcat) {
			if ($tcat->parent_id == $cat->id) {
				$output .= $this->__showCategoriesItem( $level + 1, $tcat, $cats, $output );
			}
		}

		$output .= "</li>";
		$output .= "</ul>";
	}


	function getCategoriesTree() {

		$user = JFactory::getUser();

		$where[] = " c.published=1 ";
		//$where[] = " c.access <= ".$user->gid;


		$sql ="select c.id, c.name, c.parent_id
				from #__digicom_categories c
				where " . implode(" and ", $where) . "
				order by c.ordering asc";
		$this->_db->setQuery($sql);
		$cats = $this->_db->loadObjectList();
		$this->getCategoriesTreeIterator( 0, 0, $cats ); 
		return $this->__showCategories( $cats );
	}


	function getCategoriesTreeIterator( $id,  $level, & $cats ) {

		foreach( $cats as $key => $tcat ) {

			$cat = $cats[$key];

			if ($tcat->parent_id == $id) {

				$sql = "select count(*) from #__digicom_categories c where parent_id=".$tcat->id;
				$this->_db->setQuery($sql);
				$cat->catcount = $this->_db->loadResult();

				$sql = "select count(*) from #__digicom_products p 
						inner join #__digicom_product_categories c on (p.id = c.productid) 
						where c.catid=".$tcat->id;
				$this->_db->setQuery($sql);
				$cat->prodcount = $this->_db->loadResult();

				$this->getCategoriesTreeIterator( $cat->id, $level + 1, $cats );
			}
		}
	}

	function getCategoryCategories($id = 0) {
		if (empty ($this->_category) && !$id) {
			$this->getCategory();
		}
		$this->_categorycats = null;
		if ((isset($this->_category->id) && !$this->_category->id) && !$id) return null;
		$db = JFactory::getDBO();

		$user = JFactory::getUser();

		if (empty ($this->_categorycats)) {
			$where[] = " c.published=1 ";
			//$where[] = " c.access <= ".$user->gid;
/*
			$where[] = " ((select count(p.id) from #__digicom_products p where 
					p.hide_public = 0 and
					p.id IN (
							SELECT productid
							FROM #__digicom_product_categories
							WHERE catid = c.id
						)
					) > 0 or (select count(cc.id) from #__digicom_categories cc where cc.parent_id=c.id and cc.published=1) > 0 )";
*/
			$sql = "select c.* from #__digicom_categories c where parent_id='".(($id < 1)? $this->_category->id:$id)."'".
				(count($where) > 0? " and ": "") .
				implode (" and ", $where);
				;

			$this->_total = @$this->_getListCount($sql);
			$order = " order by ordering asc ";
			$x2 = $this->_getList($sql.$order, $this->getState('limitstart'), $this->getState('limit'));
			
			$x = array();
			foreach( $x2 as $xi){
				$subids = DigiComHelper::getSubCategoriesId($xi->id);
				$subids = array_merge(array($xi->id),$subids);
				$sql = 'select count(p.id) from #__digicom_products p where 
					p.hide_public = 0 and
					p.id IN (SELECT productid
							FROM #__digicom_product_categories
							WHERE catid IN ('.implode(',', $subids).'))';
				$db->setQuery($sql);
				$res = $db->loadResult();
				if($res){
					$x[]=$xi;
				}
			}

			$this->_categorycats = $x;
		}

		return $this->_categorycats;
	}

}

