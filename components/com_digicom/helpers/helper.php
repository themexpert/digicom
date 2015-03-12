<?php
/**
 * @package            DigiCom Joomla Extension
 * @author            themexpert.com
 * @version            $Revision: 376 $
 * @lastmodified    $LastChangedDate: 2013-10-21 11:54:05 +0200 (Mon, 21 Oct 2013) $
 * @copyright        Copyright (C) 2013 themexpert.com. All rights reserved.
 * @license            GNU/GPLv3
 */

defined( '_JEXEC' ) or die ( "Go away." );

class DigiComHelper {

	public static function get_customer_shipping_add( $uid ) {
		$database = JFactory::getDBO();
		$query    = "select * from #__digicom_customers where id=" . $uid;
		$database->setQuery( $query );
		$info     = $database->loadObject();
		$ship_add = $info->shipfirstname . ' ' . $info->shiplastname . ( ( $info->shipaddress != '' ) ? ', ' . $info->shipaddress : '' ) .
		            ( ( $info->shipcity != '' ) ? ', ' . $info->shipcity : '' ) . ( ( $info->shipstate != '' ) ? ', ' . $info->shipstate : '' ) .
		            ( ( $info->shipprovince != '' ) ? ', ' . $info->shipprovince : '' ) . ( ( $info->shipcountry != '' ) ? ', ' . $info->shipcountry : '' ) .
		            ( ( $info->shipzipcode != '' ) ? '(Zip code: ' . $info->shipzipcode . ' )' : '' );

		return $ship_add;
	}

	public static function createBreacrumbs2() {
		$Itemid      = JRequest::getInt( "Itemid", 0 );
		$mainframe   = JFactory::getApplication();
		$db          = JFactory::getDBO();
		$menu        = $mainframe->getMenu();
		$menu_active = $menu->getActive();


		// Get the PathWay object from the application
		$pw = $mainframe->getPathway();

		$cids = JRequest::getVar( 'cid', 0, '', 'array' );
		$cid  = intval( $cids[0] );
		$pids = JRequest::getVar( 'pid', 0, '', 'array' );
		$pid  = intval( $pids[0] );
		$c    = JRequest::getVar( "controller", "" );
		$t    = JRequest::getVar( "task", "" );

		if ( $c != "digicomLicenses" && $c != "digicomOrders" ) {
			$sql = "SELECT name, parent_id FROM #__digicom_categories WHERE id=" . intval( $cid );
			$db->setQuery( $sql );
			$res = $db->loadObject();
			if ( $res ) {
				$cname     = $res->name;
				$parent_id = $res->parent_id;
				$sql       = "SELECT name FROM #__digicom_products WHERE id=" . intval( $pid );
				$db->setQuery( $sql );
				$pname = $db->loadResult();
			}
		} else {
			$cname = $cid;
			$pname = $pid;
		}

		$bc_added = 0;
		$catsubs  = self::getSubCategoriesId( 0, true );

		if ( $c == "digicomCategories" || $c == "digicomProducts" ) {
			$temp = $cid;
			$ta   = array();
			while ( isset( $catsubs[ $temp ] ) && $temp != 0 ) {
				$link = JRoute::_( "index.php?option=com_digicom&controller=products&task=list&cid=" . $temp . "&Itemid=" . $Itemid );
				$ta[] = array( 'link' => $link, 'name' => $catsubs[ $temp ]->name );
				$temp = $catsubs[ $temp ]->parent_id;
			}

			for ( $index = count( $ta ) - 1; $index >= 0; $index -- ) {
				$pw->addItem( $ta[ $index ]['name'], $ta[ $index ]['link'] );
			}
		}

		if ( $c == "digicomProducts" && $t == 'view' ) {
			$pw->addItem( '', '' );
		}

		if ( $c == "digicomCart" ) {
			$link = JRoute::_( "index.php?option=com_digicom&controller=cart&task=showCart&Itemid=" . $Itemid );
			$name = "Cart";
			$pw->addItem( $name, $link );
			if ( $t == "checkout" ) {
				$link = "";
				$name = "Checkout";
				$pw->addItem( $name, $link );
			}
			$bc_added = 1;
		}

		if ( strlen( trim( $c ) ) > 0 && $bc_added == 0 && $c != "digicomCategories" ) {
			$link = "";
			$name = $c;
//			exit(''.__LINE__);
//			$pw->addItem($name, $link);
		}
	}

	public static function createBreacrumbs() {
		self::createBreacrumbs2();

		return;
		exit( '' . __LINE__ );
		$bradcrumbs = "";
		$db         = JFactory::getDBO();
		$sql        = "select `show_bradcrumbs`, `continue_shopping_url` from #__digicom_settings";
		$db->setQuery( $sql );
		$db->query();
		$result          = $db->loadAssocList();
		$show_bradcrumbs = $result["0"]["show_bradcrumbs"];
		$home_link       = trim( $result["0"]["continue_shopping_url"] ) != "" ? trim( $result["0"]["continue_shopping_url"] ) : "index.php?option=com_digicom&controller=categories";

		if ( $show_bradcrumbs == "0" ) {
			$controller = JRequest::getVar( "controller", "" );
			$task       = JRequest::getVar( "task", "" );
			$position   = JRequest::getVar( "position", "" );
			if ( trim( $controller ) == "" ) {
				$controller = JRequest::getVar( "view", "" );
			}
			if ( $controller == "Products" && trim( $position ) == "" ) {
				$cid = JRequest::getVar( "cid", "0" );
				if ( intval( $cid ) == 0 ) {
					$itemid = JRequest::getVar( "Itemid", "0" );
					$sql    = "select `params` from #__menu where id=" . intval( $itemid );
					$db->setQuery( $sql );
					$db->query();
					$params = $db->loadResult();
					$params = json_decode( $params );
					$cid    = $params->category_id;
				}

				$itemid = JRequest::getVar( "Itemid", "0" );
				$sql    = "select `name` from #__digicom_categories where id=" . intval( $cid );
				$db->setQuery( $sql );
				$db->query();
				$categ_name = $db->loadResult();

// 				$bradcrumbs .= '<div id="pathway">';
// 				$bradcrumbs .= 		'<span class="breadcrumbs pathway">';
// 				$bradcrumbs .= 			'<a class="pathway" href="'.$home_link.'">'.JText::_("DIGI_HOME").'</a>&nbsp;&nbsp;';

				$bradcrumbs .= '<ul class="breadcrumb">';
				$bradcrumbs .= '	<li class="active"><span class="divider icon-location hasTooltip" title="You are here: "></span></li>';
				$bradcrumbs .= '	<li>
										<a href="' . $home_link . '" class="pathway">' . JText::_( "DIGI_HOME" ) . '</a>
										<span class="divider">/</span>
									</li>
								';

				//start - check if this is subcategory
				$sql = "select `parent_id` from #__digicom_categories where id=" . intval( $cid );
				$db->setQuery( $sql );
				$db->query();
				$parent_id        = $db->loadResult();
				$array_bradcrumbs = array();
				while ( $parent_id != "" ) {
					if ( intval( $parent_id ) != "0" ) {
						$sql = "SELECT `name` FROM #__digicom_categories WHERE id=" . intval( $parent_id );
						$db->setQuery( $sql );
						$db->query();
						$parent_cat_name = $db->loadResult();
						//$array_bradcrumbs[] = '<a class="pathway" href="index.php?option=com_digicom&controller=categories&task=view&cid='.$parent_id.'&Itemid='.$itemid.'">'.$parent_cat_name.'</a>&nbsp;&nbsp;';
						$array_bradcrumbs[] = '
							<li>
								<a href="index.php?option=com_digicom&controller=categories&task=view&cid=' . $parent_id . '&Itemid=' . $itemid . '" class="pathway">' . $parent_cat_name . '</a>
									<span class="divider">/</span>
							</li>
						';
					}
					$sql = "SELECT `parent_id` FROM #__digicom_categories WHERE id=" . intval( $parent_id );
					$db->setQuery( $sql );
					$db->query();
					$parent_id = $db->loadResult();
				}
				if ( isset( $array_bradcrumbs ) && count( $array_bradcrumbs ) > 0 ) {
					for ( $i = count( $array_bradcrumbs ) - 1; $i >= 0; $i -- ) {
						$bradcrumbs .= $array_bradcrumbs[ $i ];
					}
				}
				//stop - check if this is subcategory

				if ( $task == "list" ) {
					$bradcrumbs .= $categ_name;
				} else {
					$pid = JRequest::getVar( "pid", "0" );
					$sql = "SELECT `name` from #__digicom_products where id=" . intval( $pid );
					$db->setQuery( $sql );
					$db->query();
					$product_name = $db->loadResult();

// 					$bradcrumbs .= 		'<a class="pathway" href="index.php?option=com_digicom&controller=products&task=list&cid='.$cid.'&Itemid='.$itemid.'">'.$categ_name.'</a>&nbsp;&nbsp;';
					$bradcrumbs .= '
						<li>
							<a href="index.php?option=com_digicom&controller=products&task=list&cid=' . $cid . '&Itemid=' . $itemid . '" class="pathway">' . $categ_name . '</a>
								<span class="divider">/</span>
						</li>
					';
					$bradcrumbs .= $product_name;
				}
// 				$bradcrumbs .= 		'</span>';
// 				$bradcrumbs .= '</div>';
				$bradcrumbs .= '</ul>';
			}
		}
		echo $bradcrumbs;
	}

