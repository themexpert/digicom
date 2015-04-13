<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

/**
 * Routing class from com_digicom
 *
 * @since  1.0.0-beta2
 */
class DigiComRouter extends JComponentRouterBase
{
	/**
	 * Build the route for the com_digicom component
	 *
	 * @param   array  &$query  An array of URL arguments
	 *
	 * @return  array  The URL arguments to use to assemble the subsequent URL.
	 *
	 * @since   1.0.0-beta2
	 */
	public function build(&$query)
	{
		
		$app = JFactory::getApplication();

		$segments = array();
		// Get a menu item based on Itemid or currently active
		$params = JComponentHelper::getParams('com_digicom');

		// We need a menu item.  Either the one specified in the query, or the current active one if none specified
		if (empty($query['Itemid']))
		{
			$menuItem = $this->menu->getActive();
			$menuItemGiven = false;
		}
		else
		{
			$menuItem = $this->menu->getItem($query['Itemid']);
			$menuItemGiven = true;
		}
		
		// Check again
		if ($menuItemGiven && isset($menuItem) && $menuItem->component != 'com_digicom')
		{
			$menuItemGiven = false;
			unset($query['Itemid']);
		}

		if (isset($query['view']))
		{
			$view = $query['view'];
		}
		else
		{
			// We need to have a view in the query or it is an invalid URL
			return $segments;
		}
		
		// Are we dealing with an product or category that is attached to a menu item?
		if (($menuItem instanceof stdClass)
			&& $menuItem->query['view'] == $query['view']
			&& isset($query['id'])
			&& $menuItem->query['id'] == (int) $query['id'])
		{
			unset($query['view']);

			if (isset($query['catid']))
			{
				unset($query['catid']);
			}

			if (isset($query['layout']))
			{
				unset($query['layout']);
			}

			unset($query['id']);

			return $segments;
		}

		if ($view == 'category' || $view == 'product')
		{
			//print_r($query);die;
			if (!$menuItemGiven)
			{
				$segments[] = $view;
			}
			
			unset($query['view']);

			if ($view == 'product')
			{
				
				if (isset($query['id']) && isset($query['catid']) && $query['catid'])
				{
					if($menuItemGiven && ( $menuItem->query['view'] != 'product' and $menuItem->query['view'] != 'category' ) )
					{
						//print_r($menuItem);die;
						$item = $app->getMenu()->getItems('link', 'index.php?option=com_digicom&view=category&id='.$query['catid'], true);
						$Itemid = isset($item->id) ? $item->id : '';

						if($Itemid == ''){
							$item = $app->getMenu()->getItems('link', 'index.php?option=com_digicom&view=categories&id='.$query['catid'], true);
							$Itemid = isset($item->id) ? $item->id : '';
						}
						if($Itemid == ''){
							$item = $app->getMenu()->getItems('link', 'index.php?option=com_digicom&view=categories&id=0', true);
							$Itemid = isset($item->id) ? $item->id : '';
						}

						if($Itemid == ''){
							$item = $app->getMenu()->getItems('link', 'index.php?option=com_digicom&view=category&id=0', true);
							$Itemid = isset($item->id) ? $item->id : '';
						}

						if($Itemid){
							$query['Itemid'] = 	$Itemid;
						}else{
							$query['Itemid'] = '';
							$menuItemGiven = false;
						}

					}

					$catid = $query['catid'];

					// Make sure we have the id and the alias
					if (strpos($query['id'], ':') === false)
					{
						$db = JFactory::getDbo();
						$dbQuery = $db->getQuery(true)
							->select('alias')
							->from('#__digicom_products')
							->where('id=' . (int) $query['id']);
						$db->setQuery($dbQuery);
						$alias = $db->loadResult();
						$query['id'] = $query['id'] . ':' . $alias;
					}
				}
				else
				{
					// We should have these two set for this view.  If we don't, it is an error
					return $segments;
				}
			}
			else
			{
				if (isset($query['id']))
				{
					$catid = $query['id'];
				}
				else
				{
					// We should have id set for this view.  If we don't, it is an error
					return $segments;
				}
			}

			if ($menuItemGiven && isset($menuItem->query['id']))
			{
				$mCatid = $menuItem->query['id'];
			}
			else
			{
				$mCatid = 0;
			}

			$categories = JCategories::getInstance('DigiCom');
			$category = $categories->get($catid);

			if (!$category)
			{
				// We couldn't find the category we were given.  Bail.
				return $segments;
			}

			$path = array_reverse($category->getPath());

			$array = array();

			foreach ($path as $id)
			{
				if ((int) $id == (int) $mCatid)
				{
					break;
				}

				list($tmp, $id) = explode(':', $id, 2);

				$array[] = $id;
			}

			$array = array_reverse($array);

			$segments = array_merge($segments, $array);

			if ($view == 'product')
			{
				
				list($tmp, $id) = explode(':', $query['id'], 2);	

				$segments[] = $id;
			}

			unset($query['id']);
			unset($query['catid']);
		}

		if ($view == 'cart'){
			//print_r($query);die;
			$item = $app->getMenu()->getItems('link', 'index.php?option=com_digicom&view=cart', true);
			$Itemid = isset($item->id) ? $item->id : '';
			
			if (!$Itemid)
			{
				$segments[] = $view;
			}

			unset($query['view']);

			if(!empty($Itemid)){
				 $query['Itemid'] = $Itemid;
			}else{
				//unset($query['Itemid']);
			}


		} 
		if ($view == 'checkout' 
			or $view == 'dashboard' 
			or $view == 'downloads' 
			or $view == 'profile'
			or $view == 'register'
		)
		{
			$item = $app->getMenu()->getItems('link', 'index.php?option=com_digicom&view='.$view, true);
			$Itemid = isset($item->id) ? $item->id : '';
			
			if (!$Itemid)
			{
				$segments[] = $view;
			}else{
				$query['Itemid'] = $Itemid;
			}

			unset($query['view']);
			

			if($view == 'checkout'){
				if(isset($query['order_id'])){
					$segments[] = 'order';
					$segments[] = $query['order_id'];
					unset($query['order_id']);					
				}
			}



		}

		if ($view == 'order' or $view == "orders")
		{
			$item = $app->getMenu()->getItems('link', 'index.php?option=com_digicom&view=orders', true);
			$Itemid = isset($item->id) ? $item->id : '';
			
			if (!$menuItemGiven){
				if (!$Itemid)
				{
					$segments[] = $view;
				}
				else{
					$query['Itemid'] = $Itemid;
				}
			}

			unset($query['view']);
			
			if(isset($query['layout'])){
				$segments[] = $query['layout'];
				unset($query['layout']);
			}
			if(isset($query['id'])){
				$segments[] = $query['id'];
				unset($query['id']);
			}
		

		}

		if ($view == 'archive')
		{
			if (!$menuItemGiven)
			{
				$segments[] = $view;
				unset($query['view']);
			}

			if (isset($query['year']))
			{
				if ($menuItemGiven)
				{
					$segments[] = $query['year'];
					unset($query['year']);
				}
			}

			if (isset($query['year']) && isset($query['month']))
			{
				if ($menuItemGiven)
				{
					$segments[] = $query['month'];
					unset($query['month']);
				}
			}
		}

		
		/*
		 * If the layout is specified and it is the same as the layout in the menu item, we
		 * unset it so it doesn't go into the query string.
		 */
		if (isset($query['layout']) && $view != 'order')
		{
			if ($menuItemGiven && isset($menuItem->query['layout']))
			{
				if ($query['layout'] == $menuItem->query['layout'])
				{
					unset($query['layout']);
				}
				else{
					$segments[] = $query['layout'];
					unset($query['layout']);
				}
			}
			else
			{
				if ($query['layout'] == 'default')
				{
					unset($query['layout']);
				}
				else{
					$segments[] = $query['layout'];
					unset($query['layout']);
				}
			}
		}

		/*
		 * lets deal the processor
		 */
		if (isset($query['processor']))
		{
			unset($query['processor']);			
		}
		
		$total = count($segments);
		for ($i = 0; $i < $total; $i++)
		{
			$segments[$i] = str_replace(':', '-', $segments[$i]);
		}

		if ($view == 'product'){
			//print_r($segments);die;
		}
		
		return $segments;
	}

