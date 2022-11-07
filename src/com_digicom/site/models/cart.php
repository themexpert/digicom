<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;
use Joomla\Registry\Registry;
/**
 * DigiCom Cart model
 *
 * @package     DigiCom
 * @since       1.0.0
 */
JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_digicom/tables', 'Table');
class DigiComModelCart extends JModelItem
{
	public $orders 		= array();
	public $packages 	= array();
	public $configs 	= array();
	public $customer 	= array();

	public $_items = null;
	public $tax = null;
	public $promo_data = null;

	function __construct()
	{
		parent::__construct();
		$this->configs = JComponentHelper::getComponent('com_digicom')->params;
		$this->customer = new DigiComSiteHelperSession();
	}

	function getCustomer(){
		return $this->customer;
	}

	/**
	 * Get plugins type DigiCom payment list
	 *
	 * @return array
	 */
	function getPluginList()
	{

		if ( !empty( $this->plugins ) && is_array( $this->plugins ) ) {
			return $this->plugins;
		}

		$plugins = JPluginHelper::getPlugin( 'digicom_pay' );

		return $plugins;

	}

	/**
	 * Method to add product to cart object
	 *
	 * @return  int cart id
	 * @since   1.0.0
	 */
	function addToCart()
	{
		$dispatcher	= JEventDispatcher::getInstance();
		$app 		= JFactory::getApplication();
		$user		= JFactory::getUser();
		$db			= JFactory::getDbo();
		$customer	= $this->customer;

		$sid		= $customer->_sid; //digicom session id
		$uid		= $customer->_user->id; //joomla user id
		$pid		= $app->input->get('pid', 0);
		$cid		= $app->input->get('cid', 0);
		$qty 		= $app->input->get( 'qty', 1, 'request' ); //product quantity
		$renew 		= $app->input->get("renew", 0);
		

		// check if product id less then 1, then its too bad, return -1
		if($pid < 1)
		{
			return (-1);
		}

		// TODO: fix access checking
		$product  	= $this->getProduct($pid);
		if(!$product)
		{
			return (-1);
		}

		$productname 	= $product->name;
		// $access 		= $product->access;

		// now we have passed basic check, move on ...
		$query = $db->getQuery(true);
		$query->select(array('cid','item_id','quantity'))
			  ->from($db->quoteName('#__digicom_cart'))
			  ->where($db->quoteName('sid') . '='.$db->quote($sid))
			  ->where($db->quoteName('item_id') . '='.$db->quote($pid));
		$db->setQuery($query);
		$data = $db->loadObject();

		if($data)
		{
			//we already have this item in the cart
			$item_id 	= $data->item_id; //lets just check if item is in the cart
			$item_qty 	= $data->quantity;
			$cid 		= $data->cid;

			//lets update if not same quantity
			if($item_qty != $qty)
			{
				$db->clear();
				$query = $db->getQuery(true);
				// Fields to update.
				$fields = array(
				    $db->quoteName('quantity') . ' = ' . $db->quoteName('quantity') . ' + ' . $qty
				);

				// Conditions for which records should be updated.
				$conditions = array(
				    $db->quoteName('sid') . ' = '.intval($sid),
				    $db->quoteName('item_id') . ' = ' . $db->quote($pid)
				);

				$query->update($db->quoteName('#__digicom_cart'))->set($fields)->where($conditions);
				$db->setQuery($query);
				$db->execute();
			}
		}

		if(!isset($item_id))
		{
			// Prepare the insert query.
			$db->clear();
			$query = $db->getQuery(true);

			// Insert columns.
			$columns = array('quantity', 'item_id', 'sid', 'userid');

			// Insert values.
			$values = array($db->quote(intval($qty)),$db->quote(intval($pid)), $db->quote(intval($sid)), $db->quote(intval($uid)));

			
			$query
			    ->insert($db->quoteName('#__digicom_cart'))
			    ->columns($db->quoteName($columns))
			    ->values(implode(',', $values));

			//no such item in cart- inserting new row
			$db->setQuery($query);

			if($db->execute()){

				$cid = $db->insertid(); //cart id of the item inserted
				// DigiComSiteHelperLog::setLog('add2cart', 'cart addToCart', $pid, $productname . ' Has been added to cart', null,1);	
			}
		}

		$dispatcher->trigger('onDigicomAfterAddCartItem', array('com_digicom.cart', &$pid));

		// trigger renew event
		if($renew){
			$dispatcher->trigger('onDigicomRenewRequest', array('com_digicom.cart', &$pid));
		}
		
		return $cid;
	}

	/**
	* Method getProduct
	* @param $id
	* @return object
	*/
	public function getProduct($id)
	{
		// now get the product with access label
		$db				= JFactory::getDbo();
		$user			= JFactory::getUser();

		$query = $db->getQuery(true);
		$query->select('name')
			  ->from($db->quoteName('#__digicom_products'))
			  ->where($db->quoteName('id') . " = " . $id);

		// Check access
		// $groups = implode(',', $user->getAuthorisedViewLevels());
		// $query->where($db->quoteName('access').' IN (' . $groups . ')');

		// Check Publishdown
		if ((!$user->authorise('core.edit.state', 'com_digicom')) && (!$user->authorise('core.edit', 'com_digicom')))
		{
			// Filter by start and end dates.
			$nullDate = $db->quote($db->getNullDate());
			$date = JFactory::getDate();

			$nowDate = $db->quote($date->toSql());

			$query->where('(publish_up = ' . $nullDate . ' OR publish_up <= ' . $nowDate . ')')
				->where('(publish_down = ' . $nullDate . ' OR publish_down >= ' . $nowDate . ')');
			

			// Check status
			$query->where('(published = 1)');
		}

		// print_r($query->__toString());die;
		$db->setQuery( $query );
		return $db->loadObject();
	}

