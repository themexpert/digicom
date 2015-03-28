<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class DigiComViewOrder extends JViewLegacy
{

	protected $state;

	protected $item;

	protected $form;
	
	protected $configs;

	function display( $tpl = null )
	{
		$app = JFactory::getApplication();
		$addnew = $app->input->get('addnew',0);
		if($addnew){
			$this->prepereNewOrder();
		}
		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');
		$this->configs 	= $this->get('configs');


		JToolBarHelper::title( JText::_( 'COM_DIGICOM_ORDER_DETAILS_TOOLBAR_TITLE' ), 'generic.png' );
		$bar = JToolBar::getInstance('toolbar');
		// Instantiate a new JLayoutFile instance and render the layout
		$layout = new JLayoutFile('toolbar.title');
		$title=array(
			'title' => JText::_( 'COM_DIGICOM_ORDER_DETAILS_TOOLBAR_TITLE' ),
			'class' => 'title'
		);
		$bar->appendButton('Custom', $layout->render($title), 'title');
		
		$layout = new JLayoutFile('toolbar.settings');
		$bar->appendButton('Custom', $layout->render(array()), 'settings');
		
		JToolBarHelper::apply('order.apply');
		JToolBarHelper::save('order.save');
		JToolBarHelper::Cancel('order.cancel');
		
		DigiComHelperDigiCom::addSubmenu('orders');
		$this->sidebar = JHtmlSidebar::render();
		
		parent::display( $tpl );
	}


	function prepereNewOrder( $tpl = null )
	{

		
		$form = $this->get('Form');
		$this->assign( "form", $form );
		
		// configs
		$configs =  $this->_models['config']->getConfigs();
		$this->assign( "configs", $configs );
		
		// promocode
		$promocode =  $this->get('PromoCode');
		$this->assign( "promocode", $promocode );
		
		
		
		JToolBarHelper::title( JText::_( 'COM_DIGICOM_ORDER_CREATE_NEW_ORDER_TOOLBAR_TITLE' ), 'generic.png' );
		$bar = JToolBar::getInstance('toolbar');
		// Instantiate a new JLayoutFile instance and render the layout
		$layout = new JLayoutFile('toolbar.title');
		$title=array(
			'title' => JText::_( 'COM_DIGICOM_ORDER_CREATE_NEW_ORDER_TOOLBAR_TITLE' ),
			'class' => 'title'
		);
		$bar->appendButton('Custom', $layout->render($title), 'title');
		$layout = new JLayoutFile('toolbar.settings');
		$bar->appendButton('Custom', $layout->render(array()), 'settings');
		
		JToolBarHelper::save();
		JToolBarHelper::cancel();
		
		DigiComHelperDigiCom::addSubmenu('orders');
		$this->sidebar = JHtmlSidebar::render();
		
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
		
		$this->assign( "subscr_types", '' );

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
		$country_option = DigiComHelperDigiCom::get_country_options($customer, false, $configs);
		$lists['country_option'] = $country_option;

		$profile = new StdClass();
		$profile->country = $customer->shipcountry;
		$profile->state = $customer->shipstate;
		$shipcountry_option = DigiComHelperDigiCom::get_country_options($customer, true, $configs);
		$lists['shipcountry_options'] = $shipcountry_option;

		$lists['customerlocation'] = DigiComHelperDigiCom::get_store_province($customer);

		$profile = new StdClass();
		$profile->country = $customer->shipcountry;
		$profile->state = $customer->shipstate;
		$lists['customershippinglocation'] = DigiComHelperDigiCom::get_store_province($profile, true, $configs);

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
		
		$layout = new JLayoutFile('toolbar.settings');
		$bar->appendButton('Custom', $layout->render(array()), 'settings');
		
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

		DigiComHelperDigiCom::addSubmenu('orders');
		$this->sidebar = DigiComHelperDigiCom::renderSidebar();

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
		
		$layout = new JLayoutFile('toolbar.settings');
		$bar->appendButton('Custom', $layout->render(array()), 'settings');
		
		JToolBarHelper::addNew('order.new');
		JToolBarHelper::divider();
		JToolBarHelper::deleteList('orders.remove');
		JToolBarHelper::spacer();
	}
	
}
