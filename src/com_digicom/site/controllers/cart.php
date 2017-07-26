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
 * DigiCom Cart Controller
 *
 * @package     DigiCom
 * @since       1.0.0
 */

class DigiComControllerCart extends JControllerLegacy
{

	/**
	 * the model objcet
	 *
	 * @var    object
	 * @since  1.0.0
	 */
	protected $_model = null;

	/**
	 * the config objcet of digicom
	 *
	 * @var    object
	 * @since  1.0.0
	 */
	protected $_config = null;

	/**
	 * the model objcet of product
	 *
	 * @var    object
	 * @since  1.0.0
	 */
	protected $_product = null;

	/**
	 * the model objcet of customer
	 *
	 * @var    object
	 * @since  1.0.0
	 */
	protected $_customer = null;

	/**
	 * Construct Method for cart controller to handle task
	 *
	 * @since   1.0.0
	 */

	function __construct()
	{
		parent::__construct();
		$this->registerTask("validate_input", "validateInput");
		$this->registerTask("cancel", "cancel");
		$this->registerTask("payment", "payment");
		$this->registerTask("getcountries", "getCountries");

		$this->_model = $this->getModel("Cart");
		$this->_config = JComponentHelper::getComponent('com_digicom')->params;
		$this->_product = $this->getModel("Product");
		$this->_customer = new DigiComSiteHelperSession();
	}

	/**
	* addProduct to cart
	* update cart, and update session
	* @since 1.0.0
	*/
	function add()
	{
		$app 		= JFactory::getApplication();
		$configs 	= $this->_config;
		$cid 		= $app->input->get('cid', 0);
		$res 		= $this->_model->addToCart();
		$afteradd 	= $configs->get('afteradditem', 0); // 0=> to cart; 1=>popup

		if($res < 0) 
		{
			// Product not found; -1
			$msg = JText::_("COM_DIGICOM_CART_WRONG_PID_OR_ACCESS_LABEL");
			$link = "index.php?option=com_digicom&view=category&id=" . $cid;
			$this->setRedirect(JRoute::_($link, false), $msg);
		}
		elseif($res == 0)
		{
			// Handle error, log must be added from model
			$msg = JText::_("COM_DIGICOM_CART_ERROR_UPDATING_CART_LABEL");
			$link = "index.php?option=com_digicom&view=category&id=" . $cid;
			$this->setRedirect(JRoute::_($link, false), $msg);
		}

		// if added through content plugin 
		// or
		// forced to go to cart
		$fromplugin = $app->input->get("from_add_plugin", 0);
		$gotocart 	= $app->input->get("gotocart", 0);
		if($fromplugin or $gotocart)
		{
			$afteradd = 0; // to cart
		}

		// Act for ajax or cart popup work
		$from 		= $app->input->get('from', '');
		if($from == "ajax")
		{
			$afteradd = 2; // popup
		}

		if($afteradd == 1)
		{
			//Show cart in pop up
			$url = "index.php?option=com_digicom&view=cart&layout=cart_popup&tmpl=component";
			$this->setRedirect(JRoute::_($url, false));
		}
		else
		{
			// Take to cart
			$url = JRoute::_("index.php?option=com_digicom&view=cart", false);
			$this->setRedirect($url);
		}

		return true;
	}

	/**
	* updateCart method
	* update cart status and use session
	* @since 1.0.0
	*/
	function updateCart()
	{

		$session = JFactory::getSession();
		$processor	= JRequest::getVar("processor", '');
		if(!isset($processor) or !$processor){
			$processor = $session->get('processor', 'offline');
		}else{
			$session->set('processor', $processor);
		}

		$this->_model->updateCart($this->_customer, $this->_config);
		$this->setRedirect(JRoute::_("index.php?option=com_digicom&view=cart", false));

	}