	function getCartItems($customer, $configs)
	{
		$dispatcher	= JEventDispatcher::getInstance();

		if(!is_null($this->_items)){
			return $this->_items;
		}

		if(is_object($customer)){
			$sid = $customer->_sid;
		}

		if(is_array($customer)){
			$sid = $customer['sid'];
		}

		if(is_object($customer) && isset($customer->_user->id)){
			$uid = $customer->_user->id;
		}

		if(is_array($customer)){
			$uid = $customer['userid'];
		}

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		// $query->select(array('c.*', 'p.*'))
		$query->select(array('c.*', 'p.id','p.catid','p.product_type','p.name','p.images', 'p.price', 'p.price_type', 'p.expiration_length','p.expiration_type', 'p.bundle_source', 'p.attribs', 'p.language', 'p.attribs'))
			  ->from($db->quoteName('#__digicom_products','p'))
			  ->join('INNER', $db->quoteName('#__digicom_cart', 'c') . ' ON (' . $db->quoteName('c.item_id') . ' = ' . $db->quoteName('p.id') . ')')
			  ->where($db->quoteName('c.sid') . '='.$db->quote(intval($sid)))
			  ->where($db->quoteName('c.item_id') . '='.$db->quoteName('p.id'))
			  ->order($db->quoteName('p.ordering') . ' DESC');
		$db->setQuery($query);
		$items = $db->loadObjectList();

		// print_r($items);die;
		//change the price of items if needed
		for ( $i = 0; $i < count( $items ); $i++ )
		{
			$item = $items[$i];
			$item->discount = 0;
			$item->currency = $configs->get('currency','USD');
			
			// price : main price for product
			// $item->price = DigiComSiteHelperPrice::format_price( $item->price, $item->currency, false, $configs ); 
			
			// subtotal: total amount to be paid for all quantity but without discount
			$item->subtotal = $item->price * $item->quantity; 
			
			// subtotal_formated: Price of single product with discount
			$subtotal_formated = ($item->price - $item->discount);
			// $item->subtotal_formated = DigiComSiteHelperPrice::format_price( $subtotal_formated, $item->currency, false, $configs ); 
			$item->subtotal_formated = $subtotal_formated; 
			
			// price_formated: final price for the item with all quantity with all discount
			$total_discount = ($item->discount * $item->quantity);
			$price_formated = ($item->subtotal * ($total_discount ? $total_discount : 1)); 
			// $item->price_formated = DigiComSiteHelperPrice::format_price( $price_formated, $item->currency, false, $configs ); 
			$item->price_formated = $price_formated; 

		}
		
		$this->_items = $items;

		// trigger digicom event for after prepare cart items
		$dispatcher->trigger(
			'onDigicomAfterPrepareCartItems', 
			array('com_digicom.cart', &$items, &$this->customer)
		);

		if(count($items) > 0)
		{
			// we have items, now manupulate the price and tax variable
			$this->calc_price($items, $customer, $configs);
		}
		else
		{
			$db->clear();
			$sql = "update #__digicom_session set cart_details='' where id='" . $customer->_sid . "'";
			$db->setQuery($sql);
			$db->execute();
		}

		return $this->_items;

	}