	public static function getCartItemid() {
		$db  = JFactory::getDBO();
		$sql = "SELECT id FROM #__menu WHERE `alias`='cart' AND `menutype` = 'DigiCom-Menu'";
		$db->setQuery( $sql );
		$db->query();
		$result = $db->loadResult();

		return intval( $result );
	}

	public static function getProductItemid() {
		$db  = JFactory::getDBO();
		$sql = "SELECT id FROM #__menu WHERE `alias`='products' AND `menutype` = 'DigiCom-Menu'";
		$db->setQuery( $sql );
		$db->query();
		$result = $db->loadResult();

		return intval( $result );
	}

	public static function getGallerySimple( $prod, $prodimages, $conf ) {
		$return      = "";
		$gray_size   = $conf->get('prodlayoutlightimgprev','');
		$gray_size_w = $gray_size;
		$gray_size_h = $gray_size;

		if ( count( $prodimages ) > 0 ) {
			$cols = $conf->get('gallery_columns','');
			$i    = 0;
			$return .= '<table>';
			while ( isset( $prodimages[ $i ] ) ) {
				$return .= '<tr>';
				for ( $j = 1; $j <= $cols; $j ++ ) {
					if ( isset( $prodimages[ $i ] ) ) {
						$src = ImageHelper::GetProductThumbImageURLBySize( $prodimages[ $i ], $gray_size );
						$src = str_replace( " ", '%20', $src );

						$size = @getimagesize( $src );

						if ( isset( $size ) ) {
							$gray_size_w = $size["0"] + 100;
							$gray_size_h = $size["1"] + 100;
						}

						$return .= '<td valign="top" style="padding-right:5px;">';
						$return .= '<a onclick="javascript:grayBoxiJoomla(\'index.php?option=com_digicom&controller=products&task=previwimage&tmpl=component&position=' . $i . '&pid=' . intval( $prod->id ) . '\', ' . $gray_size_w . ', ' . $gray_size_h . ')">
											<img class="product_image_gallery" src="' . ImageHelper::GetProductImageURL( $prodimages[ $i ] ) . '"/>
										</a>';
						$return .= '</td>';
					}
					$i ++;
				}
				$return .= '</tr>';
			}
			$return .= '</table>';
		}

		return $return;
	}

	public static function getGalleryScroller( $prod, $prodimages, $conf ) {
		$gray_size   = $conf->get('prodlayoutlightimgprev','');
		$gray_size_w = $gray_size;
		$gray_size_h = $gray_size;
		if ( isset( $prodimages["0"] ) ) {
			$src = ImageHelper::GetProductThumbImageURLBySize( $prodimages["0"], $gray_size );
// 			$size = getimagesize($src);
// 			if(isset($size)){
// 				$gray_size_w = $size["0"]+100;
// 				$gray_size_h = $size["1"]+100;
// 			}
		}

		$return = "";
		$return .= '<table>
						<tr>
						<td>
							<div id="prev" style="float:left; display:none; margin-right: 3px;">
								<a href="#" onclick="prevImage(); return false;"><img alt="prev" src="' . JURI::root() . "components/com_digicom/assets/images/prev.jpg" . '" height="10"></a>
							</div>
						</td>
						<td>
							<div id="bar" style="float:left;">
								<table width="100%">
									<tr>
										<td valign="top">
											<div id="prev_div" style="float:left; margin-right: 2px;">';
		if ( isset( $prodimages["0"] ) ) {
			$return .= '<a onclick="javascript:grayBoxiJoomla(\'index.php?option=com_digicom&controller=products&task=previwimage&tmpl=component&position=0&pid=' . intval( $prod->id ) . '\', ' . $gray_size_w . ', ' . $gray_size_h . ')">
													<img class="product_image_gallery" src="' . ImageHelper::GetProductImageURL( $prodimages["0"] ) . '"/>
												</a>';
		}
		$return .= '</div>
										</td>
										<td valign="top">
											<div id="next_div" style="float:left;">';
		if ( isset( $prodimages["1"] ) ) {
			$src = ImageHelper::GetProductThumbImageURLBySize( $prodimages["1"], $gray_size );
// 			$size = getimagesize($src);
// 			if(isset($size)){
// 				$gray_size_w = $size["0"]+10;
// 				$gray_size_h = $size["1"]+10;
// 			}
			$return .= '<a onclick="javascript:grayBoxiJoomla(\'index.php?option=com_digicom&controller=products&task=previwimage&tmpl=component&position=1&pid=' . intval( $prod->id ) . '\', ' . $gray_size_w . ', ' . $gray_size_h . ')">
													<img src="' . ImageHelper::GetProductImageURL( $prodimages["1"] ) . '"/>
												</a>';
		}
		$return .= '</div>
										</td>
									</tr>
							</table>
						</div>
					</td>
					<td>';
		$display = "";
		if ( count( $prodimages ) < 2 ) {
			$display = "display:none; ";
		}

		$return .= '<div id="next" style="' . $display . 'float:left; margin-left: 3px;">
							<a href="#" onclick="nextImage(); return false;"><img alt="next" src="' . JURI::root() . "components/com_digicom/assets/images/next.jpg" . '" height="12"></a>
						</div>
					</td>
				</tr>
			</table>';

		return $return;
	}

	public static function getSubmitForm( $configs, $processor ) {
		$form          = "";
		$text_redirect = "DSPAYMENT_WITH_PAYPAL";
		$src           = JURI::root() . 'components/com_digicom/assets/images/pleasewait.gif';

		if ( $processor == "payauthorize" ) {
			//$configs_temp   = array();
			//$configs_temp[] = (array) $configs;
			//$configs        = $configs_temp;
			$request        = JRequest::get( "post" );
			$form .= '<form name="digiadminForm" id="digiadminForm" method="post" action="">';
			if ( isset( $request ) && count( $request ) > 0 ) {
				foreach ( $request as $name => $value ) {
					$form .= '<input type="hidden" name="' . $name . '" value="' . $value . '" />';
				}
			}
			$form .= '<input type="hidden" name="hidden_form" value="1" />';
			$form .= '</form>';
			$text_redirect = "DSPAYMENT_WITH_AUTHORIZE";
		}

		$style = "";
		if ( $configs->get("shopping_cart_style",0) == 1 ) { //isolated
			JRequest::setVar( "tmpl", "component" );
			if ( $configs->get('cart_alignment','') != "" ) {
				if ( $configs->get('cart_alignment','') == "0" ) {
					$style .= "float:left; ";
				} elseif ( $configs->get('cart_alignment','') == "1" ) {
					$style .= "margin:auto; ";
				} elseif ( $configs->get("cart_alignment","") == "2" ) {
					$style .= "float:right; ";
				}
			}

			if ( $configs->get("cart_width","") != "" ) {
				$type = $configs->get('cart_width_type','0') == "0" ? "px" : "%";
				$style .= "width:" . $configs->get("cart_width","") . $type . "; ";
			}

			$form .= '<table style="' . $style . '">';
			if ( trim( $configs->get("store_logo","") ) != "" ) {
				$form .= '<tr>
							<td>
								<img src="' . JURI::root() . "images/stories/digicom/store_logo/" . trim( $configs->get("store_logo","") ) . '">
							</td>
						</tr>';
			}
			$form .= ' <tr>
							<td>
								<table id="digi_table" style="' . $style . '">
									<tr>
										<td align="center">
											<span style="font-size:24px;">' . JText::_( $text_redirect ) . '</span>
										</td>
									</tr>
									<tr>
										<td align="center">
											<img src="' . $src . '" border="0" name="submit" alt="PAYPAL">
										</td>
									</tr>
								</table>
							</td>
						</tr>
			';
		} else {
			$form .= '<table style="margin:auto">';
			$form .= '<tr>';
			$form .= '<td align="center">';
			$form .= '<span style="font-size:24px;">' . JText::_( $text_redirect ) . "</span>";
			$form .= '</td>';
			$form .= '</tr>';
			$form .= '<tr>';
			$form .= '<td align="center">';
			$form .= '<img src="' . $src . '" border="0" name="submit" alt="PAYPAL">';
			$form .= '</td>';
			$form .= '<t/r>';
			$form .= '</table>';
		}

		return $form;
	}

