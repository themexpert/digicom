<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 385 $
 * @lastmodified	$LastChangedDate: 2013-10-23 12:05:15 +0200 (Wed, 23 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

defined ('_JEXEC') or die ("Go away.");
define('DSDEBUG', 0);
define('DSDEBUG2', 0);

function digicomBuildRoute(&$query){
	global $option;
	$segments = array();
	$db = JFactory::getDBO();
	
	// get a menu item based on Itemid or currently active
	$app = JFactory::getApplication();
	$menu = $app->getMenu();
	if( empty($query['Itemid']) ) {
		$menuItem = $menu->getActive();
	} else {
		$menuItem = $menu->getItem($query['Itemid']);
		$mquery = $menuItem->query;
//		echo '<pre>'.print_r( $query, true ).'</pre>';
//		echo '<pre>'.print_r( $menuItem, true ).'</pre>';
		if($mquery['view']=='digicomproducts'&& $mquery['layout']=='viewproduct'){
			return array();
		}
	}
	
	if( count($query)==2 && isset($query['option']) && isset($query['Itemid'])){
		return array();
	}


//	if($menuItem->id==281){
//
//		echo '<pre>'.print_r($segments,true).'</pre>';
//		echo '<hr/>';
//		echo '<pre>'.print_r($query,true).'</pre>';
//		echo '<hr/>';
//		echo '<pre>'.print_r($menuItem, true).'</pre>';
//	}
	
	$mView = (empty($menuItem->query['view'])) ? null : $menuItem->query['view'];

	//$mLayout = (empty($menuItem->query['layout'])) ? null : $menuItem->query['layout'];
	$mId = (empty($menuItem->query['id'])) ? null : $menuItem->query['id'];

	// Controller
	if(isset($query['controller'])){
		$segments[] = str_replace('digicom', '', strtolower($query['controller']));
		unset($query['controller']);
	}
	else{
		if(isset($query['view'])){
			$segments[] = str_replace('digicom', '', strtolower($query['view']));
			unset($query['view']);
		} else {
			if(isset($menuItem) && isset($menuItem->query)){
				$segments[] = str_replace('digicom', '', strtolower($menuItem->query['view']));
			}
		}
	}

	// Category ID
	if(isset($query['cid'])){
		if(is_array($query['cid'])){
			$cid = $query['cid']['0'];
		}
		else{
			$cid = $query['cid'];
		}

		if(intval($cid) == 0){
			$itemid = JRequest::getVar("Itemid", "0");
			$sql = "select `params` from #__menu where id=".intval($itemid);
			$db->setQuery($sql);
			$db->query();
			$params = $db->loadResult();
			if($params){
				$params = new JRegistry($params);
				$catid = $params->get('category_id');
				if($catid)
				$cid = $catid;
			}
		}

		$sql = "select name from #__digicom_categories where id=".intval($cid);
		$db->setQuery($sql);
		$cname = $db->loadResult();
		$segments[] = $query['cid']."-".JFilterOutput::stringURLSafe($cname);
		unset($query['cid']);
	}

	// Category ID and Product ID
	if(isset($query['pid'])){
		if(is_array($query['pid'])){
			$pid = $query['pid']['0'];
		}
		else{
			$pid = $query['pid'];
		}

		$sql = "select name from #__digicom_products where id=".intval($pid);
		$db->setQuery($sql);
		$pname = $db->loadResult();
		$segments[] = $query['pid']."-".JFilterOutput::stringURLSafe($pname);
		unset($query['pid']);
	}

	if(isset($query['task'])){
		// Don't add default task
		if($query['task'] != 'list' and $query['task'] != 'view' and $query['task'] != 'showCart' and $query['task'] != 'listCategories'){
			$task = $segments[] = $query['task'];
	   	}
		unset($query['task']);
	}

	if(isset($query['layout'])){
		$layoutlist = array('horiz', 'list', 'listmulti', 'table', 'vert', 'viewproduct', 'dropdown', 'listthumbs');
		$segments[] = $query['layout'];
		unset($query['layout']);
	}

	// Cart ID
	if(isset($query['cartid'])){
		if(is_array($query['cartid'])){
			$cartid = $query['cartid'][0];
		}
		else{
			$cartid = $query['cartid'];
		}
		$segments[] = $cartid;
		unset($query['cartid']);
	}

	// Order ID
	if(isset($query['orderid'])){
		if(is_array($query['orderid'])){
			$orderid = $query['orderid'][0];
		}
		else{
			$orderid = $query['orderid'];
		}
		$segments[] = $orderid;
		unset($query['orderid']);
	}

	// License ID
	if(isset($query['licid'])){
		if(is_array($query['licid'])){
			$licid = $query['licid'][0];
		}
		else{
			$licid = $query['licid'];
		}
		$segments[] = $licid;
		unset($query['licid']);
	}

	if(isset($query['returnpage'])){
		$segments[] = str_replace('digicom', 'return_to_', strtolower($query['returnpage']));
		unset($query['returnpage']);
	}

	if(isset($query['tmpl'])){
		$segments[] = $query['tmpl'];
		unset($query['tmpl']);
	}

	if(isset($query['graybox'])){
		$segments[] = $query['graybox'];
		unset($query['graybox']);
	}

	if(isset($query['fromsum'])){
		$segments[] = 'pay';
		unset($query['fromsum']);
	}

	if(isset($query['success'])){
		if($query['success'] == 1){
			$segments[] = 'success';
		}
		else{
			$segments[] = 'fail';
		}
		unset($query['success']);
	}

	if(isset($query['sid'])){
		$segments[] = $query['sid'];
		unset($query['sid']);
	}

	if(isset($query['no_html'])){
		unset($query['no_html']);
	}

	// if(isset($query['Itemid'])){
		// $segments[] = $query['Itemid'];
	// }

	if(isset($segments["0"]) && isset($segments["1"]) && $segments["0"] == $segments["1"]){
		array_shift($segments);
	}
	return $segments;
}

function digicomParseRoute($segments){
	$vars = array();
	$db = JFactory::getDBO();
	$app = JFactory::getApplication();
	$menu = $app->getMenu();
	$item = $menu->getActive();
	// Count route segments
	$count = count($segments);
	$controller_list = array('products', 'license', 'category', 'order');
	
//echo '<pre>'.print_r($segments, true).'</pre>';
//echo '<pre>'.print_r($item, true).'</pre>';
//exit();


	$controller = isset($segments['0']) && intval($segments['0']) == "0" ? 'digicom'.ucfirst($segments['0']) : null;
	$vars['controller'] = $controller;
	//Handle View and Identifier
	
	if(!isset($controller)){
		$controller = "Products";
		$vars['controller'] = $controller;
		array_unshift($segments, $controller);
	}

	switch($controller){
		case 'Products' :
			if(isset($segments['1'])){
				$start = JRequest::getVar("start", "");
				if(count($segments) == 3 || count($segments) == 2 ){// click on category and edit that category products   |  dupa adaugare produs si return la pagina de produse
					$product_added = JRequest::getVar("product_added", "");
					if(trim($start) == ""){
						JRequest::setVar("limitstart", "0", "get");
					}
					if(strpos($segments['1'], ":") !== FALSE){
						$vars['cid'] = intval($segments['1']);
					}
					$vars['task'] = 'list';
					if(isset($segments['2'])){
						$vars['pid'] = intval($segments['2']);
					}
					
					$vars['task'] = (count($segments) == 3)? 'view':'list';
				}
				elseif(count($segments) == 4){// edit a product from list and from licenses
					$vars['cid'] = intval($segments['1']);
					$vars['pid'] = intval($segments['2']);
					$vars['task'] = 'view';
					$vars['Itemid'] = $segments['3'];
				}
			}
			else{
				unset($vars['controller']);
			}
			break;

		case 'Cart' :
		case 'cart' :
				if(isset($segments[1])){
					$vars['task'] = $segments[1];
					if($vars['task'] == 'addMulti'){
						$vars['cid'] = isset($segments[2])?$segments[2]:null;
					}

					if($vars['task'] == 'deleteFromCart'){
						$vars['cartid'] = isset($segments[2]) ? $segments[2]:null;
					}

					if($vars['task'] == 'checkout'){
						if(isset($segments[2])){
							$vars['fromsum'] = ($segments[2] == 'pay')? '1' : null;
						}
					}
				}
			break;

		case 'Profile' :
				if(count($segments) == 6){ // log-in from cart
					$vars['task'] = $segments['1'];
					$vars['returnpage'] = $segments['2'];
					$vars['graybox'] = true;
					$vars['Itemid'] = $segments['5'];
					$vars['tmpl'] = "component";
				}
				elseif(isset($segments[1])){
					$vars['task'] = $segments[1];
					switch($vars['task']){
						case 'login_register' :
							$vars['returnpage'] = "login_register";
							$vars['task'] = "login_register";
							break;
						case 'login':
							$vars['returnpage'] = isset($segments[2])?$segments[2]:null;
							$vars['returnpage'] = str_replace( 'return_to_', '', strtolower($vars['returnpage']) );
							$vars['returnpage'] = 'digicom'.ucfirst($vars['returnpage']);
							break;

						case 'register':
							break;

						case 'logCustomerIn':
							break;
					}
				}
			break;

		case 'Categories' :
//			var_dump($item);exit();
			if(count($segments) == 2 && isset($item)){//from menu
				$vars['layout'] = 'listthumbs';
//				$vars['Itemid'] = $segments['1'];
				$vars['cid'] = isset($segments['1']) ? intval($segments['1']) : null;
				$vars['task'] = 'view';
//				if(isset($segments['2'])){
//					$vars['Itemid'] = $segments['2'];
//				}
			}
			elseif(count($segments) == 3){//from list of categories
				$vars['cid'] = isset($segments['1']) ? intval($segments['1']) : null;
				$vars['task'] = 'view';
				if(isset($segments['2'])){
					$vars['Itemid'] = $segments['2'];
				}
			}
			elseif(count($segments) == 1){// from cart, continue shopping
				$vars['task'] = 'listCategories';
				$vars['Itemid'] = @$segments['1'];
			}
			break;

		case 'Orders' :
				if(count($segments) == 3){
					$vars['licid'] = $segments['1'];
					$vars['Itemid'] = $segments['2'];
				}
				else{
					if(isset($segments[1])){
						$vars['task'] = $segments[1];
					}

					if(isset($vars['task']) && $vars['task'] == 'showrec'){
						$vars['orderid'] = isset($segments[2])?$segments[2]:null;
						$vars['tmpl'] = 'component';
					}

					if(isset($vars['task']) && $vars['task'] == 'view'){
						$vars['orderid'] = isset($segments[2])?$segments[2]:null;
					}
				}
			break;

		case 'Licenses' :
				if(isset($segments[1])){
					$vars['task'] = $segments[1];
				}

				if(isset($segments[2])){
					if($segments[2] == 'fail'){
						$vars['success'] = 0;
					}
					elseif($segments[2] == 'success'){
						$vars['success'] = 1;
					}
					else{
						$vars['licid'] = $segments[2];
						$vars['tmpl'] = "component";
						$vars['no_html'] = "1";
					}
				}

				if(isset($segments[3])){
					$vars['sid'] = $segments[3];
				}
			break;
	}
	if(isset($item->id))
		$vars['Itemid'] = $item->id;

	return $vars;
}

?>