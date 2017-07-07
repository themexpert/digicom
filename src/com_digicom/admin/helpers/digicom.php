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
 * DigiCom helper.
 *
 * @since  1.0.0
 */
class DigiComHelperDigiCom extends JHelperContent{

	/**
	 * Configure the Linkbar.
	 * @param   string  $vName  The name of the active view.
	 * @return  void
	 * @since   1.0.0
	 */

	public static function addSubmenu($vName)
	{
		JHtmlSidebar::addEntry(
			JText::_('COM_DIGICOM_SIDEBAR_MENU_DASHBOARD'),
			'index.php?option=com_digicom',
			$vName == 'digicom'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_DIGICOM_SIDEBAR_MENU_CATEGORIES'),
			'index.php?option=com_digicom&view=categories',
			$vName == 'categories'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_DIGICOM_SIDEBAR_MENU_PRODUCTS'),
			'index.php?option=com_digicom&view=products',
			$vName == 'products'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_DIGICOM_SIDEBAR_MENU_FILE_MANAGER'),
			'index.php?option=com_digicom&view=filemanager',
			$vName == 'filemanager'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_DIGICOM_SIDEBAR_MENU_CUSTOMERS'),
			'index.php?option=com_digicom&view=customers',
			$vName == 'customers'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_DIGICOM_SIDEBAR_MENU_ORDERS'),
			'index.php?option=com_digicom&view=orders',
			$vName == 'orders'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_DIGICOM_SIDEBAR_MENU_LICENSES'),
			'index.php?option=com_digicom&view=licenses',
			$vName == 'licenses'
		);
		
		JHtmlSidebar::addEntry(
			JText::_('COM_DIGICOM_SIDEBAR_MENU_DISCOUNTS'),
			'index.php?option=com_digicom&view=discounts',
			$vName == 'discounts'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_DIGICOM_SIDEBAR_MENU_REPORTS'),
			'index.php?option=com_digicom&view=reports',
			$vName == 'reports'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_DIGICOM_SIDEBAR_MENU_ABOUT'),
			'index.php?option=com_digicom&view=about',
			$vName == 'about'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_DIGICOM_SIDEBAR_MENU_COLLAPSE'),
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

	public static function format_price ($amount, $ccode, $add_sym = true, $configs, $decial = null)
	{
		if($decial === NULL){
			$decial = $configs->get('decimaldigits','2');
		}
		$currency_use = $configs->get('currency_use','symbol');
		$currency_symbol = $configs->get('currency_symbol','$');
		if($currency_use == 'symbol'){
			$ccode = $currency_symbol;
		}
		$price = number_format( $amount, $decial , $configs->get('dec_group_symbol','.') , $configs->get('thousands_group_symbol',',') );
		if ( $add_sym ) {
			if ( $configs->get('currency_position','1') ) {
				$price = $price . $ccode;
			} else {
				$price = $ccode . $price;
			}
		}

		return $price;
	}


	public static function cleanUpImageFolders($root, $folders) {

		foreach ($folders as $i => $folder) {
			$x = explode (myDC, $folder);
			if (trim($x[0]) == $root) unset($x[0]);

			$folders[$i] = implode(myDC, $x);

		}
		return $folders;
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


	public static function CreateIndexFile($dir)
	{
		if (file_exists($dir))
		{
			if (!file_exists($dir . "/index.html"))
			{
				$handle = @fopen($dir . "/index.html", "w");
				@fwrite($handle, '<html><body bgcolor="#FFFFFF"></body></html>');
				@fclose($handle);
			}
		}
	}


	/**
	 * Get latest orders, to use with DS Dashboard
	 * @return unknown
	 */
	public static function getOrders($limit) {
		$db = JFactory::getDBO();
		$sql = 'SELECT o.*, c.name FROM #__digicom_orders o, #__digicom_customers c WHERE `c`.`id`=`o`.`userid` AND `o`.`status` = "Active" ORDER BY `o`.`order_date` DESC LIMIT '.$limit.'';
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
		$sql = 'SELECT DISTINCT p.id,p.name,p.catid, p.introtext, p.publish_up, c.name AS category FROM #__digicom_products p, #__digicom_categories c WHERE p.published = 1 AND c.published = 1 AND p.catid = c.id ORDER BY p.id DESC LIMIT '.$limit;
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

		$date = DigiComHelperDigiCom::getStartEndDateMonth();
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
		$sql = 'SELECT o.*, c.name, c.email FROM #__digicom_orders o, #__digicom_customers c WHERE c.id=o.userid AND status = "Active" GROUP BY o.userid LIMIT '.$limit;
		$db->setQuery($sql);
		if (!$customers = $db->loadObjectList()) {
			echo $db->getErrorMsg();
		}
		return $customers;
	}

	public static function publishAndExpiryHelper($promo, $configs)
	{


		$today = date('Y-m-d 00:00:00');
		$tomorrow = date('Y-m-d  00:00:00',strtotime($today . "+1 days"));

		$now = strtotime($today);
		$tomorrow = strtotime($tomorrow);
		$timestart = strtotime($promo->codestart);
		$timeend = strtotime($promo->codeend);
		$nullDate = strtotime('0000-00-00 00:00:00');

		$status = '';
		$remain = $promo->codelimit - $promo->used;

		if(
				($timestart <= $now)
					&&
				($timeend >= $now || $timeend == $nullDate )
					&&
				($promo->codelimit == 0 || $promo->used < $promo->codelimit)
					&&
				$promo->published == "1"
				)
		{
			$status = JText::_("COM_DIGICOM_ACTIVE");
		}
		else if ($promo->published == "0")
		{
			$status = "<span style='color:red'>".(JText::_("COM_DIGICOM_UNPUBLISHED"))." </span>";
		}
		else if ($promo->codelimit > 0  && $promo->used  >= $promo->codelimit)
		{
			$status = "<span style='color:red'>".(JText::_("COM_DIGICOM_EXPIRED")).": (".(JText::_("Amount")).")</span>";
		}
		else if ($timeend != $nullDate && $timeend < $tomorrow && ($remain < 1 && $promo->codelimit > 0))
		{
			$status = "<span style='color:red'>".(JText::_("COM_DIGICOM_EXPIRED")).": (".(JText::_("Date"))." ,".(JText::_("Amount")).")</span>";
		}
		else if ($timeend < $tomorrow && $timeend != $nullDate)
		{
			$status = "<span style='color:red'>".(JText::_("COM_DIGICOM_EXPIRED")).": (".(JText::_("Date")).")</span>";
		}
		elseif($timestart > $now){
			$status = "<span style='color:blue'>".(JText::_("COM_DIGICOM_DISCOUNT_CODE_UPCOMING"))."</span>";
		}
		else
		{
			$status = "<span style='color:red'>".(JText::_("COM_DIGICOM_DISCOUNT_CODE_ERROR"))."</span>";
		}

		return $status;

	}

	public static function licenseExpiryHelper($item, $configs)
	{


		$today = date('Y-m-d 00:00:00');
		$tomorrow = date('Y-m-d  00:00:00',strtotime($today . "+1 days"));

		$now = strtotime($today);
		$tomorrow = strtotime($tomorrow);
		$timestart = strtotime($item->purchase);
		$timeend = strtotime($item->expires);
		$nullDate = strtotime('0000-00-00 00:00:00');

		$status = '';

		if(!$item->active)
		{
			$status = "<span class='label label-warning'>".(JText::_("COM_DIGICOM_INACTIVE"))."</span>";
		}
		elseif(
			($timeend >= $now || $timeend == $nullDate )
			&&
			$item->active
		)
		{
			$status = "<span class='label label-success'>".(JText::_("COM_DIGICOM_ACTIVE"))."</span>";
		}
		else
		{
			$status = "<span class='label'>".(JText::_("COM_DIGICOM_EXPIRED"))."</span>";
		}

		return $status;

	}



	//This function transforms the php.ini notation for numbers (like '2M') to an integer (2*1024*1024 in this case)
	public static function convertPHPSizeToBytes($sSize)
	{
	    if ( is_numeric( $sSize) ) {
	       return $sSize;
	    }
	    $sSuffix = substr($sSize, -1);
	    $iValue = substr($sSize, 0, -1);
	    switch(strtoupper($sSuffix)){
	    case 'P':
	        $iValue *= 1024;
	    case 'T':
	        $iValue *= 1024;
	    case 'G':
	        $iValue *= 1024;
	    case 'M':
	        $iValue *= 1024;
	    case 'K':
	        $iValue *= 1024;
	        break;
	    }
	    return $iValue;
	}

	public static function setSidebarRight(){

		$input = JFactory::getApplication()->input;
		$tmpl = $input->get('tmpl','');
		$ajax = $input->get('ajax','');
		if($tmpl == 'component' or $ajax =='1') return;

		require_once(JPATH_COMPONENT_ADMINISTRATOR.'/layouts/sidebars/sidebar-right.php');
		return true;
	}

	public static function getChargebacks($order)
	{
		$db = JFactory::getDBO();
		$sql = "SELECT SUM(`cancelled_amount`) FROM `#__digicom_orders_details` WHERE `cancelled`=1 AND `orderid`=" . (int) $order;
		$db->setQuery($sql);
		return $db->loadResult();
	}

	public static function getRefunds($order)
	{
		$db = JFactory::getDBO();
		$sql = "SELECT SUM(`cancelled_amount`) FROM `#__digicom_orders_details` WHERE `cancelled`=2 AND `orderid`=" . (int) $order;
		$db->setQuery($sql);
		return $db->loadResult();
	}

	public static function getDeleted($order, $license=0)
	{
		$db = JFactory::getDBO();
		$sql = "SELECT SUM(`amount_paid`) FROM `#__digicom_orders_details` WHERE `cancelled`=3 AND `orderid`=" . (int) $order;
		$db->setQuery($sql);
		return $db->loadResult();
	}

	public static function isProductDeleted($id)
	{
		$db = JFactory::getDBO();
		$sql = "SELECT `cancelled` FROM `#__digicom_orders_details` WHERE `id`='" . $id . "'";
		$db->setQuery($sql);
		return $db->loadResult();
	}

	public static function getOrderSratusList($status, $i, $order){

		$statuslist = array(
			'Active' => JText::_('COM_DIGICOM_ORDER_STATUS_ACTIVE'),
			'Pending' => JText::_('COM_DIGICOM_ORDER_STATUS_PENDING'),
			'Cancel' => JText::_('COM_DIGICOM_ORDER_STATUS_CANCEL'),
			'Paid' => JText::_('COM_DIGICOM_ORDER_MAKE_PAID'),
			'Refund' => JText::_('COM_DIGICOM_ORDER_MAKE_REFUND')
		);
		## Initialize array to store dropdown options ##
		$options = array();

		foreach($statuslist as $key=>$value) :
			## Create $value ##
			$options[] = JHTML::_('select.option', $key, $value);
		endforeach;

		## Create <select name="month" class="inputbox"></select> ##
		$dropdown = JHTML::_('select.genericlist', $options, 'orderstatus[]', 'class="inputbox input-small" onchange="changeOrderStatus(\'cb'.$i.'\',\'orders.cycleStatus\','.$i.',this.value);"', 'value', 'text', $status,'orderstatus'.$i);

		## Output created <select> list ##
		return $dropdown;
	}

	/**
	* get the product name n id for options list
	*/
	public static function getProductsList(){
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('name','id')))
			  ->from($db->quoteName('#__digicom_products'))
			  ->order($db->quoteName('id').' DESC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	* get products name by order id
	* return span wrap items name
	*/
	public static function getProductsNamebyOrder($id){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('p.id','p.name', 'p.catid')))
				->from($db->quoteName('#__digicom_products','p'))
				->from($db->quoteName('#__digicom_orders_details','od'))
				->where($db->quoteName('p.id').'='.$db->quoteName('od.productid'))
				->where($db->quoteName('od.orderid').'='.$db->quote($id));
		$db->setQuery($query);
		return $db->loadObjectList();

	}

	public static function addAdminStyles(){

		// load core script
		JHtml::_('jquery.framework');
		JHtml::_('bootstrap.framework');
		$document = JFactory::getDocument();
		$document->addScript(JURI::root(true).'/media/com_digicom/js/digicom-admin.js?v=1.0.0&amp;sitepath='.JURI::root(true).'/');
		$document->addStyleSheet(JURI::root(true).'/media/com_digicom/css/digicom-admin.css');
	}

}
