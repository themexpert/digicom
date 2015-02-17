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

jimport( "joomla.application.component.view" );

class DigiComAdminViewOrders extends DigiComView
{

	function display( $tpl = null )
	{
		// Access check.
		if (!JFactory::getUser()->authorise('digicom.orders', 'com_digicom'))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}

		$document = JFactory::getDocument();

		$orders = $this->get('Items');
		$pagination = $this->get('Pagination');
		
		$this->orders = $orders;
		$this->pagination = $pagination;

		$configs =  $this->_models['config']->getConfigs();
		$this->assign( "configs", $configs );

		$startdate = JRequest::getVar( "startdate", "", "request" );
		$startdate = strtotime($startdate);
		//$startdate = DigiComAdminHelper::parseDate( $configs->get('time_format','DD-MM-YYYY'), $startdate );
		$this->assign( "startdate", $startdate );
		$enddate = JRequest::getVar( "enddate", "", "request" );
		$enddate = strtotime($enddate);
		//$enddate = DigiComAdminHelper::parseDate( $configs->get('time_format','DD-MM-YYYY'), $enddate );
		$this->assign( "enddate", $enddate );

		$keyword = JRequest::getVar( "keyword", "", "request" );
		$this->assign( "keyword", $keyword );
		
		//set toolber
		$this->addToolbar();
		
		DigiComAdminHelper::addSubmenu('orders');
		$this->sidebar = JHtmlSidebar::render();
		
