<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 375 $
 * @lastmodified	$LastChangedDate: 2013-10-21 11:33:34 +0200 (Mon, 21 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

defined ('_JEXEC') or die ("Go away.");

jimport ("joomla.application.component.view");

class DigiComViewProducts extends DigiComView {

	function displayCat ($tpl =  null ) {
	//function display ($tpl =  null ) {
		$catid =  JRequest::getVar('cid', array(), 'request', 'array');
		$totalprods = 0;

		if(is_array($catid) && count($catid) > 0){
			$catid = intval($catid["0"]);
		}
		else{
			$catid = intval($catid);
		}

		if(intval($catid) == 0){
			$itemid = JRequest::getVar("Itemid", "0");
			$db = JFactory::getDBO();
			$sql = "select `params` from #__menu where id=".intval($itemid);
			$db->setQuery($sql);
			$db->query();
			$params = $db->loadResult();
			$params = json_decode($params);
			$catid = isset($params->category_id) ? intval($params->category_id) : 0;
		}

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
        
		$category_name = $this->_models['product']->getCategory();
		$this->assign("category_name", $category_name);
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


				$db = JFactory::getDBO();

				$price = null;

				switch ($prod->priceformat) {

					case '2': // Don't show price
						$price = null;
						break;

					case '3': // Price and up
						$sql = "SELECT pp.product_id, min(pp.price) as price FROM #__digicom_plans dp
							LEFT JOIN #__digicom_products_plans pp on (dp.id = pp.plan_id)
							WHERE pp.product_id = ".$prod->id."
							GROUP BY pp.product_id";
						$db->setQuery($sql);	$prodprice = $db->loadObject();
						if (!empty($prodprice)) $price = DigiComHelper::format_price2($prodprice->price,$conf->get('currency','USD'), true, $conf) . " and up";
						break;

					case '4': // Price range
						$sql = "SELECT pp.product_id, min(pp.price) as price_min, max(pp.price) as price_max FROM #__digicom_plans dp
							LEFT JOIN #__digicom_products_plans pp on (dp.id = pp.plan_id)
							WHERE pp.product_id = ".$prod->id."
							GROUP BY pp.product_id";
						$db->setQuery($sql);	$prodprice = $db->loadObject();
						if (!empty($prodprice)) $price = DigiComHelper::format_price2($prodprice->price_min,$conf->get('currency','USD'), true, $conf) . " - " . DigiComHelper::format_price($prodprice->price_max,$conf->get('currency','USD'), true, $conf);
						break;

					case '5': // Minimal price
						$sql = "SELECT pp.product_id, min(pp.price) as price FROM #__digicom_plans dp
							LEFT JOIN #__digicom_products_plans pp on (dp.id = pp.plan_id)
							WHERE pp.product_id = ".$prod->id."
							GROUP BY pp.product_id";
						$db->setQuery($sql);	$prodprice = $db->loadObject();
						if (!empty($prodprice)) $price = DigiComHelper::format_price2($prodprice->price,$conf->get('currency','USD'), true, $conf);
						break;

					case '1': // Default price
					default:
						$sql = "SELECT pp.product_id, pp.price as price FROM #__digicom_plans dp
							LEFT JOIN #__digicom_products_plans pp on (dp.id = pp.plan_id)
							WHERE pp.default = 1 and pp.product_id = ".$prod->id;
						$db->setQuery($sql);	$prodprice = $db->loadObject();
						if (!empty($prodprice)) $price = DigiComHelper::format_price2($prodprice->price,$conf->get('currency','USD'), true, $conf);
						break;
				}
				$prod->price = $price;

			}
		}

		$this->assignRef('prods', $prods);

		$maxfields = 0;
		$pids = array();
		$image_exists = 0;
		if (count($prods) > 0)
			foreach ($prods as $i => $prod) {
				$pids[] = $i;
				$maxfields = DigiComHelper::check_fields($prod->productfields, $totalfields, $optlen, $select_only, $maxfields, $prod->id);
				$lists[$prod->id]['attribs'] = DigiComHelper::add_selector ( $prod->productfields, $prod->id, $optlen, $select_only, $i, $conf, $multi);
				$lists[$prod->id]['attrib_hidden'] = DigiComHelper::add_selector_hidden ( $prod->productfields );

				$t = array();
				$t = explode( "\n",trim($prod->images) );
				if (count ($t) > 0 ) {
					$image = trim($t[0]);
					if (strpos($image, "../") === 0) {
						$image = substr($image, 3);
					}
				} else $image = null;
				if (strlen(trim($image)) > 0) $image_exists = 1;
			}

		$this->assign("image_exists", $image_exists);
		$this->assign("maxf", $maxfields);
		$this->assign("lists", $lists);
		$this->assign("pids", $pids);

		// Featured products
		$featured_prods = DigiComHelper::getFeaturedProductByCategoryID($catid);

		if (count($featured_prods) > 0) {
			$db = JFactory::getDBO();
			foreach ($featured_prods as $z => $proditem) {

				$prod = $featured_prods[$z];

				$price = null;

				switch ($prod->priceformat) {

					case '2': // Don't show price
						$price = null;
						break;

					case '3': // Price and up
						$sql = "SELECT pp.product_id, min(pp.price) as price FROM #__digicom_plans dp
							LEFT JOIN #__digicom_products_plans pp on (dp.id = pp.plan_id)
							WHERE pp.product_id = ".$prod->id."
							GROUP BY pp.product_id";
						$db->setQuery($sql);	$prodprice = $db->loadObject();
						if (!empty($prodprice)) $price = DigiComHelper::format_price2($prodprice->price,$conf->get('currency','USD'), true, $conf);
						break;

					case '4': // Price range
						$sql = "SELECT pp.product_id, min(pp.price) as price_min, max(pp.price) as price_max FROM #__digicom_plans dp
							LEFT JOIN #__digicom_products_plans pp on (dp.id = pp.plan_id)
							WHERE pp.product_id = 1
							GROUP BY pp.product_id";
						$db->setQuery($sql);	$prodprice = $db->loadObject();
						if (!empty($prodprice)) $price = DigiComHelper::format_price2($prodprice->price_min,$conf->get('currency','USD'), true, $conf) . " - " . DigiComHelper::format_price($prodprice->price_max,$conf->get('currency','USD'), true, $conf);
						break;

					case '5': // Minimal price
						$sql = "SELECT pp.product_id, min(pp.price) as price FROM #__digicom_plans dp
							LEFT JOIN #__digicom_products_plans pp on (dp.id = pp.plan_id)
							WHERE pp.product_id = ".$prod->id."
							GROUP BY pp.product_id";
						$db->setQuery($sql);	$prodprice = $db->loadObject();
						if (!empty($prodprice)) $price = DigiComHelper::format_price2($prodprice->price,$conf->get('currency','USD'), true, $conf);
						break;

					case '1': // Default price
					default:
						$sql = "SELECT pp.product_id, pp.price as price FROM #__digicom_plans dp
							LEFT JOIN #__digicom_products_plans pp on (dp.id = pp.plan_id)
							WHERE pp.default = 1 and pp.product_id = ".$prod->id;
						$db->setQuery($sql);
						$prodprice = $db->loadObject();
						if (!empty($prodprice)) $price = DigiComHelper::format_price2($prodprice->price,$conf->get('currency','USD'), true, $conf);
						break;
				}
				$prod->price = $price;
			}
		}

		jimport('joomla.html.pagination');
		$pagination = new JPagination($items["total"], $items['limitstart'], $items['limit']);

		$this->assign( "featured_prods", $featured_prods );
		$this->assignRef('total', $items['total']);
		$this->assignRef('limit', $items['limit']);
		$this->assignRef('limitstart', $items['limitstart']);
		$this->assignRef('pagination',	$pagination);
		
		$template = new DigiComTemplateHelper($this);
		$template->rander('products');
		
		parent::display($tpl);
	}


	function display($tpl = null) {
	//function showProduct($tpl = null) {
		global $isJ25;
		$catid =  JRequest::getVar('cid', array(0), 'request', 'array');
		if (is_array($catid))	$catid = intval($catid[0]);
		else $catid = intval($catid);

		$pid =  JRequest::getVar('pid', array(0), 'request', 'array');
		if (is_array($pid))	$pid = intval($pid[0]);
		else $pid = intval($pid);

		$conf = $this->_models['config']->getConfigs();
		$this->assignRef("configs", $conf);

		$this->assign("catid", $catid);
		$prod = $this->_models['product']->getProduct($pid);

		$app = JFactory::getApplication("site");
		$document	   = JFactory::getDocument();
		$dispatcher	   = JDispatcher::getInstance();
		$params 	   = $app->getParams('com_digicom');
		$limitstart = 0;

		JPluginHelper::importPlugin('content');
		$article = new stdClass;
		$article->text = $prod->description;
		$article->fulltext = $prod->description;
		$results = $dispatcher->trigger('onPrepareContent', array (& $article, & $params, $limitstart));
		$prod->description = $article->text;

		$article = new stdClass;
		$article->text = $prod->fulldescription;
		$article->fulltext = $prod->description;
		$results = $dispatcher->trigger('onPrepareContent', array (& $article, & $params, $limitstart));
		$prod->fulldescription = $article->text;

		$this->assignRef('prod', $prod);
		
		$template = new DigiComTemplateHelper($this);
		$template->rander('products');
		
		parent::display();
	}

	public static function getRelatedCategory($product_id){
		$db = JFactory::getDBO();
		$sql = "select `catid`
				from `#__digicom_product_categories`
				where `productid`=".intval($product_id);
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadResult();
		return $result;
	}

	public static function getDefaultPlan($product_id){
		$db = JFactory::getDBO();
		$sql = "select `plan_id`
				from `#__digicom_products_plans`
				where `default`=1 and `product_id`=".intval($product_id);
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadResult();
		return $result;
	}

	public static function getPageURL(){
		$pageURL = 'http';
		if(@$_SERVER["HTTPS"] == "on"){
			$pageURL .= "s";
		}
		$pageURL .= "://";
		if($_SERVER["SERVER_PORT"] != "80"){
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		}
		else{
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		return $pageURL;
	}
	
	public static function listCategories() {
		$conf = $this->_config->getConfigs();
		switch ($conf->get('catlayoutstyle')) {
			case "0":
				$view->setLayout("list");
				break;

			case "1":
				$view->setLayout("listThumbs");
				break;

			case "2":
				$view->setLayout("dropdown");
				break;

			default:
				$view->setLayout("list");
				break;

		}
		$view->display();

	}
}
