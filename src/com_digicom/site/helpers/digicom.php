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

	/**
	 * Method to get cart menu itemid
	 *
	 * @return  Int 	if itemid found return int or return empty
	 *
	 * @since   1.0.0
	 */
	public static function getCartItemid()
	{
		$app = JFactory::getApplication();
		$item = $app->getMenu()->getItems('link', 'index.php?option=com_digicom&view=cart', true);
		$Itemid = isset($item->id) ? $item->id : '';
		return $Itemid;
	}

	public static function powered_by()
	{
		//digicom_credit
		$config = JComponentHelper::getComponent('com_digicom')->params;
		$digicom_credit = $config->get('digicom_credit',1);
		if(!$digicom_credit) return;
		$html = '<div style="margin: 0 auto; width: 250px; text-align: center;" class="small">';
		$html .= '<span>Powered by ';
		$html .= '<a target="_blank" title="Joomla eCommerce - Sell Digital Products and Bundle with Joomla and Digicom" href="//www.themexpert.com/digicom">';
		$html .= 'DigiCom</a></span>';
		$html .= '</div>';

		return $html;
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

		$theURI = str_replace('/administrator','',$theURI);

		return $theURI;
	}

	function CreateIndexFile( $dir )
	{
		if ( file_exists( $dir ) ) {
			if ( ! file_exists( $dir . DS . "index.html" ) ) {
				$handle = @fopen( $dir . DS . "index.html", "w" );
				@fwrite( $handle, '<html><body bgcolor="#FFFFFF"></body></html>' );
				@fclose( $handle );
			}
		}
	}

	// check if this user has filled in profile information
	public static function checkProfileCompletion( $customer , $askforbilling = 0)
	{

		if (isset( $customer->_customer ) ) {
			$customer = $customer->_customer;
		}

		$userid = $customer->id;
		$table = JTable::getInstance('Customer', 'Table');
		$table->load($userid);
		if(empty($table->id) or $table->id < 0){
			$object = new stdClass();
            $object->id = $user->id;
            $object->name = $customer->name;
            $object->email =  $user->email;
			// $table->bind($cust);
			// $table->store();

            $db = JFactory::getDBO();
            $db->insertObject('#__digicom_customers', $object);
            $id = $db->insertId();
            $table->load($id);
		}

		if (
		     strlen( trim( $table->country ) ) < 1
		     || strlen( trim( $table->state ) ) < 1
		     || strlen( trim( $table->city ) ) < 1
		     || strlen( trim( $table->address ) ) < 1
		     || strlen( trim( $table->zipcode ) ) < 1
		) {
			return 2;
		}else{
			return 1;
		}

	}

	function str_word_count_unicode( $str, $format = 0 )
	{
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

	public static function getUsersProduct($user_id)
	{

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

	public static function getUsersProductAccess($user_id,$product_id)
	{

		if($user_id < 1) return false;
		$db = JFactory::getDBO();
		//echo $product_id;die;
		$query = $db->getQuery(true);
		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__digicom_licenses'));
		$query->where($db->quoteName('userid') . ' = '. $db->quote($user_id));
		$query->where($db->quoteName('productid') . ' = '. $product_id);
		$query->where($db->quoteName('active') . ' = '. $db->quote('1'));
		$query->where('( DATEDIFF(`expires`, now()) > -1 or DATEDIFF(`expires`, now()) IS NULL )' );
		// Reset the query using our newly populated query object.
		//echo $query->__tostring();die;
		$db->setQuery($query);
		$license = $db->loadObject();

		if(isset($license->id) && ($license->id > 0)) return true;
		// its not single purchased product
		// so check for the bundle/category item

		$query = $db->getQuery(true);

		// Select required fields from the dashboard.
		$query->select('DISTINCT p.id as productid')
			  ->select(array('p.name,p.catid,p.bundle_source,p.product_type as type'))
			  ->from($db->quoteName('#__digicom_licenses') . ' AS l')
			  ->join('inner', '#__digicom_products AS p ON l.productid = p.id');

		$query->where($db->quoteName('l.active') . ' = ' . $db->quote('1'));
		$query->where($db->quoteName('l.userid') . ' = ' . $db->quote($user_id));
		$query->where('DATEDIFF(`expires`, now()) > -1 or DATEDIFF(`expires`, now()) IS NULL' );

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		$items = $db->loadObjectList();
		//print_r($items);die;
		$bundleItems = array();
		foreach($items as $key=>$product){
			if($product->type != 'reguler'){
				switch($product->bundle_source){
					case 'category':
						$BundleTable = JTable::getInstance('Bundle', 'Table');
						$BundleList = $BundleTable->getFieldValues('product_id',$product->productid,$product->bundle_source);
						$bundle_ids = $BundleList->bundle_id;
						if($bundle_ids){
							$db = JFactory::getDBO();
							$query = $db->getQuery(true)
								->select(array('id as productid','name','catid'))
								->from($db->quoteName('#__digicom_products'))
								->where($db->quoteName('bundle_source').' IS NULL')
								->where($db->quoteName('catid').' in ('.$bundle_ids.')');
							$db->setQuery($query);
							$bundleItems[] = $db->loadObjectList();
							//we should show only items
						}

						unset($items[$key]);

						break;
					case 'product':
					default:
						// its bundle by product
						$BundleTable = JTable::getInstance('Bundle', 'Table');
						$BundleList = $BundleTable->getFieldValues('product_id',$product->productid,$product->bundle_source);
						$bundle_ids = $BundleList->bundle_id;
						//echo $bundle_ids;die;
						if($bundle_ids){
							$db = JFactory::getDBO();
							$query = $db->getQuery(true)
								->select(array('id as productid','name','catid'))
								->from($db->quoteName('#__digicom_products'))
								->where($db->quoteName('bundle_source').' IS NULL')
								->where($db->quoteName('id').' in ('.$bundle_ids.')');
							$db->setQuery($query);
							$bundleItems[] = $db->loadObjectList();
						}
						//we should show only items
						unset($items[$key]);

						break;
				}
			}
		}
		//we got all our items
		// now add bundle item to the items array
		if(count($bundleItems) >0){
			foreach($bundleItems as $item2){
				foreach($item2 as $item3){
					$items[] = $item3;
				}
			}
		}
		//print_r($items);die;
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

		return false;
		
		// Wrong Download ID
		// $msg = array(
		// 	'access' => JText::_('COM_DIGICOM_DOWNLOADS_ACCESS_DENIED')
		// );
		// $msgcode = json_encode($msg);
		// echo $msgcode;
		// JFactory::getApplication()->close();
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

	/**
	 * Method to get the payment plugin list
	 *
	 * @param  JRegistry Object	Config of Digicom settings
	 * @param  Boolian False	If listonly for query result,
	 * @return  Object    A Objectlist from payment plugins
	 *
	 * @since   1.3.3
	 */
	public static function getPaymentPlugins($configs, $listonly = false)
	{

		$lang = JFactory::getLanguage();
		$session = JFactory::getSession();

		$db = JFactory::getDBO();
		$query = $db->getQuery(true)
					->select('extension_id as id , name, element, enabled as published, params')
					->from('#__extensions')
					->where($db->quoteName('folder') . ' in (' . $db->quote('digicom_pay') .')')
					->where($db->quoteName('enabled') . ' = 1 ');
		$db->setQuery($query);
		$gatewayplugin = $db->loadobjectList();

		// if listonly, return lists
		if($listonly)
		{
			return $gatewayplugin;
		}

		$default 		= $session->get('processor', '');
		if(empty($default)){
				$default = $configs->get('default_payment','offline');
		}

		$options = array();
		foreach($gatewayplugin as $gateway)
		{
			$params = json_decode($gateway->params);
			if(isset($params->plugin_name)){
				$name = $params->plugin_name;
			}else
			{
				$name = $gateway->name;
			}

			$options[] = JHTML::_('select.option',$gateway->element, $name );
		}

		return JHTML::_('select.genericlist', $options, 'processor', 'class="inputbox required" data-digicom-id="processor"', 'value', 'text', $default, 'processor' );

	}
	

	public static function getUniqueTransactionId($order_id)
	{
		$uniqueValue = $order_id.time();
		$long = md5(uniqid($uniqueValue, true));
		return substr($long, 0, 15);
	}

	public static function prepareGCalendarUrl($item){
		//href="<?php echo
		//;&dates=20150522T090000/20150522T110000
		//&location=http://siteurl.com&details=Your product will expire at tx site on this day, add it to get remonder">
		$text = JText::sprintf('COM_DIGICOM_PRODUCT_ADD_CALENDER_TITLE',$item->name,JFactory::getConfig()->get( 'sitename' ));
		$startDate = strtotime($item->expires . "-1 days");
		$expires = new DateTime($item->expires);
		$endDate = $expires->format('Ymd');
		$endTime = $expires->format('His');
		$end = $endDate.'T'.$endTime;
		$start = date('Ymd',$startDate).'T'.$endTime;
		$location = JUri::root();
		$details = JText::sprintf('COM_DIGICOM_PRODUCT_ADD_CALENDER_DESC',$item->name,JFactory::getConfig()->get( 'sitename' ));
		return 'https://www.google.com/calendar/render?action=TEMPLATE&text='.$text.'&dates='.$start.'/'.$end.'&location='.$location.'&details='.$details;
	}

	public static function getJoomlaArticle($id) {

	    JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_content/models', 'ContentModel');

	    // Get an instance of the generic articles model
	    $model = JModelLegacy::getInstance( 'Article', 'ContentModel', [ 'ignore_request' => true ] );
	    $model->setState( 'filter.published', 1 );

	    // Access filter
	    $params = JComponentHelper::getParams( 'com_content' );
	    $access = ! $params->get( 'show_noauth' );
	    $model->setState( 'filter.access', $access );

	    // Load the parameters.
	    $app = JFactory::getApplication('site');
	    if(!$app->isAdmin()){
	        $params = $app->getParams();
	    }
	    $model->setState('params', $params);

	    // Retrieve Content
	    $item = $model->getItem($id);
	    $item->text = $item->introtext . ' ' . $item->fulltext;
		// Process the content plugins.
		JPluginHelper::importPlugin('content');
		$dispatcher = JEventDispatcher::getInstance();
		$dispatcher->trigger('onContentPrepare', array ('com_content.article', &$item, &$item->params, $offset = 0));
	    
		
	    return $item;
  	}  
  	
}