	public static function powered_by() {
		$html = '<div style="text-align: center;" class="small">';
		$html .= '<span>Powered by ';
		$html .= '<a target="_blank" title="Sale Digital Products with Joomla" href="http://www.themexpert.com/digicom">';
		$html .= 'DigiCom</a></span>';
		$html .= '</div>';

		return $html;
	}

	function getListPluginCurrency() {

		$dsplugins = JPluginHelper::getPlugin( 'digicompayment' );
		dsdebug( $dsplugins );

		foreach ( $dsplugins as $dsplugin ) {

			$path = JPATH_PLUGINS . DS . $dsplugin->name . DS . $dsplugin->name . '.xml';

			if ( file_exists( $path ) ) {
				$plugin_xml   = simplexml_load_file( $path );
				$currencylist = $plugin_xml->xpath( '/install/params/param(name=paypaypal_currency)' );
				$options      = $plugin_xml->children( 'options' );
				dsdebug( $options );
			}
		}

		die( 'helper getListPluginCurrency stop' );
		/*
		$db = JFactory::getDBO();
		$sql = "select c.*, p.name as pluginame from #__digicom_currencies c, #__digicom_plugins p where p.id=c.pluginid and p.published=1";
		$db->setQuery($sql);
		$plugs = $db->loadObjectList();
		$res = array();
		foreach ($plugs as $plug) {
			$res[$plug->pluginame][$plug->currency_name] = $plug->currency_full;
		}
		return $res;
*/
	}

	/**
	 * Build the select list for parent menu item
	 */
	function getParent( &$row ) {
		$db = JFactory::getDBO();
		// If a not a new item, lets set the menu item id
		$where = array();
		if ( $row->id ) {
			$where[] = ' id != ' . (int) $row->id;
		} else {
			$id = null;
		}

		// In case the parent was null
		if ( ! $row->parent_id ) {
			$row->parent_id = 0;
		}

		// get a list of the menu items
		// excluding the current menu item and its child elements
		$query = 'SELECT m.*' .
		         ' FROM #__digicom_categories m' .
		         ( count( $where ) > 0 ? " where " . implode( " and ", $where ) : "" ) .
		         ' ORDER BY parent_id, ordering';
		$db->setQuery( $query );
		$mitems = $db->loadObjectList();
		// establish the hierarchy of the menu
		$children = array();

		if ( $mitems ) {
			// first pass - collect children
			foreach ( $mitems as $v ) {
				$v->parent = $v->parent_id;
				$pt        = $v->parent_id;
				$list      = @$children[ $pt ] ? $children[ $pt ] : array();
				array_push( $list, $v );
				$children[ $pt ] = $list;
			}
		}


		// second pass - get an indent list of the items
		$list = JHTML::_( 'menu.treerecurse', 0, '', array(), $children, 9999, 0, 0 );
		// assemble menu items to the array
		$mitems   = array();
		$mitems[] = JHTML::_( 'select.option', '0', JText::_( 'DSTOP' ) );
		foreach ( $list as $item ) {
			$mitems[] = JHTML::_( 'select.option', $item->id, '&nbsp;&nbsp;&nbsp;' . $item->treename );
		}

		$output = JHTML::_( 'select.genericlist', $mitems, 'parent_id', 'class="inputbox" size="10"', 'value', 'text', $row->parent_id );

		return $output;
	}

	function getCatListProd( &$row, $citems ) {

		$id = '';

		// establish the hierarchy of the menu
		$children = array();

		if ( $citems ) {
			// first pass - collect children
			foreach ( $citems as $v ) {
				$pt   = $v->parent_id;
				$list = @$children[ $pt ] ? $children[ $pt ] : array();
				array_push( $list, $v );
				$children[ $pt ] = $list;
			}
		}
		foreach ( $children as $i => $v ) {
			foreach ( $children[ $i ] as $j => $vv ) {
				$children[ $i ][ $j ]->parent = $vv->parent_id;
			}
		}
		// second pass - get an indent list of the items


		$list = JHTML::_( 'menu.treerecurse', 0, "&nbsp;", array(), $children, 20, 0, 0 );

		// assemble menu items to the array
		$mitems = array();
		$msg    = JText::_( 'DSSELECTCAT' );


		$mitems[] = JHTML::_( 'select.option', $msg );//mosHTML::makeOption( '-1', $msg, 'id', 'name' );
		//= mosHTML::makeOption( '0', 'Top' );

		foreach ( $list as $item ) {
			$mitems[] = JHTML::_( 'select.option', $item->id, "&nbsp;&nbsp;&nbsp;" . $item->treename );//mosHTML::makeOption( $item->id, '&nbsp;&nbsp;&nbsp;'. $item->treename,'id', 'name' );
		}
		$output = JHTML::_( 'select.genericlist', $mitems, 'catid[]', 'class="inputbox" size="10" multiple ', 'value', 'text', $row->selection );


		return $output;
	}

	function get_topcountries_option( $configs ) {
		$db  = JFactory::getDBO();
		$sql = "SELECT DISTINCT country FROM #__digicom_states ORDER BY country";
		$db->setQuery( $sql );
		$countries = $db->loadObjectList();

		$mitems       = array();
		$topcountries = $configs->get("topcountries","");

		foreach ( $countries as $country ) {
			if ( $country != '0' ) {
				$mitems[] = JHTML::_( 'select.option', $country->country, $country->country );
			} else {

			}
		}
		$output = JHTML::_( 'select.genericlist', $mitems, 'topcountries[]', 'class="inputbox" size="10" multiple ', 'value', 'text', $topcountries );

		return $output;
	}

	public static function get_country_options( $profile, $ship = false, $configs ) {

		$db           = JFactory::getDBO();
		$country_word = 'country';
		if ( ! $profile ) {
			$profile = new stdClass();
		}
		if ( $ship ) {
			$country_word = 'ship' . $country_word;
		}
		if ( ! isset( $profile->$country_word ) ) {
			$profile->$country_word = '';
		}
		$query = "SELECT country"
		         . "\n FROM #__digicom_states"
		         . "\n GROUP BY country"
		         . "\n ORDER BY country ASC";
		$db->setQuery( $query );
		$countries = $db->loadObjectList();


		$country_option = "<select name='" . $country_word . "' id='" . $country_word . "' onChange='changeProvince" . ( $ship ? '_ship' : '' ) . "();' style='width:15.5em;'>";
		$country_option .= '<option value="" ';
		if ( ! $profile->$country_word ) {
			$country_option .= 'selected';
		}
		$country_option .= '>' . ( JText::_( 'DSSELECTCOUNTRY' ) ) . '</option>';
		$country_option .= '<option value="" ></option>';
		$topcountries = $configs->get('topcountries','');

		if ( count( $topcountries ) > 0 ) {

			foreach ( $topcountries as $topcountry ) {
				if ( $topcountry != '0' ) {
					$country_option .= '<option value="' . $topcountry . '" ';
					if ( $profile->$country_word == $topcountry && strlen( trim( $topcountry ) ) > 0 ) {
						$country_option .= 'selected';
					}
					$country_option .= ' >' . $topcountry . '</option>';
				}
			}

		} else {

			$country_option .= '<option value="United-States" ';
			if ( $profile->$country_word == 'United-States' ) {
				$country_option .= 'selected';
			}
			$country_option .= ' >United-States</option>';

			$country_option .= '<option value="Canada" ';
			if ( $profile->$country_word == 'Canada' ) {
				$country_option .= 'selected';

				$country_option .= '  >Canada</option>';

			}

		}

		$country_option .= '<option value=""  >-------</option>';
		foreach ( $countries as $country ) {
			if ( ( $country->country != 'United-States' && $country->country != 'Canada' && count( $topcountries ) < 1 ) || ( count( $topcountries ) > 0 && ! in_array( $country->country, $topcountries ) ) ) {
				$country_option .= "<option value='" . $country->country . "' ";

				if ( $country->country == $profile->$country_word ) {
					$country_option .= "selected";
				}

				$country_option .= " >" . $country->country . "</option>";
			}
		}
		$country_option .= "</select>";

		return $country_option;

	}

	public static function get_store_province( $custommer, $ship = 0 ) {
		$db            = JFactory::getDBO();
		$province_word = "province";
		$state_word    = "state";
		$shipword      = '';
		if ( $ship ) {
			$province_word = 'ship' . $province_word;
			$shipword      = "ship";
			$state_word    = "ship" . $state_word;
		}
		if ($custommer->state) {
			$query = "SELECT state FROM #__digicom_states WHERE country='" . $custommer->country . "' order by `state`";
			$db->setQuery( $query );
			$res    = $db->loadObjectList();
			$output = '
						<div id="' . $province_word . '">
							<select name="' . $state_word . '" id="' . $shipword . 'sel_province" style="width:15.5em;">';
			foreach ( $res as $i => $v ) {
				$output .= '<option value="' . $v->state . '" ';
				if ( $v->state == $custommer->state ) {
					$output .= 'selected';
				}
				$output .= '>' . $v->state . '</option>';


			}

			$output .= '</select></div>';
		} else {
			$output = '<div id="' . $province_word . '">
		 			   <select style="width:15.5em;"><option>' . ( JText::_( 'DSSELECTCOUNTRYFIRST' ) ) . '</option></select>
					</div>
					';

		}

		return $output;

	}