	/**
	 * calc_price method to calculate the discount, promocode
	 *
	 * @param   object  $items 
	 * @param   object  $cust_info customer object from session helper with _sid
	 * @param   object  $config digicom settings or config
	 *
	 * @return  array of payment finalize variable, and set $this->tax as global
	 *
	 * @since   1.0
	 */		
	function calc_price($items, $cust_info, $configs, $calculate = true)
	{
		if(empty($items)) return;
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$session = JFactory::getSession();
		$dispatcher	= JEventDispatcher::getInstance();

		if ( null != $configs->get('totaldigits','') ) {
			$configs = $this->getInstance( "Config", "digicomModel" );
			$configs = $configs->getConfigs();
		}

		$pay_flag = false;
		$can_promo = true;
		$payprocess = array();

		$total = 0;
		$price_format = '%' . $configs->get('totaldigits','') . '.' . $configs->get('decimaldigits','2') . 'f';
		$payprocess['number_of_products'] = 0;
		$payprocess['shipping'] = 0;
		$payprocess['currency'] = $configs->get('currency','USD');
		$payprocess['promo'] = 0;
		$payprocess['item_discount'] = 0;
		
		//--------------------------------------------------------
		// Promo code
		//--------------------------------------------------------
		$promo = $this->get_promo( $cust_info );
		// print_r($promo);die;
		$promovalue = 0;
		$addPromo = false;
		$ontotal = false;
		$onProduct = false;
		$promo_applied = 0;

		if($promo->id > 0 && empty($promo->error)){
			//we got real promocode
			$promoid = $promo->id;
			$promocode = $promo->code;
			$addPromo = true;
			//validate promocode
			if($promo->discount_enable_range==1){
				// for entire cart
				$ontotal = true;
			}else{
				$onProduct = true;
			}
		} else {
			$promoid = '0';
			$promocode = '';
		}
		//echo $onProduct;die;
		//print_r($items);die;
		foreach ( $items as $item )
		{
			//initial promo amount as 0, so later we can use it
			$promoamount = 0;
			$payprocess['number_of_products'] += $item->quantity;

			//check promocode on product apply
			if($addPromo && $onProduct){
				// Get product restrictions
				$query = $db->getQuery(true);
				$query->select($db->quoteName('p.productid'))
					  ->from($db->quoteName('#__digicom_promocodes_products','p'))
					  ->where($db->quoteName('p.promoid') . '='.$db->quote($promo->id))
					  ->where($db->quoteName('p.productid') . '='.$db->quote($item->id));
				$db->setQuery($query);
				$promo->product = $db->loadObject();

				if (count($promo->product) && $promo->aftertax == '0')
				{
					//promo discount should be applied before taxation
					//we get product to calculate discount
					if ($promo->promotype == '0')
					{
						// Use absolute values
						$promoamount = $promo->amount;
						if($promoamount > $item->price){
							$promoamount = $item->price;
						}

					}
					else
					{
						// Use percentage
						$promoamount = $item->price * $promo->amount / 100;
					}

					if($promoamount > 0){

						// lets prepare promoamount by quantity
						$item->discount = $promoamount = $promoamount * $item->quantity;
						$promovalue += $promoamount;

						// $item->discount = $promoamount;
						// $item->price_formated = $item->subtotal - $promoamount;

						// $item->subtotal = $item->subtotal - $promoamount;

						$payprocess['item_discount'] = 1;
					}
					$payprocess['discount_calculated'] = 1;
					//print_r($item);die;
				}
			} // end if for: product promo check

			$total += $item->subtotal;
		}

		// lets declare the total payable amount
		$payprocess['price'] = $total;

		// lets declare the subtotal without discount
		$payprocess['subtotal'] = $total;

		if($addPromo && $onProduct){
			$total -= $promovalue;
			$promo_applied = 1;
			$payprocess['promo'] = $promovalue;

			// lets recalculate it
			$payprocess['subtotal'] = $total;
		}
		elseif($addPromo && $ontotal)
		{
			//--------------------------------------------------------
			// Promo code on cart
			//--------------------------------------------------------

			//echo 'apply promo on cart';die;
			//now lets apply promo discounts if there are any
			if($promo->promotype == '0'){//use absolute values
				$cartPromototal = $promo->amount;
				if($cartPromototal > $total){
				 	$cartPromototal = $total;
				}
				$total -= $cartPromototal;
				$promovalue = $cartPromototal;
			}
			else{ //use percentage
				$promovalue = $total * $promo->amount / 100;
				$total *= 1 - $promo->amount / 100;
			}
			$payprocess['promo_order'] = 1;
			$payprocess['promo'] = $promovalue;
			$promo_applied = 1;

			$payprocess['discount_calculated'] = 1;
		}

		$name = 'promocode'.$cust_info->_sid;
		$justapplied = $session->get($name,false);

		if($promo_applied && ($promovalue > 0))
		{
			//echo $justapplied;die;
			if($justapplied){
				$session->clear($name);
				JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_DIGICOM_CART_PROMOCODE_APPLIED',$promocode),'success');
			}
		}elseif($promo_applied){
			if($justapplied){
				$session->clear($name);
				JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_DIGICOM_CART_PROMOCODE_NOT_APPLIED',$promocode),'warning');
			}
		// }elseif(!empty($promocode)){
		}else{
			$db->clear();
			$sql = "update #__digicom_session set cart_details='promocode=" . $promocode . "' where id='" . $cust_info->_sid . "'";
			$db->setQuery($sql);
			$db->execute();
		}

		$payprocess['payable_amount'] = $total;

		// lets calculate the tax
		// tax amount is in (%) percentage
		$tax_amount = DigiComSiteHelperPrice::tax_price($total, $configs, true);
		$payprocess['value'] = ($total * $tax_amount)/100;

		if(!isset($payprocess['value'])) $payprocess['value'] = 0;

		$payprocess['promo_error'] = (!$user->id && isset($promo->orders) && count($promo->orders) ? JText::_("DIGI_PROMO_LOGIN") : '');
		//$payprocess['total'] = $total;

		$price_with_tax = $configs->get('price_with_tax', 0);
		if($price_with_tax){
			$payprocess['taxed'] = $total + $payprocess['shipping'];
		}else{
			$payprocess['taxed'] = $total + $payprocess['value'] + $payprocess['shipping'];
		}

		$payprocess['discount_calculated'] = (isset($payprocess['discount_calculated']) ? $payprocess['discount_calculated'] : 0);
		//$payprocess['taxed'] = DigiComSiteHelperPrice::format_price( $payprocess['taxed'], $payprocess['currency'], false, $configs ); //sprintf($price_format, $payprocess['taxed']);//." ".$payprocess['currency'];
		$payprocess['type'] = 'TAX';
		if($calculate){
			// trigger digicom event for after calculate cart price
			$dispatcher->trigger(
				'onDigicomAfterCalculateCartItems', 
				array('com_digicom.cart', &$items, &$payprocess, &$this->customer)
			);		
			
			$this->tax = $payprocess;
			// print_r($payprocess);die;
		}
		return $payprocess;
	}

	// Check if product is in the promotion restriction
	function isRestrictedProduct($item, $products)
	{
		foreach ($products as $restrict_product)
		{
			if ($item->item_id == $restrict_product->productid)
			{
				return true;
			}
		}

		return false;
	}

	//gets promo code details from database
	//if checkvalid == 1 - checks if this promocode is still valid and can be used.

