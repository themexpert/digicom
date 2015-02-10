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

jimport('joomla.application.component.controller');

class DigiComController extends JControllerLegacy
{
	var $_customer = null;

	function __construct()
	{
		parent::__construct();
		$ajax_req = JRequest::getVar("no_html", 0, "request");
		$this->_customer = new DigiComSessionHelper();
		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::root()."media/digicom/assets/css/digicom.css");
	}

	function display($cachable = false, $urlparams = false){
		parent::display();
	}

	function debugStop($msg = ''){
		$mainframe=JFactory::getApplication();
		echo $msg;
		$mainframe->close();
	}

	function breadcrumbs(){
		$Itemid		= JRequest::getInt("Itemid", 0);
		$mainframe	= JFactory::getApplication();

		// Get the PathWay object from the application
		$pw		= $mainframe->getPathway();
		$db		= JFactory::getDBO();
		$cids	= JRequest::getVar('cid', 0, '', 'array');
		$cid	= intval($cids[0]);
		$pids	= JRequest::getVar('pid', 0, '', 'array');
		$pid	= intval($pids[0]);
		$c		= JRequest::getVar("controller", "");
		$t		= JRequest::getVar("task", "");

		if ( $c != "Licenses" && $c != "Orders" ) {
			$sql = "SELECT name, parent_id FROM #__digicom_categories WHERE id=".intval($cid);
			$db->setQuery($sql);
			$res = $db->loadObjectList();
			$res = $res[0];
			$cname = $res->name;
			$parent_id = $res->parent_id;
			$sql = "SELECT name FROM #__digicom_products WHERE id=".intval($pid);
			$db->setQuery($sql);
			$pname = $db->loadResult();
		} else {
			$cname = $cid;
			$pname = $pid;
		}

		$link = JRoute::_("index.php?option=com_digicom&controller=categories&Itemid".$Itemid);
		$name = "Category List";
		$pw->addItem($name, $link);
		$bc_added = 0;

		if($c == "Categories"){
			if($parent_id > 0){
				$sql = "select name from #__digicom_categories where id=".intval($parent_id);
				$db->setQuery($sql);
				$name = $db->loadResult();
				$link = JRoute::_("index.php?option=com_digicom&controller=categories&task=list&cid=" . $parent_id . "&Itemid=" . $Itemid);
				$pw->addItem($name, $link);
				$bc_added = 1;
			}
		}

		if($c == "Products"){
			if($t == "list"){
				$link = JRoute::_("index.php?option=com_digicom&controller=products&task=list&cid=".$parent_id."&Itemid=".$Itemid);
				$pw->addItem($cname, $link);
				$bc_added = 1;
			}

			if($t == "show"){
				$link = JRoute::_("index.php?option=com_digicom&controller=products&task=list&cid=" . $parent_id . "&Itemid=" . $Itemid);
				$pw->addItem($cname, $link);
				$bc_added = 1;
			}
		}

		if($c == "Cart"){
			$link = JRoute::_("index.php?option=com_digicom&controller=cart&task=showCart&Itemid=" . $Itemid);
			$name = "Cart";
			$pw->addItem($name, $link);
			if($t == "checkout"){
				$link = "";
				$name = "Checkout";
				$pw->addItem($name, $link);
			}
			$bc_added = 1;
		}

		if(strlen(trim($c)) > 0 && $bc_added == 0 && $c != "Categories"){
			$link = "";
			$name = $c;
			$pw->addItem($name, $link);
		}
	}
}