	public static function check_fields( $fields, &$totalfields, &$optlen, &$select_only, $maxfields, $pid ) {
		$maxfields = 0;


		$totalfields = count( $fields );
		if ( count( $fields ) > $maxfields ) {
			$maxfields = count( $fields );
		}
		if ( count( $fields ) ) {
			foreach ( $fields as $j => $field ) {
				if ( $field->publishing == 0 ) {
					continue;
				}
				$optlen[ $j ] = strlen( JText::_( "DSSELECT" ) );
				//$optlen0 = $optlen[$pid][$j];
				$long_value_found = 0;
				//$field = $field[0];
				if ( $optlen[ $j ] < strlen( JText::_( "DSSELECT" ) . $field->name ) ) {
					$optlen[ $j ] = strlen( JText::_( "DSSELECT" ) . $field->name );
				}

				$opt = explode( "\n", $field->options );
				foreach ( $opt as $v ) {
					if ( $optlen[ $j ] < strlen( $v ) ) {
						$optlen[ $j ]     = strlen( $v );
						$long_value_found = 1;
					}
				}

				if ( $long_value_found ) {
					$select_only[ $j ] = 0;
				} else {
					$optlen[ $j ]              = strlen( JText::_( "DSSELECT" ) );
					$select_only[ $pid ][ $j ] = 1;
				}
				if ( isset( $field->size ) && $field->size > 0 ) {
					$optlen[ $j ] = $field->size / 9;
				}
			}
		}

		return $maxfields;
	}

	public static function add_selector_to_cart( $item, $optlen, $select_only, $i, $multi = 0, $configs ) {

		$res = '';

		if ( isset( $item->productfields ) && count( $item->productfields ) > 0 ) {

			$res = '<ul style="padding:0;">';

			foreach ( $item->productfields as $j => $v ) {

				$res .= '<li><span class="digicom_details">';
				$res .= $v->name;
				$res .= ( $v->mandatory == 1 ) ? "<span class='error' style='color:red;'>*</span>" : "";
				$res .= ':</span> <span>';
				$res .= '<select onchange="update_cart(' . $item->cid . ')" style="width:' . ( $optlen[ $j ] * 9 ) . 'px" name="attributes[' . $item->cid . '][' . $v->id . ']" id="attributes' . $item->cid . '' . $v->id . '" size="1">
					<option value="-1" ' . ( ( $v->optionid < 0 ) ? "selected" : "" ) . '>' . ( JText::_( "DSSELECT" ) ) . ( ( $select_only[ $item->item_id ][ $j ] == 0 ) ? $v->name : "" ) . '</option>';
				$options = explode( "\n", $v->options );
				foreach ( $options as $i1 => $v1 ) {
					$val = explode( ",", $v1 );
					if ( isset( $val[1] ) && strlen( trim( $val[1] ) ) > 0 ) {
						$r = $val[0] . " - " . ( JText::_( "DSADD" ) ) . " " . ( DigiComHelper::format_price( $val[1], $configs->get('currency','USD'), true, $configs ) );
					} else {
						$r = $val[0];
					}
					$res .= ( "<option value='" . $i1 . "'" );
					$res .= ( $i1 == $v->optionid ) ? " selected " : " ";
					$res .= ( ">" . $r . "</option>" );
				}

				$res .= '</select>' . "</span></li>";

			}

			$res .= "</ul>";
		}

		return $res;
	}

	public static function add_selector_to_summary( $item, $optlen, $select_only, $i, $multi = 0, $configs ) {
		$res = '<table>';

		if ( isset( $item->productfields ) && count( $item->productfields ) > 0 ) {
			foreach ( $item->productfields as $j => $v ) {

				$res .= '<tr><td style="text-align:right">';
				$res .= $v->name;
				$res .= ( $v->mandatory == 1 ) ? "<span class='error' style='color:red;'>*</span>" : "";
				$res .= ':</td><td style="text-align:left">';
//				$res .= '<select style="width:'.($optlen[$j]*9).'px" name="attributes['.$item->cid.']['.$v->id.']" id="attributes['.$item->cid.']['.$v->id.']">
				//  				<option value="-1" '.(($v->optionid < 0)?"selected":"").'>'.(JText::_("DSSELECT")).(($select_only[$item->item_id][$j] == 0)?$v->name:"").'</option>';
				if ( $v->optionid < 0 ) {
					$res .= ( JText::_( "DSSELECT" ) ) . ( ( $select_only[ $item->item_id ][ $j ] == 0 ) ? $v->name : "" );

				} else {
					$options = explode( "\n", $v->options );
					foreach ( $options as $i1 => $v1 ) {
						$val = explode( ",", $v1 );
						if ( isset( $val[1] ) && strlen( trim( $val[1] ) ) > 0 ) {
							$r = $val[0] . " - " . ( JText::_( "DSADD" ) ) . " " . ( DigiComHelper::format_price( $val[1], $configs->get('currency','USD'), true, $configs ) );
						} else {
							$r = $val[0];
						}
//							$res .= ("<option value='".$i1."'");
						//  						$res .= ($i1==$v->optionid)?"selected":"";
						if ( $i1 == $v->optionid ) {
							$res .= ( "" . $r . "" );
						}
					}

				}

//			$res .= (($v->optionid < 0)?:"").'>'.

//				$res .= '</select>'."</td></tr>";

			}
		}
		$res .= "</table>";

		return $res;
	}

	public static function add_selector( $productfields, $prodid, $optlen = array(), $select_only = array(), $i, $configs, $multi = 0, $maxfields = 0 ) {
//		global $configs;
		$nf  = 0;
		$res = '<table style="text-align:center; width:100%;">';
		if ( count( $productfields ) > 0 ) {
			foreach ( $productfields as $j => $v ) {
				if ( $v->publishing == 0 ) {
					continue;
				}
				if ( $multi == 1 ) {
					$insertion = "prod" . $prodid;
				} else {
					$insertion = '';
				}
				$res .= '<tr><td style="text-align:right">';
				$res .= trim( $v->name );
				$res .= trim( ( $v->mandatory == 1 ) ? "<span class='error' style='color:red'>*</span>" : "" );
				$res .= ':</td><td style="text-align:left">';
				$res .= ( '<select class="span2" size="1" onchange="document.getElementById(\'attr_field' . ( $v->id . $insertion ) . '\').value = this.value;" style="width:' . ( $optlen[ $j ] * 9 )
				          . 'px" name="field' . ( $v->id . $insertion )
				          . '" id="' . ( $i . $v->id )
				          . '"><option value="-1" selected>'
				          . ( JText::_( "DSSELECT" ) ) . " " . ( ( $select_only[ $prodid ][ $j ] == 0 ) ? $v->name : "" ) . '</option>' );
				$options = explode( "\n", $v->options );
				foreach ( $options as $i1 => $v1 ) {
					$val = explode( ",", $v1 );
					if ( isset( $val[1] ) && strlen( trim( $val[1] ) ) > 0 ) {
						$r = $val[0] . " - " . ( JText::_( "DSADD" ) ) . " " . ( DigiComHelper::format_price( $val[1], $configs->get('currency','USD'), true, $configs ) );
					} else {
						$r = $val[0];
					}
					//$r = $val[0]." - "._ADD." ".format_price($val[1], $configs['currency']);
					$res .= trim( "<option value='" . $i1 . "'>" . trim( $r ) . "</option>" );
				}

				$res .= '</select>' . "</td></tr>";
				$nf ++;


			}
		}
		$res .= "</table>";
		if ( $maxfields > 0 && $maxfields > $nf ) {
			for ( $k = 0; $k < ( $maxfields - $nf ); ++ $k ) {
				$res .= '
					<span style="visibility:hidden">
 						<select disabled> <option></option></select><br />
					</span>  ';
			}

		}

		return trim( $res );

	}


