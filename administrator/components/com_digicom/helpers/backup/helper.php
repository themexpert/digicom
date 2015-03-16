<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 455 $
 * @lastmodified	$LastChangedDate: 2014-01-06 05:30:05 +0100 (Mon, 06 Jan 2014) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

defined ('_JEXEC') or die ("Go away.");

class DigiComAdminHelper {
	
	/**
	 * Configure the Linkbar.
		*
	 * @param   string  $vName  The name of the active view.
		*
	 * @return  void
		*
	 * @since   1.6
	 */

	public static function addSubmenu($vName)
	{
		JHtmlSidebar::addEntry(
			JText::_('DIGICOM_DASHBOARD'),
			'index.php?option=com_digicom',
			$vName == 'digicomadmin'
		);
		/*JHtmlSidebar::addEntry(
			JText::_('VIEWTREESETTINGMANAGER'),
			'index.php?option=com_digicom&controller=configs',
			$vName == 'configs'
		);*/
		
		JHtmlSidebar::addEntry(
			JText::_('VIEWTREECATEGORIES'),
			'index.php?option=com_digicom&controller=categories',
			$vName == 'categories'
		);
		
		JHtmlSidebar::addEntry(
			JText::_('VIEWTREEPRODUCTS'),
			'index.php?option=com_digicom&controller=products',
			$vName == 'products'
		);
		JHtmlSidebar::addEntry(
			JText::_('VIEWTREEFILEMANAGER'),
			'index.php?option=com_digicom&controller=filemanager',
			$vName == 'filemanager'
		);
		JHtmlSidebar::addEntry(
			JText::_('VIEWTREECUSTOMERS'),
			'index.php?option=com_digicom&controller=customers',
			$vName == 'customers'
		);
		JHtmlSidebar::addEntry(
			JText::_('VIEWTREEORDERS'),
			'index.php?option=com_digicom&controller=orders',
			$vName == 'orders'
		);
		/*
		JHtmlSidebar::addEntry(
			JText::_('VIEWTREELICENCES'),
			'index.php?option=com_digicom&controller=licenses',
			$vName == 'licenses'
		);
		
		JHtmlSidebar::addEntry(
			JText::_('VIEWTREEATTR'),
			'index.php?option=com_digicom&controller=attributes',
			$vName == 'attributes'
		);
		*/
		JHtmlSidebar::addEntry(
			JText::_('VIEWTREEPROMO'),
			'index.php?option=com_digicom&controller=promos',
			$vName == 'promos'
		);
		/*
		JHtmlSidebar::addEntry(
			JText::_('VIEWTREESUBSCRIP'),
			'index.php?option=com_digicom&controller=subscription',
			$vName == 'subscription'
		);
		JHtmlSidebar::addEntry(
			JText::_('VIEWTREETAX'),
			'index.php?option=com_digicom&controller=tax',
			$vName == 'logs'
		);
		*/
		JHtmlSidebar::addEntry(
			JText::_('VIEWTREEPSYSTEMEMAILS'),
			'index.php?option=com_digicom&controller=email',
			$vName == 'email'
		);
		/*
		JHtmlSidebar::addEntry(
			JText::_('VIEWTREEEMAILREMINDER'),
			'index.php?option=com_digicom&controller=emailreminders',
			$vName == 'emailreminders'
		);
		*/
		JHtmlSidebar::addEntry(
			JText::_('VIEWTREESTATS'),
			'index.php?option=com_digicom&controller=stats',
			$vName == 'stats'
		);
		
		/*JHtmlSidebar::addEntry(
			JText::_('VIEWTREELOGS'),
			'index.php?option=com_digicom&controller=logs',
			$vName == 'logs'
		);*/
		JHtmlSidebar::addEntry(
			JText::_('VIEWTREEABOUT'),
			'index.php?option=com_digicom&controller=about',
			$vName == 'about'
		);
		
		JHtmlSidebar::addEntry(
			JText::_('COM_DIGICOM_TOGGLE_SIDEBAR'),
			'#togglesidebar'
		);
		
	}
	
	public static function renderSidebar(){
		// Collect display data
		$data                 = new stdClass;
		$data->list           = JHtmlSidebar::getEntries();
		$data->filters        = JHtmlSidebar::getFilters();
		$data->action         = JHtmlSidebar::getAction();
		$data->displayMenu    = count($data->list);
		$data->displayFilters = count($data->filters);
		$data->hide           = JFactory::getApplication()->input->getBool('hidemainmenu');

		// Create a layout object and ask it to render the sidebar
		$layout      = new JLayoutFile('sidebars.submenu');
		$sidebarHtml = $layout->render($data);

		return $sidebarHtml;
		
	}
	
	public static function getListPlugins() {
		$results = array();
		$dsplugins = JPluginHelper::getPlugin('digicompayment'); 
		//dsdebug($dsplugins);
		foreach($dsplugins as $dsplugin) {

			$parameters = new JRegistry($dsplugin->params);
			$label = $parameters->get($dsplugin->name.'_label');

			$plugin = new stdClass();
			$plugin->name = $label;
			$results[] = $plugin;
		}
		return $results;
	}