	/**
	 * Parse the segments of a URL.
	 *
	 * @param   array  &$segments  The segments of the URL to parse.
	 *
	 * @return  array  The URL attributes to be used by the application.
	 *
	 * @since   1.0.0-beta2
	 */
	public function parse(&$segments)
	{
		
		$total = count($segments);
		$vars = array();

		for ($i = 0; $i < $total; $i++)
		{
			$segments[$i] = preg_replace('/-/', ':', $segments[$i], 1);
		}

		// Get the active menu item.
		$item = $this->menu->getActive();
		$params = JComponentHelper::getParams('com_digicom');
		$db = JFactory::getDbo();

		// Count route segments
		$count = count($segments);

		/*
		 * Standard routing for products.  If we don't pick up an Itemid then we get the view from the segments
		 * the first segment is the view and the last segment is the id of the product or category.
		 */
		
		if (!isset($item) && ( $segments[0] != 'categories' && $segments[0] != 'category' && $segments[0] != 'product') )
		{
			
			$segview = $segments[0];

			switch ($segview) {
				case 'cart':
				case 'checkout':
				case 'dashboard':
				case 'downloads':
				case 'login':
				case 'order':
				case 'orders':
				case 'profile':
				case 'register':

					$vars['view'] = $segments[0];
					$vars['id'] = $segments[$count - 1];
					break;
				
				default:
					$info = $this->getCategoryId($segments[0], $segments);
					
					if(!$info) return $vars;

					$vars['view'] = $info['view'];
					$vars['catid'] = $info['catid'];
					$vars['id'] = $info['id'];
					break;
			}
			
			if($segments[0] == 'cart')
			{
				if(!empty($segments[1])) $vars['layout'] = $segments[1];
			}
			elseif ($segments[0] == 'cart_popup' or $segments[0] == 'summary') 
			{
				$vars['view'] = 'cart';
				$vars['layout'] = $segments[0];
			}

			if($segments[0] == 'checkout')
			{
				if( !empty($segments[1]) && !empty($segments[2]) ) $vars['order_id'] = $segments[2];
			}

			return $vars;

		}elseif(isset($item) && $item->query['option'] == 'com_digicom' && ( $segments[0] != 'category' && $segments[0] != 'product')){
			
			//print_r($segments);die;
			$vars['view'] = $item->query['view'];

			if($segments[0] == 'cart')
			{
				if( $item->query['view'] != 'cart' ){
					$vars['view'] = $segments[0];	
				}
				if(!empty($segments[1])){
					$vars['layout'] = $segments[1];	
				} 
			}
			elseif($segments[0] == 'summary')
			{
				$vars['view'] = 'cart';
				$vars['layout'] = $segments[0];
			}
			elseif ($segments[0] == 'cart_popup' or $segments[0] == 'summary')
			{
				$vars['view'] = 'cart';
				$vars['layout'] = $segments[0];
			}elseif($segments[0] == 'checkout'){
				$totalsegs = count($segments);
				if($totalsegs > 2){
					$vars['view'] = $segments[0];
					$vars['order_id'] = $segments[2];
				}
			}
		}


		/*
		 * If there is only one segment, then it points to either an product or a category.
		 * We test it first to see if it is a category.  If the id and alias match a category,
		 * then we assume it is a category.  If they don't we assume it is an product
		 */

		if ( isset($item) && $count == 1 && $item->query['view'] != 'orders')
		{
			// We check to see if an alias is given.  If not, we assume it is an product
			//list( $id, $alias ) = explode( ':', $segments[0] , 2 );
			$id = $segments[0];
			$alias = '';

			// First we check if it is a category
			$category = JCategories::getInstance('DigiCom')->get($id);

			if ( $category && $category->alias == $alias )
			{
				$vars['view'] = 'category';
				$vars['id'] = $id;

				return $vars;
			}
			else
			{
				$query = $db->getQuery(true)
					->select($db->quoteName(array('alias', 'catid')))
					->from($db->quoteName('#__digicom_products'))
					->where($db->quoteName('id') . ' = ' . (int) $id);
				$db->setQuery($query);
				$product = $db->loadObject();

				if ($product)
				{
					if ($product->alias == $alias)
					{
						$vars['view'] = 'product';
						$vars['catid'] = (int) $product->catid;
						$vars['id'] = (int) $id;

						return $vars;
					}
				}
			}
		}
		else if( isset($item) && ( $item->query['view'] == 'orders' or $item->query['view'] == 'order') )
		{
			$vars['view'] = $item->query['view'];
			if(isset($segments[1])){
				$vars['view'] = 'order';
				$vars['layout'] = $segments[0];
				$vars['id'] = $segments[1];
			}else{
				$vars['view'] = 'order';
				$vars['id'] = $segments[0];
			}
			
			return $vars;
		}

		$activeView = $segments[0];
		switch ($activeView) {
			case 'summary':
			case 'cart':
			case 'cart_popup':
			case 'orders':
			case 'checkout':
			case 'dashboard':
			case 'profile':
			case 'downloads':
				return $vars;
			default:
				//nothing to do;
				break;
		}

		/*
		 * If there was more than one segment, then we can determine where the URL points to
		 * because the first segment will have the target category id prepended to it.  If the
		 * last segment has a number prepended, it is an product, otherwise, it is a category.
		 */
		
		// Ltes handle the product & category
		if (!isset($item) && ( $segments[0] == 'category' or $segments[0] == 'product')){
			$id= $segments[$count - 1];
		}else{
			// We get the category id from the menu item and search from there
			$id = $item->query['id'];
		}

		
		$category = JCategories::getInstance('DigiCom')->get($id);

		if (!$category)
		{
			JError::raiseError(404, JText::_('COM_DIGICOM_ERROR_PARENT_CATEGORY_NOT_FOUND'));

			return $vars;
		}

		$categories = $category->getChildren();
		$vars['catid'] = $id;
		$vars['id'] = $id;
		$found = 0;

		foreach ($segments as $segment)
		{
			$segment = str_replace(':', '-', $segment);

			foreach ($categories as $category)
			{
				if ($category->alias == $segment)
				{
					$vars['id'] = $category->id;
					$vars['catid'] = $category->id;
					$vars['view'] = 'category';
					$categories = $category->getChildren();
					$found = 1;
					break;
				}
			}

			if ($found == 0)
			{
				
				$db = JFactory::getDbo();
				$query = $db->getQuery(true)
					->select($db->quoteName('id'))
					->from('#__digicom_products')
					->where($db->quoteName('catid') . ' = ' . (int) $vars['catid'])
					->where($db->quoteName('alias') . ' = ' . $db->quote($segment));
				$db->setQuery($query);
				$cid = $db->loadResult();
				
				$vars['id'] = $cid;

				if (isset($item) && ( $item->query['view'] == 'archive' && $count != 1) )
				{
					$vars['year'] = $count >= 2 ? $segments[$count - 2] : null;
					$vars['month'] = $segments[$count - 1];
					$vars['view'] = 'archive';
				}
				else
				{
					$vars['view'] = 'product';
				}
			}

			$found = 0;
		}

		return $vars;
	}