	public static function add_selector_new( $productfields, $prodid, $optlen = array(), $select_only = array(), $i, $configs, $multi = 0, $maxfields = 0 ) {

		$nf  = 0;
		$res = '';
		//dsdebug($productfields);
		if ( count( $productfields ) > 0 ) {

			foreach ( $productfields as $j => $v ) {

				if ( $v->publishing == 0 ) {
					continue;
				}

				if ( $multi == 1 ) {
					$insertion = "prod" . $prodid;
				} else {
					$insertion = '';
				}

				$style_separator = " ijd-vertical-separator";
				if ( ( $nf % 2 ) == 1 ) {
					$style_separator = "";
				}

				$res .= '<div style="float: left;width: 50%" class="ijd-cart-attribute ' . $style_separator . '">';
				$res .= '<div class="ijd-pad5">';
				$res .= '<label for="color_field">';
				$res .= trim( $v->name );
				$res .= trim( ( $v->mandatory == 1 ) ? "&nbsp;&nbsp;<span class='error' style='color:red'>*</span>&nbsp;&nbsp;" : "" );
				$res .= '</label>&nbsp;&nbsp;&nbsp;';
				$res .= ( '<select class="span2" size="1" onchange="document.getElementById(\'attr_field' . ( $v->id . $insertion ) . '\').value = this.value;" style="width:' . ( $optlen[ $j ] * 9 )
				          . 'px" name="field' . ( $v->id . $insertion )
				          . '" id="' . ( $i . $v->id )
				          . '"><option value="-1" selected>'
				          . ( JText::_( "DSSELECT" ) ) . " " . ( ( $select_only[ $prodid ][ $j ] == 0 ) ? $v->name : "" ) . '</option>' );

				$options = explode( "\n", $v->options );
				foreach ( $options as $i1 => $v1 ) {
					$val = explode( ",", $v1 );
					if ( isset( $val[1] ) && strlen( trim( $val[1] ) ) > 0 ) {
						$r = $val[0] . " - " . ( JText::_( "DSADD" ) ) . " " . ( DigiComHelper::format_price( $val[1], $configs->get('currency','USD'), true, $configs ) );
					} else {
						$r = $val[0];
					}
					$res .= trim( "<option value='" . $i1 . "'>" . trim( $r ) . "</option>" );
				}

				$res .= '</select>' . "</div></div>";

				if ( ( $nf % 2 ) == 1 ) //$res .= '<div class="ijd-horizontal-separator"></div>';

				{
					$nf ++;
				}
			}

			if ( ! empty( $res ) ) {
				$res = '<div class="ijd-cart-attributes">' . $res . '</div>';
				//$res .= '<div class="ijd-horizontal-separator"></div>';
			}
		}

		return trim( $res );

	}

	public static function add_selector_hidden( $productfields ) {

		$result = "";
		if ( count( $productfields ) > 0 ) {
			foreach ( $productfields as $j => $v ) {
				//if () {
				if ( $v->publishing == 0 ) {
					continue;
				}
				//if ($multi == 1) {
				//	$insertion = "prod". $prodid;
				//} else {
				$insertion = '';
				//}
				//}
				$result .= '<input type="hidden" id="attr_field' . ( $v->id . $insertion ) . '" name="field' . ( $v->id . $insertion ) . '" value="-1"/>';
			}
		}

		return $result;
	}

	public static function format_price( $amount, $ccode, $add_sym = false, $configs ) {
		$code         = 0;
		$price_format = '%' . $configs->get('totaldigits','') . '.' . $configs->get('decimaldigits','2') . 'f';
		$res          = sprintf( $price_format, $amount );
		if ( $add_sym ) {
			if ( $configs->get('currency_position','1') ) {
				$res = $res . " " . $ccode;
			} else {
				$res = $ccode . " " . $res;
			}
		}

		return $res;
	}

	public static function format_price2( $amount, $ccode, $add_sym = false, $configs ) {
		$code         = 0;
		$price_format = '%' . $configs->get('totaldigits','') . '.' . $configs->get('decimaldigits','2') . 'f';
		$res          = sprintf( $price_format, $amount );
		$res          = number_format( $res, $configs->get('decimaldigits','2'), '.', $configs->get('thousands_group_symbol','') );
		if ( $add_sym ) {
			if ( $configs->get('currency_position','1') ) {
				$res = $res . " " . $ccode;
			} else {
				$res = $ccode . " " . $res;
			}
		}

		return $res;
	}

	function getPromoDisc( $totaldisc, $items ) {
		$qty = 0;
		foreach ( $items as $i => $item ) {
			if ( $i < 0 ) {
				continue;
			}
			$qty += $item->quantity;
		}
		$res = $totaldisc / $qty;

		return $res;
	}


	function getItemTax( &$items, $cust_info, $sid = 0 ) {
		$temp = array();

		foreach ( $items as $i => $item ) {
			if ( $i < 0 ) {
				continue;
			}
			$temp[0]                  = $item;
			$tax                      = calc_price( $temp, $cust_info, $sid );
			$items[ $i ]->partial_tax = $tax['value'];

		}

		return;

	}


	function getItemPrice( $promo, &$items ) {
		$promodisc = DigiComHelper::getPromoDisc( $promo, $items );
		foreach ( $items as $i => $item ) {
			if ( $i < 0 ) {
				continue;
			}
			if ( isset( $item->discounted_price ) && $item->discounted_price ) {
				$price = $item->discounted_price;
			} else if ( isset( $item->no_discounted_price ) && $item->no_discounted_price ) {
				$price = $item->no_discounted_price;
			} else {
				$price = $item->price;
			}
			$price -= $promodisc;
			$items[ $i ]->cart_price = $price;
		}
	}


	public static function addValidation( $productfields, $i ) {
		$res = '';
		//$res .= '<script language="javascript" type="text/javascript"> ';
		$res .= 'function prodformsubmitA' . $i . '() {';
		$res .= '	var mandatory = new Object();';
		$res .= '	var i;';
		if ( count( $productfields ) > 0 ) {
			foreach ( $productfields as $j => $v ) {
				$res .= "mandatory[" . $j . "] = new Object();";
				$res .= "mandatory[" . $j . "]['fld'] = '" . $i . $v->id . "';\n";
				$res .= ( $v->mandatory == 1 ) ? "mandatory[" . $j . "]['req']=1;\n" : "mandatory[" . $j . "]['req']=0;\n";
			}
		}
		//for (var i = 0; i< mandatory.length; i++) {
		$res .= 'for (i in mandatory) {';
		$res .= '	if (mandatory[i]["req"] == 1) {';
		$res .= '		var el = document.getElementById(mandatory[i]["fld"]);' . "\n";
		$res .= '		if (el.selectedIndex < 1) {';
		$res .= '			alert ("' . ( JText::_( "DSSELECTALLREQ" ) ) . '");' . "\n";
		$res .= '			return false;';
		$res .= '		}';
		$res .= '	}';
		$res .= '}';

		$res .= 'return true; }';
		//}
		/*$res .= '</script>';*/

		return $res;
	}


