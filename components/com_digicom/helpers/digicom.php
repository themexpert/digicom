<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class DigiComSiteHelperDigicom {

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

	public static function powered_by() {
		$html = '<div style="margin: 0 auto; width: 250px; text-align: center;" class="small">';
		$html .= '<span>Powered by ';
		$html .= '<a target="_blank" title="Joomla Digital Download eCommerce" href="http://www.themexpert.com">';
		$html .= 'DigiCom</a></span>';
		$html .= '</div>';

		return $html;
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
		$promodisc = DigiComSiteHelperDigiCom::getPromoDisc( $promo, $items );
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
	
	function str_word_count_unicode( $str, $format = 0 ) {
		$words = preg_split( '~[\s0-9_]|[^\w]~u', $str, - 1, PREG_SPLIT_NO_EMPTY );

		return ( $format === 0 ) ? count( $words ) : $words;
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
						if($bundle_ids){
							$db =JFactory::getDbo();
							$query = $db->getQuery(true)
								->select(array('id as productid','name','catid'))
								->from($db->quoteName('#__digicom_products'))
								->where($db->quoteName('bundle_source').' IS NULL')
								->where($db->quoteName('catid').' in ('.$bundle_ids.')');
							$db->setQuery($query);
							$bundleItems[] = $db->loadObjectList();
							//we should show only items
						}

						unset($products[$key]);
						
						break;
					case 'product':
					default:
						$BundleTable = JTable::getInstance('Bundle', 'Table');
						$BundleList = $BundleTable->getFieldValues('product_id',$product->productid,$product->bundle_source);
						$bundle_ids = $BundleList->bundle_id;
						if($bundle_ids){
							$db =JFactory::getDbo();
							$query = $db->getQuery(true)
								->select(array('id as productid','name','catid'))
								->from($db->quoteName('#__digicom_products'))
								->where($db->quoteName('bundle_source').' IS NULL')
								->where($db->quoteName('id').' in ('.$bundle_ids.')');
							$db->setQuery($query);
							$bundleItems[] = $db->loadObjectList();
						}					
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
	
	public static function checkUserAccessToFile($fileInfo,$user_id)
	{
		
		$user = JFactory::getUser($user_id);
		$access = DigiComSiteHelperDigiCom::getUsersProductAccess($user_id,$fileInfo->product_id);
		
		if($access) return true;
				
		// Wrong Download ID
		$msg = array(
			'wrong_id' => JText::_('COM_DIGICOM_WRONG_DOWNLOAD_ID')
		);
		$msgcode = json_encode($msg);
		
	}
	
	/*
	* get thumbnail 
	* images (string): image path like : /images/digicom.png
	*/
	public static function getThumbnail($image){
		$params = JComponentHelper::getComponent('com_digicom')->params;

		if(empty($image)) return '';
		if($params->get('image_thumb_enable')){
			jimport( 'joomla.filesystem.folder' );
			jimport( 'joomla.filesystem.file' );

			$image_thumb_width = $params->get('image_thumb_width');
			$image_thumb_height = $params->get('image_thumb_height');
			$image_thumb_method = $params->get('image_thumb_method',6);

			$imageunique = md5($image.$image_thumb_width.$image_thumb_height);
			$path = JPATH_ROOT . '/images/digicom/products';
			JFolder::create($path);

			// Generate thumb name
			$jimage = new JImage($image);

			$filename       = pathinfo($jimage->getPath(), PATHINFO_FILENAME);
			$fileExtension  = pathinfo($jimage->getPath(), PATHINFO_EXTENSION);
			$thumbFileName  = $filename . '_' . $image_thumb_width . 'x' . $image_thumb_height . '.' . $fileExtension;

			$thumbpath = JPATH_ROOT.'/images/digicom/products/'.$thumbFileName;
			$thumburl = JURI::root().'images/digicom/products/'.$thumbFileName;
			if(JFile::exists($thumbpath)) return $thumburl;
			
			$image = $jimage->createThumbs(array($image_thumb_width.'x'.$image_thumb_height), $image_thumb_method,$path);
			$thumburl = str_replace(JPATH_SITE.'/', '', $image[0]->getPath());
			return $thumburl;

		}else{
			return $image;
		}
		
	}

	public static function loadModules($position, $style = 'raw')
	{
		jimport('joomla.application.module.helper');
		$modules = JModuleHelper::getModules($position);
		$params = array('style' => $style);
		foreach ($modules as $module) {
			echo JModuleHelper::renderModule($module, $params);
		}
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

	public static function getPaymentPlugins($configs){
		
		$db = JFactory::getDBO();

		$condtion = array(0 => '\'digicom_pay\'');
		$condtionatype = join(',',$condtion);
		if(JVERSION >= '1.6.0')
		{
			$query = "SELECT extension_id as id,name,element,enabled as published
					  FROM #__extensions
					  WHERE folder in ($condtionatype) AND enabled=1";
		}
		else
		{
			$query = "SELECT id,name,element,published
					  FROM #__plugins
					  WHERE folder in ($condtionatype) AND published=1";
		}
		$db->setQuery($query);
		$gatewayplugin = $db->loadobjectList();

		$lang = JFactory::getLanguage();
		$options = array();
		$options[] = JHTML::_('select.option', '', 'Select payment gateway');
		foreach($gatewayplugin as $gateway)
		{
			$gatewayname = strtoupper(str_replace('plugpayment', '',$gateway->element));
			$lang->load('plg_digicom_pay_' . strtolower($gatewayname), JPATH_ADMINISTRATOR);
			$options[] = JHTML::_('select.option',$gateway->element, JText::_($gatewayname));
		}

		return JHTML::_('select.genericlist', $options, 'processor', 'class="inputbox required"', 'value', 'text', $configs->get('default_payment','offline'), 'processor' );

	}

}