	// DigiComAdminHelper::getListPluginCurrency();
	public static function getListPluginCurrency(){
		$dsplugins = JPluginHelper::getPlugin('digicompayment'); 
		$results = array();

		foreach($dsplugins as $dsplugin) {
			$path = JPATH_PLUGINS.DS.'digicompayment'.DS.$dsplugin->name.'.xml';
			$parameters = new JRegistry($dsplugin->params);
			$label = $parameters->get($dsplugin->name.'_label');

			if (file_exists($path)) {
				$plugin_xml = simplexml_load_file($path);
				$currencylist = $plugin_xml->xpath("config/fields/fieldset/field[@name='".$dsplugin->name."_currency']/option");
				if(isset($currencylist) && is_array($currencylist) && count($currencylist) > 0){
					foreach($currencylist as $key => $value) {
						 $description = ((string)$value);
						 $currency = ((string)$value->attributes());
						 $results[$label][$currency] = $description;
					}
				}
			}
		}
		return $results;
	}


/**
  Build the select list for parent menu item
 */
 	public static function getParent(&$row){
		$db = JFactory::getDBO();
		$where = array();
		if($row->id){
			$where[] = ' id != '.(int) $row->id;
		}
		else{
			$id = null;
		}

		// In case the parent was null
		if(!$row->parent_id){
			$row->parent_id = 0;
		}

		$query = 'SELECT m.*' .
				' FROM #__digicom_categories m' .
				(count($where) > 0?" where ".implode(" and ", $where):"") .
				' ORDER BY parent_id, ordering';
		$db->setQuery( $query );
		$citems = $db->loadObjectList();

		$children = array();

		$children = array();
		if($citems){
			foreach($citems as $v){
				$pt	= $v->parent_id;
				$list = @$children[$pt] ? $children[$pt] : array();
				array_push( $list, $v );
				$children[$pt] = $list;
			}
		}

		foreach($children as $i => $v){
			foreach($children[$i] as $j => $vv){
				$children[$i][$j]->parent = $vv->parent_id;
			}
		}

		$last_items = array();
		$id_level = array();

		if(isset($children) && count($children) > 0){
			foreach($children as $key=>$value){
				if(is_array($value) && count($value) > 0){
					foreach($value as $key=>$item){
						if($item->parent_id == 0){
							$id_level[$item->id] = 0;
							$item->level = 0;
							$last_items[] = $item;
						}
						else{
							$level = intval($id_level[$item->parent_id]) + 1;
							$id_level[$item->id] = $level;
							$item->level = $level;
							$last_items[] = $item;
						}
					}
				}
			}
		}
		$list = $last_items;

		$mitems = array();
		$msg = JText::_('HELPERTOP');
		$mitems[] = JHTML::_('select.option', 0, $msg );

		foreach($list as $item){
			$name = str_repeat(' - ', $item->level).$item->name;
			$mitems[] = JHTML::_('select.option',  $item->id, "&nbsp;&nbsp;".$name);
		}
		$output = JHTML::_('select.genericlist',  $mitems, 'parent_id', 'class="inputbox" size="10"', 'value', 'text', $row->parent_id );
		return $output;
	}

	public static function getCatListProd(&$row, $citems, $selector = 0, $selected = 0)
	{
		jimport('joomla.html.html.menu');
		$id = '';
		$children = array();

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
		$app = JFactory::getApplication('administrator');
		$option = 'com_digicom';
		$limistart = $app->getUserStateFromRequest($option.'.list.start', 'limitstart');
		$limit = $app->getUserStateFromRequest($option.'.list.limit', 'limit', $app->getCfg('list_limit'));
		if($limit != "0"){
			$categories = array_slice($categories, $limistart, $limit);
		}

		$mitems = array();
		$msg = JText::_('HELPERCATSEL');
		$mitems[] = JHTML::_('select.option','', $msg );
		foreach($categories as $item){
			$mitems[] = JHTML::_('select.option',  $item->id, "&nbsp;&nbsp;".$item->treename);
		}

		if(!$selector){
			$output = JHTML::_('select.genericlist',  $mitems, 'catid', 'class="inputbox"', 'value', 'text', $row->selection);
		}
		else{ 
			$output = JHTML::_('select.genericlist',  $mitems, 'catid', 'class="inputbox" onchange="window.location=\'index.php?option=com_digicom&controller=products&prc=\'+this.value" ', 'value', 'text', $selected);
		}

		return $output;
	}
	