	function get_promo( $customer, $checkvalid = 1 )
	{
		if($this->promo_data) return $this->promo_data;

		$db = JFactory::getDbo();
		$dispatcher = JDispatcher::getInstance();

		$app = JFactory::getApplication();
		$session = JFactory::getSession();
		$sid = $customer->_sid;

		$db = JFactory::getDbo();
		$sql = "select cart_details from #__digicom_session where id='" . intval($sid) . "'";
		$db->setQuery( $sql );
		$promodata = $db->loadResult();
		//print_r($promodata);die;
		$promodata = explode( "=", $promodata );
		if ( $promodata[0] == 'promocode' ){
			$promocode = $promodata[1];
		}else{
			$promocode = '';
		}

		if ( strlen( $promocode ) > 0 ) {//valid promocode was provided
			$promo_data = $this->getTable( "Discount" );
			$promo_data->load(array('code' => $promocode));

			// Get products restrictions
			$sql = "SELECT p.`productid` FROM `#__digicom_promocodes_products` AS p WHERE p.`promoid`='" . $promo_data->id."'";
			$db->setQuery( $sql );
			$promo_data->products = $db->loadObjectList();

		}else{
			$promo_data = $this->getTable( "Discount" );
			$promo_data->error = '';

			// prepare table to fresh info
			$properties = $promo_data->getProperties(1);
			$promo_data = JArrayHelper::toObject($properties, 'JObject');

			return $this->promo_data = $promo_data;
		}

		// lets trigger the event
		$dispatcher->trigger('onDigicomPrepareDiscountValue', array('com_digicom.cart', &$promo_data, &$customer));

		//code exists and we're about to validate it
		if ( $promo_data->id > 0 && $checkvalid == 1 )
		{
			// chekc if we need to update promo info if fails
			$update = true;

			$today = date('Y-m-d 00:00:00');
			$tomorrow = date('Y-m-d  00:00:00', strtotime($today . "+1 days"));

			$now = strtotime($today);
			$tomorrow = strtotime($tomorrow);
			$timestart = strtotime($promo_data->codestart);
			$timeend = strtotime($promo_data->codeend);
			$nullDate = strtotime('0000-00-00 00:00:00');

			$remain = $promo_data->codelimit - $promo_data->used;
			$used = $promo_data->used;
			$limit = $promo_data->codelimit;
			$published = $promo_data->published;

			if ( ($timestart <= $now) && ($timeend >= $now || $timeend == $nullDate ) && ($limit == 0 || $used < $limit) && $published == "1")
			{
				// we got correct promo, no need to update
				$update = false;

				//add this code to user's cart
				$promoerror = '';
				$name = 'promocode'.$customer->_sid;
				$promomesg = $session->get($name, false);
				if(!$promomesg){
					$session->clear($name);
				}

			}
			else if ($published == "0")
			{
				$promoerror = JText::_( "COM_DIGICOM_DISCOUNT_CODE_UNP" );
				$sql = "update #__digicom_session set cart_details='promoerror=" . $promoerror . "' where id='" . intval($sid) . "'";
				// $app->enqueueMessage($promoerror,'warning');
			}
			else if ($limit > 0  && $used  >= $limit)
			{
				$promoerror = JText::_( "COM_DIGICOM_DISCOUNT_CODE_EXPIRED_AMOUNT" );
				$sql = "update #__digicom_session set cart_details='promoerror=" . $promoerror . "' where id='" . intval($sid) . "'";
				// $app->enqueueMessage($promoerror,'warning');
			}
			else if ($timeend < $tomorrow && $timeend != $nullDate)
			{
				$promoerror = JText::_( "COM_DIGICOM_DISCOUNT_CODE_EXPIRED_DATE" );
				$sql = "update #__digicom_session set cart_details='promoerror=" . $promoerror . "' where id='" . intval($sid) . "'";
			}
			else
			{
				// seems its not yet published
				$promoerror = JText::_( "COM_DIGICOM_DISCOUNT_CODE_NOT_FOR_APPLY" );
				$sql = "update #__digicom_session set cart_details='promoerror=" . $promoerror . "' where id='" . intval($sid) . "'";
			}

			$promo_data->error = $promoerror;
			if($update){
				$db->setQuery( $sql );
				$db->execute();				
			}

			if ( !empty($promoerror) ) {
				//promo code is invalid
				JFactory::getApplication()->enqueueMessage(JText::_($promoerror), 'warning');
			}

		}else{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_DIGICOM_DISCOUNT_CODE_WRONG'), 'warning');
		}

		// prepare table to fresh info
		$properties = $promo_data->getProperties(1);
		$promo_data = JArrayHelper::toObject($properties, 'JObject');
		
		return $this->promo_data = $promo_data;

	}

	function updateCart( $customer, $configs )
	{
		$app = JFactory::getApplication();
		$input = $app->input;
		$db = JFactory::getDbo();
		$sid = $customer->_sid;
		// $uid = $customer->_user->id;
		$session = JFactory::getSession();
		// Update prosessor
		$processor = $input->get( 'processor', '' );
		if(empty($processor)){
			$processor = $session->get('processor','offline');
		}
		$promocode = $input->get( 'promocode', '' );
		if($promocode){
			$name = 'promocode'.$customer->_sid;
			$session->set($name, true);
		}
		
		// Create and populate an object.
		$object = new stdClass();
		$object->id 			= $sid;
		$object->processor 		= $processor;
		$object->cart_details 	= "promocode=" . $promocode;
				 
		// Insert the object into the session object table.
		$result = JFactory::getDbo()->updateObject('#__digicom_session', $object, 'id');
		

		// $sql = "update #__digicom_session set shipping_details='' where sid='" . intval($sid) . "'";
		// 	$db->setQuery( $sql );
		// 	$db->execute();

		return $result;

	}

	function deleteFromCart( $customer, $configs )
	{

		$db = JFactory::getDbo();

		$cid = JRequest::getInt( 'cartid', -1 );
		$qty = JRequest::getInt( 'qty', 0 );

		if ( (JRequest::getVar( 'discount', 0 ) == 0) && ($cid != -1) ) {
			$sql = "delete from #__digicom_cart where cid='" . intval($cid) . "'";
			$db->setQuery( $sql );
			$db->query();

		} elseif ( ($cid != -1) && ($qty > 0) ) {
			$sql = "update #__digicom_cart set quantity=quantity-" . $qty . " where cid='" . intval($cid) . "'";
			$db->setQuery( $sql );
			$db->query();
		}

	}

	function checkCartIsEmpty() 
	{

		$conf = $this->getInstance( "config", "digicomModel" );
		$configs = $conf->getConfigs();

		$items = $this->getCartItems( $this->customer, $configs );

		if ( count($items) > 0 ) {
			return false;
		} else {
			return true;
		}
	}

