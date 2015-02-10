<?php
/**
 * @version		$Id: dsp.php 341 2013-10-10 12:28:28Z thongta $
 * @package		Joomla
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPLv3, see LICENSE
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once(JPATH_SITE.'/components/com_digicom/router.php');

class plgSearchDsp extends JPlugin{


	function onContentSearchAreas(){
		static $areas = array(
			'categories' => 'Categories'
			);
			return $areas;
	}

	/**
	 * Categories Search method
	 *
	 * The sql must return the following fields that are
	 * used in a common display routine: href, title, section, created, text,
	 * browsernav
	 * @param string Target search string
	 * @param string mathcing option, exact|any|all
	 * @param string ordering option, newest|oldest|popular|alpha|category
	 * @param mixed An array if restricted to areas, null if search all
	 */
	//function searchDSProducts( $text, $phrase='', $ordering='', $areas=null )
	function onContentSearch($text, $phrase='', $ordering='', $areas=null){
		$db		= JFactory::getDBO();
		$user	= JFactory::getUser();
		$searchText = $text;
	
		if(is_array($areas)){
			if(!array_intersect($areas, array_keys(searchDSProductAreas()))){
				return array();
			}
		}
		
		$limit = $this->params->get('search_limit',	50);
	
		$text = trim($text);
		if($text == ''){
			return array();
		}
		
		switch($ordering){
			case 'alpha':
				$order = 'a.name ASC';
				break;
	
			case 'category':
			case 'popular':
			case 'newest':
			case 'oldest':
			default:
				$order = 'a.name DESC';
		}
	
		$text = $db->Quote( '%'.$db->getEscaped( $text, true ).'%', false);
		$query	= 'SELECT a.id, a.name as title, a.description AS text, "" AS created, a.name,'
				. ' "2" AS browsernav,'
				. '  a.id AS catid, a.publish_up as created'
				. ' FROM #__digicom_products AS a'
				. ' WHERE ( a.name LIKE '.$text
				. ' OR a.description LIKE '.$text	
				. ' OR a.fulldescription LIKE '.$text.' )'
				. ' AND a.published = 1'
				. ' GROUP BY a.id'
				. ' ORDER BY '. $order
		;
		$db->setQuery($query, 0, $limit);
		$rows = $db->loadObjectList();
		
		$sql = "SELECT * FROM #__digicom_categories";
		$db->setQuery($sql);
		$c = $db->loadObjectList();
		$res = array();
	
		$sql = "SELECT `productid`, `catid` FROM #__digicom_product_categories";
		$db->setQuery($sql);
		$pc = $db->loadObjectList("productid");
		$res = array();
	
		$count = count($rows);
		for($i = 0; $i < $count; $i++){
			$rows[$i]->href = JRoute::_("index.php?option=com_digicom&controller=products&task=view&cid=".$pc[$rows[$i]->id]->catid."&pid=".$rows[$i]->id,false);
			foreach($pc as $j => $p){
				if($p->productid == $rows[$i]->id){
					$rows[$i]->pid = $p->catid;
					break;
				}
			}
			$this->getDSPathProduct($c, $rows[$i], $res);
			$res = array_reverse($res);
			$path = '';
			$path = implode("/", $res);
			$rows[$i]->section = $path."";
		}
	
		$return = array();
		if($count >0){
			foreach($rows AS $key => $category){
				if(searchHelper::checkNoHTML($category, $searchText, array('name', 'title', 'text'))){
					$return[] = $category;
				}
			}
		}
		
		return $return;
	}
	
	function getDSPathProduct($c, $item, &$res){
		if(isset($item->pid) && $item->pid > 0){
			foreach($c as $i => $v){
				if($item->pid == $v->id){
					$res[] = $v->name;
					if($v->parent_id > 0){
						$this->getDSPathProduct($c, $v->parent_id, $res);
					}
					else{
						$res[] = JText::_('Store');
					}
				}
			}
		}
		else{
			$res[] = JText::_( 'Store' );
		}
	}
}

?>