		parent::display( $tpl );
	}

	function showOrder( $tpl = null )
	{
		JToolBarHelper::title( JText::_( 'VIEWDSADMINORDERS' ), 'generic.png' );
		$bar = JToolBar::getInstance('toolbar');
		// Instantiate a new JLayoutFile instance and render the layout
		$layout = new JLayoutFile('toolbar.title');
		$title=array(
			'title' => JText::_( 'VIEWDSADMINORDERS' ),
			'class' => 'title'
		);
		$bar->appendButton('Custom', $layout->render($title), 'title');
		
		JToolBarHelper::Cancel();
		$db = JFactory::getDBO();
		$order =  $this->_models['order']->getOrder();
		
		$this->assign( "order", $order );
		$configs =  $this->_models['config']->getConfigs();
		$this->assign( "configs", $configs );
		
		DigiComAdminHelper::addSubmenu('orders');
		$this->sidebar = JHtmlSidebar::render();
		
		parent::display( $tpl );
	}

	function addNewOrder( $tpl = null )
	{
		JToolBarHelper::title( JText::_( 'Create Order' ), 'generic.png' );
		JToolBarHelper::cancel();
		$configs =  $this->_models['config']->getConfigs();
		$this->assign( "configs", $configs );
		parent::display( $tpl );
	}

	function prepereNewOrder( $tpl = null )
	{

		$db = JFactory::getDBO();
		JHtml::_( 'behavior.modal' );

		JToolBarHelper::title( JText::_( 'Create Order' ), 'generic.png' );
		JToolBarHelper::cancel();

		// get user info
		$customer_model = $this->getModel('Customer');
		$userid = JRequest::getVar('userid',0);
		$cust = $customer_model->getUserByID($userid);
		$this->assign( "cust", $cust );

		// Subcription type
		$subscr_types[] = JHTML::_('select.option',  'new',  'New Subcription' );
		$subscr_types[] = JHTML::_('select.option',  'new',  'Renewal' );
		$subscr_types = JHTML::_('select.genericlist',  $subscr_types, 'subscr_type', 'class="inputbox" size="1" ', 'value', 'text');
		$this->assign( "subscr_types", $subscr_types );

		// License to renew
		$licenses[] = JHTML::_('select.option',  'none',  'none' );
		$licenses = JHTML::_('select.genericlist',  $licenses, 'subscr_type', 'class="inputbox" size="1" ', 'value', 'text');
		$this->assign( "licenses", $licenses );

		// Subcription plain
		$plans[] = JHTML::_('select.option',  'none',  'none' );
		$plans = JHTML::_('select.genericlist',  $plans, 'subscr_type', 'class="inputbox" size="1" ', 'value', 'text');
		$this->assign( "plans", $plans );

		// get plugin info
		$plugin_items = JPluginHelper::getPlugin( 'digicompayment' );
		$plugins = array();
		foreach ($plugin_items as $plugin_item) {
			$plugin_params = new JRegistry($plugin_item->params);
			$pluginname = $plugin_params->get($plugin_item->name.'_label');
			$plugins[] = JHTML::_('select.option',  $plugin_item->name,  $pluginname );
		}
		$plugins = JHTML::_('select.genericlist',  $plugins, 'processor', 'class="inputbox" size="1" ', 'value', 'text');
		$this->assign( "plugins", $plugins );

		// Promocode
		$sql = 'SELECT *, TRIM(code) AS alphabetical
				FROM #__digicom_promocodes
				WHERE published=1
				ORDER BY alphabetical ASC';
		$db->setQuery($sql);
		$promocodes = $db->loadObjectList();
		// echo "<pre>";var_dump($promocodes);die();

		$promocode_valid[] = (object) array('text' => 'none', 'value' => 'none');
		$nullDate = 0;

		foreach($promocodes as $promo)
		{
			$timestart = $promo->codestart;
			$timeend = $promo->codeend;
			$limit = $promo->codelimit;
			$used = $promo->used;
			$now = time();

			$promo_status = false;

			if ( $timeend == 0)
			{
				$promo_status = true;
			}
			else
			{
				if ( $now < $timestart && ( $now <= $timeend || $timeend == $nullDate ) )
				{
					$promo_status = true;
				}
			}
			if ($limit > 0 && $limit == $used)
			{
				$promo_status = false;
			}

			if ($promo_status)
				$promocode_valid[] = (object) array( 'text' => $promo->code, 'value' => $promo->code );
		}

		$promocode = JHTML::_('select.genericlist',  $promocode_valid, 'promocode', 'class="inputbox" size="1" onchange="changePlain();" ', 'value', 'text', 'none');
		$this->assign( "promocode", $promocode );

		// Amount paid
		$amount_paid = '$ <input type="text" name="amount_paid" value=""/>';
		$this->assign( "amount_paid", $amount_paid );

		// configs
		$configs =  $this->_models['config']->getConfigs();
		$this->assign( "configs", $configs );
		parent::display( $tpl );
	}

	function selectUsername( $tpl = null )
	{
		JToolBarHelper::title( JText::_( 'Create Order' ), 'generic.png' );
		JToolBarHelper::cancel();

		$configs =  $this->_models['config']->getConfigs();
		$this->assign( "configs", $configs );
		parent::display( $tpl );
	}

	function getCustomerLicensesCount()
	{
		$db = JFactory::getDBO();
		$userid = JRequest::getVar("userid", "0");
		$sql = "select count(*) from #__digicom_licenses where userid=".intval($userid);
		$db->setQuery($sql);
		$db->query();
		$count = $db->loadResult();
		return $count; 
	}

	function productitem( $tpl = null )
	{
		// Rand ID
		$id_rand = uniqid (rand ());
		$this->assign( "id_rand", $id_rand );

		// Customer
		$userid = JRequest::getVar('userid', 0);

		// Subcription type
		$subscr_types_options[] = JHTML::_('select.option',  'new',  'New Subcription' );
		$subscr_types_options[] = JHTML::_('select.option',  'renewal',  'Renewal' );

		$nr_licenses = $this->getCustomerLicensesCount();
		$subscr_types = "";
		if($nr_licenses != "0"){
			$subscr_types = JHTML::_('select.genericlist',  $subscr_types_options, 'subscr_type_select['.$id_rand.']', 'class="inputbox" size="1" onchange="show_licences_renew(\''.$id_rand.'\')"', 'value', 'text');
		}

		$this->assign( "subscr_types", $subscr_types );

		// License to renew
		$licenses[] = JHTML::_('select.option',  'none',  'none' );
		$licenses = JHTML::_('select.genericlist',  $licenses, 'licences_select['.$id_rand.']', 'class="inputbox" size="1" ', 'value', 'text');
		$this->assign( "licenses", $licenses );

		// Subcription plain
		$plans[] = JHTML::_('select.option',  'none',  'none' );
		$plans = JHTML::_('select.genericlist',  $plans, 'subscr_plan_select['.$id_rand.']', 'class="inputbox" size="1" ', 'value', 'text');
		$this->assign( "plans", $plans );

		$configs =  $this->_models['config']->getConfigs();
		$this->assign( "configs", $configs );

		parent::display( $tpl );
	}

	function newCustomer( $tpl = null )
	{
		JToolBarHelper::title( JText::_( 'Create Order' ), 'generic.png' );
		JToolBarHelper::cancel();

		require_once( JPATH_COMPONENT.DS.'helpers'.DS.'sajax.php' );
		$db = JFactory::getDBO();

		$username = JRequest::getVar('username','');
		$user = $this->_models['customer']->getUserByName($username);
		if (!empty($user->id)) {
			$customer = $this->_models['customer']->getCustomerbyID($user->id);
			if (empty($customer->email)) $customer->email = $user->email;
			if (empty($customer->firstname)) $customer->firstname = $user->name;
		} else {
			$customer = $this->_models['customer']->getCustomerbyID(0);
			if (empty($customer->username)) $customer->username = $username;
		}
		$this->assign("cust", $customer);

		$configs = $this->_models['config']->getConfigs();
		$country_option = DigiComAdminHelper::get_country_options($customer, false, $configs);
		$lists['country_option'] = $country_option;

		$profile = new StdClass();
		$profile->country = $customer->shipcountry;
		$profile->state = $customer->shipstate;
		$shipcountry_option = DigiComAdminHelper::get_country_options($customer, true, $configs);
		$lists['shipcountry_options'] = $shipcountry_option;

		$lists['customerlocation'] = DigiComAdminHelper::get_store_province($customer);

		$profile = new StdClass();
		$profile->country = $customer->shipcountry;
		$profile->state = $customer->shipstate;
		$lists['customershippinglocation'] = DigiComAdminHelper::get_store_province($profile, true, $configs);

		$cclasses = explode("\n", $customer->taxclass);
		$data = $this->get('listCustomerClasses', 'digicomCustomer');
		$select = '<select name="taxclass" >';
		if (count($data) > 0)
		foreach($data as $i => $v) {
			$select .= '<option value="'.$v->id.'" ';
			if (in_array($v->id, $cclasses)) {
				$select .= ' selected ' ;
			}
			$select .= ' > '.$v->name.'</option>';
		}
		$select .= '</select>';
		$lists['customer_class'] = $select;
		$this->assign("lists", $lists);

		$this->assign( "configs", $configs );
		parent::display( $tpl );
	}

	function editForm( $tpl = null )
	{

		$db = JFactory::getDBO();
		$order = $this->get( 'order' );
		$isNew = ($order->id < 1);
		$text = $isNew ? JText::_('New') : JText::_('Edit');

		JToolBarHelper::title( JText::_( 'Order' ) . ":<small>[" . $text . "]</small>" );

		$bar = JToolBar::getInstance('toolbar');
		$layout = new JLayoutFile('toolbar.title');
		$title=array(
			'title' => JText::_( 'Order' ) . ":<small>[" . $text . "]</small>",
			'class' => 'title'
		);
		$bar->appendButton('Custom', $layout->render($title), 'title');

//		JToolBarHelper::save();
		if ( $isNew ) {
			JToolBarHelper::cancel();
		} else {
			JToolBarHelper::cancel( 'cancel', 'Close' );
		}

		$this->assign( "order", $order );
		//dsdebug($order);

		$configs =  $this->_models['config']->getConfigs();
		$lists = array();
		
		$prods =  $this->_models['product']->getListProducts();
		$opts = array();
		$opts[] = JHTML::_( 'select.option', "", JText::_( "Select product" ) );
		foreach ( $prods as $prod ) {
			$opts[] = JHTML::_( 'select.option', $prod->id, $prod->name );
		}
		$lists['productid'] = JHTML::_( 'select.genericlist', $opts, 'productid', 'class="inputbox" size="1" ', 'value', 'text', '' ); // $license->productid

		$this->assign( "configs", $configs );
		$this->assign( "lists", $lists );
		$this->assign( "currency_options", array() );

		// get user info
		$customer_model = $this->getModel('Customer');
		$cust = $customer_model->getUserByID($order->userid);
		$this->assign( "cust", $cust );

		// plugins
		$this->assign( "plugins", $order->processor );

		// promocode
		if ($order->promocode == 0) { $order->promocode = 'none'; }
		$this->assign( "promocode", $order->promocode );

		// products
		
		$products = array();
		if ( $order->products ) {

			$products = $order->products;

			foreach( $products as $key => $product) {
				//print_r($product);die;
				
				// get Plain
				//orderDetails
				/*
				$license = $this->_models['license']->getLicense( $product->lid );
				$products[$key]->license = $license;

				if ($license->renew) {
					$plans = $this->_models['plain']->getPlanitemRenewal($product->id, $license->plan_id);
				} else {
					$plans = $this->_models['plain']->getPlanitemNew($product->id, $license->plan_id);
				}
				$products[$key]->plans = $plans;

				// get renew license
				if ( $license->renewlicid != -1 ) {
					$renewlicense = $this->_models['license']->getLicense( $license->renewlicid );
					$products[$key]->renewlicense = $renewlicense;
				} else {
					$products[$key]->renewlicense = null;
				}
				*/
			}
		}
		
		//dsdebug($products);die;

		$this->assign( "products", $products );

		$this->assign( "amount_paid", $order->amount_paid );
		$this->assign( "total", $order->amount );
		$this->assign( "tax", '0' );

		DigiComAdminHelper::addSubmenu('orders');
		$this->sidebar = JHtmlSidebar::render();

		parent::display( $tpl );
	}

	/**
	 * Add the page title and toolbar.
		*
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		JToolBarHelper::title( JText::_( 'VIEWDSADMINORDERS' ), 'generic.png' );

		$bar = JToolBar::getInstance('toolbar');
		// Instantiate a new JLayoutFile instance and render the layout
		$layout = new JLayoutFile('toolbar.title');
		$title=array(
			'title' => JText::_( 'VIEWDSADMINORDERS' ),
			'class' => 'title'
		);
		$bar->appendButton('Custom', $layout->render($title), 'title');
		
		JToolBarHelper::addNew();
		JToolBarHelper::divider();
		JToolBarHelper::deleteList();
		JToolBarHelper::spacer();
	}
	
}
