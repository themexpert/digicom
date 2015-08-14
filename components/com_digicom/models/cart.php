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

	function __construct()
	{

		parent::__construct();
		$this->configs = JComponentHelper::getComponent('com_digicom')->params;
		$this->customer = new DigiComSiteHelperSession();

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

	/*
	function existUser($username, $email){
		$db = JFactory::getDbo();
		if(trim($username) == "" || trim($email) == ""){
			return false;
		}else{
			$query = $db->getQuery(true);

			$query->select('count(*)')
				  ->from($db->quoteName('#__users'))
				  ->where($db->quoteName('username') . '='.$db->quote($username));
			$db->setQuery($query);
			$db->execute();

			// $sql = "select count(*) from #__users where username='".addslashes(trim($username))."'";
			// $db->setQuery($sql);
			// $db->query();

			$result = $db->loadResult();
			if($result > 0){
				return true;
			}

			$db->clear();
			$query = $db->getQuery(true);
			$query->select('count(*)')
				  ->from($db->quoteName('#__users'))
				  ->where($db->quoteName('email') . '='.$db->quote($email));
			$db->setQuery($query);
			$db->execute();

			// $sql = "select count(*) from #__users where email='".addslashes(trim($email))."'";
			// $db->setQuery($sql);
			// $db->query();
			$result = $db->loadResult();
			if($result > 0){
				return true;
			}
		}
		return false;
	}
	*/

	/**
	 * Method to add product to cart object
	 *
	 * @return  int cart id
	 * @since   1.0.0
	 */
	function addToCart()
	{
		$user			= JFactory::getUser();
		$db				= JFactory::getDbo();
		$customer		= $this->customer;

		$sid			= $customer->_sid; //digicom session id
		$uid			= $customer->_user->id; //joomla user id
		$pid			= JFactory::getApplication()->input->get('pid',0);
		$cid			= JFactory::getApplication()->input->get('cid',0);

		// check if product id less then 1, then its too bad, return -1
		if($pid < 1){
			return (-1);
		}

		// now get the product with access label
		$query = $db->getQuery(true);
		$query->select(array('name', 'access'))
			  ->from($db->quoteName('#__digicom_products'));

		$groups = implode(',', $user->getAuthorisedViewLevels());
		$query->where($db->quoteName('access').' IN (' . $groups . ')');

		$db->setQuery( $query );
		$res = $db->loadObject();

		$productname 	= $res->name;
		$access 		= $res->access;

		// if product name is empty, return -1
		if(strlen($productname) < 1){
			return -1;
		}

		// now we have passed basic check, move on ...
		$qty = JRequest::getVar( 'qty', 1, 'request' ); //product quantity

		$db->clear();
		$query = $db->getQuery(true);
		$query->select(array('cid','item_id','quantity'))
			  ->from($db->quoteName('#__digicom_cart'))
			  ->where($db->quoteName('sid') . '='.$db->quote($sid))
			  ->where($db->quoteName('item_id') . '='.$db->quote($pid));
		$db->setQuery($query);
		$data = $db->loadObject();

		if($data){
			//we already have this item in the cart
			$item_id = $data->item_id; //lets just check if item is in the cart
			$item_qty = $data->quantity;
			$cid = $data->cid;

			//lets update if not same quantity
			if($item_qty != $qty){
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

				//$sql = "update #__digicom_cart set quantity =quantity+".$qty." where sid='".intval($sid)."' AND item_id='".intval($pid)."'";
				$db->setQuery($query);
				$db->execute();
			}
		}

		if(!isset($item_id)){
			$db->clear();
			$query = $db->getQuery(true);

			// Insert columns.
			$columns = array('quantity', 'item_id', 'sid', 'userid');
			 
			// Insert values.
			$values = array($db->quote(intval($qty)),$db->quote(intval($pid)), $db->quote(intval($sid)), $db->quote(intval($uid)));

			// Prepare the insert query.
			$query
			    ->insert($db->quoteName('#__digicom_cart'))
			    ->columns($db->quoteName($columns))
			    ->values(implode(',', $values));

			//no such item in cart- inserting new row
			//$sql = "insert into #__digicom_cart (quantity, item_id, sid, userid)"
			//	. " values ('".$qty."', '".intval($pid)."', '".intval($sid)."', '".intval($uid)."')";
			$db->setQuery($query);
			$db->execute();
			$cid = $db->insertid(); //cart id of the item inserted
		}

		return $cid;
	}

	function getCartItems($customer, $configs)
	{

		if(count($this->_items)){
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
		$query->select(array('c.*', 'p.*'))
			  ->from($db->quoteName('#__digicom_products','p'))
			  ->join('INNER', $db->quoteName('#__digicom_cart', 'c') . ' ON (' . $db->quoteName('c.item_id') . ' = ' . $db->quoteName('p.id') . ')')
			  ->where($db->quoteName('c.sid') . '='.$db->quote(intval($sid)))
			  ->where($db->quoteName('c.item_id') . '='.$db->quoteName('p.id'))
			  ->order($db->quoteName('p.ordering') . ' DESC');
		$db->setQuery($query);
		$items = $db->loadObjectList();

		//print_r($items);die;
		//change the price of items if needed
		for ( $i = 0; $i < count( $items ); $i++ )
		{
			$item = $items[$i];
			$item->discount = 0;
			$item->currency = $configs->get('currency','USD');
			$item->subtotal = $item->price * $item->quantity;
			$item->price_formated = DigiComSiteHelperPrice::format_price( $item->price, $item->currency, false, $configs ); //sprintf( $price_format, $item->subtotal );
			
		}


		//return $this->_items = $items;
		$this->_items = $items;
		
		
		if(count($items) > 0){
			$this->calc_price($items, $customer, $configs);
		}
		return $this->_items;
		
	}

	function calc_price($items,$cust_info,$configs)
	{
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		if (is_object($cust_info))	$sid = $cust_info->_sid;
		if (is_array($cust_info))	$sid = $cust_info['sid'];
		$customer = $cust_info;

		if ( null != $configs->get('totaldigits','') ) {
			$configs = $this->getInstance( "Config", "digicomModel" );
			$configs = $configs->getConfigs();
		}

		if(isset($cust_info->_customer) && !isset($cust_info->_customer->country)){
			$cust_info->_customer->country = '';
		}
		if(isset($cust_info->_customer) && !isset($cust_info->_customer->state)){
			$cust_info->_customer->state = '';
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
		//print_r($promo);die;
		$promovalue = 0;
		$addPromo = false;
		$ontotal = false;
		$onProduct = false;
		$promo_applied = 0;

		if($promo->id > 0){
			//we got real promocode
			$promoid = $promo->id;
			$promocode = $promo->code;

			//validate promocode
			if(!($promo->codelimit <= $promo->used && $promo->codelimit > 0)){
				$addPromo = true;
				//we can use it, it has limit
				if($promo->discount_enable_range==1){
					// for entire cart
					$ontotal = true;
				}else{
					$onProduct = true;
				}
			}
		} else {
			$promoid = '0';
			$promocode = '';
		}

		foreach ( $items as $item )
		{
			//initial promo amount as 0, so later we can use it
			$promoamount = 0;

			$payprocess['price'] = $total += $item->subtotal;
			$payprocess['number_of_products'] += $item->quantity;

			//check promocode on product apply
			if($addPromo && $onProduct){
				//TODO: Apply Product promo
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
						$promovalue += $promoamount;
					}
					else
					{
						// Use percentage
						$promoamount = $item->price * $promo->amount / 100;
						$promovalue += $promoamount;
					}

					if($promoamount > 0){
						$item->discount = $promoamount;
						$item->price_formated = $item->price - $promoamount;
						//$item->subtotal = $item->subtotal - $promoamount;
						$payprocess['item_discount'] = 1;
					}
					$payprocess['discount_calculated'] = 1;
					//print_r($item);die;
				}
			} // end if for: product promo check
		}

		if($addPromo && $onProduct){
			$total -= $promovalue;
			$promo_applied = 1;
			$payprocess['promo'] = $promovalue;
		}

		//--------------------------------------------------------
		// Promo code on cart
		//--------------------------------------------------------
		if($addPromo && $ontotal){
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

		$payprocess['payable_amount'] = $total;

		//final price calculations
		$tmp_customer = $customer;

		if (is_object($customer) && isset($customer->_customer) && !empty($customer->_customer)) $tmp_customer = $customer->_customer;
		if (is_array($customer)) $tmp_customer = $customer;
		$customer = $tmp_customer;
		//final calculations end here

		if(!isset($payprocess['value'])) $payprocess['value'] = 0;
		$sum_tax = $total + $payprocess['value']; //$vat_tax + $state_tax;//total tax

		$payprocess['promo_error'] = (!$user->id && isset($promo->orders) && count($promo->orders) ? JText::_("DIGI_PROMO_LOGIN") : '');
		//$payprocess['total'] = $total;

		$payprocess['taxed'] = $payprocess['shipping'] + $sum_tax;
		$payprocess['discount_calculated'] = (isset($payprocess['discount_calculated']) ? $payprocess['discount_calculated'] : 0);
		//$payprocess['taxed'] = DigiComSiteHelperPrice::format_price( $payprocess['taxed'], $payprocess['currency'], false, $configs ); //sprintf($price_format, $payprocess['taxed']);//." ".$payprocess['currency'];
		$payprocess['type'] = 'TAX';

		$this->_tax = $payprocess;

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

		if(empty($customer->_sid))
		{
			$customer = new DigiComSiteHelperSession();
		}

		$uid = 0;

		if (is_object($customer) && isset($customer->_sid) && !empty($customer->_sid)) $sid = $customer->_sid;
		if (is_array($customer) && isset($customer['sid']) && !empty($customer['sid'])) $sid = $customer['sid'];

		if (is_object($customer) && isset($customer->_user) && isset($customer->_user->id) && !empty($customer->_user->id)) $uid = $customer->_user->id;
		if (is_array($customer) && isset($customer['userid']) && !empty($customer['userid'])) $uid = $customer['userid'];

		if ( !$sid )
			return null; //$sid = get_sid();

		$db = JFactory::getDbo();
		$sql = "select cart_details from #__digicom_session where sid='" . intval($sid) . "'";
		$db->setQuery( $sql );
		$promodata = $db->loadResult();
		
		$promodata = explode( "=", $promodata );
		if ( $promodata[0] == 'promocode' )
			$promocode = $promodata[1];
		else
			$promocode = '';


		if ( strlen( $promocode ) > 0 ) {//valid promocode was provided
			$sql = "select *
					from #__digicom_promocodes
					where code='" . $promocode . "'";
			$db->setQuery( $sql );
			$promo = $db->loadObject();

			// Get products restrictions
			$sql = "SELECT p.`productid`
					FROM `#__digicom_promocodes_products` AS p
					WHERE p.`promoid`=" . $promo->id;
			$db->setQuery( $sql );
			$promo->products = $db->loadObjectList();

		} else {
			//TODO:: handle the exception correctly so it dosent return complete table object;
			//$promo = $this->getTable( "Discount" );
			$promo = new StdClass();
			$promo->id = '';
			$promo->code = '';
		}

		$promo->error = "";
		if ( $promodata[0] == "promoerror" )
			$promo->error = $promodata[1];

		//code exists and we're about to validate it
		if ( $promo->id > 0 && $checkvalid == 1 ) 
		{

			$promo_data = $promo;
			$today = date('Y-m-d 00:00:00');
			$tomorrow = date('Y-m-d  00:00:00',strtotime($today . "+1 days"));
			
			$now = strtotime($today);
			$tomorrow = strtotime($tomorrow);
			$timestart = strtotime($promo_data->codestart);
			$timeend = strtotime($promo_data->codeend);
			$nullDate = strtotime('0000-00-00 00:00:00');

			$remain = $promo_data->codelimit - $promo_data->used;
			$used = $promo_data->used;
			$limit = $promo_data->codelimit;
			$published = $promo_data->published;

			if ( ($timestart >= $now) && ($timeend >= $now || $timeend == $nullDate ) && ($limit == 0 || $used < $limit) && $published == "1") 
			{
				$error = 0; //code is valid
				$promoerror = '';
			}
			else if ($published == "0") 
			{
				$promoerror = JText::_( "COM_DIGICOM_DISCOUNT_CODE_UNP" );
			}
			else if ($limit > 0  && $used  >= $limit) 
			{
				$promoerror = JText::_( "COM_DIGICOM_DISCOUNT_CODE_EXPIRED_AMOUNT" );
			}
			else if ($timeend < $tomorrow && $timeend != $nullDate)
			{
				$promoerror = JText::_( "COM_DIGICOM_DISCOUNT_CODE_EXPIRED_DATE" );
			}
			else 
			{
				$promoerror = JText::_( "COM_DIGICOM_DISCOUNT_CODE_ENA" );
			}
			
			if ( !empty($promoerror) ) {//promo code is invalid
				$promo->error = $promoerror;
				JFactory::getApplication()->enqueueMessage(JText::_($promoerror),'warning');
			} else {
				$promo->error = "";
			}
			
		}

		return $promo;

	}

	function updateCart( $customer, $configs )
	{
		$jAp = JFactory::getApplication();
		$db = JFactory::getDbo();
		$sid = $customer->_sid;
		$uid = $customer->_user->id;

		// Update prosessor
		$processor = JRequest::getVar( 'processor', '' );
		$sql = "update #__digicom_session set processor='" . $processor . "' where sid='" . intval($sid) . "'";
		$db->setQuery( $sql );
		$db->query();

		if ( empty( $this->_items ) ) {
			$this->getCartItems( $customer, $configs );
		}
		$items = $this->_items;

		$sql = "select c.* from #__digicom_cart c where c.sid='".intval($sid)."'";
		$db->setQuery( $sql );
		$cartitems = $db->loadObjectList();


		//now we have to add rows for choosen items - rows are items of selected product
		//with blank option set and qty = 1
		$replicate = JRequest::getVar( 'replicate', '', 'post' );
		if ( !empty( $replicate ) )
			foreach ( $replicate as $cid => $value ) {
				$cid = (int) $cid;
				$value = (int) $value;
				//possibly will add more specific check here later
				if ( $value > 0 && $cid > 0 ) {
					$sql = "insert into #__digicom_cart (item_id, userid, quantity, sid) values ('" . $value . "', '" . $uid . "', '1', '" . intval($sid) . "')";
					$db->setQuery( $sql );
					$db->query();
					$incid = $db->insertid();
				}
			}

		//get promo code if submitted
		$promo = JRequest::getVar('promocode');

		if ( strlen( $promo ) > 0 ) { //code was submitted
			$sql = "select * from #__digicom_promocodes where code='" . $promo . "' ";
			$db->setQuery( $sql );
			$promo_exists = $db->loadObjectList();

			if ( count( $promo_exists ) > 0 ) {//and there is such code in dabase
				$promo_data = $promo_exists[0];
				
				$today = date('Y-m-d 00:00:00');
				$tomorrow = date('Y-m-d  00:00:00',strtotime($today . "+1 days"));
				
				$now = strtotime($today);
				$tomorrow = strtotime($tomorrow);
				$timestart = strtotime($promo_data->codestart);
				$timeend = strtotime($promo_data->codeend);
				$nullDate = strtotime('0000-00-00 00:00:00');

				$remain = $promo_data->codelimit - $promo_data->used;
				$used = $promo_data->used;
				$limit = $promo_data->codelimit;
				$published = $promo_data->published;

				if ( ($timestart >= $now) && ($timeend >= $now || $timeend == $nullDate ) && ($limit == 0 || $used < $limit) && $published == "1") 
				{
					//add this code to user's cart
					$sql = "update #__digicom_session set cart_details='promocode=" . $promo . "' where sid='" . $sid . "'";
					//$jAp->enqueueMessage(JText::sprintf('COM_DIGICOM_CART_PROMOCODE_APPLIED',$promo),'success');
				}
				else if ($published == "0") 
				{
					$sql = "update #__digicom_session set cart_details='promoerror=" . (JText::_( "COM_DIGICOM_DISCOUNT_CODE_UNP" )) . "' where sid='" . intval($sid) . "'";
				}
				else if ($limit > 0  && $used  >= $limit) 
				{
					$sql = "update #__digicom_session set cart_details='promoerror=" . (JText::_( "COM_DIGICOM_DISCOUNT_CODE_EXPIRED_AMOUNT" )) . "' where sid='" . intval($sid) . "'";
				}
				else if ($timeend < $tomorrow && $timeend != $nullDate)
				{
					$sql = "update #__digicom_session set cart_details='promoerror=" . (JText::_( "COM_DIGICOM_DISCOUNT_CODE_EXPIRED_DATE" )) . "' where sid='" . intval($sid) . "'";
				}
				else 
				{
					$sql = "update #__digicom_session set cart_details='promoerror=" . (JText::_( "COM_DIGICOM_DISCOUNT_CODE_ENA" )) . "' where sid='" . intval($sid) . "'";
				}

				//adding status entry to user's session
				$db->setQuery( $sql );
				$db->execute();
				//$jAp->enqueueMessage(nl2br($db->getErrorMsg()),'error');
			} else {
				$sql = "update #__digicom_session set cart_details='promoerror=" . (JText::_( "COM_DIGICOM_DISCOUNT_CODE_WRONG" )) . "' where sid='" . intval($sid) . "'";
				$db->setQuery( $sql );
				$db->execute();
				//$jAp->enqueueMessage(nl2br($db->getErrorMsg()),'error');
			}


		} else {//cleaning up promocode entry
			$sql = "update #__digicom_session set cart_details='' where sid='" . intval($sid) . "'";
			$db->setQuery( $sql );
			$db->query();
			//$jAp->enqueueMessage(nl2br($db->getErrorMsg()),'error');
		}


		$sql = "update #__digicom_session set shipping_details='0' where sid='" . intval($sid) . "'";
		$db->setQuery( $sql );
		$db->query();
		//$jAp->enqueueMessage(nl2br($db->getErrorMsg()),'error');
		return true;
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

	function checkCartIsEmpty() {

		$customer = new DigiComSiteHelperSession();

		$conf = $this->getInstance( "config", "digicomModel" );
		$configs = $conf->getConfigs();

		$items = $this->getCartItems( $customer, $configs );

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

		//$sid = $reg->set("digisid", 0);
		if(!$sid){
			return;
		}
		$sql = "update #__digicom_session set cart_details='' where sid='" . $sid . "'";
		$db->setQuery( $sql );
		$db->execute();

		$sql = "delete from #__digicom_cart where sid='" . intval($sid) . "'";
		$db->setQuery( $sql );
		$db->execute();

	}

	function addFreeProduct($items, $customer, $tax){
		$config			= JFactory::getConfig();
		$tzoffset		= $config->get('offset');//$now = time();
		$now 				= date('Y-m-d H:i:s', time() + $tzoffset);

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

	function addOrderInfo($items, $customer, $tax, $status,$prosessor){
		$config = JFactory::getConfig();
		$tzoffset = $config->get('offset');
		//$now = time();
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

	function storelog($name,$data)
	{
		$data1=array();
		$data1['raw_data']=isset($data['raw_data'])?$data['raw_data']:array();
		$data1['JT_CLIENT']="com_digicom";
		$dispatcher=JDispatcher::getInstance();
		JPluginHelper::importPlugin('digicom_pay',$name);
		$dispatcher->trigger('onTP_Storelog',array($data1));

		return true;
	}

	function proccessSuccess($post, $pg_plugin, $order_id, $sid,$responce,$items)
	{
		$app = JFactory::getApplication();
		$customer = $this->loadCustomer($sid);
		if(!$customer){
			$order = $this->getOrder($order_id);
			$sid = $customer = $order->userid;
			//print_r($customer);
		}
		$conf = $this->getInstance( "config", "digicomModel" );
		$configs = $conf->getConfigs();

		$result = $post;
		$data=$responce[0];

		$this->storelog($pg_plugin, $data);

		//print_r($data);jexit();
		if(isset($data['status']))
		{
			$_SESSION['in_trans'] = 1;

			if($data['status']=='C'){
				$msg = JText::_("COM_DIGICOM_PAYMENT_SUCCESSFUL_THANK_YOU");
				$status = "Active";
				$app->enqueueMessage($msg,'message');
			} elseif($data['status']=='P') {
				$status = "Pending";
				$msg = JText::_("COM_DIGICOM_PAYMENT_PENDING_THANK_YOU");
				$app->enqueueMessage($msg, 'notice');
			}else{
				$status = $data['status'];
				$msg = JText::_("COM_DIGICOM_PAYMENT_WAITING_THANK_YOU");
				$app->enqueueMessage($msg, 'notice');
			}

			$info = array(
				'orderid' => $order_id,
				'data' => $data,
				'plugin' => $pg_plugin
			);

			DigiComSiteHelperLog::setLog('status', 'cart proccessSuccess', $order_id, 'Order id#'.$order_id.' updated & method is '.$pg_plugin, json_encode($info),$status);

			$this->updateOrder($order_id,$result,$data,$pg_plugin,$status,$items,$customer);

		}

		// orders page
		if ($configs->get('afterpurchase',1) == 1)
		{
			$app->redirect(JRoute::_("index.php?option=com_digicom&view=order&id=".$order_id));
		}
		// download page
		else
		{
			$app->redirect(JRoute::_("index.php?option=com_digicom&view=downloads"));
		}

		return true;
	}

	function updateOrder($order_id,$result,$data,$pg_plugin,$status,$items,$customer){

		$orderTable = $this->getTable('Order');
		$orderTable->load($order_id);

		//amount_paid
		$orderTable->amount_paid = $orderTable->amount_paid + $data['total_paid_amt'];

		//transection id
		$orderTable->transaction_number = $data['transaction_id'];

		//processor
		$orderTable->processor = $data['processor'];
		$warning = '';
		$type = 'process_order';
		//status
		if($orderTable->amount_paid >= $orderTable->amount){
			$orderTable->status = $status;
			$type = 'complete_order';
		}else if(($orderTable->amount_paid > 0) && ($orderTable->amount_paid < $orderTable->amount)){
			$warning = JText::_('COM_DIGICOM_PAYMENT_FROUD_CASE_PAYMENT_MANUPULATION');
			$orderTable->status = 'Pending';
		}else{
			$orderTable->status = $status;
		}

		if($type == 'complete_order'){
			DigiComSiteHelperLicense::updateLicenses($order_id, $orderTable->number_of_products, $items, $orderTable->userid , $type);
		}

		$comment = array();
		$comment[] = $orderTable->comment;
		$comment[] = (isset($result['comment']) ? $result['comment'] : '');

		$orderTable->comment = implode("\n", $comment);

		$orderparams = json_decode($orderTable->params);
		$orderparams->paymentinfo 	= array();
		$orderparams->paymentinfo[] = $pg_plugin;
		$orderparams->paymentinfo[] = $result;
		$orderparams->paymentinfo[] = $data;
		$orderparams->warning 	= $warning;

		$orderTable->params = json_encode($orderparams);
		$orderTable->store();

		//triggere email
		$config = JFactory::getConfig();
		$tzoffset = $config->get('offset');
		$now = date('Y-m-d H:i:s', time() + $tzoffset);
		DigiComSiteHelperEmail::dispatchMail( $order_id, $orderTable->amount_paid, $orderTable->number_of_products, $now, $items, $customer , $type, $status);

		return true;
	}

	function storeOrderParams($user_id,$order_id ,$params){

		$table = $this->getTable('Order');
		$table->load(array('id'=>$order_id,'userid'=>$user_id));
		$table->params = json_encode($params);
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

		$customer = new DigiComSiteHelperSession();
		$_Itemid = $Itemid;
		if ( isset( $customer->_Itemid ) && ($customer->_Itemid > 0) )
			$_Itemid = $customer->_Itemid;

		$cart = $this->getInstance( "cart", "digicomModel" );

		if ( isset( $cart_items ) ) {
			$items = $cart_items;
		} else {
			$items = $cart->getCartItems( $customer, $configs );
		}

		$tax = $cart->calc_price( $items, $customer, $configs );

		if ( $orderid == 0 && is_array( $cust_info ) && isset( $cust_info['cart'] ) && isset( $cust_info['cart']['orderid'] ) )
			$orderid = $cust_info['cart']['orderid'];
		if ( $orderid == 0 && is_object( $cust_info ) && isset( $cust_info->cart['orderid'] ) )
			$orderid = $cust_info->cart['orderid']; // перестраховка если cart это об'ект

		//triggere email
		$config = JFactory::getConfig();
		$tzoffset = $config->get('offset');
		$now = date('Y-m-d H:i:s', time() + $tzoffset);

		$total = $tax['taxed'];
		$number_of_products = $tax['number_of_products'];

		DigiComSiteHelperEmail::dispatchMail( $orderid, $total, $number_of_products, $now, $items, $customer , $type, 'Active');

		$cart->emptyCart( $sid );

		return true;

	}

	function getFinalize( $sid, $msg = '', $orderid = 0 , $type, $status)
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
			//debug($cart_items);
		}

		$customer = new DigiComSiteHelperSession();
		$_Itemid = $Itemid;
		if ( isset( $customer->_Itemid ) && ($customer->_Itemid > 0) )
			$_Itemid = $customer->_Itemid;

		$cart = $this->getInstance( "cart", "digicomModel" );

		if ( isset( $cart_items ) ) {
			$items = $cart_items;
		} else {
			$items = $cart->getCartItems( $customer, $configs );
		}

		$tax = $cart->calc_price( $items, $customer, $configs );

		//triggere email
		$config = JFactory::getConfig();
		$tzoffset = $config->get('offset');
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
	function loadCustomer($sid){
		$db = JFactory::getDbo();
		$sql = "select transaction_details from #__digicom_session where sid=" . intval($sid);
		$db->setQuery( $sql );
		$prof = $db->loadResult();
		return unserialize(base64_decode($prof));
	}

	function getCat_url(){
		return '#';
	}

	function storeTransactionData( $items, $orderid, $tax, $sid ){

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
		$sql = "update #__digicom_session set transaction_details='" . $insert . "' where sid='" . intval($sid) . "'";
		$database->setQuery( $sql );
		$database->query();
		return true;
	}

	function goToFailedURL( $sid, $msg = '' )
	{

		$customer = $this->loadCustomer( $sid );
		$cart = $this->getInstance( "cart", "digicomModel" );
		$items = $cart->getCartItems( $customer, $configs );
		$tax = $cart->calc_price( $items, $customer, $configs );
		$this->storeTransactionData( $items, -1, $tax, $sid );

	}

	function addOrder( $items, $tax, $cust_info, $now, $paymethod, $status = "Active" )
	{
		$cart	 		= $this;
		$db 			= JFactory::getDbo();
		$conf 		= $this->getInstance( "config", "digicomModel" );
		$configs 	= $conf->getConfigs();
		$customer = $cust_info;
		//$tax = $cart->calc_price( $items, $cust_info, $configs );
		//print_r($tax);die;

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
			$transectionid = '';
		}
		//--------------------------------------------------------
		// Create a new query object.
		$query = $db->getQuery(true);
		// Insert columns.
		$columns = array( 'userid', 'transaction_number', 'order_date', 'price', 'amount', 'discount', 'amount_paid', 'currency', 'processor', 'number_of_products', 'status', 'promocodeid', 'promocode', 'published' );
		// Insert values.
		$values = array( $uid, $db->quote($transectionid),$db->quote($now), $db->quote($tax['price']), $db->quote($tax['payable_amount']), $tax['promo'], 0, $db->quote($tax['currency']), $db->quote($paymethod), $tax['number_of_products'], $db->quote($status), $promoid, $db->quote($promocode), 1 );

		// Prepare the insert query.
		$query
		    ->insert($db->quoteName('#__digicom_orders'))
		    ->columns($db->quoteName($columns))
		    ->values(implode(',', $values));
		// Set the query using our newly populated query object and execute it.
		$db->setQuery($query);
		//echo $query->__toString();die;
		$db->execute();

		$orderid = $db->insertid();
		$this->storeTransactionData( $items, $orderid, $tax, $sid );

		return $orderid;

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
			$price = (isset($item->discount) && ($item->discount > 0)) ? $item->price_formated : $item->price ;
			$date = JFactory::getDate();
			$purchase_date = $date->toSql();
			$package_type = (!empty($item->bundle_source) ? $item->bundle_source : 'reguler');
			$sql = "insert into #__digicom_orders_details(userid, productid,quantity, orderid, price, published, package_type, purchase_date) "
					. "values ('{$user_id}', '{$item->item_id}', '{$item->quantity}', '".$orderid."', '{$price}', ".$published.", '".$package_type."', '".$purchase_date."')";
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
		$customer = new DigiComSiteHelperSession();
		$db 	= JFactory::getDbo();
		$sql 	= 'SELECT `p`.*, `od`.quantity FROM
					`#__digicom_products` AS `p`
						INNER JOIN
					`#__digicom_orders_details` AS `od` ON (`od`.`productid` = `p`.`id`)
				WHERE `orderid` ='.$order_id;

		$db->setQuery($sql);
		$items = $db->loadObjectList();

		//change the price of items if needed
		for ( $i = 0; $i < count( $items ); $i++ )
		{
			$item = &$items[$i];
			$item->discount = 0;
			$item->currency = $configs->get('currency','USD');
			$item->price = DigiComSiteHelperPrice::format_price( $item->price, $item->currency, false, $configs ); //sprintf( $price_format, $item->product_price );
			//$item->subtotal = DigiComSiteHelperPrice::format_price( $item->price, $item->currency, false, $configs ); //sprintf( $price_format, $item->subtotal );

			//$item->price_formated = DigiComSiteHelperPrice::format_price( $item->price, $item->currency, false, $configs ); //sprintf( $price_format, $item->product_price );
			//$item->subtotal_formated = DigiComSiteHelperPrice::format_price( $item->subtotal, $item->currency, false, $configs ); //sprintf( $price_format, $item->subtotal );

			$item->subtotal = $item->price * $item->quantity;
		}

		return $items ;

	}

}