	public static function getCategoryId($info, $segments){
		
		$return = array();
		$id = $info;
		$category = JCategories::getInstance('DigiCom')->get($id);

		if (!$category)
		{
			JError::raiseError(404, JText::_('COM_DIGICOM_ERROR_PARENT_CATEGORY_NOT_FOUND'));

			return false;
		}

		$categories = $category->getChildren();
		$return['catid'] = $id;
		$return['id'] = $id;
		$found = 0;

		foreach ($segments as $segment)
		{
			$segment = str_replace(':', '-', $segment);

			foreach ($categories as $category)
			{
				if ($category->alias == $segment)
				{
					$return['id'] = $category->id;
					$return['catid'] = $category->id;
					$return['view'] = 'category';
					$categories = $category->getChildren();
					$found = 1;
					break;
				}
			}

			if ($found == 0)
			{
				
				$db = JFactory::getDbo();
				$query = $db->getQuery(true)
					->select($db->quoteName('id'))
					->from('#__digicom_products')
					->where($db->quoteName('catid') . ' = ' . (int) $return['catid'])
					->where($db->quoteName('alias') . ' = ' . $db->quote($segment));
				$db->setQuery($query);
				$cid = $db->loadResult();
				
				$return['id'] = $cid;
				$return['view'] = 'product';

			}

			$found = 0;
		}
		return $return;
	}
}

/**
 * DigiCom router functions
 *
 * These functions are proxys for the new router interface
 * for old SEF extensions.
 *
 * @param   array  &$query  An array of URL arguments
 *
 * @return  array  The URL arguments to use to assemble the subsequent URL.
 *
 * @deprecated  4.0  Use Class based routers instead
 */
function DigiComBuildRoute(&$query)
{
	$router = new DigiComRouter;

	return $router->build($query);
}

/**
 * Parse the segments of a URL.
 *
 * This function is a proxy for the new router interface
 * for old SEF extensions.
 *
 * @param   array  $segments  The segments of the URL to parse.
 *
 * @return  array  The URL attributes to be used by the application.
 *
 * @since   1.0.0-beta2
 * @deprecated  4.0  Use Class based routers instead
 */
function DigiComParseRoute($segments)
{
	$router = new DigiComRouter;

	return $router->parse($segments);
}