	public static function getLiveSite() {
		// Check if a bypass url was set
		$config    = JFactory::getConfig();
		$live_site = $config->get( 'live_site' );

		// Determine if the request was over SSL (HTTPS)
		if ( isset( $_SERVER['HTTPS'] ) && ! empty( $_SERVER['HTTPS'] ) && ( strtolower( $_SERVER['HTTPS'] ) != 'off' ) ) {
			$https = 's://';
		} else {
			$https = '://';
		}
//	print_r($_SERVER);
		$subdom = $_SERVER['PHP_SELF'];
		$subdom = explode( "/", $subdom );
		$res    = array();
		foreach ( $subdom as $i => $v ) {
			if ( strtolower( trim( $v ) ) != "index.php" ) {
				$res[] = trim( $v );
			} else {
				break;
			}
		}
		$subdom = implode( "/", $res );
		/*
		* Since we are assigning the URI from the server variables, we first need
		* to determine if we are running on apache or IIS.  If PHP_SELF and REQUEST_URI
		* are present, we will assume we are running on apache.
		*/
		if ( ! empty ( $_SERVER['PHP_SELF'] ) && ! empty ( $_SERVER['REQUEST_URI'] ) ) {

			/*
			 * To build the entire URI we need to prepend the protocol, and the http host
			 * to the URI string.
			*/
			if ( ! empty( $live_site ) ) {
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
			if ( ! empty( $live_site ) ) {
				$theURI = $live_site . $_SERVER['SCRIPT_NAME'];
			} else {
				$theURI = 'http' . $https . $_SERVER['HTTP_HOST'] . $subdom;//. $_SERVER['SCRIPT_NAME'];
			}

			// If the query string exists append it to the URI string
			if ( isset( $_SERVER['QUERY_STRING'] ) && ! empty( $_SERVER['QUERY_STRING'] ) ) {
//					$theURI .= '?' . $_SERVER['QUERY_STRING'];
			}
		}

		return $theURI;
	}

	function CreateIndexFile( $dir ) {
		if ( file_exists( $dir ) ) {
			if ( ! file_exists( $dir . DS . "index.html" ) ) {
				$handle = @fopen( $dir . DS . "index.html", "w" );
				@fwrite( $handle, '<html><body bgcolor="#FFFFFF"></body></html>' );
				@fclose( $handle );
			}
		}
	}

	public static function DisplayContinueUrl( $configs, $cat_url ) {
		$continue_shopping_url = trim( $configs->get('continue_shopping_url','') );

		if ( ! empty( $continue_shopping_url ) ) {
			$protocol = '';
			if ( strpos( $continue_shopping_url, 'http://' ) === false ) {
				$protocol = 'http://';
			}
			$continue_shopping_url = $protocol . $continue_shopping_url;
		} else {
			$continue_shopping_url = $cat_url;
		}
		$result = JRoute::_( $continue_shopping_url );

		return $result;
	}

	public static function getDurationType( $count, $type ) {

		if ( $count > 1 ) {
			if ( $type == 0 ) {
				$result = JText::_( 'SUBCRUB_DURATION_DOWNLOADS' );
			}
			if ( $type == 1 ) {
				$result = JText::_( 'SUBCRUB_DURATION_HOURS' );
			}
			if ( $type == 2 ) {
				$result = JText::_( 'SUBCRUB_DURATION_DAYS' );
			}
			if ( $type == 3 ) {
				$result = JText::_( 'SUBCRUB_DURATION_MONTHS' );
			}
			if ( $type == 4 ) {
				$result = JText::_( 'SUBCRUB_DURATION_YEARS' );
			}
		} else {
			if ( $type == 0 ) {
				$result = JText::_( 'SUBCRUB_DURATION_DOWNLOAD' );
			}
			if ( $type == 1 ) {
				$result = JText::_( 'SUBCRUB_DURATION_HOUR' );
			}
			if ( $type == 2 ) {
				$result = JText::_( 'SUBCRUB_DURATION_DAY' );
			}
			if ( $type == 3 ) {
				$result = JText::_( 'SUBCRUB_DURATION_MONTH' );
			}
			if ( $type == 4 ) {
				$result = JText::_( 'SUBCRUB_DURATION_YEAR' );
			}
		}

		return $count . " " . $result;
	}

	function getRenewalAmountType( $type ) {
		if ( $type == 4 ) {
			return '%';
		}
	}

	// check if this user has filled in profile information
	public static function checkProfileCompletion( $customer ) {
		$tcustomer = "";

		if ( ! empty( $customer ) ) {
			if ( isset( $customer->_customer ) ) {
				$tcustomer = &$customer->_customer;
			} else {
				$tcustomer = $customer;
			}
		} else {
			return - 1;
		}

		$user_email = "";
		if ( isset( $tcustomer->id ) && ( $tcustomer->id > 0 ) ) {
			$user       = JFactory::getUser( $tcustomer->id );
			$user_email = $user->email;
		}

		if ( ! isset( $tcustomer->id )
		     || ( (int) $tcustomer->id <= 0 )
		     || strlen( trim( $tcustomer->firstname ) ) < 1
		     || strlen( trim( $tcustomer->lastname ) ) < 1
		     || strlen( trim( $user_email ) ) < 1
		) {
			return - 1;
		}
		
		$userid = $tcustomer->id;
		$table = JTable::getInstance('Customer', 'Table');
		$table->loadCustommer($userid);
		
		if(empty($table->id) or $table->id < 0){			
			$cust = new stdClass();
			$cust->id = $user->id;
			$cust->firstname = $tcustomer->firstname;
			$cust->lastname =  $tcustomer->lastname;
			$table->bind($cust);
			$table->store();
		}
		
		return 1;
	}

	//integrate with idev_affiliate
	function affiliate( $total, $orderid, $configs ) {
		$mosConfig_live_site = DigiComHelper::getLiveSite();

		$my = JFactory::getUser();
		if ( $configs->get('idevaff','notapplied') == 'notapplied' ) {
			return;
		}
		@session_start();
		$idev_psystems_1 = $total;
		$idev_psystems_2 = $orderid;
		$name            = "iJoomla Products";
		$email           = $my->email;//"cust@cust.cust";
		$item_number     = 1;
		$ip_address      = $_SERVER['REMOTE_ADDR'];
		if ( $configs->get('idevaff','notapplied') == 'standalone' && file_exists( JPATH_SITE . "/" . $configs->get('idevpath','notapplied') . "/sale.php" ) ) {
			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_URL, $mosConfig_live_site . "/" . $configs->get('idevpath','notapplied') . "/sale.php?profile=72198&idev_saleamt=" . $total . "&idev_ordernum=" . $orderid . "&ip_address=" . $ip_address );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_exec( $ch );
			curl_close( $ch );
		} else if ( $configs->get('idevaff','notapplied') == 'component' ) {
			$orderidvar     = $configs->get('orderidvar','');
			$ordersubtotvar = $configs->get('ordersubtotalvar','');
			echo '<img border="0" src="' . $mosConfig_live_site . '/components/com_idevaffiliate/sale.php?' . $ordersubtotvar . '=' . sprintf( "%.2f", $total ) . '&' . $orderidvar . '=' . $orderid . '" width="1" height="1">';
		}
	}

	public static function ShowHomeDescriptionBlock( $configs ) {

		$html = '';
		if ( $configs->get('displaystoredesc','') ) {
			$html = '
				<!-- Show description on store home page -->
				<div class="well well-small">
					<h3 style="margin:5px;">' . $configs->get('store_name','DigiCom Store') . '</h3>
					<p style="margin:5px;">' . $configs->get('storedesc','') . '</p>
				</div>
				<!-- /Show description on store home page -->
			';
		}

		return $html;
	}

	public static function getFeaturedProductByCategoryID( $cid ) {
		$db    = JFactory::getDbo();
		$limit = "3";
		$sql   = "select featured_row, featured_col from #__digicom_settings where id=1";
		$db->setQuery( $sql );
		$db->query();
		$result = $db->loadAssocList();
		if ( $result["0"]["featured_row"] != "0" && $result["0"]["featured_col"] != "0" ) {
			$limit = " " . ( $result["0"]["featured_row"] * $result["0"]["featured_col"] );
		}


		$sql = "select p.* 
				from #__digicom_products p
					inner join #__digicom_product_categories c on ( c.productid = p.id and c.catid = " . $cid . " )
					where p.featured = 1 and p.published=1
				limit " . $limit;
		$db->setQuery( $sql );
		$featured_products = $db->loadObjectList();

		// move images -----------------------------------------------------
		if ( isset( $featured_products ) && count( $featured_products ) > 0 ) {
			foreach ( $featured_products as $key => $product ) {
				$images = "";
				if ( trim( $product->images ) != "" ) {
					$product->images = str_replace( "/", DS, trim( $product->images ) );
					$product->images = str_replace( "\\", DS, trim( $product->images ) );
					$source          = JPATH_SITE . trim( $product->images );
					$images          = explode( DS, trim( $product->images ) );
					$images          = $images[ count( $images ) - 1 ];
					if ( ! is_dir( JPATH_SITE . DS . "images" . DS . "stories" . DS . "digicom" . DS . "products" ) ) {
						JFolder::create( JPATH_SITE . DS . "images" . DS . "stories" . DS . "digicom" . DS . "products" );
					}
					copy( $source, JPATH_SITE . DS . "images" . DS . "stories" . DS . "digicom" . DS . "products" . DS . $images );
				}

				$product->prodimages = $images;

				if ( trim( $product->prodimages ) != "" ) {
					$all_images_string = trim( $product->prodimages );
					$all_images_array  = explode( ",\\n", $all_images_string );
					$default_image     = trim( $product->defprodimage );
					if ( isset( $all_images_array ) && count( $all_images_array ) > 0 ) {
						$sql = "insert into #__digicom_products_images (`product_id`, `path`, `title`, `default`, `order`) values ";
						die( "1 - " . $sql );
						foreach ( $all_images_array as $key => $value ) {
							$default = trim( $value ) == trim( $default_image ) ? "1" : "0";
							$sql .= "(" . intval( $product->id ) . ", '" . trim( $value ) . "', '', " . $default . ", " . ( $key + 1 ) . "), ";
						}
						$sql = substr( $sql, 0, - 2 );
						$db->setQuery( $sql );
						if ( $db->query() ) {
							$sql = "update #__digicom_products set `prodimages`='', `defprodimage`='', `images`='' where id=" . intval( $product->id );
							$db->setQuery( $sql );
							$db->query();
						}
					}
				}
				$sql = "select `path`, `title` from #__digicom_products_images where `product_id`=" . intval( $product->id ) . " and `default`=1";
				$db->setQuery( $sql );

				$result                = $db->loadAssoc();
				$product->defprodimage = '';
				$product->image_title  = '';
				if ( count( $result ) ) {
					$product->defprodimage = $result["path"];
					$product->image_title  = $result["title"];
				}
				if ( trim( $product->defprodimage ) == "" ) {
					$sql = "select `path`, `title` from #__digicom_products_images where `product_id`=" . intval( $product->id ) . " and `default`= 0 limit 1";
					$db->setQuery( $sql );
					$result                = $db->loadAssoc();
					$product->defprodimage = '';
					$product->image_title  = '';
					if ( count( $result ) ) {
						$product->defprodimage = $result["path"];
						$product->image_title  = $result["title"];
					}
				}

				$featured_products[ $key ] = $product;
			}
		}

		// move images -----------------------------------------------------

		return $featured_products;
	}