	function emptyCart($sid = 0)
	{
		$db = JFactory::getDbo();
		$reg = JSession::getInstance("none", array());
		$sid = (!$sid ? $reg->set("digicomid", 0) : $sid);
		if(!$sid) return;

		$sql = "update #__digicom_session set cart_details='', transaction_details='' where id='" . $sid . "'";
		$db->setQuery( $sql );
		$db->execute();

		$sql = "delete from #__digicom_cart where sid='" . intval($sid) . "'";
		$db->setQuery( $sql );
		$db->execute();

	}

	function addFreeProduct($items, $customer, $tax){
		$config			= JFactory::getConfig();
		$tzoffset		= (int) $config->get('offset'); //$now = time();
		$now 			= date('Y-m-d H:i:s', time() + $tzoffset);

		$orderid = $this->addOrder($items, $tax, $customer, $now, 'free');
		$this->addOrderDetails($items, $orderid, $now, $customer);

		if($orderid){
			$info = array(
				'products' => $items,
				'tax' => $tax
			);
			DigiComSiteHelperLog::setLog('purchase', 'cart checkout', $orderid, 'Order id#'.$orderid.' Free purchase with '.$tax['number_of_products'].' products', json_encode($info));
		}


		$type = 'complete_order';
		DigiComSiteHelperLicense::addLicenceSubscription($items, $customer->_customer->id, $orderid, $type);
		$this->goToSuccessURL($customer->_sid, '', $orderid , $type);
		return $orderid;
	}

	function addOrderInfo($items, $customer, $tax, $status,$prosessor)
	{
		$config = JFactory::getConfig();
		$tzoffset = (int) $config->get('offset');
		$now = date('Y-m-d H:i:s', time() + $tzoffset);
		
		$orderid = $this->addOrder($items, $tax, $customer, $now, $prosessor,$status);
		$this->addOrderDetails($items, $orderid, $now, $customer,$status);

		if($orderid){
			$info = array(
				'products' => $items,
				'tax' => $tax
			);
			DigiComSiteHelperLog::setLog('purchase', 'cart checkout', $orderid, 'Order id#'.$orderid.' just placed order with '.$tax['number_of_products'].' products & method is '.$prosessor, json_encode($info),$status);
		}

		DigiComSiteHelperLicense::addLicenceSubscription($items, $customer->_customer->id, $orderid, $status);

		return $orderid;
	}

	/**
	 * Get return url
	 * @param <boolean> $type - true if success url, false to fail url
	 */
	function getReturnUrl($type){
		global $Itemid;

		$conf = $this->getInstance( "config", "digicomModel" );
		$configs = $conf->getConfigs();

		if($configs->get('afterpurchase',1) == 0){
			$controller = "Downloads";
			$task = "show";
		}
		else{
			$controller = "Orders";
			$task = "list";
		}

		$order_id = JRequest::getVar('order_id','');
		$sid = JRequest::getVar('sid',$order_id);
		$mosConfig_live_site = DigiComSiteHelperDigiCom::getLiveSite();
		$success_url = $mosConfig_live_site . "/index.php?option=com_digicom&controller=" . $controller . "&task=" . $task . "&success=1&sid=" . $sid;
		$failed_url = $mosConfig_live_site . "/index.php?option=com_digicom&controller=" . $controller . "&task=" . $task . "&success=0&sid=" . $sid;
		$success_url = str_replace("https://", "http://", $success_url);
		$failed_url = str_replace("https://", "http://", $failed_url);
		$success_url = JRoute::_($success_url . '&Itemid=' . $Itemid);
		$failed_url = JRoute::_($failed_url . '&Itemid=' . $Itemid);

		if($type){
			return $success_url;
		}
		else{
			return $failed_url;
		}
	}

	function checkSuccess( $sid )
	{
		if ( empty( $sid ) ) {
			dsdebug( 'Empty sid' );
			die;
		}
		$db = JFactory::getDbo();
		$sql = "select count(sid) from #__digicom_cart where sid='" . intval($sid) . "'";
		$db->setQuery( $sql );
		$sidcount = $db->loadResult();

		if ( $sidcount > 0 ) {
			return false;
		} else {
			return true;
		}

	}

