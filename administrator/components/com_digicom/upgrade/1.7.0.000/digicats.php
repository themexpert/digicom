<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 341 $
 * @lastmodified	$LastChangedDate: 2013-10-10 14:28:28 +0200 (Thu, 10 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

	// Menu DigiCom Categories

	$this->deleteFromTable('#__modules', " title='digicats' and module='mod_mainmenu' and params like '%menutype=digicats%' ");

	$row = JTable::getInstance("module");

	$row->params = 'menutype=digicats';
	$row->position = 'left';
	$row->published = '0';
	$row->title = 'digicats';
	$row->iscore = '0';
	$row->module = 'mod_mainmenu';
	$row->params = "menutype=digicats\n
			moduleclass_sfx=_menu
			";

	if (!$row->check()) {}
	if (!$row->store()) {}
	$row->checkin();

	$sql = "select count(*) from #__modules_menu where moduleid='".$row->id."' and menuid=0";
	$this->db->setQuery($sql);
	$n = $this->db->loadResult();
	if ($n < 1) {
		$query = "INSERT INTO #__modules_menu VALUES ( ".(int)$row->id.", 0 )";
		$this->db->setQuery( $query );
		if ( !$this->db->query() ) {}
	}

	$sql = "select id, name, parent_id, published from #__digicom_categories";
	$this->db->setQuery($sql);
	$catids = $this->db->loadObjectList();

	$children = array();

	if ( $catids ) {
		// first pass - collect children
		foreach ( $catids as $v ) {
			$pt 	= $v->parent_id;
			$list 	= @$children[$pt] ? $children[$pt] : array();
			array_push( $list, $v );
			$children[$pt] = $list;
		}
	}

	foreach ($children as $i => $v) {
		foreach ($children[$i] as $j => $vv) {
			$children[$i][$j]->parent = $vv->parent_id;
		}
	}

	$list = JHTML::_('menu.treerecurse', 0, "&nbsp;", array(), $children, 20, 0, 0);

	$sql = "select count(*) from #__menu_types where menutype='digicats'";
	$db->setQuery($sql);
	$digitype = $db->loadResult();
	if (!$digitype) {
		$sql = "insert into #__menu_types (`menutype`, `title`, `description`) values ('digicats', 'DigiCom Categories', 'DigiCom Category links')";
		$db->setQuery($sql);
		$db->query();
	}

	$sql = "delete from #__menu where menutype='digicats'";
	$db->setQuery($sql);
	$db->query();

	foreach ($catids as $i => $v) {
		$sql = "select count(*) from #__digicom_categories where parent_id='".$v->id."' and published='1'";
		$db->setQuery($sql);
		$sibs = $db->loadResult();
		if ($sibs > 0) {
			$link = 'index.php?option=com_digicom&controller=categories&task=view&cid[]='.$v->id; 
		} else {
			$link ='index.php?option=com_digicom&controller=products&task=list&cid[]='.$v->id;
		}
		$parent_link = 'index.php?option=com_digicom&controller=products&task=list&cid[]='.$v->parent_id;
		$sql = "select id from #__menu where menutype='digicats' and link='".$parent_link."'";
		$db->setQuery($sql);
		$parent_id = $db->loadResult();
		if (!$parent_id) {
			$parent_link = 'index.php?option=com_digicom&controller=categories&task=view&cid[]='.$v->parent_id;
			$sql = "select id from #__menu where menutype='digicats' and link='".$parent_link."'";
			$db->setQuery($sql);
			$parent_id = $db->loadResult();
		}

		if (!$parent_id) $parent_id = 0; 
		$sql = "INSERT INTO #__menu	(menutype,
				name, link,	type, published, parent, componentid,
				sublevel, ordering, checked_out, checked_out_time,
				pollid, browserNav,	access,	utaccess, params) 
			VALUES ('digicats',
				'".$v->name."','".$link."','url', '".$v->published."', '".$parent_id."', '0',
				'0', '0', '0','0000-00-00 00:00:00',
				'0', '0', '0', '0', ''	)
			";
		$db->setQuery($sql);
		$db->query();
	}

	// End Menu DigiCom Categories

?>