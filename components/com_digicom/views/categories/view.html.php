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

jimport ("joomla.application.component.view");

class DigiComViewCategories extends DigiComView {

	function display ($tpl =  null ) {
		$catid =  JFactory::getApplication()->input->get('cid',0);
		$totalprods = 0; // 0 unlimited
		$conf = $this->_models['config']->getConfigs();
		$this->assignRef("configs", $conf);
		
		$items = array();
		if(!$catid){
			$prods = array();
			$items["totalprods"] = null;
			$items["total"] = null;
			$items["limitstart"] = null;
			$items["limit"] = null;
		}
		else{
			$items = $this->_models['product']->getCategoryProducts($catid, $totalprods);
			$prods = $items["items"];
		}
		
		$category = $this->_models['product']->getCategory();
		
		$this->assign("category", $category);
		$this->assign("totalprods", $totalprods);

		$app = JFactory::getApplication("site");
		$document	   = JFactory::getDocument();
		$dispatcher	   = JDispatcher::getInstance();
		$params 	   = $app->getParams('com_digicom');
		$limitstart = 0;
		JPluginHelper::importPlugin('content');
		$article = new stdClass;
		
		$lists = array();
		if (count($prods) > 0) {
			foreach ($prods as $key=>$prod) {
				$article->text = $prod->description;
				$article->fulltext = $prod->description;
				$dispatcher->trigger('onPrepareContent', array (& $article, & $params, $limitstart));
			}
		}
		
		$this->assignRef('prods', $prods);

		jimport('joomla.html.pagination');
		$pagination = new JPagination($items["total"], $items['limitstart'], $items['limit']);

		$this->assignRef('total', $items['total']);
		$this->assignRef('limit', $items['limit']);
		$this->assignRef('limitstart', $items['limitstart']);
		$this->assignRef('pagination',	$pagination);
		
		$template = new DigiComTemplateHelper($this);
		$template->rander('categories');
		
		parent::display($tpl);
	}

	public static function countSublist($cat_id){
		$return = "";
		$db = JFactory::getDBO();
		$sql = "select count(*) from #__digicom_categories where `parent_id`=".intval($cat_id)." and `published`=1";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadResult();
		if($result == 1){
			$return = $result." ".JText::_("DIGI_CATEGORY");
		}
		elseif($result > 1){
			$return = $result." ".JText::_("DIGI_CATEGORIES");
		}
		elseif($result == 0){
			$sql = "select count(*)
					from #__digicom_products p
					where p.catid=".intval($cat_id)."
					  and p.published=1
					  and p.hide_public=0";
			$db->setQuery($sql);
			$db->query();
			$result = $db->loadResult();
			if($result == 1){
				$return = $result." ".JText::_("DIGI_PRODUCT");
			}
			elseif($result > 1){
				$return = $result." ".JText::_("DIGI_PRODUCTS");
			}
		}
		return $return;
	}

}
