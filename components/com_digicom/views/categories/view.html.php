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
		$this->assign("catid", $catid);

		$app = JFactory::getApplication("site");
		$document	   = JFactory::getDocument();
		$dispatcher	   = JDispatcher::getInstance();
		$params 	   = $app->getParams('com_digicom');
		$limitstart = 0;
		JPluginHelper::importPlugin('content');
		$article = new stdClass;
		
		$lists = array();
		if (count($prods) > 0) {
			foreach ($prods as $z => $proditem) {
				$prod = $prods[$z];

				$article->text = $prod->description;
				$article->fulltext = $prod->description;
				$results = $dispatcher->trigger('onPrepareContent', array (& $article, & $params, $limitstart));
				$prod->description = $article->text;
				$qty = array();
				if ($prod->usestock > 0) {
					$lim = ($prod->stock - $prod->used) + 1;
				} else {
					$lim = 26;
				}

				// Used Stock to "Display as Normal" if stock = 0
				if ( $prod->usestock && ($prod->stock==0) ) {
					$lim = 26;
				}
				$lim = 26;
				for ( $i = 1; $i < $lim; $i++ ) {
					$qty[] = JHTML::_('select.option',  $i );
				}

				$active = 1;
				if ($this->_layout == "listMulti") {
					$qty_name = "quantity[]";
					$multi = 1;
				} else {
					$qty_name = "qty";
					$multi = 0;
				}
				$lists['qty'][$prod->id] = JHTML::_('select.genericlist',  $qty, $qty_name, 'class="inputbox" onchange="document.getElementById(\'product_qty_id_'.$prod->id.'\').value=this.value"', 'value', 'text', "/");

			}
		}
		
		$this->assignRef('prods', $prods);

		$this->assign("lists", $lists);
		$this->assign("pids", $pids);

		jimport('joomla.html.pagination');
		$pagination = new JPagination($items["total"], $items['limitstart'], $items['limit']);

		$this->assign( "featured_prods", $featured_prods );
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
					from #__digicom_product_categories pc, #__digicom_products p
					where pc.catid=".intval($cat_id)."
					  and pc.productid=p.id
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