	function proccessSuccess($post, $pay_plugin, $order_id, $sid, $data, $items)
	{
		$app 			= JFactory::getApplication();
		$session 	= JFactory::getSession();
		$customer = $this->loadCustomer($sid);
		if(!$customer)
		{
			$order 	= $this->getOrder($order_id);
			$sid 		= $customer = $order->userid;
		}

		$conf 		= $this->getInstance( "config", "digicomModel" );
		$configs	= $conf->getConfigs();

		$result 	= $post;

		$logtype = 'status';
		$link = JRoute::_('index.php?option=com_digicom&view=orders');
		if(isset($data['status']))
		{
			$_SESSION['in_trans'] = 1;

			switch ($data['status']) 
			{
				case 'A':
					$status 	= "Active";
					$logtype 	= "payment";
					$msg 			= JText::_("COM_DIGICOM_PAYMENT_SUCCESSFUL_THANK_YOU");
					break;

				case 'C':
					$msg 			= JText::_("COM_DIGICOM_PAYMENT_CANCEL_THANK_YOU");
					$status 	= "Cancel";
					$logtype 	= "payment";
					$app->enqueueMessage($msg,'message');
					break;

				case 'P':
					$status = "Pending";
					$msg 		= JText::_("COM_DIGICOM_PAYMENT_PENDING_THANK_YOU");
					$app->enqueueMessage($msg, 'notice');
					break;

				case 'RF':
					$status = "Refund";
					$msg 		= JText::_("COM_DIGICOM_PAYMENT_REFUND_THANK_YOU");
					$app->enqueueMessage($msg, 'notice');
					break;

				default:
					$status = $data['status'];
					$msg 		= JText::_("COM_DIGICOM_PAYMENT_WAITING_THANK_YOU");
					$app->enqueueMessage($msg, 'notice');
					break;
			}

			$info = array(
				'orderid' => $order_id,
				'data' => $data,
				'plugin' => $pay_plugin
			);

			//$callback, $callbackid, $status = 'Active', $type = 'payment'
			$log = DigiComSiteHelperLog::getLog('cart proccessSuccess', $order_id, $status, $logtype);
			// print_r($log);jexit();
			if($log === NULL or $log->status != 'Active'){
				// let update order
				DigiComSiteHelperLog::setLog($logtype, 'cart proccessSuccess', $order_id, 'Order id#'.$order_id.' updated & method is '.$pay_plugin, json_encode($info), $status);

				$this->updateOrder($order_id, $result, $data, $pay_plugin, $status, $items, $customer);

			}
			else
			{
				DigiComSiteHelperLog::setLog($logtype, 'cart proccessSuccess', $order_id, 'Post recieved for Order id#'.$order_id.' from '.$pay_plugin, json_encode($info), $status);
			}

			// redirect after payment complete
			$afterpurchase = $configs->get('afterpurchase', 2);
			switch ($afterpurchase) {
				case '2':
					if('Active' == $status){
						$session->set('com_digicom', array('action' => 'payment_complete', 'id' => $order_id));
						$link 	= JRoute::_('index.php?option=com_digicom&view=thankyou', false);					
					}else{
						$app->enqueueMessage($msg, 'message');
						$link 	= JRoute::_('index.php?option=com_digicom&view=order&id='.$order_id, false);
					}
					break;
				case '1':
					$app->enqueueMessage($msg, 'message');
					$link 	= JRoute::_('index.php?option=com_digicom&view=order&id='.$order_id, false);
					break;
				default:
					$app->enqueueMessage($msg,'message');
					$item 	= $app->getMenu()->getItems('link', 'index.php?option=com_digicom&view=downloads', true);
					$link 	= JRoute::_('index.php?option=com_digicom&view=downloads', false);
					break;
			}

		}
		
		$app->redirect($link);
		return true;
	}

	function updateOrder($order_id, $result, $data, $pay_plugin, $status, $items, $customer)
	{

		$orderTable = $this->getTable('Order');
		$orderTable->load($order_id);
		// print_r($data);die;
		//amount_paid
		$type = 'process_order';
		if($status != 'Refund')
		{
			if((int)$data['total_paid_amt']>0){
				$orderTable->amount_paid = (int)$orderTable->amount_paid + (int)$data['total_paid_amt'];
			}
			$orderTable->amount_paid = (int)$orderTable->amount_paid;
		}
		else{
			// as refund, check if has refund amout else all
			$balance_amount = ($data['total_paid_amt'] > 0 ? ($orderTable->amount_paid - $data['total_paid_amt']) : 0); 
			$orderTable->amount_paid = $balance_amount;
			$type = 'refund_order';
		}

		//transection id
		$orderTable->transaction_number = $data['transaction_id'];

		//processor
		$orderTable->processor = $data['processor'];
		$warning = '';

		//status
		if($orderTable->amount_paid >= $orderTable->amount){
			$orderTable->status = $status;
			$type = 'complete_order';
		}
		else if(($orderTable->amount_paid > 0) && ($orderTable->amount_paid < $orderTable->amount))
		{
			$warning = JText::_('COM_DIGICOM_PAYMENT_FROUD_CASE_PAYMENT_MANUPULATION');
			$orderTable->status = 'Pending';
		}
		else{
			$orderTable->status = $status;
		}

		$dispatcher = JDispatcher::getInstance();
		if($type == 'complete_order')
		{
			$dispatcher->trigger('onDigicomAfterPaymentComplete', array($order_id, $result, $pay_plugin, $items, $customer));
		}
		elseif($type == 'refund_order')
		{
			$dispatcher->trigger('onDigicomAfterPaymentRefund', array($order_id, $result, $pay_plugin, $items, $customer));
		}

		if($type == 'complete_order' or $type == 'refund_order'){
			DigiComSiteHelperLicense::updateLicenses($order_id, $orderTable->number_of_products, $items, $orderTable->userid , $type);
		}

		$comment = array();
		$comment[] = $orderTable->comment;
		$comment[] = (isset($result['comment']) ? $result['comment'] : '');

		$orderTable->comment = implode("\n", $comment);

		$registry = new Registry;
		$registry->loadString($orderTable->params);

		$orderparams = new stdClass();
		$orderparams->paymentinfo 	= array();
		$orderparams->paymentinfo[] = $pay_plugin;
		$orderparams->paymentinfo[] = $result;
		$orderparams->paymentinfo[] = $data;
		$orderparams->warning 	= $warning;

		$registry->loadObject($orderparams);
		$orderTable->params = (string) $registry;

		$orderTable->store();

		//triggere email
		$config = JFactory::getConfig();
		$tzoffset = (int) $config->get('offset');
		$now = date('Y-m-d H:i:s', time() + $tzoffset);
		DigiComSiteHelperEmail::dispatchMail( $order_id, $orderTable->amount_paid, $orderTable->number_of_products, $now, $items, $customer , $type, $status);

		return true;
	}

	function storeOrderParams($user_id,$order_id ,$params)
	{

		$table = $this->getTable('Order');
		$table->load(array('id'=>$order_id,'userid'=>$user_id));

		$registry = new Registry;
		$registry->loadString($table->params);
		$registry->loadObject($params);

		$table->params = (string) $registry;
		$table->store();
		return true;
	}