	/**
	* cart item delete method
	* delete cart item and redirect to cart page
	* @since 1.0.0
	*/
	function deleteFromCart()
	{
		$session = JFactory::getSession();
		$this->_model->deleteFromCart($this->_customer, $this->_config);
		$from = JRequest::getVar("from", "");
		$processor = JRequest::getVar("processor", "");
		$session->set('processor',$processor);

		if($from == "ajax"){
			//$this->showCart();
			$this->setRedirect(JRoute::_("index.php?option=com_digicom&view=cart&layout=cart_popup&tmpl=component"));
		}
		else{
			$this->setRedirect(JRoute::_("index.php?option=com_digicom&view=cart"));
		}
	}

	/**
	* getPageURL method
	* to use core url or redirect from script it can be helpful
	* @since 1.0.0
	* @return string
	*/
	function getPageURL(){
		$pageURL = 'http';

		if($_SERVER["HTTPS"] == "on"){
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

	/**
	* handle https request
	* @since 1.0.0
	* @return string
	*/
	function getReqhttps($content)
	{
		$reqhttps = "1";
		if(trim($content) != ""){
			$by_n = explode("\n", $content);
			if(isset($by_n)){
				foreach($by_n as $key=>$value){
					$by_equal = explode("=", $value);
					if(is_array($by_equal) && count($by_equal) > 0){
						if($by_equal["0"] == "reqhttps"){
							$reqhttps = trim($by_equal["1"]);
						}
					}
				}
			}
		}
		return $reqhttps;
	}

	/**
	* process the checkout
	* finalize cart, check login status, billing info, customer info, set params
	* @since 1.0.0
	* @return string
	*/
	function checkout()
	{
		$dispatcher = JDispatcher::getInstance();
		$session 		= JFactory::getSession();
		$app 				= JFactory::getApplication();
		$processor	= JRequest::getVar("processor", '');
		$user 			= JFactory::getUser();
		$cart 			= $this->_model;
		$customer 	= $this->_customer;
		$configs 		= $this->_config;

		$plugins_enabled = $cart->getPluginList();
		if(!isset($processor) or !$processor){
			$processor = $session->get('processor','offline');
		}else{
			$session->set('processor',$processor);
		}

		// set default redirect url
		$return = base64_encode(JRoute::_("index.php?option=com_digicom&view=cart&task=cart.checkout"));

		// Check Login
		if(!$user->id or $customer->_user->id < 1){
 			$this->setRedirect(JRoute::_("index.php?option=com_digicom&view=register&return=".$return));
			return true;
		}

		// Check Payment Plugin installed
		if (empty($plugins_enabled)) {
			$msg = JText::_('COM_DIGICOM_PAYMENT_PLUGIN_NOT_INSTALLED');
			$this->setRedirect(JRoute::_("index.php?option=com_digicom&view=cart"), $msg);
			return;
		}

		$askforbilling = $configs->get('askforbilling',0);

		// return -1 for not found core info, 2 for missing billing info, 1 for has core info
		$res = DigiComSiteHelperDigiCom::checkProfileCompletion($customer, $askforbilling);

		$plugin 			= JPluginHelper::getPlugin('digicom_pay',$processor);
		$pluginParams = json_decode($plugin->params);

		if(
				($askforbilling != 0 && $res == 2)
					or
				(isset($pluginParams->askforbilling) && $pluginParams->askforbilling && $res == 2)
			)
		{
			$this->setRedirect(JRoute::_('index.php?option=com_digicom&view=billing&return='.$return));
			JFactory::getApplication()->enqueueMessage(JText::_('COM_DIGICOM_BILLING_INFO_REQUIRED'));

			return true;
		}

		if( $res == 1 ) {

			$name = $customer->_user->name;
			$db = JFactory::getDBO();

			$sql = "SELECT `name` FROM #__digicom_customers WHERE id=".intval($customer->_user->id);
			$db->setQuery($sql);
			$db->query();
			$result = $db->loadObject();
			if(isset($result) && (trim($result->name) == ""))
			{
				$sql = "UPDATE #__digicom_customers set `name`='".addslashes(trim($name))."' where id=".intval($customer->_user->id);
			} elseif (!$result){
				$sql = "INSERT INTO #__digicom_customers(`id`, `name`) VALUES (".intval($customer->_user->id).", '".addslashes(trim($name))."')";
			}

			$db->setQuery($sql);
			$db->query();
		}

		$items 		= $cart->getCartItems($customer, $configs);
		// $tax 			= $cart->calc_price($items, $customer, $configs);
		$tax 			= $cart->tax;
		$total 		= $tax['taxed'];

		// Add free product
		if( (double)$total == 0 )
		{
			if(count($items) != "0")
			{

				$orderid 	= $cart->addFreeProduct($items, $customer, $tax);
				$dispatcher->trigger('onDigicomAfterPlaceOrder', array($orderid, $items, $tax, $customer, 'free'));

				// Order complete, now redirect to the original page
				$afterpurchase = $configs->get('afterpurchase', 2);
				$msg = JText::_("COM_DIGICOM_PAYMENT_FREE_PRUCHASE_COMPLETE_MESSAGE");
				switch ($afterpurchase) {
					case '2':
						$session->set('com_digicom', array('action' => 'payment_complete', 'id' => $orderid));
						$link 	= 'index.php?option=com_digicom&view=thankyou';
						break;
					case '1':
						JFactory::getApplication()->enqueueMessage($msg);
						$link 	= 'index.php?option=com_digicom&view=order&id='.$orderid;
						break;
					default:
						JFactory::getApplication()->enqueueMessage($msg);
						$item 	= $app->getMenu()->getItems('link', 'index.php?option=com_digicom&view=downloads', true);
						$Itemid = isset($item->id) ? '&Itemid=' . $item->id : '';
						$link 	= 'index.php?option=com_digicom&view=downloads'.$Itemid;
						break;
				}
				
				$this->setRedirect(JRoute::_($link, false));
				return true;
			}
		}
		else
		{
			// add products not free
			// print_r($customer);die;
			$db = JFactory::getDBO();
			$sql = "update #__digicom_session set transaction_details='" . base64_encode(serialize($customer)) . "' where id=" . $customer->_sid;
			$db->setQuery($sql);
			$db->query();

			$sql = "select processor from #__digicom_session where id='".$customer->_sid."'";
			$db->setQuery($sql);
			$prosessor = $db->loadResult();

			if(!isset($prosessor) || trim($prosessor) == ""){
				$prosessor = $processor;
			}

			//store order
			$order_id = $cart->addOrderInfo($items, $customer, $tax, $status = 'Pending', $prosessor);
			$dispatcher->trigger('onDigicomAfterPlaceOrder', array($order_id, $items, $tax, $customer));

			$cart->getFinalize($customer->_sid, $msg = '', $order_id, $type= 'new_order', $status);

			/* Prepare params*/
			$params = array();
			$params['user_id'] = $customer->_user->id;

			if(isset($customer) && isset($customer->_customer)){
				$customer->_customer->id = $user->id;
				$user = JFactory::getUser();

				$params['customer'] = new stdClass();
				$params['customer']->id = $user->id;
				$params['customer']->email = $user->get('email');

			}

			$params['products'] = $items; // array of products
			$params['processor'] = $prosessor;//JRequest::getVar('processor'); //'payauthorize';

			$gataways = JPluginHelper::getPlugin('digicom_pay', $params['processor']);

			if(is_array($gataways)){
				foreach($gataways as $gw) {
					if($gw->name == $prosessor) {
						$params['params'] = $gw->params;
						break;
					}
				}
			}
			else{
				$params['params'] = $gataways->params;
			}

			$params['order_id'] = $order_id;
			$params['sid'] = $customer->_sid;
			$params['order_amount'] = $tax['taxed'];
			$params['order_currency'] = $tax['currency'];

			$cart->storeOrderParams( $user->id, $order_id ,$params);
			$this->setRedirect(JRoute::_("index.php?option=com_digicom&view=checkout&id=".$order_id, false));

		}

		return true;
	}

	/**
	* get cart item products
	* @since 1.0.0
	* @return json_encoded string and then die;
	*/
	function getCartItem()
	{

		$cid 	= JRequest::getVar('cid', -1);
		$qty 	= JRequest::getVar('quantity'.$cid, 1);
		$db 	= JFactory::getDBO();
		$promocode 	= JRequest::getVar('promocode');

		$cart 			= $this->_model;
		$customer 	= $this->_customer;
		$configs 		= $this->_config;
		$sid 				= $this->_customer->_sid;

		if ($cid > 0) {

			$sql = "UPDATE `#__digicom_cart` SET `quantity` = ".$qty." WHERE `cid`=" . $cid; // sid = " . $sid . " and
			$db->setQuery( $sql );
			$db->query();

		}

		// if($promocode){
		// 	$cart->updateCart($customer, $configs);
		// }

		$items 	= $cart->getCartItems($customer, $configs);
		// $tax 	= $cart->calc_price($items, $customer, $configs);
		$tax 	= $cart->tax;
		$result = array();

		if ($cid > 0) {
			foreach($items as $key=> $item) {

				if ($item->cid == $cid)
				{
					$result['cid'] = $cid;
					$result['cart_item_qty'.$cid] = $item->quantity;
					$result['cart_item_price'.$cid] = DigiComSiteHelperPrice::format_price($item->price, $item->currency, true, $configs);
					$result['cart_item_discount'.$cid] = DigiComSiteHelperPrice::format_price($item->discount, $item->currency, true, $configs);
					$result['cart_item_total'.$cid] = DigiComSiteHelperPrice::format_price($item->subtotal-$item->discount, $item->currency, true, $configs);
				}
			}
		}

		$price = DigiComSiteHelperPrice::format_price($tax['price'], $tax['currency'], true, $configs);
		$total = DigiComSiteHelperPrice::format_price($tax['taxed'], $tax['currency'], true, $configs);
		$subtotal = DigiComSiteHelperPrice::format_price($tax['subtotal'], $tax['currency'], true, $configs);
		$result['cart_subtotal'] = $subtotal;
		$result['cart_total'] = $total;//"{$tax['taxed']}";

		//$cart = $this->_model;
		//$tax = $cart->calc_price($items, $customer, $configs);
		$result['cart_discount'] = DigiComSiteHelperPrice::format_price($tax["promo"], $tax['currency'], true, $configs);
		$result['cart_tax'] = DigiComSiteHelperPrice::format_price($tax["value"], $tax['currency'], true, $configs);

		echo json_encode($result);

		JFactory::getApplication()->close();
	}

	/**
	* validate users
	* it takes input field name n value
	* @since 1.0.0
	* @return true or false in binary. 0/1
	*/
	function validateInput()
	{
		$user = JFactory::getUser();
		$value = JRequest::getVar("value", "");
		if(trim($value) != ""){
			$input = JRequest::getVar("input", "");
			$db = JFactory::getDBO();
			$sql = "select count(*) from #__users where `".$input."` = '".$value."' and id !=".$user->id;
			$db->setQuery($sql);
			$db->query();
			$response = $db->loadResult();
			if($response > "0"){
				echo "1";
			}
			else{
				echo "0";
			}
		}
		JFactory::getApplication()->close();

	}

	/**
	* process payment method
	* validate submission n check processor n trigger plugins
	* @since 1.0.0
	* @return from model method proccessSuccess
	*/
	function processPayment()
	{

		$session 	= JFactory::getSession();
	 	$app			= JFactory::getApplication();
		$input 		= $app->input;

		$processor 	= $session->get('processor','');
		if(empty($processor)){
			$processor = $input->get('processor','');
		}

		if($processor == '')
		{
			$app->redirect(JRoute::_('index.php?option=com_digicom&view=orders', false),JText::_('COM_DIGICOM_PAYMENT_NO_PROCESSOR_SELECTED'));
			return false;
		}

		$post 			= $input->post->getArray();
		// $rawDataPost 			= $input->post->getArray();
		// $rawDataGet 			= $input->get->getArray();
		// $post = array_merge($rawDataGet, $rawDataPost);

		// after recieved payment request, get the status info
		$dispatcher = JDispatcher::getInstance();
		
		// add post field exception
		if( !count($post) ) $post = @file_get_contents('php://input');		

		$data = $dispatcher->trigger('onDigicom_PayProcesspayment', array($post));
		$data = $data[0];
		$order_id 	= $input->get('order_id', '', 'int');
		$sid 		= $input->get('sid', '', 'int');

		if(empty($sid)){
			$sid = $input->get('user_id','');
		}
		
		if(!$order_id)
		{
			if(isset($data['order_id'])){
				$order_id = $data['order_id'];
			}else{
				$app->redirect(JRoute::_("index.php?option=com_digicom&view=orders", false),JText::_('COM_DIGICOM_PAYMENT_NO_ORDER_PASSED'));
			}
		}

		$param = array();
		$param['params'] = JPluginHelper::getPlugin('digicom_pay', $processor)->params;
		// $param['handle'] = &$this;

		$configs 	= $this->_config;
		$cart 		= $this->_model;
		$items 		= $cart->getOrderItems($order_id);

		$products = array();
		if(isset($items) && count($items) > 0){
			foreach($items as $key=>$product){
					if(trim($product->name) != ""){
						$products[] = trim($product->name);
					}
			}
		}

		//after recieved payment, trigger any additional events
		$param["cart_products"] = implode(" - ", $products);
		$param["transaction"] = $data;

		$dispatcher->trigger('onDigicom_PayReceivePayment', array(& $param));

		$this->_model->proccessSuccess($post, $processor, $order_id, $sid, $data, $items);

		return true;
	}

	/**
	* cancel method
	* @since 1.0.0
	* @return redirect
	*/
	function cancel()
	{
		$mainframe = JFactory::getApplication();
		$mainframe->redirect(JURI::root(),JText::_('COM_DIGICOM_PAYMENT_CANCELLED_NOTICE'));
	}

	/**
	* get list of country method
	* @since 1.0.0
	* @return country option in list item
	*/
	function getCountries()
	{
		$db = JFactory::getDBO();
		$country = JRequest::getVar('ct', '');
		$cardstate = JRequest::getVar('cardstate', '');
		$html = '';

		if ($country != '')
		{
			$sql = "SELECT DISTINCT(`state`)
					FROM `#__digicom_states`
					WHERE `country`=" . $db->quote($country) . "
					ORDER BY `state`";
			$db->setQuery($sql);
			$states = $db->loadObjectList();

			for ($i=0; $i<count($states); $i++)
			{
				$html.= '<option value="'.$states[$i]->state.'" '.($cardstate == $states[$i]->state ? 'selected' : '').'>'.$states[$i]->state.'</option>';
			}
		}

		echo $html;
	}

	/**
	* update cart module content
	* @since 1.0.0
	* @return country option in list item
	*/
	function get_cart_content(){
		$module = JModuleHelper::getModule('mod_digicom_cart');
		echo JModuleHelper::renderModule($module);
		JFactory::getApplication()->close();
	}

	/*
	 * PayOrder method
	 * @since 1.0.0
	 * redirect users to checkout page
	 * */
	function payOrder(){

		$processor	= JRequest::getVar("processor", "");
		$id			= JRequest::getVar("id", "");

		$session = JFactory::getSession();
		$session->set('processor', $processor);

		$url = JRoute::_('index.php?option=com_digicom&view=checkout&id='.$id, false);
		//print_r($url);die;
		$this->setRedirect($url);
	}

	/**
	 * Proxy for getModel
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  The array of possible config values. Optional.
	 *
	 * @return  object  The model.
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'Cart', $prefix = 'DigiComModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}
}
