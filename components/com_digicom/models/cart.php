<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

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

	function existUser($username, $email){
		$db = JFactory::getDBO();
		if(trim($username) == "" || trim($email) == ""){
			return false;
		}
		else{
			$sql = "select count(*) from #__users where username='".addslashes(trim($username))."'";
			$db->setQuery($sql);
			$db->query();
			$result = $db->loadResult();
			if($result > 0){
				return true;
			}

			$sql = "select count(*) from #__users where email='".addslashes(trim($email))."'";
			$db->setQuery($sql);
			$db->query();
			$result = $db->loadResult();
			if($result > 0){
				return true;
			}
		}
		return false;
	}

	function addToCart(){
		$customer = $this->customer;
		$db = JFactory::getDBO();
		$sid = $customer->_sid; //digicom session id
		$uid = $customer->_user->id; //joomla user id
		$pid = JFactory::getApplication()->input->get('pid',0);
		$my = JFactory::getUser($uid);
		$cid = JFactory::getApplication()->input->get('cid',0);

		if($pid < 1){//bad product id
			return (-1);
		}

		$plan_id = JRequest::getVar('plan_id', -1);
		
		$sql = "select name, access from #__digicom_products where id=".(int)($pid);
		$db->setQuery( $sql );
		$res = $db->loadObject();

		$productname 	= $res->name;
		$access 		= $res->access;

		if(strlen($productname) < 1 /*|| $access > $customer->_user->gid*/){
			return -1;
		}
		$qty = JRequest::getVar( 'qty', 1, 'request' ); //product quantity
		//check if item already in the cart
		$sql = "select cid, item_id, quantity from #__digicom_cart where sid='".intval($sid)."' AND item_id='".intval($pid)."'";
		$db->setQuery($sql);
		$data = $db->loadObjectList();
		$item_id = @$data["0"]->item_id; //lets just check if item is in the cart
		$item_qty = @$data["0"]->quantity;
		$cid = @$data["0"]->cid;


		if(!$item_id){//no such item in cart- inserting new row
			$sql = "insert into #__digicom_cart (quantity, item_id, sid, userid)"
				. " values ('".$qty."', '".intval($pid)."', '".intval($sid)."', '".intval($uid)."')";
			$db->setQuery($sql);
			$db->query();
			$cid = $db->insertid(); //cart id of the item inserted
		}
				
		$sql = "select quantity from #__digicom_cart where item_id='".intval($pid)."' and sid='".intval($sid)."' and userid='".@$my->id."' and cid='".intval($cid). "'";
		$db->setQuery( $sql );
		$quant = $db->loadResult();

		return $cid;
	}

	function getCartItems($customer, $configs)
	{	

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

		$db = JFactory::getDBO();
		$sql = "SELECT 
						`c`.*,
						`p`.*
					FROM
						`#__digicom_products` AS `p`
							INNER JOIN
						`#__digicom_cart` AS `c` ON (`c`.`item_id` = `p`.`id`)
					WHERE
						`c`.`sid` = '" . intval($sid) . "' AND `c`.`item_id` = `p`.`id`
					ORDER BY `p`.`ordering`";
		$db->setQuery($sql);
		$items = $db->loadObjectList();

		//change the price of items if needed
		for ( $i = 0; $i < count( $items ); $i++ )
		{
			$item = &$items[$i];
			$item->discount = 0;
			$item->currency = $configs->get('currency','USD');
			$item->price = DigiComSiteHelperDigiCom::format_price( $item->price, $item->currency, false, $configs ); //sprintf( $price_format, $item->product_price );
			$item->subtotal = DigiComSiteHelperDigiCom::format_price( $item->price, $item->currency, false, $configs ); //sprintf( $price_format, $item->subtotal );

			$item->price_formated = DigiComSiteHelperDigiCom::format_price2( $item->price, $item->currency, false, $configs ); //sprintf( $price_format, $item->product_price );
			$item->subtotal_formated = DigiComSiteHelperDigiCom::format_price2( $item->subtotal, $item->currency, false, $configs ); //sprintf( $price_format, $item->subtotal );
		
			$item->subtotal = $item->price * $item->quantity;
		}
		
		
		$this->_items = $items;

		if(count($items) > 0){
			$this->calc_price($items, $customer, $configs);
			foreach($items as $i => $v){
				if($i < 0){
					continue;
				}
			}
		}
		return $this->_items;
	}

	function calc_price($items,$cust_info,$configs)
	{
		
		if(isset($items[-1]) && $items[-1] == "PayProcessed"){
			return $items[-2];
		}
		
		$db = JFactory::getDBO();
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
		//--------------------------------------------------------
		// Promo code
		//--------------------------------------------------------
		$promo = $this->get_promo( $cust_info );
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

			$total += $item->subtotal;
			$payprocess['number_of_products'] += $item->quantity;

			//check promocode on product apply
			if($addPromo && $onProduct){
				//TODO: Apply Product promo
				// Get product restrictions
				$sql = "SELECT p.`productid` FROM `#__digicom_promocodes_products` AS p WHERE p.`promoid`=" . $promo->id ." and p.`productid`=".$item->id;
				$this->_db->setQuery( $sql );
				$promo->product = $this->_db->loadObject();

				if (count($promo->product) && $promo->aftertax == '0')
				{
					//promo discount should be applied before taxation
					//we get product to calculate discount
					if ($promo->promotype == '0')
					{
						// Use absolute values
						$promoamount = $promo->amount;
						$promovalue += $promo->amount;
					}
					else
					{
						// Use percentage
						$promoamount = $item->price * $promo->amount / 100;
						$promovalue += $item->price * $promo->amount / 100;
					}

					$sql = "update #__digicom_promocodes set used=used+1 where id = '" . $promo->id . "'";
					$this->_db->setQuery( $sql );
					$this->_db->query();
					
					$item->discount += $promoamount;
					$payprocess['discount_calculated'] = 1;
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
				$total -= $promo->amount;
				$promovalue = $promo->amount;
			}
			else{ //use percentage
				$promovalue = $total * $promo->amount / 100;
				$total *= 1 - $promo->amount / 100;
			}
			$payprocess['promo_order'] = 1;
			$payprocess['promo'] = $promovalue;
			$promo_applied = 1;

			$sql = "update #__digicom_promocodes set used=used+1 where id = '" . $promo->id . "'";
			$this->_db->setQuery( $sql );
			$this->_db->query();
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
		$payprocess['total'] = $total;
		
		$payprocess['taxed'] = $payprocess['shipping'] + $sum_tax;
		$payprocess['discount_calculated'] = (isset($payprocess['discount_calculated']) ? $payprocess['discount_calculated'] : 0);
		$payprocess['shipping'] = DigiComSiteHelperDigiCom::format_price( $payprocess['shipping'], $payprocess['currency'], false, $configs ); //sprintf($price_format, $payprocess['shipping']);
		$payprocess['taxed'] = DigiComSiteHelperDigiCom::format_price( $payprocess['taxed'], $payprocess['currency'], false, $configs ); //sprintf($price_format, $payprocess['taxed']);//." ".$payprocess['currency'];
		$payprocess['type'] = 'TAX';

		$this->_tax = $payprocess;
		if(count($items) > 0){
			$this->_items[-1] = "PayProcessed";
			$this->_items[-2] = $payprocess;
		}
		return $payprocess;
	}

	function calc_price_backup($items,$cust_info,$configs)
	{
		
		if(isset($items[-1]) && $items[-1] == "PayProcessed"){
			return $items[-2];
		}
		
		$db = JFactory::getDBO();
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
		//$payprocess['licenses'] = 0;
		$payprocess['number_of_products'] = 0;
		$payprocess['shipping'] = 0;
		$payprocess['currency'] = $configs->get('currency','USD');
		
		//print_r($items);die;
		foreach ( $items as $item )
		{
			//debug($item);
			$total += $item->subtotal;
			$payprocess['currency'] = $item->currency;
			$payprocess['number_of_products'] += $item->quantity;
			$shipping = 0;
		}
		$payprocess['payable_amount'] = $total;
		
		$promo = $this->get_promo( $cust_info );
		if ($promo->id > 0) {
			$promoid = $promo->id;
			$promocode = $promo->code;
		} else {
			$promoid = '0';
			$promocode = '';
		}

		$promo_applied = -1; //no need for promo
		$payprocess['promo'] = 0;
		$payprocess['promo_order'] = 0;

		if($promo->id > 0 && $can_promo) { //valid promocode was provided
			$payprocess['promoaftertax'] = $promo->aftertax;
			

			if($promo->codelimit <= $promo->used && $promo->codelimit > 0 ){
				//nothing to do now
			} else {
				if($promo->aftertax == '0'){//promo discount should be applied before taxation
					
					$promo_applied = 1; //all discounts are applied
					
					//$restricted_order = (int) $this->havePreviousOrderOfProduct($promo);
					// If there are restrictions must check line per line in the products
					//if (count($promo->products) || count($promo->orders))
					if (count($promo->products))
					{
						$payprocess['promo_order'] = 0;

						foreach ($items as $item)
						{
							$restricted_product = (int) $this->isRestrictedProduct($item, $promo->products);
							$have_discount = 0;
							$item->discount = 0;
							/*if ($restricted_order && count($promo->orders) && $restricted_product && count($promo->products))
							{
								$have_discount = 1;
							}*/
							if ($restricted_product && count($promo->products) && count($promo->orders) == 0)
							{
								$have_discount = 1;
							}
							/*if ($restricted_order && count($promo->orders) && count($promo->products) == 0)
							{
								$have_discount = 1;
							}
							*/
							if (count($promo->products) == 0 && count($promo->orders) == 0)
							{
								$have_discount = 1;
							}
							if ($have_discount)
							{
								if ($promo->promotype == '0')
								{
									// Use absolute values
									$total -= $promo->amount;
									$payprocess['promo'] += $promo->amount;
									$item->discount += $promo->amount;
									$payprocess['discount_calculated'] = 1;
								}
								else
								{
									// Use percentage
									$total -= ($item->price * $promo->amount) / 100;
									$payprocess['promo'] += (($item->price * $promo->amount) / 100);
									$item->discount = ($item->price * $promo->amount) / 100;
									$payprocess['discount_calculated'] = 1;
								}
							}
						}
					}
					else
					{
						if($promo->promotype == '0'){//use absolute values
							$total -= $promo->amount;
							$payprocess['promo'] += $promo->amount;
							$payprocess['promo_order'] = 1;
						}
						else{ //use percentage
							$payprocess['promo'] += ($promo->amount * $total) / 100;
							$total -= ($promo->amount * $total) / 100;
							$payprocess['promo_order'] = 1;
						}
					}
				}
				else{//promo discount should be applied after tax
					$promo_applied = 0; //we should apply promo later
					//nothing to do here - tax is not calculated yet
				}
			}
		}

		//final price calculations
		$tmp_customer = $customer;

		if (is_object($customer) && isset($customer->_customer) && !empty($customer->_customer)) $tmp_customer = $customer->_customer;
		if (is_array($customer)) $tmp_customer = $customer;
		$customer = $tmp_customer;
		//final calculations end here
		
		if(!isset($payprocess['value'])) $payprocess['value'] = 0;
		$sum_tax = $total + $payprocess['value']; //$vat_tax + $state_tax;//total tax
		
		//now lets apply promo discounts if there are any
		if ( $promo_applied == 0 )
			if ( $promo->promotype == '0' ) {//use absolute values
				$sum_tax -= $promo->amount;
				$payprocess['promo'] = $promo->amount;
			} else { //use percentage
				$payprocess['promo'] = $sum_tax * $promo->amount / 100;
				$sum_tax *= 1 - $promo->amount / 100;
			}

			// Fixed PromoCode > Total Price
			if ( $sum_tax < 0 )
				$sum_tax = 0;

		$payprocess['promo_error'] = (!$user->id && isset($promo->orders) && count($promo->orders) ? JText::_("DIGI_PROMO_LOGIN") : '');
		$payprocess['total'] = $total;
		
		$payprocess['taxed'] = $payprocess['shipping'] + $sum_tax;
		$payprocess['discount_calculated'] = (isset($payprocess['discount_calculated']) ? $payprocess['discount_calculated'] : 0);
		$payprocess['shipping'] = DigiComSiteHelperDigiCom::format_price( $payprocess['shipping'], $payprocess['currency'], false, $configs ); //sprintf($price_format, $payprocess['shipping']);
		$payprocess['taxed'] = DigiComSiteHelperDigiCom::format_price( $payprocess['taxed'], $payprocess['currency'], false, $configs ); //sprintf($price_format, $payprocess['taxed']);//." ".$payprocess['currency'];
		$payprocess['type'] = 'TAX';

		$this->_tax = $payprocess;
		if(count($items) > 0){
			$items[-1] = "PayProcessed";
			$items[-2] = $payprocess;
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

	// Check required products in previous order(s)
	function havePreviousOrderOfProduct($promo)
	{
		$db = JFactory::getDBO();
		$user = JFactory::getUser();

		if (count($promo->orders) == 0)
			return true;

		$previous_order = '';
		for ($i=0; $i<count($promo->orders); $i++)
		{
			$previous_order.= "l.`productid`=".$promo->orders[$i]->productid.' OR ';
		}

		$sql = "SELECT COUNT(*)
				FROM `#__digicom_promocodes_orders` AS o
					 INNER JOIN `#__digicom_licenses` AS l ON l.`productid`=o.`productid`
				WHERE l.`published`=1
				  AND (".substr($previous_order,0,strlen($previous_order)-4).")
				  AND l.`userid`=".$user->id."";
		// AND l.`expires`>='" . date("Y-m-d H:i:s") . "'
		$db->setQuery($sql);
		if ($db->loadResult())
		{
			return true;
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

		$db = JFactory::getDBO();
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
			$promo = $this->getTable( "Discount" );
		}


		$promo->error = "";
		if ( $promodata[0] == "promoerror" )
			$promo->error = $promodata[1];

		//code exists and we're about to validate it
		if ( $promo->id > 0 && $checkvalid == 1 ) {
			
			/*if ( $uid > 0 ) {
				$sql = "select count(*) from #__digicom_orders where userid='" . $uid . "' and promocode='".$promocode."'";
				$db->setQuery( $sql );
				$licensecount = $db->loadResult();
			} else {
				$licensecount = 0;
			}		*/	

			$licensecount = 0;

			$now = time();
			$promo_data = $promo;
			$error = 1;
			//if code is published and not expired by date or amount
			if ( ($promo_data->codeend >= $now || $promo_data->codeend == 0) && $promo_data->published == '1' && (($promo_data->codelimit - $promo_data->used) > 0 || $promo_data->codelimit == 0 ) && !($promo_data->forexisting != 0 && ($uid < 1 || $licensecount < 1)) ) {
				$error = 0; //code is valid
			} else if ( $promo_data->published != '1' ) {
				$promoerror = _PROMO_NOT_PUBLISHED;
			} else if ( $promo_data->codeend < $now && $promo_data->codeend != 0 ) {
				$promoerror = DS_PROMO_EXPIRED_DATE;
			} else if ( ($promo_data->codelimit - $promo_data->used) < 1 && $promo_data->codelimit != 0 ) {
				$promoerror = DS_PROMO_EXPIRED_AMOUNT;
			} else if ( $promo_data->forexisting != 0 && ($my->id < 1 || $licensecount < 1) ) {
				$promoerror = DS_PROMO_REGISTERED_ONLY;
			} else {
				$promoerror = DS_PROMO_CANT_BEUSED;
			}
			if ( $error ) {//promo code is invalid
				$promo->error = $promoerror;
			} else {
				$promo->error = "";
			}
		}
		return $promo;

	}

	function updateCart( $customer, $configs )
	{
		$jAp = JFactory::getApplication();
		$db = JFactory::getDBO();
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

				$now = time();
				//if code is published and not expired by date or amount
				if ( ($promo_data->codeend >= $now || $promo_data->codeend == 0) && $promo_data->published == '1' && (($promo_data->codelimit - $promo_data->used) > 0 || $promo_data->codelimit == 0 ) && !($promo_data->forexisting != 0 && ($my->id < 1 || $licensecount < 1)) ) {
					$sql = "update #__digicom_session set cart_details='promocode=" . $promo . "' where sid='" . $sid . "'";
					//add this code to user's cart
				} else if ( $promo_data->published != '1' ) {
					$sql = "update #__digicom_session set cart_details='promoerror=" . (JText::_( "DSPROMOCODENP" )) . "' where sid='" . intval($sid) . "'";
				} else if ( $promo_data->codeend < $now && $promo_data->codeend != 0 ) {
					$sql = "update #__digicom_session set cart_details='promoerror=" . (JText::_( "DSPROMOCODEEXPIREDDATE" )) . "' where sid='" . intval($sid) . "'";
				} else if ( ($promo_data->codelimit - $promo_data->used) < 1 && $promo_data->codelimit != 0 ) {
					$sql = "update #__digicom_session set cart_details='promoerror=" . (JText::_( "DSPROMOCODEEXPIREDAMOUNT" )) . "' where sid='" . intval($sid) . "'";
				} else if ( $promo_data->forexisting != 0 && ($my->id < 1 || $licensecount < 1) ) {
					$sql = "update #__digicom_session set cart_details='promoerror=" . (JText::_( "DSPROMOCODEEXPIREDREGUSERONLY" )) . "' where sid='" . intval($sid) . "'";
				} else {
					$sql = "update #__digicom_session set cart_details='promoerror=" . (JText::_( "DSPROMOCODENA" )) . "' where sid='" . intval($sid) . "'";
				}
				//adding status entry to user's session
				$db->setQuery( $sql );
				$db->query();
				//$jAp->enqueueMessage(nl2br($db->getErrorMsg()),'error');
			} else {
				$sql = "update #__digicom_session set cart_details='promoerror=" . (JText::_( "DSPROMOCODEWRONG" )) . "' where sid='" . intval($sid) . "'";
				$db->setQuery( $sql );
				$db->query();
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

		$db = JFactory::getDBO();

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
		$db = JFactory::getDBO();
		$reg = JSession::getInstance("none", array());
		//$sid = $reg->set("digisid", 0);

		if(!$sid){
			return;
		}
		
		$sql = "delete from #__digicom_cart where sid='" . intval($sid) . "'";
		$db->setQuery( $sql );
		$db->query();
	}

	function addFreeProduct($items, $customer, $tax){
		$config = JFactory::getConfig();
		$tzoffset = $config->get('offset');
		//$now = time();
		$now = date('Y-m-d H:i:s', time() + $tzoffset);
		$now = strtotime($now);
		$non_taxed = $tax['total']; //$total;
		$total = $tax['taxed'];
		$currency = $tax['currency'];
		$taxa = $tax['value'];
		$shipping = $tax['shipping'];
		$orderid = $this->addOrder($items, $customer, $now, 'free');
		$this->addOrderDetails($items, $orderid, $now, $customer);
		$type = 'complete_order';
		$this->goToSuccessURL($customer->_sid, '', $orderid , $type);
		return $orderid;
	}
	
	function addOrderInfo($items, $customer, $tax, $status,$prosessor){
		$config = JFactory::getConfig();
		$tzoffset = $config->get('offset');
		//$now = time();
		$now = date('Y-m-d H:i:s', time() + $tzoffset);
		$now = strtotime($now);
		$non_taxed = $tax['total']; //$total;
		$total = $tax['taxed'];
		$currency = $tax['currency'];
		$taxa = $tax['value'];
		$shipping = $tax['shipping'];
		$orderid = $this->addOrder($items, $customer, $now, $prosessor,$status);
		$this->addOrderDetails($items, $orderid, $now, $customer,$status);

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
			$controller = "Licenses";
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
		$db = JFactory::getDBO();
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
		$dispatcher = JDispatcher::getInstance();

		$conf = $this->getInstance( "config", "digicomModel" );
		$configs = $conf->getConfigs();

		$result = $post;
		$data=$responce[0];

		$this->storelog($pg_plugin, $data);
		
		if(isset($data['status']))
		{
			$_SESSION['in_trans'] = 1;
			$customer = $this->loadCustomer($sid);
			
			if($data['status']=='C'){
				$msg = JText::_("COM_DIGICOM_PAYMENT_SUCCESSFUL_THANK_YOU");
				$status = "Active";
			} elseif($data['status']=='P') {
				$status = "Pending";
				$msg = JText::_("COM_DIGICOM_PAYMENT_PENDING_THANK_YOU");
			}else{
				$status = $data['status'];
				$msg = JText::_("COM_DIGICOM_PAYMENT_WAITING_THANK_YOU");
			}
			
			$config = JFactory::getConfig();
			$tzoffset = $config->get('offset');
			$now = date('Y-m-d H:i:s', time() + $tzoffset);
			$now = strtotime($now);

			$this->updateOrder($order_id,$result,$data,$pg_plugin,$status,$items,$customer);
			
		}

		$item 	= $app->getMenu()->getItems('link', 'index.php?option=com_digicom&view=orders', true);
		$Itemid = isset($item->id) ? '&Itemid=' . $item->id : '';

		if($status != "Active"){			
			$app->redirect(JURI::root()."index.php?option=com_digicom&view=order&id=".$order_id.$Itemid,$msg);
		}
		
		// orders page
		if ($configs->get('afterpurchase',1) == 1)
		{
			$app->redirect(JURI::root()."index.php?option=com_digicom&view=order&id=".$order_id.$Itemid,$msg);
		}
		// download page
		else
		{
			$item 	= $app->getMenu()->getItems('link', 'index.php?option=com_digicom&view=downloads', true);
			$Itemid = isset($item->id) ? '&Itemid=' . $item->id : '';

			$app->redirect(JURI::root()."index.php?option=com_digicom&view=downloads".$Itemid,$msg);
		}
		
		return true;
	}
	
	function updateOrder($order_id,$result,$data,$pg_plugin,$status,$items,$customer){
		
		$table = $this->getTable('Order');
		$table->load($order_id);

		//amount_paid
		$table->amount_paid = $table->amount_paid + $data['total_paid_amt'];

		//processor
		$table->processor = $data['processor'];
		$warning = '';
		$type = 'process_order';
		//status
		if($table->amount_paid >= $table->amount){
			$table->status = $status;
			$type = 'complete_order';
		}else if(($table->amount_paid > 0) && ($table->amount_paid < $table->amount)){
			$warning = JText::_('COM_DIGICOM_PAYMENT_FROUD_CASE_PAYMENT_MANUPULATION');
			$table->status = 'Pending';
		}else{
			$table->status = $status;

		}

		$comment = array();
		$comment[] = $table->comment;
		$comment[] = (isset($result['comment']) ? $result['comment'] : '');


		$table->comment = implode("\n", $comment);

		$orderparams = json_decode($table->params);
		$orderparams->paymentinfo 	= array();
		$orderparams->paymentinfo[] = $pg_plugin;
		$orderparams->paymentinfo[] = $result;
		$orderparams->paymentinfo[] = $data;
		$orderparams->warning 	= $warning;

		$table->params = json_encode($orderparams);
		$table->store();

		//triggere email
		$config = JFactory::getConfig();
		$tzoffset = $config->get('offset');
		$now = date('Y-m-d H:i:s', time() + $tzoffset);
		$now = strtotime($now);

		$this->dispatchMail( $order_id, $table->amount_paid, $table->number_of_products, $now, $items, $customer , $type, $status);

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

		$now = time();
		$total = $tax['taxed'];
		$number_of_products = $tax['number_of_products'];

		$this->dispatchMail( $orderid, $total, $number_of_products, $now, $items, $customer , $type);
		$cart->emptyCart( $sid );
		
		return true;

	}
	
	function getFinalize( $sid, $msg = '', $orderid = 0 , $type)
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
		
		$now = time();
		$total = $tax['taxed'];
		$number_of_products = $tax['number_of_products'];

		/* fixed return after payment, before paypal IPN */
		$plugin = JRequest::getVar( 'plugin', '' );
		$this->dispatchMail( $orderid, $total, $number_of_products, $now, $items, $customer , $type);
		$cart->emptyCart( $sid );
		
		return true;

	}

	/**
	 * Get load Customer information cart
	 * @param int $sid - session id
	 * @return Array
	 */
	function loadCustomer($sid){
		$db = JFactory::getDBO();
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

		$database = JFactory::getDBO();
		$my = JFactory::getUser();

		$data = array();
		$data['cart'] = array();
		$data['cart']['orderid'] = $orderid;
		$data['cart']['total'] = $tax['taxed'];
		$data['cart']['tax'] = $tax['taxed'] - $tax['total'] - $tax['shipping'];

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

		$data['nontaxed'] = $tax['total'];
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

	//mail sending function
	
	function dispatchMail($orderid, $amount, $number_of_products, $timestamp, $items, $customer, $type = 'new_order', $status = '')
	{
		
		$app 	= JFactory::getApplication();
		$db 	= JFactory::getDbo();
		$site_config = JFactory::getConfig();
		// get sid & uid
		if (is_object($customer)) $sid = $customer->_sid;
		if (is_array($customer)) $sid = $customer['sid'];

		if (is_object($customer) && isset($customer->_user->id))  $uid = $customer->_user->id;
		if (is_array($customer)) $uid = $customer['userid'];

		if ( !$sid ) return;

		$my = JFactory::getUser($uid);

		$database = JFactory::getDBO();
		$cart = $this->getInstance( "Cart", "digicomModel" );
		$configs = $this->getInstance( "Config", "digicomModel" );
		$configs = $configs->getConfigs();
		$tax = $cart->calc_price( $items, $customer, $configs );
		//echo $type;die;
		$email = $configs->get('email');
		
		$message = $email->$type->body;
		$subject = $email->$type->subject;
		
		// Replace all variables in template
		$uri = JURI::getInstance();
		$sitename = (trim( $configs->get('store_name','DigiCom Store') ) != '') ? $configs->get('store_name','DigiCom Store') : $site_config->get( 'sitename' );
		$siteurl = (trim( $configs->get('store_url','') ) != '') ? $configs->get('store_url','') : $uri->base();

		$message = str_replace( "[SITENAME]", $sitename, $message );

		$message = str_replace( "../%5BSITEURL%5D", $siteurl, $message );
		$message = str_replace( "%5BSITEURL%5D", $siteurl, $message );
		$message = str_replace( "[SITEURL]", $siteurl, $message );

		$query = "select lastname, company from #__digicom_customers where id=" . $my->id;
		$db->setQuery( $query );
		$customer_database = $db->loadAssocList();
		$lastname = (isset($customer_database["0"]["lastname"]) ? $customer_database["0"]["lastname"] : '');
		$copany = (isset($customer_database["0"]["copany"]) ? $customer_database["0"]["copany"] : '');

		$message = str_replace("[CUSTOMER_COMPANY_NAME]", $copany, $message);
		$message = str_replace( "[CUSTOMER_USER_NAME]", $my->username, $message );
		$message = str_replace( "[CUSTOMER_FIRST_NAME]", $my->name, $message );
		$message = str_replace( "[CUSTOMER_LAST_NAME]", $lastname, $message );
		$message = str_replace( "[CUSTOMER_EMAIL]", $my->email, $message );

		$message = str_replace( "[TODAY_DATE]", date( $configs->get('time_format','d-m-Y'), $timestamp), $message );
		$message = str_replace( "[ORDER_ID]", $orderid, $message );
		$message = str_replace( "[ORDER_AMOUNT]", $amount, $message );
		$message = str_replace( "[NUMBER_OF_PRODUCTS]", $number_of_products, $message );
		$message = str_replace( "[PROMO]", $tax['promo'], $message );
		$message = str_replace( "[STATUS]", $status, $message );
		$displayed = array();
		$product_list = '';


		$counter = array();
		foreach ( $items as $i => $item ) {
			if ( $i < 0 )
				continue;
			if ( !isset( $counter[$item->id] ) )
				$counter[$item->id] = 1;
			$counter[$item->id]++;
		}
		foreach ( $items as $i => $item ) {
			if ( $i < 0 )
				continue;
			$optionlist = '';
			if ( !in_array( $item->name, $displayed ) ) {
				//$product_list .= $counter[$item->id]." - ".$item->name.'<br />';
				$product_list .= $item->quantity . " - " . $item->name . '<br />';
			}
			$displayed[] = $item->name;
		}
		$message = str_replace( "[PRODUCTS]", $product_list, $message );
		$email = new stdClass();
		$email->body = $message;

		//subject
		$subject = str_replace( "[SITENAME]", $sitename, $subject );
		$subject = str_replace("[CUSTOMER_COMPANY_NAME]", $copany, $subject);
		$subject = str_replace( "../%5BSITEURL%5D", $siteurl, $subject );
		$subject = str_replace( "%5BSITEURL%5D", $siteurl, $subject );
		$subject = str_replace( "[SITEURL]", $siteurl, $subject );

		$subject = str_replace( "[CUSTOMER_USER_NAME]", $my->username, $subject );
		$subject = str_replace( "[CUSTOMER_FIRST_NAME]", $my->name, $subject );
		$subject = str_replace( "[CUSTOMER_LAST_NAME]", $lastname, $subject );
		$subject = str_replace( "[CUSTOMER_EMAIL]", $my->email, $subject );

		$subject = str_replace( "[TODAY_DATE]", date( $configs->get('time_format','d-m-Y'), $timestamp ), $subject );
		$subject = str_replace( "[ORDER_ID]", $orderid, $subject );
		$subject = str_replace( "[ORDER_AMOUNT]", $amount, $subject );
		$subject = str_replace( "[NUMBER_OF_PRODUCTS]", $number_of_products, $subject );
		$subject = str_replace( "[PROMO]", $tax['promo'], $subject );
		$subject = str_replace( "[STATUS]", $status, $subject );
		$displayed = array();
		$product_list = '';
		foreach ( $items as $i => $item ) {
			if ( $i < 0 )
				continue;
			if ( !in_array( $item->name, $displayed ) )
				$product_list .= $item->name . '<br />';
			$displayed[] = $item->name;
		}
		$subject = str_replace( "[PRODUCTS]", $product_list, $subject );

		//echo $message;die;

		$subject = html_entity_decode( $subject, ENT_QUOTES );

		$message = html_entity_decode( $message, ENT_QUOTES );

		$app = JFactory::getApplication('site');

		$mosConfig_mailfrom = $app->getCfg("mailfrom");
		$mosConfig_fromname = $app->getCfg("fromname");

		if ( $configs->get('usestoremail',1) == '1' && strlen( trim( $configs->get('store_name','DigiCom Store') ) ) > 0 && strlen( trim( $configs->get('store_email','') ) ) > 0 ) {
			$adminName2 = $configs->get('store_name','DigiCom Store');
			$adminEmail2 = $configs->get('store_email','');
		} else{
			$adminName2 = $mosConfig_fromname;
			$adminEmail2 = $mosConfig_mailfrom;
		}

		$mailSender = JFactory::getMailer();
		$mailSender->IsHTML( true );
		$mailSender->addRecipient( $my->email );
		$mailSender->setSender( array($adminEmail2, $adminName2) );
		$mailSender->setSubject( $subject );
		$mailSender->setBody( $message );
		//Log::write( $message );
		if ( !$mailSender->Send() ) {
			//<Your error code management>
		}

		if ( $configs->get('sendmailtoadmin',1) != 0 ) {
			$mailSender = JFactory::getMailer();
			$mailSender->IsHTML( true );
			$mailSender->addRecipient( $adminEmail2 );
			$mailSender->setSender( array($adminEmail2, $adminName2) );
			$mailSender->setSubject( $subject );
			$mailSender->setBody( $message );
			
			//Log::write( $message );
			if ( !$mailSender->Send() ) {
				//error code
			}
		}
		
		return true;
	}

	//integrate with idev_affiliate
	function affiliate( $total, $orderid, $configs )
	{

		$mosConfig_live_site = DigiComSiteHelperDigiCom::getLiveSite();

		$my = JFactory::getUser();
		if ( $configs->get('idevaff','notapplied') == 'notapplied' )
			return;
		@session_start();
		$idev_psystems_1 = $total;
		$idev_psystems_2 = $orderid;
		$name = "iJoomla Products";
		$email = $my->email; //"cust@cust.cust";
		$item_number = 1;
		$ip_address = $_SERVER['REMOTE_ADDR'];
		if ( $configs->get('idevaff','notapplied') == 'standalone' && file_exists( JPATH_SITE . "/" . $configs->get('idevpath','notapplied') . "/sale.php" ) )
		{
			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_URL, $mosConfig_live_site . "/" . $configs->get('idevpath','notapplied') . "/sale.php?profile=72198&idev_saleamt=" . $total . "&idev_ordernum=" . $orderid . "&ip_address=" . $ip_address );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_exec( $ch );
			curl_close( $ch );
		} else if ( $configs->get('idevaff','notapplied') == 'component' ) {
			$orderidvar = $configs->get('orderidvar','');
			$ordersubtotvar = $configs->get('ordersubtotalvar','');
			echo '<img border="0" src="' . $mosConfig_live_site . '/components/com_idevaffiliate/sale.php?' . $ordersubtotvar . '=' . sprintf( "%.2f", $total ) . '&' . $orderidvar . '=' . $orderid . '" width="1" height="1">';
		}

	}

	function addOrder( $items, $cust_info, $now, $paymethod, $status = "Active" )
	{
		$cart = $this;
		$conf = $this->getInstance( "config", "digicomModel" );
		$configs = $conf->getConfigs();
		$db = JFactory::getDBO();
		$tax = $cart->calc_price( $items, $cust_info, $configs );
		
		//print_r($tax);die;
		
		$customer = $cust_info;

		if (is_object($customer)) $sid = $customer->_sid;
		if (is_array($customer)) $sid = $customer['sid'];

		if (is_object($customer) && isset($customer->_user->id))  $uid = $customer->_user->id;
		if (is_array($customer)) $uid = $customer['userid'];

		if($uid == 0){
			return 0;
		}

		$non_taxed = $tax['total'];
		$total = $tax['taxed'];
		$payable_amount = $tax['payable_amount'];
		$taxa = $tax['value'];
		$shipping = $tax['shipping'];
		$currency = $tax['currency'];
		$number_of_products = $tax['number_of_products'];

		$promo = $cart->get_promo( $cust_info );
		if($promo->id > 0){
			$promoid = $promo->id;
			$promocode = $promo->code;
		}
		else{
			$promoid = '0';
			$promocode = '0';
		}
		
		$sql = "insert into #__digicom_orders ( userid, order_date, amount, amount_paid, currency, processor, number_of_products, status, promocodeid, promocode, promocodediscount, published ) "
		. " values ('{$uid}','".$now."','".$payable_amount."', '0', '" . $currency . "','" . $paymethod . "','".$number_of_products."', '" . $status . "', '" . $promoid . "', '" . $promocode . "', '" . $tax['promo'] . "', '1') ";
		
		$db->setQuery( $sql );
		$db->query();
		$orderid = $db->insertid();
		$this->storeTransactionData( $items, $orderid, $tax, $sid );

		if ( $promoid > 0 ) {
			$sql = "update #__digicom_promocodes set `used`=`used`+1 where id=" . $promoid;
			$db->setQuery( $sql );
			$db->query();
		}
		return $orderid;

	}

	function addOrderDetails($items, $orderid, $now, $customer, $status = "Active")
	{
		$license = array();
		if($status != "Pending")
			$published = 1;
		else
			$published = 0;

		$database = JFactory::getDBO();
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
			if($key >= 0)
			{
				$price = (isset($item->discount) && ($item->discount > 0)) ? $item->discount : $item->subtotal_formated;
				$date = JFactory::getDate();
				$purchase_date = $date->toSql();
				$expire_string = "0000-00-00 00:00:00";
				$package_type = (!empty($item->bundle_source) ? $item->bundle_source : 'reguler');
				$sql = "insert into #__digicom_orders_details(userid, productid,quantity, orderid, amount_paid, published, package_type, purchase_date, expires) "
						. "values ('{$user_id}', '{$item->item_id}', '{$item->quantity}', '".$orderid."', '{$price}', ".$published.", '".$package_type."', '".$purchase_date."', '".$expire_string."')";
				//echo $sql;die;
				$database->setQuery($sql);
				$database->query();

				$site_config = JFactory::getConfig();
				$tzoffset = $site_config->get('offset');
				$buy_date = date('Y-m-d H:i:s', time() + $tzoffset);
				$sql = "insert into #__digicom_logs (`userid`, `productid`, `buy_date`, `buy_type`)
						values (".$user_id.", ".$item->item_id.", '".$buy_date."', 'new')";
				$database->setQuery($sql);
				$database->query();
				
				
				$sql = "update #__digicom_products set used=used+1 where id = '" . $item->item_id . "'";
				$database->setQuery( $sql );
				$database->query();
				
			}
		}
		// end foreach
		
		return true;
	}

	function addUserToList($user_id, $product_id){
		require_once(JPATH_COMPONENT.DS.'helpers'.DS.'MCAPI.class.php');
		$db = JFactory::getDBO();
		$sql = "select p.mailchimpapi, p.mailchimplist, p.mailchimpregister, p.mailchimpgroupid, u.email, c.firstname, c.lastname from #__users u, #__digicom_products p, #__digicom_customers c where u.id=c.id and u.id=".intval($user_id)." and p.id=".intval($product_id);
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadAssocList();

		if(isset($result) && count($result) > 0){
			$mc_username = $result["0"]["mailchimpapi"];
			$mc_listid  = $result["0"]["mailchimplist"];
			$mc_autoregister = $result["0"]["mailchimpregister"] == 0 ? FALSE : TRUE;
			$mc_groupid = $result["0"]["mailchimpgroupid"];
			$mc_email = $result["0"]["email"];

			if(trim($mc_username) == ""){
				$sql = "select `mailchimpapi` from #__digicom_settings";
				$db->setQuery($sql);
				$db->query();
				$mc_username = $db->loadResult();
			}

			if(trim($mc_listid) == ""){
				$sql = "select `mailchimplist` from #__digicom_settings";
				$db->setQuery($sql);
				$db->query();
				$mc_listid = $db->loadResult();
			}

			if(trim($mc_username) != "" && trim($mc_listid) != ""){
				$api = new MCAPI($mc_username);
				$user_info = $api->listMemberInfo($mc_listid, $mc_email);

				if($user_info === FALSE){//add new user
					$mergeVars = array('FNAME'=>$result["0"]["firstname"], 'LNAME'=>$result["0"]["lastname"]);
					if(trim($mc_groupid) != ""){
						$mergeVars["INTERESTS"] = trim($mc_groupid);
					}
					$api->listSubscribe($mc_listid, $mc_email, $mergeVars, 'html', $mc_autoregister, true);
				}
				else{//already exist and update user group
					if(trim($mc_groupid) != ""){
						$user_group_string = $user_info["merges"]["INTERESTS"];
						$user_group_array = explode(",", $user_group_string);
						$exist = FALSE;
						foreach($user_group_array as $key=>$group){
							if(trim($group) == trim($mc_groupid)){
								$exist = TRUE;
							}
						}

						if($exist === FALSE){
							$new_group_list = trim($user_group_string);
							if(trim($new_group_list) != ""){
								$new_group_list = trim($user_group_string).", ".trim($mc_groupid);
							}
							else{
								$new_group_list = trim($mc_groupid);
							}
							$mergeVars = array('INTERESTS' => $new_group_list);

							$name = "";
							$groups = $api->listInterestGroupings($mc_listid);
							if(isset($groups) && count($groups) > 0){
								foreach($groups as $key_group=>$group){
									if(isset($group["groups"]) && count($group["groups"]) > 0){
										foreach($group["groups"] as $key_subgroup=>$subgroup){
											if(trim($subgroup["name"]) == trim($mc_groupid)){
												$name = $group["name"];
												break;
											}
										}
									}
								}
							}
							if(trim($name) != ""){
								$mergeVars = array('INTERESTS' => $new_group_list, 'GROUPINGS'=>array(array('name'=>$name, 'groups'=>$new_group_list)));
							}
							$api->listUpdateMember($mc_listid, $mc_email, $mergeVars, 'html', false);
						}
					}
				}
			}
		}
	}
	
	function addLicensePackage($package_id,$orderid, $userid, $published){
		$db = JFactory::getDbo();
		$sql = 'SELECT  
					`p`.`id` AS `productid` , 
					`p`.`domainrequired`
					
				FROM `#__digicom_products` AS  `p`
				WHERE 
					`p`.`id`='.$package_id.'
				LIMIT 1';
		$db->setQuery($sql);
		$res = $db->loadObject();
		if( $res && $res->domainrequired==3 ) {
			$this->createLicense($orderid, $res, $userid , 0, $published);
		}
	}

	/**
	 * Create license for product or package
	 */
	public function createLicense( $order_id, $product4sell, $user_id=null, $package_item=0, $published=0 ) {
		if( $product4sell->domainrequired==3 ) {
			$items = $this->getSubProduct($product4sell->productid);
			if( $items && count($items) ) {
				foreach( $items as $item ) {
					$this->createLicense( $order_id, $item,  $user_id, $product4sell->productid, $published );
				}
			}
		} else {
			$this->createLicense2( $order_id, $product4sell, $user_id, $package_item, $published );
		}
	}
	
	/**
	 * Create license for end product (not package)
	 */
	public function createLicense2( $order_id, $product, $user_id=null, $package_item=0, $published ){
		$db 	= JFactory::getDbo();
		$app 	= JFactory::getApplication();
		$order 		= $this->getOrder($order_id);
		$order_date = $order->order_date;
		$licenseid = $this->getNewLicenseId();
		$ltype = ($package_item)?'package_item':'common';
		if(!$user_id){
			$user_id = $order->userid;
		}
		$expires = "";
		$time_unit = array( 1=>'HOUR', 2=>'DAY', 3=>'MONTH', 4=>'YEAR' );
		if( $product->duration_type!=0 && $product->duration_count!= -1 ) {
			$expires = ' DATE_ADD(FROM_UNIXTIME('.$order->order_date.'), INTERVAL '.$product->duration_count.' '.$time_unit[$product->duration_type].') ';
		} else {
			$expires = ' "0000-00-00 00:00:00" ';
		}
		$sql = 'INSERT INTO `#__digicom_licenses`
					( `licenseid`, `userid`, `productid`, `domain`, `amount_paid`, `orderid`, `dev_domain`, `hosting_service`, `published`, `ltype`, `package_id`, `purchase_date`, `expires`, `renew`, `download_count`, `plan_id`)
						VALUES
					("'.$licenseid.'", '.$user_id.', '.$product->productid.', "", '.$product->price.', '.$order_id.', "", "", '.$published.', "'.$ltype.'",'.$package_item.', FROM_UNIXTIME('.$order->order_date.'), '.$expires.', 0, 0, '.$product->plan_id.')';
		$db->setQuery($sql);
		$db->query();
		if($db->getErrorNum()){
			$app->enqueuemessage($db->getErrorMsg(), 'error');
		}
	}

	public function getSubProduct( $package_id ) {
		if( !isset( $this->packages[$package_id] ) ) {
			$db = JFactory::getDbo();
			$sql = 'SELECT  
						`f`.`featuredid` AS `productid` , 
						`pp`.`price` , 
						`p`.`domainrequired` , 
						`pl`.`duration_count`, 
						`pl`.`duration_type`,
						`pl`.`id` AS `plan_id`
						
					FROM `#__digicom_featuredproducts` AS  `f` 
							INNER JOIN  
						`#__digicom_products` AS  `p` ON  `f`.`featuredid` =  `p`.`id` 
							LEFT JOIN  
						`#__digicom_products_plans` AS `pp` ON (`f`.`featuredid` =`pp`.`product_id` AND `f`.`planid` = `pp`.`plan_id` )
							LEFT JOIN 
						`#__digicom_plans` AS `pl` ON `f`.`planid` = `pl`.`id`
					WHERE 
						`f`.`productid`='.$package_id;
			$db->setQuery($sql);
			$res = $db->loadObjectList();
			$this->packages[$package_id] = $res;
		}
		return $this->packages[$package_id];
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

	public function getNewLicenseId(){
		$db 	= JFactory::getDbo();
		$sql 	= "SELECT max(licenseid) FROM `#__digicom_licenses` WHERE CONCAT('',`licenseid`*1)=`licenseid`";
		$db->setQuery( $sql );
		$licenseid = $db->loadResult();
		if(isset($licenseid) && intval($licenseid) != "0"){
			$licenseid = intval($licenseid)+1;
		} else {
			$licenseid = 100000001;
		}
		return $licenseid;
	}
	
	protected function getPlanId($product_id, $plan_id=0, $renew=0){
		if ($renew == 0 )
		{
			$sql = "SELECT pl.id as value, pl.name as text, pp.price, pp.default
					FROM #__digicom_products_plans pp
						 LEFT JOIN #__digicom_plans pl ON ( pp.plan_id = pl.id )
					WHERE pp.product_id = ".$product_id;
		} else {
			$sql = "SELECT pl.id as value, pl.name as text, pp.price, pp.default
					FROM #__digicom_products_renewals pp
						 LEFT JOIN #__digicom_plans pl ON ( pp.plan_id = pl.id )
					WHERE pp.product_id = " .$product_id;
		}

		$db->setQuery( $sql );
		$item_plains = $db->loadObjectList('value');
		if (empty($item_plains))
		{
			$sql = "SELECT pl.id as value, pl.name as text, pp.price, pp.default
					FROM #__digicom_products_plans pp
						 LEFT JOIN #__digicom_plans pl ON ( pp.plan_id = pl.id )
					WHERE pp.product_id = ".$product_id;
			$db->setQuery( $sql );
			$item_plains = $db->loadObjectList('value');
		}

		if( !$plan_id || !isset($item_plains[$plan_id]) ){
			$plain_default_value = null;
			foreach ($item_plains as $plain_key => $plain_item)
			{
				if($plain_item->default == 1)
				{
					$plain_default_value = &$item_plains[$plain_key];
					$plan_id = $plain_key;
					break;
				}
			}
		}

		return $plan_id;
	}

}