	function goToSuccessURL( $sid, $msg = '', $orderid = 0 , $type = 'new_order')
	{

		global $Itemid;

		$conf = $this->getInstance( "config", "digicomModel" );
		$configs = $conf->getConfigs();

		$cust_info = $this->loadCustomer( $sid );
		if ( isset( $cust_info ) && is_array( $cust_info ) && isset( $cust_info['cart'] ) ) {
			if ( isset( $cust_info['cart']['total'] ) )
				$cart_total = $cust_info['cart']['total'];
			if ( isset( $cust_info['cart']['items'] ) )
				$cart_items = unserialize( $cust_info['cart']['items'] );
		}

		$customer = $this->customer;
		$_Itemid = $Itemid;
		if ( isset( $customer->_Itemid ) && ($customer->_Itemid > 0) )
			$_Itemid = $customer->_Itemid;

		$cart = $this->getInstance( "cart", "digicomModel" );

		if ( isset( $cart_items ) ) {
			$items = $cart_items;
		} else {
			$items = $cart->getCartItems( $customer, $configs );
		}

		// $tax = $cart->calc_price( $items, $customer, $configs );
		$tax = $cart->tax;

		if ( $orderid == 0 && is_array( $cust_info ) && isset( $cust_info['cart'] ) && isset( $cust_info['cart']['orderid'] ) )
			$orderid = $cust_info['cart']['orderid'];
		if ( $orderid == 0 && is_object( $cust_info ) && isset( $cust_info->cart['orderid'] ) )
			$orderid = $cust_info->cart['orderid']; // перестраховка если cart это об'ект

		//triggere email
		$config = JFactory::getConfig();
		$tzoffset = (int) $config->get('offset');
		$now = date('Y-m-d H:i:s', time() + $tzoffset);

		$total = $tax['taxed'];
		$number_of_products = $tax['number_of_products'];

		DigiComSiteHelperEmail::dispatchMail( $orderid, $total, $number_of_products, $now, $items, $customer , $type, 'Active');

		$cart->emptyCart( $sid );

		return true;

	}

	function getFinalize( $sid, $msg, $orderid, $type, $status)
	{
		if(empty($orderid)){
			$orderid = 0;
		}

		if(empty($msg)){
			$msg = "";
		}
		global $Itemid;

		$conf = $this->getInstance( "config", "digicomModel" );
		$configs = $conf->getConfigs();

		$cust_info = $this->loadCustomer( $sid );
		if ( isset( $cust_info ) && is_array( $cust_info ) && isset( $cust_info['cart'] ) ) {
			if ( isset( $cust_info['cart']['total'] ) )
				$cart_total = $cust_info['cart']['total'];
			if ( isset( $cust_info['cart']['items'] ) )
				$cart_items = unserialize( $cust_info['cart']['items'] );
			//debug($cart_items);
		}

		$customer = $this->customer;
		$_Itemid = $Itemid;
		if ( isset( $customer->_Itemid ) && ($customer->_Itemid > 0) )
			$_Itemid = $customer->_Itemid;

		$cart = $this->getInstance( "cart", "digicomModel" );

		if ( isset( $cart_items ) ) {
			$items = $cart_items;
		} else {
			$items = $cart->getCartItems( $customer, $configs );
		}

		// $tax = $cart->calc_price( $items, $customer, $configs );
		$tax = $cart->tax;

		//triggere email
		$config = JFactory::getConfig();
		$tzoffset = (int) $config->get('offset');
		$now = date('Y-m-d H:i:s', time() + $tzoffset);

		$total = $tax['taxed'];
		$number_of_products = $tax['number_of_products'];

		/* fixed return after payment, before paypal IPN */
		$plugin = JRequest::getVar( 'plugin', '' );
		DigiComSiteHelperEmail::dispatchMail( $orderid, $total, $number_of_products, $now, $items, $customer , $type, $status);
		$cart->emptyCart( $sid );

		return true;

	}

	/**
	 * Get load Customer information cart
	 * @param int $sid - session id
	 * @return Array
	 */
	function loadCustomer($sid)
	{
		$db = JFactory::getDbo();
		$sql = "select transaction_details from #__digicom_session where id=" . intval($sid);
		$db->setQuery( $sql );
		$prof = $db->loadResult();
		return unserialize(base64_decode($prof));
	}

	function getCat_url()
	{
		return '#';
	}

	function storeTransactionData( $items, $orderid, $tax, $sid )
	{

		global $Itemid;

		$database = JFactory::getDbo();
		$my = JFactory::getUser();

		$data = array();
		$data['cart'] = array();
		$data['cart']['orderid'] = $orderid;
		$data['cart']['payable_amount'] = $tax['taxed'];
		$data['cart']['tax'] = $tax['taxed'] - $tax['payable_amount'] - $tax['shipping'];

		$query = "select state, country, city from #__digicom_customers where id=" . $my->id;
		$database->setQuery( $query );
		$location = $database->loadObjectList();
		$data['cart']['city'] = (isset($location[0]->city) ? $location[0]->city : '');
		$data['cart']['state'] = (isset($location[0]->state) ? $location[0]->state : '');
		$data['cart']['country'] = (isset($location[0]->country) ? $location[0]->country : '');

		// Items
		$data['cart']['items'] = serialize( $items );
		$data['sid'] = $sid;
		$data['userid'] = $my->id;
		$data['option'] = 'com_digicom';
		$data['Itemid'] = $Itemid;

		$data['nontaxed'] = $tax['payable_amount'];
		$insert = base64_encode( serialize( $data ) );
		$sql = "update #__digicom_session set transaction_details='" . $insert . "' where id='" . intval($sid) . "'";
		$database->setQuery( $sql );
		$database->query();
		return true;
	}

	function goToFailedURL( $sid, $msg = '' )
	{

		$customer = $this->loadCustomer( $sid );
		$cart = $this->getInstance( "cart", "digicomModel" );
		$items = $cart->getCartItems( $customer, $configs );
		// $tax = $cart->calc_price( $items, $customer, $configs );
		$tax = $cart->tax;
		$this->storeTransactionData( $items, -1, $tax, $sid );

	}