	public static function getCatListProd2(&$row, $citems, $selector = 0, $selected = 0)
	{
		$db = JFactory::getDBO();
		$query = 'SELECT 
						`id`,
						`parent_id` AS `parent`,
						`parent_id`,
						`name`,
						`name` as `title`
					FROM
						`#__digicom_categories`
					WHERE
						`published` = 1
					ORDER BY `ordering`';
		$db->setQuery($query);
		$mitems = $db->loadObjectList();
//		echo '<Pre>'.print_r($mitems, true).'<pre>';

		$children = array();
		if ($mitems)
		{
			foreach ($mitems as $v)
			{
				$v->title 		= $v->name;
				$v->parent_id 	= $v->parent;
				$pt = $v->parent;
				$list = @$children[$pt] ? $children[$pt] : array();
				array_push($list, $v);
				$children[$pt] = $list;
			}
		}
		$list = JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);
//		echo '<pre>'.print_r($list, true).'<pre>';exit();
		$msg = JText::_('HELPERCATSEL');
		$t = new stdClass();
		$t->id= '';
		$t->treename = $msg;
		$t->title = $msg;
		$list = array_merge(array($t),$list);
//		$mitems[] = JHTML::_('select.option','', $msg );
		if(!$selector){
			$output = JHTML::_('select.genericlist',  $list, 'catid', 'class="inputbox"', 'id', 'treename', $row->catid);
		}
		else{ 
			$output = JHTML::_('select.genericlist',  $list, 'catid', 'class="inputbox" onchange="document.adminForm.submit();"', 'id', 'treename', $selected);
		}
		return $output;
	}

	public static function getCatAndProductToLisenceIdHtml($citems, $options, $selected){
		$id = '';
		$children = array();
		if($citems){
			foreach($citems as $v){
				$pt	= $v->parent_id;
				$list = @$children[$pt] ? $children[$pt] : array();
				array_push( $list, $v );
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

		$app = JFactory::getApplication('administrator');
		$option = 'com_digicom';
		$limistart = $app->getUserStateFromRequest($option.'.list.start', 'limitstart');
		$limit = $app->getUserStateFromRequest($option.'.list.limit', 'limit', $app->getCfg('list_limit'));
		if($limit != "0"){
			$categories = array_slice($categories, $limistart, $limit);
		}

		$mitems = array();
		$msg = JText::_('HELPERCATSEL');
		$mitems[] = JHTML::_('select.option', 0, $msg);

		foreach($categories as $item){
			$mitems[] = JHTML::_('select.option',  $item->id, "&nbsp;&nbsp;".$item->treename);
		}

		$output = JHTML::_('select.genericlist',  $mitems, 'filter_prod', 'class="inputbox" onchange="document.adminForm.task.value=\'\'; changeCategory(this.value);" ', 'value', 'text', $selected);

		return $output;
	}

	public static function getSelectCatListProd( &$row, $citems, $selector = 0, $selected = 0)
	{
		$type = JRequest::getVar('type','');
		$id = '';

		// establish the hierarchy of the menu
		$children = array();

		if ( $citems ) {
			// first pass - collect children
			foreach ( $citems as $v ) {
				if(trim($v->name) != ""){
					$v->title = $v->name;
				}
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
		// second pass - get an indent list of the items


		$list = JHTML::_('menu.treerecurse', 0, "&nbsp;", array(), $children, 20, 0, 0);

		// assemble menu items to the array
		$mitems 	= array();
		$msg = JText::_('HELPERCATSEL');


				$mitems[]	   = JHTML::_('select.option',  $msg );//mosHTML::makeOption( '-1', $msg, 'id', 'name' );
		 	//= mosHTML::makeOption( '0', 'Top' );

		foreach ( $list as $item ) {
			$mitems[] = JHTML::_('select.option',  $item->id, "&nbsp;&nbsp;".$item->treename );//mosHTML::makeOption( $item->id, '&nbsp;&nbsp;&nbsp;'. $item->treename,'id', 'name' );
		}
		if (!$selector) :
			$output = JHTML::_('select.genericlist',  $mitems, 'catid[]', 'class="inputbox" size="10" multiple ', 'value', 'text', $row->selection);
		else :
			$domid = JRequest::getVar('id',0);
			$search = JRequest::getVar('search','');
			$output = JHTML::_('select.genericlist',  $mitems, 'catid[]', 'class="inputbox" onchange="window.location=\'index.php?option=com_digicom&controller=products&task=selectProducts&id='.$domid.'&search='.$search.'&type='.$type.'&tmpl=component&prc=\'+this.value" ', 'value', 'text', $selected);
		endif;

		return $output;
	}


	public static function getSelectCatListProdInclude( &$row, $citems, $selector = 0, $selected = 0) {

		$id = '';

		// establish the hierarchy of the menu
		$children = array();

		if ( $citems ) {
			// first pass - collect children
			foreach ( $citems as $v ) {
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
		// second pass - get an indent list of the items
		//print_r($children);die;

		$list = JHTML::_('menu.treerecurse', 0, "&nbsp;", array(), $children, 20, 0, 0);

		// assemble menu items to the array
		$mitems 	= array();
		$msg = JText::_('HELPERCATSEL');


				$mitems[]	   = JHTML::_('select.option',  $msg );//mosHTML::makeOption( '-1', $msg, 'id', 'name' );
		 	//= mosHTML::makeOption( '0', 'Top' );

		foreach ( $list as $item ) {
			$mitems[] = JHTML::_('select.option',  $item->id, "&nbsp;&nbsp;".$item->treename );//mosHTML::makeOption( $item->id, '&nbsp;&nbsp;&nbsp;'. $item->treename,'id', 'name' );
		}
		if (!$selector)
			$output = JHTML::_('select.genericlist',  $mitems, 'catid[]', 'class="inputbox" size="10" multiple ', 'value', 'text', $row->selection);
		else{
			$id = JRequest::getVar("id", "0");
			$output = JHTML::_('select.genericlist',  $mitems, 'catid', 'class="inputbox" onchange="window.location=\'index.php?option=com_digicom&controller=products&task=selectProductInclude&id='.$id.'&tmpl=component&prc=\'+this.value" ', 'value', 'text', $selected);
		}

		return $output;
	}



	public static function get_topcountries_option ($configs) {
			$db = JFactory::getDBO();
		$sql = "select distinct country from #__digicom_states order by country";
		$db->setQuery($sql);
		$countries = $db->loadObjectList();

		$mitems = array();
		$topcountries = $configs->get('topcountries','');
		foreach ($countries as $country) {

			if ($country != '0') {
				$mitems[] = JHTML::_('select.option',  $country->country, $country->country );
			} else {

			}
		}
		$output = JHTML::_('select.genericlist',  $mitems, 'topcountries[]', 'class="inputbox" size="10" multiple ', 'value', 'text', $topcountries);

		return $output;
	}

	public static function get_country_options ($profile, $ship = false, $configs) {
		$db = JFactory::getDBO();
		$country_word = 'country';
			if ($ship) $country_word = 'ship'.$country_word;
		if (!isset($profile->$country_word)) $profile->$country_word = '';
		$query = "SELECT country"
				. "\n FROM #__digicom_states"
			. "\n GROUP BY country"
			. "\n ORDER BY country ASC";
		$db->setQuery( $query );
		$countries = $db->loadObjectList();


		$country_option = "<select name='".$country_word."' id='".$country_word."' onChange='changeProvince".($ship?'_ship':'')."();'>";
		$country_option .= '<option value="" ';
		if (!$profile->$country_word) $country_option .= 'selected';
		$country_option .= '>'.(JText::_('HELPERCOUNSEL')).'</option>';			 
		$country_option .= '<option value="" ></option>';			   
		$topcountries = $configs->get('topcountries','');

			if (count ($topcountries) > 0) {
			foreach ($topcountries as $topcountry){
				if ($topcountry != '0') {
					$country_option .= '<option value="'.$topcountry.'" ';
					if ( $profile->$country_word == $topcountry && strlen (trim ($topcountry)) > 0) {
							$country_option .= 'selected';  
						}
					$country_option .= ' >'.$topcountry.'</option>';  
				}

			}

		} else {
			$country_option .= '<option value="United-States" ';
			if ( $profile->$country_word == 'United-States') {
				$country_option .= 'selected';  
			}
				$country_option .= ' >United-States</option>';  

				$country_option .= '<option value="Canada" ';
			if ($profile->$country_word == 'Canada') {
				$country_option .= 'selected';  

				$country_option .= '  >Canada</option>';

			}

			}
		$country_option .= '<option value=""  >-------</option>';			   
		foreach( $countries as $country ) {
				if (($country->country != 'United-States' && $country->country != 'Canada' && count($topcountries) < 1) || (count($topcountries) > 0 && !in_array($country->country, $topcountries))) {
					$country_option .= "<option value='" . $country->country ."' ";

				if ($country->country == $profile->$country_word) $country_option .= "selected";

						$country_option .=  " >" . $country->country . "</option>"; 
				}
		}
		$country_option .= "</select>"; 
		return $country_option; 

	}

	public static function get_store_province ($profile, $ship = 0) {
		$db = JFactory::getDBO();
		$province_word = "province";
			if ($ship) $province_word = 'ship'.$province_word;
			//if (isset($configs->get('state',''))){
			if (null !== $profile->state){
		 		$query = "select state FROM #__digicom_states where country='".$profile->country."'";
					$db->setQuery($query);
				$res = $db->loadObjectList();
				$output = '
						<div id="'.$province_word.'">
							<select name="state">';
			foreach ($res as $i => $v ) {
							$output .= '<option value="'.$v->state.'" '; 
						if ($v->state == $profile->state) $output .= 'selected';
				$output .= '>'.$v->state.'</option>';


				}

				$output .= '</select></div>';
		   	} else {
			$output = '<div id="'.$province_word.'">
		 			   <select><option>'.(JText::_('HELPERSELECTCOUNTY')).'</option></select>
					</div>
					';

		   	}
		return $output;

	}

	public static function parseDate($format, $date) {
		$format = explode ("-", $format);
		$date = explode ("-", $date);
		$res = 0;
		if (count($date) == count($format)){
			foreach ($format as $i => $v) {
				switch ($v) {
					case "YYYY":
					case "Y":
						$year = $date[$i];
						break;

					case "MM":
					case "m":
						$month = $date[$i];
						break;

					case "DD":
					case "d":
						$day = $date[$i];
						break;
				}
			} 
		} else {
			$day = 0; $month = 0; $year = 0;
		}

		if ((int )$day > 0 && (int )$month > 0 && (int )$year > 0) {
			$res = mktime (0,0,0, (int)$month, (int)$day, (int)$year);
		} else {
			$res = 0;
		}

		return $res;
	}

	public static function publishAndExpiryHelper(&$img, &$alt, &$times, &$status, $timestart, $timeend, $published, $configs, $limit = 0, $used = 0) {

		$now = time();
		$nullDate = 0;

		if ( $now <= $timestart && $published == "1" ) {
					$img = "tick.png";
					$alt = JText::_('HELPERPUBLISHED');
		} else if ($limit > 0 && $used >= $limit) {
				$img = "publish_r.png";
				$alt = JText::_('HELPERUSEAGEEXPIRED');
		} else if ( ( $now <= $timeend || $timeend == $nullDate ) && $published == "1" ) {
				$img = "tick.png";
				$alt = JText::_('HELPERPUBLISHED');
		} else if ( $now > $timeend && $published == "1" && $timeend != $nullDate) {
				$img = "publish_r.png";
				$alt = JText::_('HELPEREXPIRED');
		} elseif ( $published == "0" ) {
				$img = "publish_x.png";
				$alt = JText::_('HELPERUNPUBLICHED');
		}
		$times = '';

		if (isset( $timestart)) {
			if ( $timestart == $nullDate) {
					$times .= "<tr><td>".(JText::_("HELPERALWAWSPUB"))."</td></tr>";
				} else {
					$times .= "<tr><td>".(JText::_("HELPERSTARTAT"))." ".date($configs->get('time_format','DD-MM-YYYY'), $timestart)."</td></tr>";
				}
		}

		if ( isset( $timeend ) ) {
			if ( $timeend == $nullDate) {
				$times .= "<tr><td>".(JText::_("HELPERNEVEREXP"))."</td></tr>";
			} else {
				$times .= "<tr><td>".(JText::_("HELPEXPAT"))." ".date($configs->get('time_format','DD-MM-YYYY'), $timeend)."</td></tr>";
			}
		}

		$status = '';
		$promo = new stdClass();
		if (!isset ($promo->codelimit)) {
			$promo->codelimit = 0;
		}
		if (!isset ($promo->used)) {
			$promo->used = 0;
		}

		$remain = $promo->codelimit - $promo->used;
		if (($timeend > $now || $timeend == $nullDate )&& ($limit == 0 || $used < $limit) && $published == "1") {
			$status = JText::_("HELPERACTIVE");
		} else if ($published == "0") {
			$status = "<span style='color:red'>".(JText::_("HELPERUNPUBLISHED"))." </span>";
		} else if ($limit >0  && $used  >= $limit) {
			$status = "<span style='color:red'>".(JText::_("HELPEREXPIRE")).": (".(JText::_("Amount")).")</span>";
		} else if ($timeend != $nullDate && $timeend < $now && ($remain < 1 && $promo->codelimit > 0)) {
			$status = "<span style='color:red'>".(JText::_("HELPEREXPIRE")).": (".(JText::_("Date"))." ,".(JText::_("Amount")).")</span>";
		} else if ($timeend < $now && $timeend != $nullDate){
			$status = "<span style='color:red'>".(JText::_("HELPEREXPIRE")).": (".(JText::_("Date")).")</span>";
		} else {
			$status = "<span style='color:red'>".(JText::_("HELPERPROMOERROR"))."</span>";
		}
	}

	public static function format_price ($amount, $ccode, $add_sym = true, $configs) {

		$db = JFactory::getDBO();

		$code = 0;

		$price_format = '%'.$configs->get('totaldigits','5').'.'.$configs->get('decimaldigits','2').'f';
		$res =  sprintf($price_format,$amount) ;//. " " . $tax['currency'] . '<br>';

		if ($add_sym) {
			if ($configs->get('currency_position','1'))
				$res = $res . " " . $ccode;
			else
				$res = $ccode. " " . $res;
		}

		return $res; 
	}

	public static function get_currency($ccode)
	{
		$db = JFactory::getDBO();

		$sql = "select id, csym
				from #__digicom_currency_symbols
				where ccode='".strtoupper($ccode)."'";
		$db->setQuery($sql);
		$codea = $db->loadObjectList();
		$ccode = $codea[0]->csym;
		$ccode = explode (",", $ccode);
		foreach ($ccode as $i => $code)
		{
			return "&#".trim($code);
		}
	}

	public static function cleanUpImageFolders($root, $folders) {

		foreach ($folders as $i => $folder) {
			$x = explode (myDC, $folder);
			if (trim($x[0]) == $root) unset($x[0]);

			$folders[$i] = implode(myDC, $x);

		}
		return $folders;
	}

	public static function getImageFolderList($directory = "images", &$folders) {

		jimport( 'joomla.filesystem.folder' );
		$imageFolders = JFolder::folders( JPATH_SITE.myDC.$directory );
		foreach ( $imageFolders as $folder ) {
			$folders[] = $directory.myDC.$folder;//JHTML::_('select.option',  $directory.myDC.$folder );
			if ($folder != myDC && $folder != "\\" && $folder != "/")
				DigiComAdminHelper::getImageFolderList($directory.myDC.$folder, $folders);
		}
		return $folders;
	}

	public static function getFolderImageList($root = "", $folders = array()) {

		foreach ($folders as $folder) {
			$folder_path = JPath::clean( JPATH_SITE  . DS . $root . DS . $folder );
			$imageFiles = JFolder::files( $folder_path , "\.bmp$|\.gif$|\.jpg$|\.png$");
			$images[$folder] = array(  JHTML::_('select.option',  '', '- '. JText::_( 'Select Image' ) .' -' ) );
			foreach ( $imageFiles as $file ) {
				$images[$folder][] = JHTML::_('select.option',  $file );
			}
		}

		return $images;
	}

	public static function check_fields($fields, &$totalfields, &$optlen, &$select_only, $maxfields, $pid) {

		$maxfields = 0;

		$totalfields = count ($fields);
		if (count($fields) > $maxfields) $maxfields = count($fields);

			if (count($fields)) {
				foreach ($fields as $j => $field){

				if ($field->published == 0) continue;
					$optlen[$j] = strlen (JText::_("DSSELECT"));
					//$optlen0 = $optlen[$pid][$j];
					$long_value_found = 0;
					//$field = $field[0];
					if ($optlen[$j] < strlen (JText::_("DSSELECT").$field->name)) $optlen[$j] = strlen (JText::_("DSSELECT").$field->name);

				$opt = explode ("\n", $field->options);
					foreach ($opt as $v) {
					if ($optlen[$j] < strlen($v)) {
						$optlen[$j] = strlen($v);
						$long_value_found = 1;
					}
					}

				if ($long_value_found) {
					$select_only[$j] = 0;
				} else {
						$optlen[$j] = strlen (JText::_("DSSELECT"));
					$select_only[$pid][$j] = 1;
				}
				if (isset($field->size) && $field->size > 0) $optlen[$j] = $field->size/9;

				}
			}
		return $maxfields;
	}


	public static function getLiveSite() {

		// Check if a bypass url was set
		$config 	= JFactory::getConfig();
		$live_site 	= $config->get('live_site');

		// Determine if the request was over SSL (HTTPS)
		if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off')) {
			$https = 's://';
		} else {
			$https = '://';
		}

		$subdom = $_SERVER['PHP_SELF']	;
		$subdom = explode ("/", $subdom);
		$res = array();
		foreach ($subdom as $i => $v) {
			if (strtolower(trim($v)) != "index.php") $res[] = trim($v);
			else break;
		}
		$subdom = implode ("/", $res);
		/*
		* Since we are assigning the URI from the server variables, we first need
		* to determine if we are running on apache or IIS.  If PHP_SELF and REQUEST_URI
		* are present, we will assume we are running on apache.
		*/
		if (!empty ($_SERVER['PHP_SELF']) && !empty ($_SERVER['REQUEST_URI'])) {

			/*
			 * To build the entire URI we need to prepend the protocol, and the http host
			 * to the URI string.
			 */
			if (!empty($live_site)) {
				$theURI = $live_site;// . $_SERVER['REQUEST_URI'];
			} else {
				$theURI = 'http' . $https . $_SERVER['HTTP_HOST'] . $subdom;// . $_SERVER['REQUEST_URI'];
			}

		/*
		* Since we do not have REQUEST_URI to work with, we will assume we are
		* running on IIS and will therefore need to work some magic with the SCRIPT_NAME and
		* QUERY_STRING environment variables.
		*/
		} else {
			// IIS uses the SCRIPT_NAME variable instead of a REQUEST_URI variable... thanks, MS
			if (!empty($live_site)) {
					$theURI = $live_site . $_SERVER['SCRIPT_NAME'];
			} else {
					$theURI = 'http' . $https . $_SERVER['HTTP_HOST'] . $subdom;//. $_SERVER['SCRIPT_NAME'];
			}

			// If the query string exists append it to the URI string
			if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
//					$theURI .= '?' . $_SERVER['QUERY_STRING'];
			}
		}

		return $theURI;
	}

	public static function get_tax_country_options ($profile, $config = false, $configs) {

		$db = JFactory::getDBO();
		$ship = false;
		$country_word = 'country';
			if ($ship) $country_word = 'ship'.$country_word;
		if ($config) $country_word = 'tax_'.$country_word;
		if (!isset($profile->$country_word)) $profile->$country_word = '';
		$query = "SELECT country"
				. "\n FROM #__digicom_states"
			. "\n GROUP BY country"
			. "\n ORDER BY country ASC";
		$db->setQuery( $query );
		$countries = $db->loadObjectList();


		$country_option = "<select name='".$country_word."' id='".$country_word."' onChange='changeProvince".($ship?'_ship':'').($config?'_configstax':'')."();'>";
		$country_option .= '<option value="All" ';
		if (!$profile->$country_word) $country_option .= 'selected';
		$country_option .= '>'.(JText::_('DSALL')).'</option>';			 
		$country_option .= '<option value="" ></option>';			   
		$topcountries = $configs->get('topcountries','');

			if (count ($topcountries) > 0) {
			foreach ($topcountries as $topcountry){
				if ($topcountry != '0') {
					$country_option .= '<option value="'.$topcountry.'" ';
					if ( $profile->$country_word == $topcountry && strlen (trim ($topcountry)) > 0) {
							$country_option .= 'selected';  
						}
					$country_option .= ' >'.$topcountry.'</option>';  
				}

			}

		} else {
			$country_option .= '<option value="United-States" ';
			if ( $profile->$country_word == 'United-States') {
				$country_option .= 'selected';  
			}
				$country_option .= ' >United-States</option>';  

				$country_option .= '<option value="Canada" ';
			if ($profile->$country_word == 'Canada') {
				$country_option .= 'selected';  
				$country_option .= '  >Canada</option>';
			}

		}
		$country_option .= '<option value=""  >-------</option>';			   
		foreach( $countries as $country ) {
				if (($country->country != 'United-States' && $country->country != 'Canada' && count($topcountries) < 1) || (count($topcountries) > 0 && !in_array($country->country, $topcountries))) {
					$country_option .= "<option value='" . $country->country ."' ";

				if ($country->country == $profile->$country_word) $country_option .= "selected";

						$country_option .=  " >" . $country->country . "</option>"; 
				}
		}
		$country_option .= "</select>"; 
		return $country_option; 

	}

	public static function get_tax_province ($configs, $config = 0) {

		$db = JFactory::getDBO();
		$ship = false;
		$province_word = "province";
			if ($ship) $province_word = 'ship'.$province_word;
		if ($config != 0) { $configs_word = "tax_"; $province_word = 'configs'.$province_word;} else $configs_word = "";
			//if (isset($configs->get('state',''))){
			if (null !== $configs->get('state','')){
		 		$query = "select state FROM #__digicom_states where country='".$configs->get('country','')."'";
					$db->setQuery($query);
				$res = $db->loadObjectList();
				$output = '
						<div id="'.$province_word.'">
							<select name="'.$configs_word.'state">
					';
			$output .= '<option value="All" ';
			if (!$configs->get('state','')) $output .= 'selected';
			$output .= '>'.(JText::_('DSALL')).'</option>';		 

			foreach ($res as $i => $v ) {
							$output .= '<option value="'.$v->state.'" '; 
						if ($v->state == $configs->get('state','')) $output .= 'selected';
				$output .= '>'.$v->state.'</option>';


				}

				$output .= '</select></div>';
		} else {
			$output = '<div id="'.$province_word.'">
				   <select name="'.$configs_word.'state"><option value="All" >'.(JText::_('DSALL')).'</option></select>
				</div>
				';
		}
		return $output;
	}

	public static function CreateIndexFile($dir)
	{
		if (file_exists($dir))
		{
			if (!file_exists($dir.DS."index.html"))
			{
				$handle = @fopen($dir.DS."index.html", "w");
				@fwrite($handle, '<html><body bgcolor="#FFFFFF"></body></html>');
				@fclose($handle);
			}
		}
	}

	public static function getDurationType($count, $type) {

		if ($count > 1) {
			if ($type == 0) $result = JText::_('SUBCRUB_DURATION_DOWNLOADS');
			if ($type == 1) $result =  JText::_('SUBCRUB_DURATION_HOURS');
			if ($type == 2) $result =  JText::_('SUBCRUB_DURATION_DAYS');
			if ($type == 3) $result =  JText::_('SUBCRUB_DURATION_MONTHS');
			if ($type == 4) $result =  JText::_('SUBCRUB_DURATION_YEARS');
		} else {
			if ($type == 0) $result = JText::_('SUBCRUB_DURATION_DOWNLOAD');
			if ($type == 1) $result =  JText::_('SUBCRUB_DURATION_HOUR');
			if ($type == 2) $result =  JText::_('SUBCRUB_DURATION_DAY');
			if ($type == 3) $result =  JText::_('SUBCRUB_DURATION_MONTH');
			if ($type == 4) $result = JText::_('SUBCRUB_DURATION_YEAR');
		}

		return $count . " " .$result;
	}

	public static function getRenewalAmountType($type)
	{
		if ($type == 4) return '%';
	}

	public static function getEmailReminderType($type, $calc, $date_calc, $period)
	{
		$value = JText::_("TRIGGER_LABEL");
		$value = str_replace("%number", $type, $value);
		$value = strtolower(str_replace("%period", JText::_("SUBCRUB_DURATION_".strtoupper($period).($type > 1 ? 'S' : '')), $value));
		$value = strtolower(str_replace("%calc", JText::_("TRIGGER_".strtoupper($calc)), $value));
		$value = strtolower(str_replace("%date_calc", JText::_("DATE_CALC_".strtoupper($date_calc)), $value));

		return $value;
	}

	public static function plural( $count, $word, $words )
	{
		if ($count > 1)
			return $words;
		else
			return $word;
	}

	public static function expireUserProduct($user_id)
	{
		jimport( 'joomla.access.access' );

		$db = JFactory::getDBO();

		$count = 0;

		// Get products and their ACL settings

		// Validate
		$sql = "SELECT *
				FROM #__digicom_product_groups";

		$db->setQuery($sql);
		$db->query();
		$rows = $db->loadObjectList();

		$product_group_join = array();
//echo '<pre>' . print_r( $rows, true ) . '</pre>';
		foreach($rows as $row) {
			if(!isset($product_group_join[$row->id_product])){
				$product_group_join[$row->id_product] = array();
			}
			if(!in_array($row->id_group, $product_group_join[$row->id_product])) {
				$product_group_join[$row->id_product][] = $row->id_group;
			}
		}

		// Expiry
		$sql = "SELECT *
				FROM #__digicom_product_groups_exp";

		$db->setQuery($sql);
		$db->query();
		$rows = $db->loadObjectList();

		$product_group_exp = array();

		foreach($rows as $row) {
			if( !isset($product_group_exp[$row->id_product]) || !in_array($row->id_group, $product_group_exp[$row->id_product]) ) {
				$product_group_exp[$row->id_product][] = $row->id_group;
			}
		}

		$sql = "SELECT DISTINCT(`id`)
				FROM #__digicom_products";

		$db->setQuery($sql);
		$db->query();
		$products = $db->loadObjectList();

		$count_valid = 0;
		$count_exp = 0;

		$expire_groups_to_remove = array();
		$expire_groups_to_add = array();

		$groups_to_remove = array();
		$groups_to_add = array();

		foreach($products as $product)
		{
			$sql = "SELECT `productid`
					FROM #__digicom_licenses
					WHERE (`expires` < NOW() OR `published`='0')
					AND `productid` = " . $product->id . "
					AND `userid` = " . $user_id . "
					AND `userid` NOT IN (SELECT `userid` FROM #__digicom_licenses WHERE `expires` > NOW() AND `published`='1' AND `productid` = " . $product->id . ")
					GROUP BY `userid`, `productid`";

			$db->setQuery($sql);
			$db->query();
			$rows = $db->loadObjectList();

			// EXPIRE
			foreach($rows as $row)
			{
				if(isset($product_group_join[$product->id]) && $product_group_join[$product->id]) {
					$expire_groups_to_remove = array_merge($expire_groups_to_remove, $product_group_join[$product->id]);
				}
				if(isset($product_group_exp[$product->id]) && $product_group_exp[$product->id]) {
					$expire_groups_to_add = array_merge($expire_groups_to_add, $product_group_exp[$product->id]);
				}
				$count_exp++;
			}

			$sql = "SELECT `productid`
					FROM #__digicom_licenses
					WHERE `expires` > NOW()
					AND `published` = '1'
					AND `productid` = " . $product->id . "
					AND `userid` = " . $user_id . "
					GROUP BY `userid`, `productid`";

			$db->setQuery($sql);
			$db->query();
			$rows = $db->loadObjectList();

			// VALIDATE
			foreach($rows as $row)
			{
				if(isset($product_group_exp[$product->id]) && $product_group_exp[$product->id]) {
					$groups_to_remove = array_merge($groups_to_remove, $product_group_exp[$product->id]);
				}
				if(isset($product_group_join[$product->id]) && $product_group_join[$product->id]) {
					$groups_to_add = array_merge($groups_to_add, $product_group_join[$product->id]);
				}
				$count_valid++;
			}
		}

		// Ugly quick fix for an issue where you cant immediately remove and add a user to a group

		foreach($expire_groups_to_remove as $group_id) {
			JUserHelper::removeUserFromGroup($user_id, $group_id);
		}

		foreach($expire_groups_to_add as $group_id) {
			if(!in_array($group_id, $groups_to_remove)) {
				JUserHelper::addUserToGroup($user_id, $group_id);
			}
		}

		foreach($groups_to_remove as $group_id) {
			JUserHelper::removeUserFromGroup($user_id, $group_id);
		}

		foreach($groups_to_add as $group_id) {
			JUserHelper::addUserToGroup($user_id, $group_id);
		}
	}
	
	/**
	 * Get latest orders, to use with DS Dashboard
	 * @return unknown
	 */
	public static function getOrders($limit) {
		$db = JFactory::getDBO();
		$sql = '
			SELECT o.*, u.username, c.firstname, c.lastname
			FROM
				#__digicom_orders o,
				#__users u,
				#__digicom_customers c
			WHERE
				`u`.`id`=`o`.`userid` AND
				`c`.`id`=`u`.`id` AND 
				`status` = "Active"
			ORDER BY `o`.`order_date` DESC
			LIMIT '.$limit.'';
		$db->setQuery($sql);
		if (!$orders = $db->loadObjectList()) {
			echo $db->getErrorMsg();
		}
		return $orders;
	}

	/**
	 * Get latest products, to use with DS Dashboard
	 * @return unknown
	 */
	public static function getProducts($limit) {
		$db = JFactory::getDBO();
		$sql = '
			SELECT
				DISTINCT p.id,p.name,p.catid, p.description, p.publish_up,
				c.name AS category
			FROM
				#__digicom_products p,
				#__digicom_categories c
			WHERE
				p.published = 1 AND
				c.published = 1 AND
				p.catid = c.id
			ORDER BY p.id DESC
			LIMIT '.$limit.'
		';
		$db->setQuery($sql);
		if (!$products = $db->loadObjectList()) {
			echo $db->getErrorMsg();
		}
		return $products;
	}
	/**
	 * Get latest products, to use with DS Dashboard
	 * @return unknown
	 */
	public static function getMostSoldProducts($limit) {
		$db = JFactory::getDbo();
		// Create a new query object.

		$query = $db->getQuery(true);
		$query->select( 'SUM('.$db->quoteName('od.quantity') .') as total');
		$query->select($db->quoteName(array('od.productid', 'od.package_type')));
		
		$query->select($db->quoteName(array('p.name','p.price')));

		$query->from($db->quoteName('#__digicom_orders_details').' od');
		$query->from($db->quoteName('#__digicom_products').' p');

		$query->where($db->quoteName('p.id') . '= '. $db->quoteName('od.productid'));

		$date = DigiComAdminHelper::getStartEndDateMonth();
		$startdate_str = $date["0"];
		$enddate_str = $date["1"];

		$query->where($db->quoteName('od.purchase_date') . ' >= '. $db->quote($startdate_str));
		$query->where($db->quoteName('od.purchase_date') . ' < '. $db->quote($enddate_str));

		$query->where($db->quoteName('od.published') . ' = '. $db->quote('1'));

		$query->group($db->quoteName('od.productid'));
		$query->order($db->quoteName('total').' DESC');
		$query->setLimit($limit);

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		
		if (!$products = $db->loadObjectList()) {
			echo $db->getErrorMsg();
		}
		return $products;
	}
	
	public static function getStartEndDateMonth(){
		$return = array();
		$date = new DateTime('now');
		$date->modify('first day of this month');
		$return[] = $date->format('Y-m-d') . ' 00:00:00';

		$date->modify('first day of next month');
		$return[] = $date->format('Y-m-d') . ' 00:00:00';

		return $return;
	}


	/**
	 * Get Top Customers, to use with DS Dashboard
	 * @param unknown $limit
	 */
	public static function getCustomers($limit) {
		$db = JFactory::getDBO();
		$sql = '
			SELECT o.*, u.username, c.firstname, c.lastname
			FROM
				#__digicom_orders o,
				#__users u,
				#__digicom_customers c
			WHERE
				u.id=o.userid AND
				c.id=u.id AND 
				status = "Active"
			GROUP BY o.userid
			LIMIT '.$limit.'
		';
		$db->setQuery($sql);
		if (!$customers = $db->loadObjectList()) {
			echo $db->getErrorMsg();
		}
		return $customers;
	}
	
	public static function updateLicenses()
	{
		$db = JFactory::getDBO();
		$sql = "SELECT count(*) FROM #__digicom_licenses WHERE `purchase_date` <> '0000-00-00 00:00:00'";
		$db->setQuery($sql);
		$db->query();
		$count = $db->loadResult();
		if($count == 0){
			$sql = "SELECT o.`order_date`, l.`id` FROM #__digicom_orders o, #__digicom_licenses l WHERE o.id=l.orderid";
			$db->setQuery($sql);
			$db->query();
			$result = $db->loadAssocList();
			if(isset($result) && count($result) > 0){
				foreach($result as $key=>$value){
					$date_string = date("Y-m-d H:i:s", $value["order_date"]);
					$sql = "UPDATE #__digicom_licenses SET `purchase_date`='".$date_string."' WHERE id=".intval($value["id"]);
					$db->setQuery($sql);
					$db->query();
				}
			}
		}
	}
	
	
	public static function setSidebarRight(){
		
		$input = JFactory::getApplication()->input;
		$tmpl = $input->get('tmpl','');
		$ajax = $input->get('ajax','');
		if($tmpl == 'component' or $ajax =='1') return;
		
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'layouts'.DS.'toolbar'.DS.'sidebar-right.php');
		return true;
	}
}
