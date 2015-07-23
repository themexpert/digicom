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
		$this->registerTask("summary", "showSummary");
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
		$db = JFactory::getDBO();
		$pid = JFactory::getApplication()->input->get('pid',0);

		//check if this product is unpublished
		$sql = "select count(*)
				from #__digicom_products
				where `id`=".intval($pid)." and `published`=1 and `publish_up` <= ".time()." and (`publish_down` >= ".time()." OR `publish_down` = 0)";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadResult();

		//check if this product is unpublished
		$cid = JFactory::getApplication()->input->get('cid',0);
		$res = $this->_model->addToCart();
		$configs = $this->_config;
		$afteradditem = $configs->get('afteradditem',0);
		$from = JFactory::getApplication()->input->get('from','');

		if($from == "ajax"){
			$renew = JRequest::getVar("renew", "");
			//$this->showCart();
			$url = JRoute::_(JRoute::_("index.php?option=com_digicom&view=cart&layout=cart_popup&tmpl=component"), false);
			$this->setRedirect($url);
			return true;
		}
		global $Itemid;
		$cart_itemid = DigiComSiteHelperDigiCom::getCartItemid();

		if(JRequest::getVar('status', '-1') == 'change'){
			$url = JRoute::_("index.php?option=com_digicom&view=cart", false);
			$this->setRedirect($url);
		}
		else{
			if($res < 0) {
				$msg = JText::_("COM_DIGICOM_CART_WRONG_PID_OR_ACCESS_LABEL");
				$link = "index.php?option=com_digicom&view=category&id=" . $cid;
				echo $link;die;
				$this->setRedirect(JRoute::_($link, false), $msg);
			}
			elseif($res == 0){
				$msg = JText::_("COM_DIGICOM_CART_ERROR_UPDATING_CART_LABEL");
				$link = "index.php?option=com_digicom&view=category&id=" . $cid;
				$this->setRedirect(JRoute::_($link, false), $msg);
			}

			$from_add_plugin = JRequest::getVar("from_add_plugin", "0");
			if($from_add_plugin == 1){
				$afteradditem = 0;
			}

			$type_afteradd = $afteradditem;
			$gotocart = JRequest::getVar("gotocart", "");
			if($gotocart != ""){
				$type_afteradd = 0;
			}

			if($type_afteradd == 0){// Take to cart
				$url = JRoute::_("index.php?option=com_digicom&view=cart", false);
				$this->setRedirect($url);
			}
			// elseif($type_afteradd == 1){//Stay on product list
			// 	$msg = "";
			// 	$items = $this->_model->getCartItems($this->_customer, $configs);
			// 	if(count($items) > 0){
			// 		$temp_msg = array();
			// 		foreach($items as $key=>$value){
			// 			if(isset($value->name) && trim($value->name) != ""){
			// 				$temp_msg[] = $value->name;
			// 			}
			// 		}
			// 		$products = implode(", ", $temp_msg);
			// 		$msg = urlencode($products);
			// 	}
			// 	$link = JRoute::_("index.php?option=com_digicom&view=product&cid=" . $cid . $layout . "&product_added=" . $msg . "&Itemid=" . $Itemid, false);
			// 	$this->setRedirect($link, "");
			// }
			// elseif($type_afteradd == 2){//Show cart in pop up
			else{//Show cart in pop up
				$task = JRequest::getVar("task", "", "get");
				$url = JRoute::_("index.php?option=com_digicom&view=cart", false);
				$this->setRedirect($url);
			}
		}
	}

	/**
	* updateCart method
	* update cart status and use session
	* @since 1.0.0
	*/
	function updateCart()
	{

		$session = JFactory::getSession();
		$res = $this->_model->updateCart($this->_customer, $this->_config);

		$from = JRequest::getVar("from", "");
		if($from == "ajax")
		{
			$url = JRoute::_("index.php?option=com_digicom&view=cart&from=ajax&tmpl=component", false);
			$this->setRedirect($url);
		}
		else
		{
			$rp = JRequest::getVar('returnpage', '', 'request');
			$Itemid = JRequest::getInt('Itemid', 0);

			$firstname = JRequest::getVar("firstname", "");
			$lastname = JRequest::getVar("lastname", "");
			$company = JRequest::getVar("company", "");
			$email = JRequest::getVar("email", "");
			$username = JRequest::getVar("username", "");
			$password = JRequest::getVar("password", "");
			$password_confirm = JRequest::getVar("password_confirm", "");
			$address = JRequest::getVar("address", "");
			$city = JRequest::getVar("city", "");
			$zipcode = JRequest::getVar("zipcode", "");
			$country = JRequest::getVar("country", "");
			$state = JRequest::getVar("state", "");
			$array = array("firstname"=>$firstname, "lastname"=>$lastname, "company"=>$company, "email"=>$email, "username"=>$username, "password"=>$password, "password_confirm"=>$password_confirm, "address"=>$address, "city"=>$city, "zipcode"=>$zipcode, "country"=>$country, "state"=>$state);
			$processor = JRequest::getVar("processor", "");
			$session->set('new_customer', $array);
			$session->set('processor', $processor);

			if(strlen($rp) < 1)
			{
				$cart_itemid = DigiComSiteHelperDigiCom::getCartItemid();
				$this->setRedirect(JRoute::_("index.php?option=com_digicom&view=cart", false));
			}
			else
			{
				$this->setRedirect(JRoute::_("index.php?option=com_digicom&view=cart&task=cart.checkout&processor=".$processor, false, ($processor=='authorizenet' ? true : false)));
			}
		}
	}

	/**
	* cart item delete method
	* delete cart item and redirect to cart page
	* @since 1.0.0
	*/
	function deleteFromCart()
	{
		$session = JFactory::getSession();
		$res = $this->_model->deleteFromCart($this->_customer, $this->_config);
		$itemid = DigiComSiteHelperDigiCom::getCartItemid();
		$from = JRequest::getVar("from", "");
		$processor = JRequest::getVar("processor", "");
		$session->set('processor',$processor);

		if($from == "ajax"){
			//$this->showCart();
			$this->setRedirect(JRoute::_("index.php?option=com_digicom&view=cart&layout=cart_popup".($processor ? "&processor=".$processor : '')."&tmpl=component"));
		}
		else{
			$this->setRedirect(JRoute::_("index.php?option=com_digicom&view=cart&Itemid=".$itemid.($processor ? "&processor=".$processor : '')));
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
		$session = JFactory::getSession();
		$app = JFactory::getApplication();
		$Itemid = JRequest::getInt("Itemid", 0);
		$processor =  JRequest::getVar("processor", '');
		$session->set('processor',$processor);
		$returnpage = JRequest::getVar("returnpage", "");
		$_Itemid = $Itemid;
		$user = JFactory::getUser();
		$cart = $this->_model;
		$plugins_enabled = $cart->getPluginList();

		// set default redirect url
		$uri = JURI::getInstance();
		//echo $uri->toString();die;
		$return = base64_encode($uri->toString());

		// Check Login
		if(!$user->id or $this->_customer->_user->id < 1){
 			$this->setRedirect(JRoute::_("index.php?option=com_digicom&view=register&layout=register_cart&return=".$return));
			return true;
		}

		// Check Payment Plugin installed
		if (empty($plugins_enabled)) {
			$msg = JText::_('COM_DIGICOM_PAYMENT_PLUGIN_NOT_INSTALLED');
			$this->setRedirect(JRoute::_("index.php?option=com_digicom&view=cart"), $msg);
			return;
		}

		$customer = $this->_customer;
		$configs = $this->_config;
		$askforbilling = $configs->get('askforbilling',0);

		// return -1 for not found core info, 2 for missing billing info, 1 for has core info
		$res = DigiComSiteHelperDigiCom::checkProfileCompletion($customer, $askforbilling);

		//if username, firstname, email, id not found for user
		if( $res < 1 ) {
			$this->setRedirect("index.php?option=com_digicom&view=profile&layout=edit&processor=".$processor.'&return='.$return);
		}

		$plugin 			= JPluginHelper::getPlugin('digicom_pay',$processor);
		$pluginParams = json_decode($plugin->params);

		if(
				($askforbilling != 0 && $res == 2)
					or
				(isset($pluginParams->askforbilling) && $pluginParams->askforbilling && $res == 2)
			)
		{
			$this->setRedirect("index.php?option=com_digicom&view=profile&layout=edit&processor=".$processor.'&return='.$return);
			JFactory::getApplication()->enqueueMessage(JText::_('COM_DIGICOM_BILLING_INFO_REQUIRED'));

			return true;
		}

		if( $res == 1 ) {

			$fromsum = JRequest::getVar('fromsum', '0');
			if(!$fromsum) {
				$this->setRedirect(JRoute::_("index.php?option=com_digicom&view=cart&layout=summary&processor=".$processor));
				return true;
			}

			$name = $this->_customer->_user->name;
			$name_array = explode(" ", $name);
			$first_name = "";
			$last_name = "";
			if(count($name_array) == 1){
				$first_name = $name;
				$last_name = $name;
			}
			else{
				$last_name = $name_array[count($name_array)-1];
				unset($name_array[count($name_array)-1]);
				$first_name = implode(" ", $name_array);
			}

			$db = JFactory::getDBO();

			$sql = "SELECT `firstname`, `lastname` FROM #__digicom_customers WHERE id=".intval($this->_customer->_user->id);
			$db->setQuery($sql);
			$db->query();
			$result = $db->loadObject();
			if(isset($result) && (trim($result->firstname) == "" || trim($result->lastname) == "")){
				$sql = "UPDATE #__digicom_customers set `firstname`='".addslashes(trim($first_name))."', `lastname`='".addslashes(trim($last_name))."' where id=".intval($this->_customer->_user->id);
			} elseif (!$result){
				$sql = "INSERT INTO #__digicom_customers(`id`, `firstname`, `lastname`) VALUES (".intval($this->_customer->_user->id).", '".addslashes(trim($first_name))."', '".addslashes(trim($last_name))."')";
			}

			$db->setQuery($sql);
			$db->query();
			$this->_customer = new DigiComSiteHelperSession();
			$customer = $this->_customer;
		}
		$menu = $app->getMenu()->getItems('link', 'index.php?option=com_digicom&view=orders', true);
		$Itemid = isset($item->id) ? '&Itemid=' . $item->id : '';

		$total = 0;
		$fromsum 	= JRequest::getVar('fromsum', '0');
		$items 		= $cart->getCartItems($customer, $configs);
		$tax 		= $cart->calc_price($items, $customer, $configs);
		$total 		= $tax['taxed'];
		$now 		= time();
		if( (double)$total == 0 ) {
			if(count($items) != "0"){
				$orderid = $cart->addFreeProduct($items, $customer, $tax);

				// Order complete, now redirect to the original page
				if ( $configs->get('afterpurchase',1) == 1 ) {
					$link = 'index.php?option=com_digicom&view=orders'.$Itemid;
				} else {
					$item 	= $app->getMenu()->getItems('link', 'index.php?option=com_digicom&view=downloads', true);
					$Itemid = isset($item->id) ? '&Itemid=' . $item->id : '';
					$link = 'index.php?option=com_digicom&view=downloads'.$Itemid;
				}

				$this->setRedirect($link, JText::_("COM_DIGICOM_PAYMENT_FREE_PRUCHASE_COMPLETE_MESSAGE"));
			}
		}
		else
		{
			$db = JFactory::getDBO();
			$profile = "";
			$sql = "update #__digicom_session set transaction_details='" . base64_encode(serialize($customer)) . "' where sid=" . $customer->_sid;
			$db->setQuery($sql);
			$db->query();

			$sql = "select processor from #__digicom_session where sid='".$this->_customer->_sid."'";
			$db->setQuery($sql);
			$prosessor = $db->loadResult();

			if(!isset($prosessor) || trim($prosessor) == ""){
				$prosessor = $processor;
			}

			//store order
			$order_id = $cart->addOrderInfo($items, $customer, $tax, $status = 'Pending', $prosessor);
			$cart->getFinalize($this->_customer->_sid, $msg = '', $order_id, $type= 'new_order');

			/* Prepare params*/
			$params = array();
			$params['user_id'] = $this->_customer->_user->id;

			if(isset($this->_customer) && isset($this->_customer->_customer)){
				$this->_customer->_customer->id = $user->id;
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
			$params['sid'] = $this->_customer->_sid;
			$params['order_amount'] = $items[-2]['taxed'];
			$params['order_currency'] = $items[-2]['currency'];

			$cart->storeOrderParams( $user->id, $order_id ,$params);
			$this->setRedirect(JRoute::_("index.php?option=com_digicom&view=checkout&order_id=".$order_id."&processor=".$params['processor']));

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

		$cid = JRequest::getVar('cid', -1);
		$qty = JRequest::getVar('quantity'.$cid, 1);
		$db = JFactory::getDBO();
		if ($cid > 0) {

			$cart = $this->_model;
			$customer = $this->_customer;
			$configs = $this->_config;

			$sid = $this->_customer->_sid;
			$sql = "UPDATE #__digicom_cart SET quantity = ".$qty." where cid=" . $cid; // sid = " . $sid . " and
			$db->setQuery( $sql );
			$db->query();

			// get product id
			$sql = "select item_id from #__digicom_cart where cid = " . $cid . " and sid = " . $sid;
			$db->setQuery( $sql );
			$pid = (int)$db->loadResult();

			$items = $cart->getCartItems($customer, $configs);
			$result = array();

			foreach($items as $key=>$item) {
				if ($key < 0)	continue;
				if ($item->cid == $cid)
				{
					$result['cid'] = $cid;
					$result['cart_item_qty'.$cid] = $item->quantity;
					$result['cart_item_price'.$cid] = DigiComSiteHelperDigiCom::format_price($item->price, $item->currency, true, $configs);
					$result['cart_item_discount'.$cid] = DigiComSiteHelperDigiCom::format_price($item->discount, $item->currency, true, $configs);
					$result['cart_item_total'.$cid] = DigiComSiteHelperDigiCom::format_price($item->subtotal-$item->discount, $item->currency, true, $configs);
				}
			}
			//print_r($items);die;
			$total = DigiComSiteHelperDigiCom::format_price($items[-2]['taxed'], $items[-2]['currency'], true, $configs);
			$result['cart_total'] = $total;//"{$items[-2]['taxed']}";

			$cart = $this->_model;
			$cart_tax = $cart->calc_price($items, $customer, $configs);
			$result['cart_discount'] = DigiComSiteHelperDigiCom::format_price($cart_tax["promo"], $items[-2]['currency'], true, $configs);
			$result['cart_tax'] = DigiComSiteHelperDigiCom::format_price($cart_tax["value"], $items[-2]['currency'], true, $configs);
			echo json_encode($result);

		} else {

			echo json_encode(array());
		}

		exit;
	}

	/**
	* validate users
	* it takes input field name n value
	* @since 1.0.0
	* @return true or false in binary. 0/1
	*/
	function validateInput()
	{
		$value = JRequest::getVar("value", "");
		if(trim($value) != ""){
			$input = JRequest::getVar("input", "");
			$db = JFactory::getDBO();
			$sql = "select count(*) from #__users where `".$input."` = '".$value."'";
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

	 	$session = JFactory::getSession();
	 	$app		= JFactory::getApplication();
		$input 		= $app->input;

		$processor 	= $session->get('processor','');
		if(empty($processor)) $processor = $input->get('processor','');
		$order_id 	= $input->get('order_id',0);
		$sid 		= $input->get('sid','');
		$pay 		= $input->get('pay','');
		$post 		= $input->post->getArray();

		if($processor == ''){
			$item 	= $app->getMenu()->getItems('link', 'index.php?option=com_digicom&view=cart', true);
			$Itemid = isset($item->id) ? '&Itemid=' . $item->id : '';
			$app->redirect(JRoute::_('index.php?option=com_digicom&view=orders'),JText::_('COM_DIGICOM_PAYMENT_NO_PROCESSOR_SELECTED'));
			return false;
		}

		if($order_id == 0)
		{
			$item 	= $app->getMenu()->getItems('link', 'index.php?option=com_digicom&view=cart', true);
			$Itemid = isset($item->id) ? '&Itemid=' . $item->id : '';
			$app->redirect(JRoute::_("index.php?option=com_digicom&view=orders"),JText::_('COM_DIGICOM_PAYMENT_NO_ORDER_PASSED'));
		}

		$param = array();
		$param['params'] = JPluginHelper::getPlugin('digicom_pay', $processor)->params;
		$param['handle'] = &$this;

		$customer 	= $this->_customer;
		$configs 	= $this->_config;
		$cart 		= $this->_model;
		$items 		= $cart->getOrderItems($order_id, $configs);

		$products = array();
		if(isset($items) && count($items) > 0){
			foreach($items as $key=>$product){
				if($key >= 0)
				{
					if(trim($product->name) != ""){
						$products[] = trim($product->name);
					}
				}
			}
		}

		// after recieved payment request, get the status info

		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('digicom_pay', $processor);
		$data = $dispatcher->trigger('onTP_Processpayment', array($post));

		//after recieved payment, trigger any additional events
		$param["cart_products"] = implode(" - ", $products);
		$param["transaction"] = $data;

		JPluginHelper::importPlugin('digicom_pay');
		$results_plugins = $dispatcher->trigger('onReceivePayment', array(& $param));

		$this->_model->proccessSuccess($post, $processor, $order_id, $sid,$data, $items);

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