	public static function getCategoryByProductID( $pid ) {
		$db    = JFactory::getDBO();
		$query = "SELECT d.* FROM #__digicom_product_categories c
		left join #__digicom_categories d on (d.id = c.catid)
		WHERE c.productid = $pid";
		$db->setQuery( $query );

		return $db->loadColumn();
	}

	public static function getRelatedItems( $id ) {
		$db      = JFactory::getDBO();
		$related = array();
		// select the meta keywords from the item
		$query = 'SELECT metakeywords' .
		         ' FROM #__digicom_products' .
		         ' WHERE id = ' . (int) $id;
		$db->setQuery( $query );

		if ( $metakey = trim( $db->loadResult() ) ) {
			$catids = DigiComHelper::getCategoryByProductID( $id );
			// explode the meta keys on a comma
			$keys  = explode( ',', $metakey );
			$likes = array();
			// assemble any non-blank word(s)
			foreach ( $keys as $key ) {
				$key = trim( $key );
				if ( $key ) {
					$likes[] = '' . $db->getEscaped( $key ) . ''; // surround with commas so first and last items have surrounding commas
				}
			}

			if ( count( $likes ) ) {
				// select other items based on the metakey field 'like' the keys found
				$limit = 3;
				$sql   = "select relatedrows, relatedcolumns from #__digicom_settings where id=1";
				$db->setQuery( $sql );
				$db->query();
				$related_result = $db->loadAssocList();
				$rows           = isset( $related_result["0"]["relatedrows"] ) ? $related_result["0"]["relatedrows"] : "0";
				$cols           = isset( $related_result["0"]["relatedcolumns"] ) ? $related_result["0"]["relatedcolumns"] : "0";
				$total          = intval( $rows ) * intval( $cols );
				if ( $total != 0 ) {
					$limit = $total;
				}

				$query = 'SELECT p.* ' .
				         ' FROM #__digicom_products AS p ' .
				         ' WHERE p.id != ' . (int) $id .
				         ' AND ( CONCAT(",", REPLACE(p.metakeywords,", ",","),",") LIKE "%' . implode( '%" OR CONCAT(",", REPLACE(p.metakeywords,", ",","),",") LIKE "%', $likes ) . '%" )' . //remove single space after commas in keywords
				         ' AND p.published=1 ' .
				         ' LIMIT ' . $limit;


				$db->setQuery( $query );
				$temp = $db->loadObjectList();

				if ( count( $temp ) ) {
					foreach ( $temp as $row ) {
						$related[] = $row;
					}
				}
				unset ( $temp );
			}
		}
		// move images -----------------------------------------------------
		if ( isset( $related ) && count( $related ) > 0 ) {
			foreach ( $related as $key => $product ) {
				$images = "";
				if ( trim( $product->images ) != "" ) {
					$product->images = str_replace( "/", DS, trim( $product->images ) );
					$product->images = str_replace( "\\", DS, trim( $product->images ) );
					$source          = JPATH_SITE . trim( $product->images );
					$images          = explode( DS, trim( $product->images ) );
					$images          = $images[ count( $images ) - 1 ];
					if ( ! is_dir( JPATH_SITE . DS . "images" . DS . "stories" . DS . "digicom" . DS . "products" ) ) {
						JFolder::create( JPATH_SITE . DS . "images" . DS . "stories" . DS . "digicom" . DS . "products" );
					}
					copy( $source, JPATH_SITE . DS . "images" . DS . "stories" . DS . "digicom" . DS . "products" . DS . $images );
				}

				$product->prodimages = $images;

				if ( trim( $product->prodimages ) != "" ) {
					$all_images_string = trim( $product->prodimages );
					$all_images_array  = explode( ",\\n", $all_images_string );
					$default_image     = trim( $product->defprodimage );
					if ( isset( $all_images_array ) && count( $all_images_array ) > 0 ) {
						$sql = "insert into #__digicom_products_images (`product_id`, `path`, `title`, `default`, `order`) values ";
						die( "2 - " . $sql );
						foreach ( $all_images_array as $key => $value ) {
							$default = trim( $value ) == trim( $default_image ) ? "1" : "0";
							$sql .= "(" . intval( $product->id ) . ", '" . trim( $value ) . "', '', " . $default . ", " . ( $key + 1 ) . "), ";
						}
						$sql = substr( $sql, 0, - 2 );
						$db->setQuery( $sql );
						if ( $db->query() ) {
							$sql = "update #__digicom_products set `prodimages`='', `defprodimage`='', `images`='' where id=" . intval( $product->id );
							$db->setQuery( $sql );
							$db->query();
						}
					}
				}
				$sql = "select `path`, `title` from #__digicom_products_images where `product_id`=" . intval( $product->id ) . " and `default`=1";
				$db->setQuery( $sql );
				$db->query();
				$result = $db->loadAssocList();
				if ( isset( $result ) && count( $result ) > 0 ) {
					$product->defprodimage = $result["0"]["path"];
					$product->image_title  = $result["0"]["title"];
				}

				if ( trim( $product->defprodimage ) == "" ) {
					$sql = "select `path`, `title` from #__digicom_products_images where `product_id`=" . intval( $product->id ) . " and `default`= 0 limit 1";
					$db->setQuery( $sql );
					$db->query();
					$result                = $db->loadAssocList();
					$product->defprodimage = $result["0"]["path"];
					$product->image_title  = $result["0"]["title"];
				}

				$sql = "select `price` from #__digicom_products_plans where product_id=" . intval( $product->id ) . " and `default`=1";
				$db->setQuery( $sql );
				$db->query();
				$price          = $db->loadResult();
				$product->price = $price;

				$related[ $key ] = $product;
			}
		}

		// move images -----------------------------------------------------

		return $related;
	}

	function str_word_count_unicode( $str, $format = 0 ) {
		$words = preg_split( '~[\s0-9_]|[^\w]~u', $str, - 1, PREG_SPLIT_NO_EMPTY );

		return ( $format === 0 ) ? count( $words ) : $words;
	}

	public static function ShowCatDesc( $text, $configs ) {
		if ( $configs->get('catlayoutdesctype','') == 1 ) {
			$results = array();

			$format = 1;
			$words  = preg_split( '~[\s0-9_]|[^\w]~u', $text, - 1, PREG_SPLIT_NO_EMPTY );
			$words  = ( $format === 0 ) ? count( $words ) : $words;
// 			$words = DigiComHelper::str_word_count_unicode(strip_tags($text), 1);
// 			var_dump($words);
			$i = 0;
			foreach ( $words as $word ) {
				if ( $i > $configs->get('catlayoutdesclength',0) ) {
					break;
				}
				$results[] = $word;
				$i ++;
			}
			$results = implode( ' ', $results );
		} else {
			$results = '';
			$results = substr( strip_tags( $text ), 0, $configs->get('catlayoutdesclength',0) );
		}

		return $results;
	}


	/**
	 *
	 */
	public static function getSubCategoriesId( $catid, $object = false ) {
		$db    = JFactory::getDBO();
		$query = 'SELECT 
						`id`,
						`parent_id` AS `parent`,
						`parent_id`,
						`title`,
						`title` as `name`
					FROM
						`#__digicom_categories`
					WHERE
						`published` = 1
					ORDER BY `ordering`';
		$db->setQuery( $query );
		$mitems = $db->loadObjectList();

		$children = array();
		if ( $mitems ) {
			foreach ( $mitems as $v ) {
				$v->title     = $v->name;
				$v->parent_id = $v->parent;
				$pt           = $v->parent;
				$list         = @$children[ $pt ] ? $children[ $pt ] : array();
				array_push( $list, $v );
				$children[ $pt ] = $list;
			}
		}
		$list = JHTML::_( 'menu.treerecurse', $catid, '', array(), $children, 9999, 0, 0 );
		if ( $object ) {
			return $list;
		}
//		echo '<pre>'.print_r($list, true ).'</pre>';exit();
		$subids = array();
		foreach ( $list as $item ) {
			$subids[] = ( $item->id );
		}

		return $subids;
	}