	function addOrder( $items, $tax, $cust_info, $now, $paymethod, $status = "Active" )
	{
		$cart	 		= $this;
		$db 			= JFactory::getDbo();
		$conf 		= $this->getInstance( "config", "digicomModel" );
		$configs 	= $conf->getConfigs();
		$customer = $cust_info;

		if (is_object($customer)) $sid = $customer->_sid;
		if (is_array($customer)) $sid = $customer['sid'];

		if (is_object($customer) && isset($customer->_user->id))  $uid = $customer->_user->id;
		if (is_array($customer)) $uid = $customer['userid'];

		if($uid == 0){
			return 0;
		}

		$promo = $cart->get_promo( $cust_info );

		if ( $promo->id > 0 ) {
			$query = $db->getQuery(true);

			// Fields to update.
			$fields = array(
			    $db->quoteName('used') . ' = ' . $db->quote($promo->used+1)
			);
			// Conditions for which records should be updated.
			$conditions = array(
			    $db->quoteName('id') . ' = '.$promo->id
			);
			$query->update($db->quoteName('#__digicom_promocodes'))->set($fields)->where($conditions);
			// Set the query using our newly updated query object and execute it.
			$db->setQuery($query);
			//echo $query->__toString();die;
			$db->execute();

			$promoid = $promo->id;
			$promocode = $promo->code;
		}else{
			$promoid = '0';
			$promocode = '0';
		}

		if($paymethod == 'free'){
			$transectionid = $tax['number_of_products'].$paymethod.$now;
			$transectionid = substr($transectionid,0,15);
		}else{
			$transectionid = null;
		}

		// print_r($tax);die;

		// trigger dispatcher
		$table = $this->getTable('order');
		$table->userid								= $uid;
		$table->transaction_number		= $transectionid;
		$table->order_date						= $now;
		$table->price									= $tax['price'];
		$table->amount								= $tax['taxed'];
		$table->tax										= $tax['value'];
		$table->discount							= $tax['promo'];
		$table->currency							= $tax['currency'];
		$table->processor							= $paymethod;
		$table->number_of_products		= $tax['number_of_products'];
		$table->status								= $status;
		$table->promocodeid						= $promoid;
		$table->promocode							= $promocode;
		$table->published							= 1;
		// print_r($table);die;

		// Trigger the event
		$dispatcher=JDispatcher::getInstance();
		$dispatcher->trigger('onDigicomBeforePlaceOrder',array($table));
		if($table->store()){
			$orderid = $table->id;
			$this->storeTransactionData( $items, $orderid, $tax, $sid );
			return $orderid;
		}

		return false;

	}

	function addOrderDetails($items, $orderid, $now, $customer, $status = "Active")
	{
		$license = array();
		if($status != "Pending")
			$published = 1;
		else
			$published = 0;

		$database = JFactory::getDbo();
		$license_index = 0;
		$jconfig = JFactory::getConfig();

		$user_id = isset($customer->_user->id) ? $customer->_user->id : $customer["userid"];

		if($user_id == 0){
			return false;
		}

		//print_r($items);die;

		// start foreach
		foreach($items as $key=>$item)
		{
			$price 				= $item->price;
			$amount_paid 	= $item->price_formated;
			$date = JFactory::getDate();
			$purchase_date = $date->toSql();
			$package_type = (!empty($item->bundle_source) ? $item->bundle_source : 'reguler');
			$sql = "insert into #__digicom_orders_details(userid, productid,quantity, orderid, price, amount_paid, published, package_type, purchase_date) "
					. "values ('{$user_id}', '{$item->item_id}', '{$item->quantity}', '{$orderid}', '{$price}', '{$amount_paid}', ".$published.", '{$package_type}', '{$purchase_date}')";
			//echo $sql;die;
			$database->setQuery($sql);
			$database->query();

			$sql = "update #__digicom_products set used=used+1 where id = '" . $item->item_id . "'";
			$database->setQuery( $sql );
			$database->query();

		}
		// end foreach

		return true;
	}

	public function getOrder( $order_id ){
		if(!isset( $this->orders[$order_id] )) {
			$db 	= JFactory::getDbo();
			$sql 	= 'SELECT * FROM `#__digicom_orders` WHERE `id`='.$order_id;
			$db->setQuery($sql);
			$order 	= $db->loadObject();
			$this->orders[$order_id]=$order;
		}
		return $this->orders[$order_id];
	}

	public function getOrderItems( $order_id ){

		$configs = $this->configs;
		$customer = $this->customer;
		$db 	= JFactory::getDbo();
		$sql 	= 'SELECT `p`.*, ';
		$sql 	.= '`od`.`price`, `od`.`amount_paid`, `od`.`quantity` ';
		$sql 	.= 'FROM `#__digicom_products` AS `p` ';
		$sql 	.= 'INNER JOIN `#__digicom_orders_details` AS `od` ON (`od`.`productid` = `p`.`id`) ';
		$sql 	.= 'WHERE `orderid` ='.$order_id;

		$db->setQuery($sql);
		$items = $db->loadObjectList();

		//change the price of items if needed
		for ( $i = 0; $i < count( $items ); $i++ )
		{
			$item = &$items[$i];
			$item->discount = 0;
			$item->currency = $configs->get('currency','USD');
			$item->subtotal = $item->price * $item->quantity;

			$item->price = DigiComSiteHelperPrice::format_price( $item->price, $item->currency, false, $configs ); 
			$item->price_formated = DigiComSiteHelperPrice::format_price( $item->amount_paid, $item->currency, false, $configs ); 
			$item->subtotal_formated = DigiComSiteHelperPrice::format_price( $item->subtotal, $item->currency, false, $configs ); 
			
			unset($item->fulltext);
		}

		return $items ;

	}

}