	public static function ShowProdDesc( $text, $configs, $page ) {
		$text    = trim( strip_tags( $text ) );
		$results = "";

		if ( $page == "list" ) {
			if ( $configs->get('prodlayoutdesctype',1) == "1" ) {//words
				if ( trim( $configs->get('prodlayoutdesclength','') ) == "" || $configs->get('prodlayoutdesclength','') == "0" ) {
					$results = "";
				} else {
					$words1  = explode( " ", $text );
					$words2  = array_splice( $words1, 0, $configs->get('prodlayoutdesclength','') );
					$results = implode( ' ', $words2 );
					if ( count( $words1 ) > count( $words2 ) ) {
						$results .= "...";
					}
				}
			} else { //characters
				if ( trim( $configs->get('prodlayoutdesclength','') ) == "" || $configs->get('prodlayoutdesclength','') == "0" ) {
					$results = "";
				} else {
					if ( strlen( $text ) <= $configs->get('prodlayoutdesclength','') ) {
						$results = $text;
					} else {
						$results = substr( $text, 0, $configs->get('prodlayoutdesclength','') ) . "...";
					}
				}
			}
		}

		return $results;

		/*$text = trim(strip_tags($text));
		if($configs->imageproddesctype == 0){
			$results = array();
			$words = str_word_count(strip_tags($text), 1);
			$i = 0;
			foreach($words as $word){
				if($i > $configs->imageproddescvalue){
					break;
				}
				$results[] = $word;
				$i++;
			}
			$results = implode(' ', $results);
		}
		else{
			$results = '';
			$results = substr(strip_tags($text), 0, $configs->imageproddescvalue);
		}

		if(trim($text) == trim($results)){
			$etc = '';
		}
		else{
			$etc = '...';
		}
		return ($results) ? $results.$etc : '';*/
	}
	
	/** 
	* Converts bytes into human readable file size. 
	* 
	* @param string $bytes 
	* @return string human readable file size (2,87 Мб)
	* @author Mogilev Arseny 
	*/ 
	public static function FileSizeConvert($bytes)
	{
		$result = $bytes . ' Bytes';
		$bytes = floatval($bytes);
		$arBytes = array(
			0 => array(
				"UNIT" => "TB",
				"VALUE" => pow(1024, 4)
			),
			1 => array(
				"UNIT" => "GB",
				"VALUE" => pow(1024, 3)
			),
			2 => array(
				"UNIT" => "MB",
				"VALUE" => pow(1024, 2)
			),
			3 => array(
				"UNIT" => "KB",
				"VALUE" => 1024
			),
			4 => array(
				"UNIT" => "B",
				"VALUE" => 1
			),
		);

		foreach($arBytes as $arItem)
		{
			if($bytes >= $arItem["VALUE"])
			{
				$result = $bytes / $arItem["VALUE"];
				$result = strval(round($result, 2))." ".$arItem["UNIT"];
				break;
			}
		}
		return $result;
	}
	
	public static function getUsersProduct($user_id){
		
		if($user_id < 1) return false;
		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('DISTINCT('.$db->quoteName('od.productid').')');
		$query->select($db->quoteName(array('p.name', 'p.catid', 'p.bundle_source')));
		$query->select($db->quoteName('od.package_type').' type');
		$query->from($db->quoteName('#__digicom_products').' p');
		$query->from($db->quoteName('#__digicom_orders_details').' od');
		$query->where($db->quoteName('od.userid') . ' = '. $db->quote($user_id));
		$query->where($db->quoteName('od.productid') . ' = '. $db->quoteName('p.id'));
		$query->where($db->quoteName('od.published') . ' = '. $db->quote('1'));
		$query->order('ordering ASC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		$products = $db->loadObjectList();
		//print_r($products);die;
		$bundleItems = array();
		foreach($products as $key=>$product){
			if($product->type != 'reguler'){
				switch($product->type){
					case 'category':
						
						$BundleTable = JTable::getInstance('Bundle', 'Table');
						$BundleList = $BundleTable->getFieldValues('product_id',$product->productid,$product->bundle_source);
						$bundle_ids = $BundleList->bundle_id;
						
						$query = $db->getQuery(true)
							->select(array('id as productid','name','catid'))
							->from($db->quoteName('#__digicom_products'))
							->where($db->quoteName('bundle_source').' IS NULL')
							->where($db->quoteName('catid').' in ('.$bundle_ids.')');
						$db->setQuery($query);
						$bundleItems[] = $db->loadObjectList();
						// Unset current product as its category bundle.
						//we should show only items
						unset($products[$key]);
						
						break;
					case 'product':
					default:
						$BundleTable = JTable::getInstance('Bundle', 'Table');
						$BundleList = $BundleTable->getFieldValues('product_id',$product->productid,$product->bundle_source);
						$bundle_ids = $BundleList->bundle_id;
						
						$query = $db->getQuery(true)
							->select(array('id as productid','name','catid'))
							->from($db->quoteName('#__digicom_products'))
							->where($db->quoteName('bundle_source').' IS NULL')
							->where($db->quoteName('id').' in ('.$bundle_ids.')');
						$db->setQuery($query);
						$bundleItems[] = $db->loadObjectList();
						// Unset current product as its category bundle.
						//we should show only items
						unset($products[$key]);
						
						break;
				}
			}
		}
		//print_r($products);die;
		//we got all our products
		// now add bundle item to the products array
		if(count($bundleItems) >0){
			foreach($bundleItems as $item2){
				foreach($item2 as $item3){
					$products[] = $item3;
				}
			}
		}
		return $products;
		
	}
	
	public static function getUsersProductAccess($user_id,$product_id){
		
		if($user_id < 1) return false;
		$db = JFactory::getDBO();
		//$product_id
		$query = $db->getQuery(true);
		$query->select($db->quoteName('od.productid'));
		$query->from($db->quoteName('#__digicom_orders_details').' od');
		$query->where($db->quoteName('od.userid') . ' = '. $db->quote($user_id));
		$query->where($db->quoteName('od.productid') . ' = '. $product_id);
		$query->where($db->quoteName('od.published') . ' = '. $db->quote('1'));

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$orders = $db->loadObject();
		if(isset($orders->id) && ($orders->id > 0)) return true;
		
		$query = $db->getQuery(true);
		$query->select('DISTINCT('.$db->quoteName('od.productid').')');
		$query->select($db->quoteName(array('p.name', 'p.catid', 'p.bundle_source')));
		$query->select($db->quoteName('od.package_type').' type');
		$query->from($db->quoteName('#__digicom_products').' p');
		$query->from($db->quoteName('#__digicom_orders_details').' od');
		$query->where($db->quoteName('od.userid') . ' = '. $db->quote($user_id));
		$query->where($db->quoteName('od.productid') . ' = '. $db->quoteName('p.id'));
		$query->where($db->quoteName('od.published') . ' = '. $db->quote('1'));
		$query->order('ordering ASC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		$products = $db->loadObjectList();
		//print_r($products);die;
		$bundleItems = array();
		foreach($products as $key=>$product){
			
			if($product->type != 'reguler'){
				switch($product->type){
					case 'category':
						
						$BundleTable = JTable::getInstance('Bundle', 'Table');
						$BundleList = $BundleTable->getFieldValues('product_id',$product->productid,$product->bundle_source);
						$bundle_ids = $BundleList->bundle_id;
						
						$query = $db->getQuery(true)
							->select(array('id as productid','name','catid'))
							->from($db->quoteName('#__digicom_products'))
							->where($db->quoteName('bundle_source').' IS NULL')
							->where($db->quoteName('catid').' in ('.$bundle_ids.')');
						$db->setQuery($query);
						$bundleItems[] = $db->loadObjectList();
						// Unset current product as its category bundle.
						//we should show only items
						unset($products[$key]);
						
						break;
					case 'product':
					default:
						$BundleTable = JTable::getInstance('Bundle', 'Table');
						$BundleList = $BundleTable->getFieldValues('product_id',$product->productid,$product->bundle_source);
						$bundle_ids = $BundleList->bundle_id;
						
						$query = $db->getQuery(true)
							->select(array('id as productid','name','catid'))
							->from($db->quoteName('#__digicom_products'))
							->where($db->quoteName('bundle_source').' IS NULL')
							->where($db->quoteName('id').' in ('.$bundle_ids.')');
						$db->setQuery($query);
						$bundleItems[] = $db->loadObjectList();
						// Unset current product as its category bundle.
						//we should show only items
						unset($products[$key]);
						
						break;
				}
			}
		}
		//print_r($products);die;
		//we got all our products
		// now add bundle item to the products array
		if(count($bundleItems) >0){
			foreach($bundleItems as $item2){
				foreach($item2 as $item3){
					if($item3->productid == $product_id) return true;
				}
			}
		}
		return false;
		
	}
	
	public static function checkUserAccessToFile($fileInfo,$user_id){
		
		$user = JFactory::getUser($user_id);
		//$products = DigiComHelper::getUsersProduct($user_id);
		$access = DigiComHelper::getUsersProductAccess($user_id,$fileInfo->product_id);
		
		if($access) return true;
		
		// Wrong Download ID
		$msg = array(
			'wrong_id' => JText::_('COM_DIGICOM_WRONG_DOWNLOAD_ID')
		);
		$msgcode = json_encode($msg);
		echo $msgcode;die;
		
	}
	
}