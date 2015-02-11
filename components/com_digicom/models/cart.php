<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 457 $
 * @lastmodified	$LastChangedDate: 2014-01-26 08:51:32 +0100 (Sun, 26 Jan 2014) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license


*/

defined ('_JEXEC') or die ("Go away.");
jimport( "joomla.aplication.component.model" );
jimport('joomla.filesystem.file');

class DigiComModelCart extends DigiComModel
{
	public $orders 		= array();
	public $packages 	= array();
	
	public $_items = null;
	public $tax = null;

	function __construct()
	{
		parent::__construct();
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

		$plugins = JPluginHelper::getPlugin( 'digicompayment' );

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

	function addToCart($customer){
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
		$sql = "select cid, item_id, quantity from #__digicom_cart where sid='".intval($sid)."' AND item_id='".intval($pid)."' AND plan_id = '".intval($plan_id)."'";
		$db->setQuery($sql);
		$data = $db->loadObjectList();
		$item_id = @$data["0"]->item_id; //lets just check if item is in the cart
		$item_qty = @$data["0"]->quantity;
		$cid = @$data["0"]->cid;


		if(!$item_id){//no such item in cart- inserting new row
			$renew = (JRequest::getVar('renew', '') != '') ? JRequest::getVar('renew', '') : '0';
			$renewlicid = (JRequest::getVar('renewlicid', '') != '') ? JRequest::getVar('renewlicid', '') : '-1';
			$sql = "insert into #__digicom_cart (quantity, item_id, sid, userid, plan_id, renew, renewlicid)"
				. " values ('".$qty."', '".intval($pid)."', '".intval($sid)."', '".intval($uid)."', '".intval($plan_id)."', '".$renew."', '".$renewlicid."')";
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
						`s`.`currency`,
						`c`.*,
						`c`.`plan_id` as `plan_id`,
						`pl`.*,
						`p`.*,
						`pc`.`catid`,
						`pp`.`price` AS `price`,
						`ppr`.`price` AS `price_renew`
					FROM
						`#__digicom_settings` AS `s`,
						`#__digicom_products` AS `p`
							INNER JOIN
						`#__digicom_cart` AS `c` ON (`c`.`item_id` = `p`.`id`)
							LEFT JOIN
						`#__digicom_plans` AS `pl` ON (`c`.`plan_id` = `pl`.`id`)
							LEFT JOIN
						`#__digicom_products_plans` AS `pp` ON (`pp`.`product_id` = `p`.`id`
							AND `pp`.`plan_id` = `pl`.`id`)
							LEFT JOIN
						`#__digicom_products_renewals` AS `ppr` ON (`ppr`.`product_id` = `p`.`id`
							AND `ppr`.`plan_id` = `pl`.`id`)
							LEFT JOIN
						`#__digicom_product_categories` AS `pc` ON `p`.`id` = `pc`.`productid`
					WHERE
						`c`.`sid` = '" . intval($sid) . "' AND `c`.`item_id` = `p`.`id`
					ORDER BY `p`.`ordering`";
		$db->setQuery($sql);
		$items = $db->loadObjectList();
		foreach ($items as $i => $item)
		{
			$new_plan_id = JRequest::getVar('plan_id', array());
			if (!empty($new_plan_id))
			{
				if (is_array($new_plan_id))
				{
					$new_plan_id = $new_plan_id[$item->cid];
				}

				if (($item->plan_id < 1) && ($new_plan_id != -1))
				{
					$new_plan_id = $this->getPlanId($item->id,$new_plan_id,$item->renew);
					$sql = "update #__digicom_cart set plan_id = ".$new_plan_id." where sid = ".intval($sid)." and cid=".$item->cid;
					$db->setQuery($sql);
					$db->query();
					$items[$i]->plan_id = $new_plan_id;
				}
			}

			// Get default plain and set price
			if (($item->plan_id < 1))
			{
				$renew_req = JRequest::getVar("renew", "");
				if ($renew_req == "renew")
				{
					$item->renew = 1;
				}
				if ($item->renew == 0 )
				{
					$sql = "SELECT pl.id as value, pl.name as text, pp.price, pp.default
							FROM #__digicom_products_plans pp
								 LEFT JOIN #__digicom_plans pl ON ( pp.plan_id = pl.id )
							WHERE pp.product_id = ".$item->id;
				}
				else
				{
					$sql = "SELECT pl.id as value, pl.name as text, pp.price, pp.default
							FROM #__digicom_products_renewals pp
								 LEFT JOIN #__digicom_plans pl ON ( pp.plan_id = pl.id )
							WHERE pp.product_id = " . $item->id;
				}

				$db->setQuery( $sql );
				$item_plains = $db->loadObjectList();
				if (!isset($item_plains) || count($item_plains) <= 0)
				{
					$sql = "SELECT pl.id as value, pl.name as text, pp.price, pp.default
							FROM #__digicom_products_plans pp
								 LEFT JOIN #__digicom_plans pl ON ( pp.plan_id = pl.id )
							WHERE pp.product_id = ".$item->id;
					$db->setQuery( $sql );
					$item_plains = $db->loadObjectList();
				}
				$plain_default_value = null;

				foreach ($item_plains as $plain_key => $plain_item)
				{
					if($plain_item->default == 1)
					{
						$plain_default_value = &$item_plains[$plain_key];
					}
				}

				if ($plain_default_value)
				{
					$item->plan_id = $plain_default_value->value;
					$item->price = $plain_default_value->price;
				}
			} else { // Check plan
				if ($item->renew == 0 )
				{
					$sql = "SELECT pl.id as value, pl.name as text, pp.price, pp.default
							FROM #__digicom_products_plans pp
								 LEFT JOIN #__digicom_plans pl ON ( pp.plan_id = pl.id )
							WHERE pp.product_id = ".$item->id;
				} else {
					$sql = "SELECT pl.id as value, pl.name as text, pp.price, pp.default
							FROM #__digicom_products_renewals pp
								 LEFT JOIN #__digicom_plans pl ON ( pp.plan_id = pl.id )
							WHERE pp.product_id = " . $item->id;
				}

				$db->setQuery( $sql );
				$item_plains = $db->loadObjectList('value');
				if (empty($item_plains))
				{
					$sql = "SELECT pl.id as value, pl.name as text, pp.price, pp.default
							FROM #__digicom_products_plans pp
								 LEFT JOIN #__digicom_plans pl ON ( pp.plan_id = pl.id )
							WHERE pp.product_id = ".$item->id;
					$db->setQuery( $sql );
					$item_plains = $db->loadObjectList('value');
				}
				if( !isset($item_plains[$item->plan_id]) ){
					$plain_default_value = null;
					foreach ($item_plains as $plain_key => $plain_item)
					{
						if($plain_item->default == 1)
						{
							$plain_default_value = &$item_plains[$plain_key];
						}
					}

					if ($plain_default_value)
					{
						$item->plan_id = $plain_default_value->value;
						$item->price = $plain_default_value->price;
					}
				}
			}
		}

		//change the price of items if needed
		for ( $i = 0; $i < count( $items ); $i++ )
		{
			$item = &$items[$i];

			if ($item->renew == 1 && $item->price_renew != "")
			{
				$item->price = $item->price_renew;
			}

			//$item->subtotal = $item->quantity * $item->price;
			$item->subtotal = $item->price;
			$item->discount = 0;

			$item->price = DigiComHelper::format_price( $item->price, $item->currency, false, $configs ); //sprintf( $price_format, $item->product_price );
			$item->subtotal = DigiComHelper::format_price( $item->subtotal, $item->currency, false, $configs ); //sprintf( $price_format, $item->subtotal );

			$item->price_formated = DigiComHelper::format_price2( $item->price, $item->currency, false, $configs ); //sprintf( $price_format, $item->product_price );
			$item->subtotal_formated = DigiComHelper::format_price2( $item->subtotal, $item->currency, false, $configs ); //sprintf( $price_format, $item->subtotal );
		}

		$productfields = array();
		//loose moment - we have duplicate rows - cids are different
		//yet fields should be the same... however this division
		//is not on database level - thus no actual cid difference should be there

		foreach ( $items as $i => $product ) {

			$item = &$items[$i];
			$where1 = array();
			$where1[] = " f.published=1 ";
			$where1[] = " fd.productid=" . $product->id;
			$where1[] = " fd.publishing=1 ";

			$sql = "select f.size, f.name, f.options, f.id, fd.mandatory, fd.publishing
					from #__digicom_customfields f
						 left join #__digicom_prodfields fd on (f.id=fd.fieldid)" . (count( $where1 ) > 0 ? "
					where " . implode( " and ", $where1 ) : "") . "
					order by ordering ";
			$db->setQuery( $sql );
			$fields = $db->loadObjectList();

			if ( count( $fields ) > 0 ) {

				$item->productfields = $fields; //now all products in the list should have correct option sets

				$price_options = 0;

				foreach ( $item->productfields as $j => $v ) {

					$options = explode( "\n", $v->options );
					$sql = "select optionid from #__digicom_cartfields where fieldid='" . $v->id . "' and cid='" . $product->cid . "'";
					$db->setQuery( $sql );
					$optionid = $db->loadObjectList();
					if ( count( $optionid ) < 1 )
						$optionid = -1;
					else
						$optionid = $optionid[0]->optionid;

					$item->productfields[$j]->optionid = $optionid;
					$v->optionid = $optionid;

					foreach ( $options as $i1 => $v1 ) {

						if ( isset( $v->optionid ) && $v->optionid == $i1 ) {

							$val = explode( ",", $v1 );

							if ( isset( $val[1] ) && strlen( trim( $val[1] ) ) > 0 ) {

								$item->no_discounted_price = ($item->subtotal * $item->quantity + $item->quantity * floatval( trim( $val[1] ) )) / $item->quantity;
								//$item->subtotal = $item->price * $item->quantity + $item->quantity * floatval( trim( $val[1] ) );
								//$item->subtotal = $item->price * $item->quantity + $item->quantity * floatval( trim( $val[1] ) );
								$price_options += floatval( trim( $val[1] ) );

								if ( isset( $item->discounted_price ) && $item->discounted_price > 0 ) {
									$item->discounted_price = (floatval( trim( $val[1] ) ) * $item->quantity + $item->discounted_price * $item->quantity * (100 - $item->discount) / 100) / $item->quantity;
									//$item->subtotal = $item->discounted_price * $item->quantity;
								}
							}
						}
					}
				}

				$item->price = $item->price + $price_options;

			}

			$item->subtotal = $item->price * $item->quantity;

			$sql = "select f.featuredid as id, p.name as name, f.planid, pl.name as planname, pl.duration_count, pl.duration_type from #__digicom_featuredproducts f, #__digicom_products p, #__digicom_plans pl where f.planid = pl.id and f.featuredid=p.id and f.productid=".intval($product->id);

			$db->setQuery( $sql );
			$featured_list = $db->loadObjectList();
			$item->featured = $featured_list;
		}

		if(count($items) > 0){
			$this->calc_price($items, $customer, $configs);
			if($configs->get('tax_price','') and false){
				foreach($items as $i => $v){
					if($i < 0){
						continue;
					}
					$items[$i]->price += $v->itemtax;
					$items[$i]->subtotal += $v->itemtax;
				}
			}
			else{
				foreach($items as $i => $v){
					if($i < 0){
						continue;
					}
				}
			}
		}

		$this->_items = $items;

		if(isset($items) && count($items) > 0){
			foreach($items as $key=>$value){
				if(isset($value) && isset($value->id)){
					$sql = "select `path`, `title` from #__digicom_products_images where product_id=".intval($value->id)." and `default`=1";
					$db->setQuery($sql);
					$db->query();
					$result = $db->loadAssocList();
					if(!isset($result) || count($result) == 0){
						$sql = "select `path`, `title` from #__digicom_products_images where product_id=".intval($value->id)." and `default`= 0 limit 1";
						$db->setQuery($sql);
						$db->query();
						$result = $db->loadAssocList();
					}
					@$value->defprodimage = trim($result["0"]["path"]);
					@$value->image_title = trim($result["0"]["title"]);
					$items[$key] = $value;
				}
			}
		}

		return $items;
	}

	function calc_price(&$items, $cust_info, $configs)
	{
		if(isset($items[-1]) && $items[-1] == "Taxed"){
			return $items[-2];
		}
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		//$tax_handler = $this->getInstance( "Tax", "digicomModel" );
		if (is_object($cust_info))	$sid = $cust_info->_sid;
		if (is_array($cust_info))	$sid = $cust_info['sid'];
		$customer = $cust_info;
		//		if (!$sid )	$sid = get_sid();

		//if ( !isset( $configs->get('totaldigits','') ) ) {
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
		$can_promo = false;
		$has_renewal = false;
		$tax = array();
		$total = 0;
		$price_format = '%' . $configs->get('totaldigits','') . '.' . $configs->get('decimaldigits','2') . 'f';
		$tax['licenses'] = 0;

		$tax['shipping'] = 0;

		$tax['currency'] = $configs->get('currency','USD');

		$promo = $this->get_promo( $cust_info );

		foreach ( $items as $item )
		{
			//debug($item);
			$total += $item->subtotal;
			$tax['currency'] = $item->currency;
			$tax['licenses'] += $item->quantity;
			$tax['licenses'] += count( $item->featured );
			$shipping = 0;

			if ($item->renew) {
				$has_renewal = true;
			}
		}

		if($has_renewal) {
			if( $promo->validforrenewal) {
				$can_promo = true;
			}
		} else {
			if( $promo->validfornew) {
				$can_promo = true;
			}
		}

		//$tax_handler->getShipping($tax, $items, $configs, $cust_info);

		if ($promo->id > 0) {
			//$promo = "";
			$promoid = $promo->id;
			$promocode = $promo->code;
		} else {
			$promoid = '0';
			$promocode = '';
		}

		$promo_applied = -1; //no need for promo
		$tax['promo'] = 0;
		$tax['promo_order'] = 0;

		if($promo->id > 0 && $can_promo) { //valid promocode was provided
			$tax['promoaftertax'] = $promo->aftertax;
			if($promo->codelimit <= $promo->used && $promo->codelimit > 0 ){

			} 
			else{
				if($promo->aftertax == '0'){//promo discount should be applied before taxation
					$promo_applied = 1; //all discounts are applied
					$restricted_order = (int) $this->havePreviousOrderOfProduct($promo);

					/*echo "<!-- ";
					echo "<p>DEBUG: restricted to specific products = ".count($promo->products)."<br>
							 DEBUG: restricted to previous orders = ".count($promo->orders)." -- $restricted_order<br>
							 DEBUG: products: ";
					print_r($promo->products);
					echo "<br>DEBUG: orders: ";
					print_r($promo->orders);
					echo "<p>total = ".$total;
					echo " -->";*/

					// If there are restrictions must check line per line in the products
					if (count($promo->products) || count($promo->orders))
					{
						$tax['promo_order'] = 0;

						foreach ($items as $item)
						{
							$restricted_product = (int) $this->isRestrictedProduct($item, $promo->products);
							$have_discount = 0;
							$item->discount = 0;

							//echo "<hr><p>DEBUG: is product in the discount selected = ".$restricted_product."<br>
							//	  DEBUG: have valid previous order for required products = ".$restricted_order."<br>";

							if ($restricted_order && count($promo->orders) && $restricted_product && count($promo->products))
							{
								//echo "<!-- DEBUG: case 1<br> -->";
								$have_discount = 1;
							}

							if ($restricted_product && count($promo->products) && count($promo->orders) == 0)
							{
								//echo "<!-- DEBUG: case 2<br> -->";
								$have_discount = 1;
							}

							if ($restricted_order && count($promo->orders) && count($promo->products) == 0)
							{
								//echo "<!-- DEBUG: case 3<br> -->";
								$have_discount = 1;
							}

							if (count($promo->products) == 0 && count($promo->orders) == 0)
							{
								//echo "<!-- DEBUG: case 4<br> -->";
								$have_discount = 1;
							}

							if ($have_discount)
							{
								if ($promo->promotype == '0')
								{
									// Use absolute values
									$total -= $promo->amount;
									$tax['promo'] += $promo->amount;
									$item->discount += $promo->amount;
									$tax['discount_calculated'] = 1;
								}
								else
								{
									// Use percentage
									$total -= ($item->price * $promo->amount) / 100;
									$tax['promo'] += (($item->price * $promo->amount) / 100);
									$item->discount = ($item->price * $promo->amount) / 100;
									$tax['discount_calculated'] = 1;
								}
							}
							/*echo "<!-- ";
							echo "<p>have discount = ".$have_discount;
							echo "<br>promo type = ".$promo->promotype;
							echo "<br>tax[promo] = ".$tax['promo'];
							echo "<br>item discount = ".$item->discount;
							echo "<br>total = ".$total;
							echo " -->";*/
						}
					}
					else
					{
						if($promo->promotype == '0'){//use absolute values
							$total -= $promo->amount;
							$tax['promo'] += $promo->amount;
							$tax['promo_order'] = 1;
						}
						else{ //use percentage
							$tax['promo'] += ($promo->amount * $total) / 100;
							$total -= ($promo->amount * $total) / 100;
							$tax['promo_order'] = 1;
						}
					}

				}
				else{//promo discount should be applied after tax
					$promo_applied = 0; //we should apply promo later
					//nothing to do here - tax is not calculated yet
				}
			}
		}

		//tax calculations
		$tmp_customer = $customer;

		if (is_object($customer) && isset($customer->_customer) && !empty($customer->_customer)) $tmp_customer = $customer->_customer;
		if (is_array($customer)) $tmp_customer = $customer;

		$customer = $tmp_customer;

		//$tax_handler->getTax($tax, $items, $configs, $customer);
		//tax calculations end here
		if(!isset($tax['value'])) $tax['value'] = 0;
		$sum_tax = $total + $tax['value']; //$vat_tax + $state_tax;//total tax
		//now lets apply promo discounts if there are any
		if ( $promo_applied == 0 )
			if ( $promo->promotype == '0' ) {//use absolute values
				$sum_tax -= $promo->amount;
				$tax['promo'] = $promo->amount;
			} else { //use percentage
				$tax['promo'] = $sum_tax * $promo->amount / 100;
				$sum_tax *= 1 - $promo->amount / 100;
			}

		// Fixed PromoCode > Total Price
		if ( $sum_tax < 0 )
			$sum_tax = 0;

		$tax['promo_error'] = (!$user->id && isset($promo->orders) && count($promo->orders) ? JText::_("DIGI_PROMO_LOGIN") : '');
		$tax['total'] = $total;
		$tax['taxed'] = $tax['shipping'] + $sum_tax;
		$tax['discount_calculated'] = (isset($tax['discount_calculated']) ? $tax['discount_calculated'] : 0);
		$tax['shipping'] = DigiComHelper::format_price( $tax['shipping'], $tax['currency'], false, $configs ); //sprintf($price_format, $tax['shipping']);
		$tax['taxed'] = DigiComHelper::format_price( $tax['taxed'], $tax['currency'], false, $configs ); //sprintf($price_format, $tax['taxed']);//." ".$tax['currency'];
		$tax['type'] = JText::_( "DSTAXTYPE" );

		$this->_tax = $tax;
		if(count($items) > 0){
			$items[-1] = "Taxed";
			$items[-2] = $tax;
		}
		return $tax;
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
			$promo = $db->loadObjectList();
			$promo = $promo[0];
			// Get products restrictions
			$sql = "SELECT p.`productid`
					FROM `#__digicom_promocodes_products` AS p
					WHERE p.`promoid`=" . $promo->id;
			$db->setQuery( $sql );
			$promo->products = $db->loadObjectList();
			// Get previous orders restrictions
			$sql = "SELECT o.`productid`
					FROM `#__digicom_promocodes_orders` AS o
					WHERE o.`promoid`=" . $promo->id;
			$db->setQuery( $sql );
			$promo->orders = $db->loadObjectList();
		} else {
			$promo = $this->getTable( "Promo" );
		}
		$promo->error = "";
		if ( $promodata[0] == "promoerror" )
			$promo->error = $promodata[1];
		//code exists and we're about to validate it
		if ( $promo->id > 0 && $checkvalid == 1 ) {
			if ( $uid > 0 ) {
				$sql = "select count(*) from #__digicom_licenses where userid='" . $uid . "' and published='1'";
				$db->setQuery( $sql );
				$licensecount = $db->loadResult();
			} else {
				$licensecount = 0;
			}
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

		/* *****************************
		 *  Plain
		 * *************************** */
		$plans = JRequest::getVar( 'plan_id', array() );

		// Update prosessor
		$processor = JRequest::getVar( 'processor', '' );
		$sql = "update #__digicom_session set processor='" . $processor . "' where sid='" . intval($sid) . "'";

		$db->setQuery( $sql );
		$db->query();

		if ( empty( $this->_items ) ) {
			$this->getCartItems( $customer, $configs );
		}
		$items = $this->_items;

		//lets remove entries from attribute list for items with zero qty
		foreach ( JRequest::getVar( 'quantity', array(), 'post' ) as $key => $value ) {

		}
		foreach ( JRequest::getVar( 'attributes', array(), 'post' ) as $i => $v ) {
			if ( $value == 0 && $key == $i )
				unset( $_POST['attributes'][$i] );
		}

		$new_plan_id = JRequest::getVar( 'plan_id', array() );

		foreach ( $items as $i => $item ) {

			if ($i < 0 ) continue;

			if ( !empty( $new_plan_id ) ) {

				$plan_id = $new_plan_id[$item->cid];

				if ( $plan_id > 0 ) {
					$sql = "update #__digicom_cart set plan_id =".intval($plan_id)." where sid=".intval($sid)." and cid=".intval($item->cid);
					$db->setQuery( $sql );
					$db->query();
					$items[$i]->plan_id = $plan_id;
				}
			}
		}

		$sql = "select c.* from #__digicom_cart c where c.sid='".intval($sid)."'";
		$db->setQuery( $sql );
		$cartitems = $db->loadObjectList();

		//$jAp->enqueueMessage(nl2br($db->getErrorMsg()),'error');
		foreach ( $cartitems as $i => $item ) {
			$sql = "select fieldid, optionid from #__digicom_cartfields where sid='" . intval($sid) . "' and cid='" . $item->cid . "'";
			$db->setQuery( $sql );
			$fields = $db->loadObjectList();
			$cartitems[$i]->fields = $fields;
		}

		for ( $i = 0; $i < (sizeof( $cartitems ) - 1); ++$i ) {
			$item = &$cartitems[$i];

			for ( $i1 = $i; $i1 < sizeof( $cartitems ); ++$i1 ) {
				$differ = 0;
				$item1 = &$cartitems[$i1];
				//lets compare all items in the cart

				if ( $i != $i1 && $item->item_id == $item1->item_id ) { //if items of the same product...
					foreach ( $item->fields as $field ) {
						foreach ( $item1->fields as $field1 ) {
							//...lets compare fields
							if ( $field->fieldid == $field1->fieldid && $field->optionid != $field1->optionid ) {
								$differ = 1;
								break;
							}
						}
						if ( $differ == 1 ) {//fields with different options are found
							//no need for further compare - exit foreach(item->fields) loop
							break;
						}
					}
				} //finish compare of two similar items
				//again if items are of the same product,
				//it is not same item and there are no difference in options for these
				//items - lets merge them
				/*
				  if ($i != $i1 && $item->item_id == $item1->item_id && $differ == 0) {
				  //merging two fields with same option list
				  //delete second of existing entries
				  $sql = "delete from #__digicom_cart where cid='".$item1->cid."' and sid='".$sid."'";
				  $db->setQuery($sql);
				  $db->query();
				  //delete field set for the removed entry
				  $sql = "delete from #__digicom_cartfields where cid='".$item1->cid."' and sid='".$sid."'";
				  $db->setQuery($sql);
				  $db->query();
				  //update first entry
				  $sql = "update #__digicom_cart set quantity=quantity+".$item1->quantity." where cid='".$item->cid."' and sid='".$sid."'";
				  $db->setQuery($sql);
				  $db->query();
				  }
				 */
			}
		} //finish check for similar items and merging
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
					//$jAp->enqueueMessage(nl2br($db->getErrorMsg()),'error');

					$sql = "select fieldid from #__digicom_prodfields where productid='" . $value . "' and publishing='1' ";
					$db->setQuery( $sql );
					$fields = $db->loadObjectList();
					foreach ( $fields as $field ) {
						$sql = "insert into #__digicom_cartfields(cid, sid, productid, fieldid, optionid) values ('" . $incid . "','" . $sid . "','" . $value . "','" . $field->fieldid . "', '-1')";
						$db->setQuery( $sql );
						$db->query(); //and create new corresponding to cid obtained during processing
						//$jAp->enqueueMessage(nl2br($db->getErrorMsg()),'error');
					}
				}
			}

		//get promo code if submitted
		$promo = JRequest::getVar('promocode');

		if ( strlen( $promo ) > 0 ) { //code was submitted
			$sql = "select * from #__digicom_promocodes where code='" . $promo . "' ";
			$db->setQuery( $sql );
			$promo_exists = $db->loadObjectList();

			if ( $uid > 0 ) {
				$sql = "select count(*) from #__digicom_licenses where userid='" . $uid . "' and published='1'";
				$db->setQuery( $sql );
				$licensecount = $db->loadResult();
				//$jAp->enqueueMessage(nl2br($db->getErrorMsg()),'error');
			} else {
				$licensecount = 0;
			}
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

		$shipto = JRequest::getVar( 'shipto', '0', 'post' );
		if ( $shipto != '0' && intval( $shipto ) < 3 && intval( $shipto ) > 0 ) {
			$sql = "update #__digicom_session set shipping_details='" . $shipto . "' where sid='" . intval($sid) . "'";
			$db->setQuery( $sql );
			$db->query();
			//$jAp->enqueueMessage(nl2br($db->getErrorMsg()),'error');
		} else {
			$sql = "update #__digicom_session set shipping_details='0' where sid='" . intval($sid) . "'";
			$db->setQuery( $sql );
			$db->query();
			//$jAp->enqueueMessage(nl2br($db->getErrorMsg()),'error');
		}

		//mosRedirect("index.php?option=com_digicom&task=checkout&Itemid=".$configs['itid'] );

	}

	function deleteFromCart( $customer, $configs )
	{

		$db = JFactory::getDBO();

		$cid = JRequest::getInt( 'cartid', -1 );
		$qty = JRequest::getInt( 'qty', 0 );

		if ( (JRequest::getVar( 'discount', 0 ) == 0) && ($cid != -1) ) {

			$sql = "select item_id, sid from #__digicom_cart where cid='" . intval($cid) . "'";

			$db->setQuery( $sql );
			$d = $db->loadObject();

			if ( isset( $d->sid ) && isset( $d->item_id ) ) {
				$sql = "delete from #__digicom_cartfields where sid='" . $d->sid . "' and productid='" . $d->item_id . "' and cid='" . intval($cid) . "'";
				$db->setQuery( $sql );
				$db->query();
			}

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

		$customer = new DigiComSessionHelper();

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
		$reg = JSession::getInstance("none", array());
		//$sid = $reg->set("digisid", 0);

		if(!$sid){
			return;
		}
		$db = JFactory::getDBO();
		$sql = "delete from #__digicom_cartfields where sid='" . intval($sid) . "'";
		$db->setQuery( $sql );
		$db->query();

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
		$licenses = $tax['licenses'];
		$taxa = $tax['value'];
		$shipping = $tax['shipping'];
		$orderid = $this->addOrder($items, $customer, $now, 'free');
		$this->addLicenses($items, $orderid, $now, $customer);
		$this->goToSuccessURL($customer->_sid, '', $orderid);
		return true;
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
		$mosConfig_live_site = DigiComHelper::getLiveSite();
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

	function proccessWait( $controller, $result )
	{

		if ( $this->checkSuccess( $result['sid'] ) ) {

			$return_url = $this->getReturnUrl( true );

		} else {

			$count_redirect = JRequest::getInt( 'count_redirect', 0 );
			$sid = $result['sid'];
			$processor = $result['processor'];
			$return_url = "index.php?option=com_digicom&controller=cart&task=wait&processor={$processor}&pay=wait&sid={$sid}&count_redirect={$count_redirect}";
		}

		$controller->setRedirect( $return_url, $msg );
	}

	function proccessIPN($controller, $result){
		$this->proccessSuccess($controller, $result, true);
	}

	function storelog($name,$data)
	{
		return;
		$data1=array();
		$data1['raw_data']=isset($data['raw_data'])?$data['raw_data']:array();
		$data1['JT_CLIENT']="com_digicom";
		$dispatcher=JDispatcher::getInstance();
		JPluginHelper::importPlugin('payment',$name);
		$data=$dispatcher->trigger('onTP_Storelog',array($data1));
	}

	function proccessSuccess($result, $pg_plugin, $order_id, $sid)
	{
		unset($_SESSION["creditCardNumber"]);
		unset($_SESSION["expDateMonth"]);
		unset($_SESSION["expDateYear"]);
		unset($_SESSION["cvv2Number"]);

		$dispatcher = JDispatcher::getInstance();

		JPluginHelper::importPlugin('payment', $pg_plugin);
		$data = $dispatcher->trigger('onTP_Processpayment', array($result));

		$this->storelog($pg_plugin, $result);
		$this->storelog($pg_plugin, $data);
		$data=$data[0];
		$this->storelog($pg_plugin, $data);
		if($data['status']=='C' || $data['status']=='P')//if payment status is confirmed
		{
			$db = JFactory::getDBO();
			$sql = "update #__digicom_settings
					set `in_trans`=1";
			$db->setQuery($sql);
			$db->query();
			$_SESSION['in_trans'] = 1;

			$msg = JText::_("DIGI_THANK_YOU_FOR_PAYMENT");
			$customer = $this->loadCustomer($sid);

			$conf = $this->getInstance( "config", "digicomModel" );
			$configs = $conf->getConfigs();
			
			$status = "";
			if($data['status']=='C'){
				$status = "Active";
			} elseif($data['status']=='P') {
				$status = "Pending";
			}
			
			$items = $this->getCartItems( $customer, $configs );

			$config = JFactory::getConfig();
			$tzoffset = $config->get('offset');
			$now = date('Y-m-d H:i:s', time() + $tzoffset);
			$now = strtotime($now);
			$order_id = $this->addOrder($items, $customer, $now, $pg_plugin, $status);

			if(intval($order_id) != "0")
			{
				$sql = "update #__digicom_settings
						set `in_trans`=".intval($order_id);
				$db->setQuery($sql);
				$db->query();
				$_SESSION['in_trans'] = 1;
			}

			if($order_id == 0)
			{
				return false;
			}
			$this->addLicenses($items, $order_id, $now, $customer, $status);

			$tax = $this->calc_price( $items, $customer, $configs );
			$total = $tax['taxed'];
			$licenses = $tax['licenses'];
			$this->dispatchMail( $order_id, $total, $licenses, $now, $items, $customer );
			$this->emptyCart($sid);
		}

		if ($pg_plugin == 'authorizenet')
		{
			// downloads page
			if ($configs->get('afterpurchase',1) == 0)
			{
				$return = JRoute::_(JURI::root()."index.php?option=com_digicom&view=licenses");
			}
			// orders page
			else
			{
				$return = JRoute::_(JURI::root()."index.php?option=com_digicom&view=orders");
			}
			header("Location: " . $return);
		}
	}

	function proccessFail($controller, $result)
	{
		$msg = JText::_("DIGI_PAYMENT_FAIL");
		$customer = $this->loadCustomer( $result['sid'] );
		$conf = $this->getInstance( "config", "digicomModel" );
		$configs = $conf->getConfigs();
		$items = $this->getCartItems( $customer, $configs );
		$tax = $this->calc_price( $items, $customer, $configs );
		$this->storeTransactionData( $items, -1, $tax, $result['sid'] );
		$return_url = $this->getReturnUrl(false);

		if(isset($result['msg']) && !empty($result['msg'])){
			$msg .= " :" . $result['msg'];
		}

		if($result["processor"] == "payauthorize"){
			$return_url = "index.php?option=com_digicom&controller=cart&task=checkout";
			$msg = JText::_("DIGI_ERROR_AUTHORIZE");
		}

		$controller->setRedirect($return_url, $msg);
	}

	function goToSuccessURL( $sid, $msg = '', $orderid = 0 )
	{

		global $Itemid;

		$mosConfig_live_site = DigiComHelper::getLiveSite(); //$jconf->live_site;

		$conf = $this->getInstance( "config", "digicomModel" );
		$configs = $conf->getConfigs();

		$cust_info = $this->loadCustomer( $sid );
		//Log::write("cust_info===");
		//Log::write(print_r($cust_info,1));
		//if ( isset($cust_info['cart']) ) {
		if ( isset( $cust_info ) && is_array( $cust_info ) && isset( $cust_info['cart'] ) ) {
			if ( isset( $cust_info['cart']['total'] ) )
				$cart_total = $cust_info['cart']['total'];
			if ( isset( $cust_info['cart']['items'] ) )
				$cart_items = unserialize( $cust_info['cart']['items'] );
			//debug($cart_items);
		}

		$customer = new DigiComSessionHelper();
		//Log::write("customer===");
		//Log::write(print_r($customer,1));

		$_Itemid = $Itemid;
		if ( isset( $customer->_Itemid ) && ($customer->_Itemid > 0) )
			$_Itemid = $customer->_Itemid;

		$cart = $this->getInstance( "cart", "digicomModel" );

		if ( isset( $cart_items ) ) {
			$items = $cart_items;
		} else {
			$items = $cart->getCartItems( $customer, $configs );
		}

		//Log::write("items calculation===");
		//Log::write(print_r($items,1));

		$tax = $cart->calc_price( $items, $customer, $configs );
		//Log::write("tax===");
		//Log::write(print_r($tax,1));

		if ( $orderid == 0 && is_array( $cust_info ) && isset( $cust_info['cart'] ) && isset( $cust_info['cart']['orderid'] ) )
			$orderid = $cust_info['cart']['orderid'];
		if ( $orderid == 0 && is_object( $cust_info ) && isset( $cust_info->cart['orderid'] ) )
			$orderid = $cust_info->cart['orderid']; // перестраховка если cart это об'ект

			$now = time();
		$total = $tax['taxed'];
		$licenses = $tax['licenses'];

		if ( $configs->get('afterpurchase',1) == 0 ) {
			$controller = "Licenses";
			$task = "show";
		} else {
			$controller = "Orders";
			$task = "list";
		}

		/* fixed return after payment, before paypal IPN */
		$plugin = JRequest::getVar( 'plugin', '' );
		if ( $plugin != 'paypal' ) {
			/*
			  Log::debug("Order ID from store session: ".$cust_info['cart']['orderid']);
			  Log::debug("Order ID: ".$orderid);
			  Log::debug($cust_info);die;
			 */
			$this->dispatchMail( $orderid, $total, $licenses, $now, $items, $customer );
			$cart->emptyCart( $sid );
		}

		// Get return urls
		$success_url = /* $mosConfig_live_site. */JRoute::_( "index.php?option=com_digicom&controller=" . $controller . "&task=" . $task . "&success=1&sid=" . intval($sid) . '&Itemid=' . $_Itemid, false, false );
		$failed_url = /* $mosConfig_live_site. */JRoute::_( "index.php?option=com_digicom&controller=" . $controller . "&task=" . $task . "&success=0&sid=" . intval($sid) . '&Itemid=' . $_Itemid, false, false );

		$uri = JURI::getInstance();
		$prefix = $uri->toString( array('host', 'port') );

		// Get Full url with host and port
		$success_url = 'http://' . $prefix . $success_url;
		$failed_url = 'http://' . $prefix . $failed_url;

		if ( empty( $msg ) && $orderid > 0 )
			$msg = JText::_( "DSREFERENCEOID" ) . " " . $orderid;

		// Encode return urls
		$success_url = base64_encode( $success_url );
		$failed_url = base64_encode( $failed_url );

		$content = '
			<form name="dsform" method="post" action="' . JRoute::_("index.php?option=com_digicom&controller=cart&task=showCart", false, false ) . '">
				<input type="hidden" name="success_url" value="' . $success_url . '" />
				<input type="hidden" name="failed_url" value="' . $failed_url . '" />
				<input type="hidden" name="msg" value="' . $msg . '" />
				<input type="hidden" name="sid" value="' . $sid . '" />
				<input type="hidden" name="orderid" value="' . $orderid . '" />
				<input type="hidden" name="plugin" value="' . $plugin . '" />
				<input type="hidden" name="option" value="com_digicom" />
				<input type="hidden" name="controller" value="Cart" />
				<input type="hidden" name="task" value="landingSuccessPage" />
				<input type="hidden" name="Itemid" value="' . $_Itemid . '" />
			</form>
			<script>document.dsform.submit();</script>
		';

		echo $content;

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
	function dispatchMail( $orderid, $amount, $licenses, $timestamp, $items, $customer )
	{
		$app 	= JFactory::getApplication();
		$db 	= JFactory::getDbo();
		$site_config = JFactory::getConfig();
		// get sid & uid
		if (is_object($customer)) $sid = $customer->_sid;
		if (is_array($customer)) $sid = $customer['sid'];

		if (is_object($customer) && isset($customer->_user->id))  $uid = $customer->_user->id;
		if (is_array($customer)) $uid = $customer['userid'];

//		$cust_info = $customer;

		if ( !$sid ) return;

		$my = JFactory::getUser($uid);

		$database = JFactory::getDBO();
		$cart = $this->getInstance( "cart", "digicomModel" );
		$configs = $this->getInstance( "Config", "digicomModel" );
		$configs = $configs->getConfigs();
		$mes = new stdClass();

		$mes->body = "Template is empty";
		$sql = "SELECT * FROM #__digicom_mailtemplates where `type`='order'";
		$db->setQuery( $sql );
		$db = JFactory::getDBO();
		$db->setQuery( $sql );
		$mes = $db->loadObjectList();
		$mes = $mes[0];
		$message = $mes->body;

		JTable::addIncludePath(JPATH_COMPONENT.DS.'tables');
		$email = $this->getTable("Mail");
		$email->date = $timestamp;
		$email->flag = "order";
		$email->email = trim( $my->email );


		$subject = $mes->subject;
		// Replace all variables in template
		$flag = "order";
		$promo = $cart->get_promo( $customer );
		if ( $promo->id > 0 ) {
			$promoid = $promo->id;
			$promocode = $promo->code;
		} else {
			$promoid = '0';
			$promocode = '0';
		}

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

		$ship_add = DigiComHelper::get_customer_shipping_add($my->id);
		$message = str_replace( "[SHIPPING_ADDRESS]", $ship_add, $message );
		$message = str_replace("[CUSTOMER_COMPANY_NAME]", $copany, $message);
		$message = str_replace( "[CUSTOMER_USER_NAME]", $my->username, $message );
		$message = str_replace( "[CUSTOMER_FIRST_NAME]", $my->name, $message );
		$message = str_replace( "[CUSTOMER_LAST_NAME]", $lastname, $message );
		$message = str_replace( "[CUSTOMER_EMAIL]", $my->email, $message );

		$message = str_replace( "[TODAY_DATE]", date( $configs->get('time_format','d-m-Y'), $timestamp), $message );
		$message = str_replace( "[ORDER_ID]", $orderid, $message );
		$message = str_replace( "[ORDER_AMOUNT]", $amount, $message );
		$message = str_replace( "[NUMBER_OF_LICENSES]", $licenses, $message );
		$message = str_replace( "[PROMO]", $promo->code, $message );
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
			if ( !empty( $item->productfields ) )
				foreach ( $item->productfields as $i => $v ) {
					$options = explode( "\n", $v->options );
					if ( $v->optionid >= 0 ) {
						$optionname = $options[$v->optionid];
					} else {
						$optionname = "Nothing Selected";
					}
					$optionlist .= $v->name . ": " . $optionname . "<br />";
				}
			//Log::debug($item);
			if ( !in_array( $item->name, $displayed ) ) {
				//$product_list .= $counter[$item->id]." - ".$item->name.'<br />';
				$product_list .= $item->quantity . " - " . $item->name . '<br />';
				$product_list .= $optionlist . '<br />';
			} else if ( count( $item->productfields > 0 ) ) {
				//echo $optionlist;
				$product_list .= $item->quantity . " - " . $item->name . '<br />';
				$product_list .= $optionlist . '<br />';
			}
			$displayed[] = $item->name;
		}
		//Log::debug($product_list);
		//die;
		$message = str_replace( "[PRODUCTS]", $product_list, $message );
		$email->body = $message;

		//subject
		$subject = str_replace( "[SHIPPING_ADDRESS]", $ship_add, $subject );
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
		$subject = str_replace( "[NUMBER_OF_LICENSES]", $licenses, $subject );
		$subject = str_replace( "[PROMO]", $promo->code, $subject );
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

		$subject = html_entity_decode( $subject, ENT_QUOTES );

		$message = html_entity_decode( $message, ENT_QUOTES );

		$app = JFactory::getApplication('site');

		$mosConfig_mailfrom = $app->getCfg("mailfrom");
		$mosConfig_fromname = $app->getCfg("fromname");

		if ( $configs->get('usestoremail',1) == '1' && strlen( trim( $configs->get('store_name','DigiCom Store') ) ) > 0 && strlen( trim( $configs->get('store_email','') ) ) > 0 ) {
			$adminName2 = $configs->get('store_name','DigiCom Store');
			$adminEmail2 = $configs->get('store_email','');
		} else if ( $mosConfig_mailfrom != "" && $mosConfig_fromname != "" ) {
			$adminName2 = $mosConfig_fromname;
			$adminEmail2 = $mosConfig_mailfrom;
		} else {

			$query = "SELECT name, email"
			. "\n FROM #__users"
			. "\n WHERE LOWER( usertype ) = 'superadministrator'"
			. "\n OR LOWER( usertype ) = 'super administrator'"
			;
			$db->setQuery( $query );
			$rows = $db->loadObjectList();
			$row2 = $rows[0];
			$adminName2 = $row2->name;
			$adminEmail2 = $row2->email;
		}


		$mailSender = JFactory::getMailer();
		$mailSender->IsHTML( true );
		$mailSender->addRecipient( $my->email );
		$mailSender->setSender( array($adminEmail2, $adminName2) );
		$mailSender->setSubject( $subject );
		$mailSender->setBody( $message );
//		Log::write( $message );
		if ( !$mailSender->Send() ) {
//			<Your error code management>
		}

		if ( $configs->get('sendmailtoadmin',1) != 0 ) {
			$mailSender = JFactory::getMailer();
			$mailSender->IsHTML( true );
			$mailSender->addRecipient( $adminEmail2 );
			$mailSender->setSender( array($adminEmail2, $adminName2) );
			$mailSender->setSubject( $subject );
			$mailSender->setBody( $message );
//			Log::write( $message );
			if ( !$mailSender->Send() ) {
//					<Your error code management>
			}
		}

		$sent = array();

		//send per product emails
		foreach ( $items as $i => $item ) {
			if ( $i < 0 )
				continue;
			if ( !in_array( $item->name, $sent ) && $item->sendmail == '1' && !empty($item->productemailsubject) && !empty($item->productemail) ) {
				$subject = $item->productemailsubject;
				$subject = str_replace( "[SHIPPING_ADDRESS]", $ship_add, $subject );
				$subject = str_replace( "[SITENAME]", $sitename, $subject );
				$subject = str_replace("[CUSTOMER_COMPANY_NAME]", $my->copany, $subject);
				$subject = str_replace( "../%5BSITEURL%5D", $siteurl, $subject );
				$subject = str_replace( "%5BSITEURL%5D", $siteurl, $subject );
				$subject = str_replace( "[SITEURL]", $siteurl, $subject );


				$subject = str_replace( "[CUSTOMER_USER_NAME]", $my->username, $subject );
				$subject = str_replace( "[CUSTOMER_FIRST_NAME]", $my->name, $subject );
				$subject = str_replace( "[CUSTOMER_LAST_NAME]", $lastname, $subject );
				$subject = str_replace( "[CUSTOMER_EMAIL]", $my->email, $subject );

				$subject = str_replace( "[TODAY_DATE]", date( $configs->get('time_format','d-m-Y'), $timestamp ), $subject );

				$message = $item->productemail;
				$message = str_replace( "[SITENAME]", $sitename, $message );

				$message = str_replace( "../%5BSITEURL%5D", $siteurl, $message );
				$message = str_replace( "%5BSITEURL%5D", $siteurl, $message );
				$message = str_replace( "[SITEURL]", $siteurl, $message );

				$query = "select lastname from #__digicom_customers where id=" . $my->id;
				$db->setQuery( $query );
				$lastname = $db->loadResult();

				$message = str_replace( "[CUSTOMER_USER_NAME]", $my->username, $message );
				$message = str_replace( "[CUSTOMER_FIRST_NAME]", $my->name, $message );
				$message = str_replace( "[CUSTOMER_LAST_NAME]", $lastname, $message );
				$message = str_replace( "[CUSTOMER_EMAIL]", $my->email, $message );

				$message = str_replace( "[TODAY_DATE]", date( $configs->get('time_format','d-m-Y'), $timestamp ), $message );

				$optionlist = '';
				if ( !empty( $item->productfields ) )
					foreach ( $item->productfields as $i => $v ) {

						$options = explode( "\n", $v->options );
						if ( $v->optionid >= 0 ) {
							$optionname = $options[$v->optionid];
						} else {
							$optionname = "Nothing Selected";
						}
						$optionlist .= $v->name . ": " . $optionname . "<br />";
					}

				$message = str_replace( "[ATTRIBUTES]", $optionlist, $message );
				$message = str_replace( "[PRODUCT_NAME]", $item->name, $message );

				$subject = str_replace( "[ATTRIBUTES]", $optionlist, $subject );
				$subject = str_replace( "[PRODUCT_NAME]", $item->name, $subject );
				$mailSender = JFactory::getMailer();
				$mailSender->IsHTML( true );
				$mailSender->addRecipient( $my->email );
				$mailSender->setSender( array($adminEmail2, $adminName2) );
				$mailSender->setSubject( $subject );
				$mailSender->setBody( $message );
//				Log::write( $message );
				if ( !$mailSender->Send() ) {
//						<Your error code management>
				}

				$site_config = JFactory::getConfig();
				$tzoffset = $site_config->get('offset');
				$today = date('Y-m-d H:i:s', time() + $tzoffset);

				$sql = "insert into #__digicom_logs(`userid`, `productid`, `emailname`, `to`, `subject`, `body`, `send_date`) values (".$my->id.", ".$item->id.", 'Product Email', '".$my->email."', '".addslashes(trim($subject))."', '".addslashes($message)."', '".$today."')";
				$db->setQuery($sql);
				$db->query();
				$sent[] = $item->name;
			}
		}

	}

	//integrate with idev_affiliate
	function affiliate( $total, $orderid, $configs )
	{

		$mosConfig_live_site = DigiComHelper::getLiveSite();

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

	function addOrder($items, $cust_info, $now, $paymethod, $status = "Active")
	{
		$cart = $this;
		$conf = $this->getInstance( "config", "digicomModel" );
		$configs = $conf->getConfigs();
		$db = JFactory::getDBO();
		$tax = $cart->calc_price( $items, $cust_info, $configs );

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
		$taxa = $tax['value'];
		$shipping = $tax['shipping'];
		$currency = $tax['currency'];
		$licenses = $tax['licenses'];

		$promo = $cart->get_promo( $cust_info );
		if($promo->id > 0){
			$promoid = $promo->id;
			$promocode = $promo->code;
		}
		else{
			$promoid = '0';
			$promocode = '0';
		}
		$sql = "select shipping_details from #__digicom_session where sid='" . $sid . "'";
		$db->setQuery( $sql );
		$shipto = $db->loadResult();
		$sql = '';
		if ( $shipto != '0' ) {
			if ( $shipto == '2' ) {
				$sql = "select shipaddress as a, shipzipcode as z, shipcity as c, shipstate as s, shipcountry as r from #__digicom_customers where id='" . $cust_info->_user->id . "'";
			} else if ( $shipto == '1' ) {
				$sql = "select address as a, zipcode as z, city as c, state as s, country as r from #__digicom_customers where id='" . $cust_info->_user->id . "'";
			} else {
				$sql = '';
			}
		}
		if ( strlen( $sql ) > 0 ) {
			$db->setQuery( $sql );
			$d = $db->loadObjectList();
			$d = $d[0];
			$shipaddress = $d->r
			. "\n" . $d->s
			. "\n" . $d->c
			. "\n" . $d->a
			. "\n" . $d->z;
		} else {
			$shipaddress = '';
		}

		$sql = "insert into #__digicom_orders ( userid, order_date, amount, amount_paid, currency, processor, number_of_licenses, status, tax, shipping, promocodeid, promocode, promocodediscount, shipto, fullshipto, published ) "
		. " values ('{$uid}','".$now."','$total', '-1', '" . $currency . "','" . $paymethod . "','$licenses', '" . $status . "', '$taxa','$shipping', '" . $promoid . "', '" . $promocode . "', '" . $tax['promo'] . "', '" . $shipto . "', '" . $shipaddress . "', '1') ";

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

	function addLicenses($items, $orderid, $now, $customer, $status = "Active")
	{
		$license = array();
		if($status != "Pending")
			$published = 1;
		else
			$published = 0;

		$database = JFactory::getDBO();
		$license_index = 0;
		$jconfig = JFactory::getConfig();

		$sql = "select id from #__digicom_plans where duration_count=-1";
		$database->setQuery($sql);
		$database->query();
		$unlimited_plan_id = $database->loadResult();
		$user_id = isset($customer->_user->id) ? $customer->_user->id : $customer["userid"];

		if($user_id == 0){
			return false;
		}
// start foreach
		foreach($items as $key=>$item)
		{
			if($key >= 0)
			{
				if(count($item->featured) <= 0)
				{
					for($i = 0; $i < $item->quantity; $i++)
					{
						$price = (isset($item->discounted_price) && ($item->discounted_price > 0)) ? $item->discounted_price : $item->price;
						$plan_id = $item->plan_id;
						$lic_date = JFactory::getDate();
						//$lic_date->setOffset($jconfig->get('offset'));
						$license_date = $lic_date->toSql();
						//$license_date = date("Y-m-d H:i:s", $now);

						if($item->renew == "1")
						{// for renew
							$sql = "select id
									from #__digicom_plans
									where duration_count <> -1
									  and duration_type=0";
							$database->setQuery($sql);
							$database->query();
							$download_plans_ids = $database->loadAssocList("id");
							$download_plans_ids = array_keys($download_plans_ids);

							$sql = "select *
									from #__digicom_licenses
									where id=".intval($item->renewlicid);
							$database->setQuery($sql);
							$database->query();
							$license_details = $database->loadAssocList();

							if(in_array($item->plan_id, $download_plans_ids)){
								$license_details["0"]["expires"] = "0000-00-00 00:00:00";
							}
							elseif($license_details["0"]["plan_id"] != $unlimited_plan_id){//not unlimited
								$license_expire_date_int = strtotime($license_details["0"]["expires"]);
								$today_int = strtotime($license_date);
								if($today_int > $license_expire_date_int){ // if expired from some time
									$sql = "select `duration_count`, `duration_type`
											from #__digicom_plans
											where id=".intval($plan_id);
									$database->setQuery($sql);
									$database->query();
									$plan_values = $database->loadAssocList();

									$time = "";
									switch($plan_values["0"]["duration_type"]){
										case "1" :
												$time = "hour";
												break;
										case "2" :
												$time = "day";
												break;
										case "3" :
												$time = "month";
												break;
										case "4" :
												$time = "year";
												break;
									}
									if($plan_values["0"]["duration_type"] != "0"){//dowloads
										$expire_int = strtotime($license_date);
										$expire_int = strtotime("+".$plan_values["0"]["duration_count"]." ".$time, $expire_int);
										$expire_string = date("Y-m-d H:i:s", $expire_int);
										$license_details["0"]["expires"] = $expire_string;
									}
								}
								else{
									$sql = "select `duration_count`, `duration_type`
											from #__digicom_plans
											where id=".intval($plan_id);
									$database->setQuery($sql);
									$database->query();
									$plan_values = $database->loadAssocList();

									$time = "";
									switch($plan_values["0"]["duration_type"])
									{
										case "1" :
												$time = "hour";
												break;
										case "2" :
												$time = "day";
												break;
										case "3" :
												$time = "month";
												break;
										case "4" :
												$time = "year";
												break;
									}
									if($plan_values["0"]["duration_type"] != "0")
									{//dowloads
										$expire_int = strtotime($license_details["0"]["expires"]);
										$expire_int = strtotime("+".$plan_values["0"]["duration_count"]." ".$time, $expire_int);
										$expire_string = date("Y-m-d H:i:s", $expire_int);
										$license_details["0"]["expires"] = $expire_string;
									}
								}
							}
							else
							{//unlimited
								$license_details["0"]["expires"] = "0000-00-00 00:00:00";
							}
							$license_details["0"]["amount_paid"] = $item->price;
							$license_details["0"]["orderid"] = $orderid;
							$license_details["0"]["plan_id"] = $item->plan_id;

							//start save old orderid, to update new orderid
							$sql = "select `orderid`, `old_orders`
									from #__digicom_licenses
									where id=".$license_details["0"]["id"];
							$database->setQuery($sql);
							$database->query();
							$old_result = $database->loadAssocList();
							$old_orders = $old_result["0"]["old_orders"];
							$old_orders .= $old_result["0"]["orderid"]."|";
							$sql = "update #__digicom_licenses
									set `old_orders`='".trim($old_orders)."'
									where id=".$license_details["0"]["id"];
							$database->setQuery($sql);
							$database->query();
							//stop save old orderid, to update new orderid

							$sql = "update #__digicom_licenses
									set `amount_paid` = ".$license_details["0"]["amount_paid"].",
										`orderid` = ".$license_details["0"]["orderid"].",
										`purchase_date`='".$license_details["0"]["purchase_date"]."',
										`expires`='".$license_details["0"]["expires"]."',
										`plan_id` = ".$license_details["0"]["plan_id"]."
									where id=".$license_details["0"]["id"];
							$database->setQuery($sql);
							$database->query();

							$site_config = JFactory::getConfig();
							$tzoffset = $site_config->get('offset');
							$buy_date = date('Y-m-d H:i:s', time() + $tzoffset);
							$sql = "insert into #__digicom_logs (`userid`, `productid`, `buy_date`, `buy_type`)
									values (".$user_id.", ".$item->item_id.", '".$buy_date."', 'renewal')";
							$database->setQuery($sql);
							$database->query();
						}
						else
						{	
			//for a new license
							$sql = "select `duration_count`, `duration_type`
									from #__digicom_plans
									where id=".intval($plan_id);
							$database->setQuery($sql);
							$database->query();
							$plan_values = $database->loadAssocList();

							$time = "";
							switch($plan_values["0"]["duration_type"])
							{
								case "1" :
										$time = "hour";
										break;
								case "2" :
										$time = "day";
										break;
								case "3" :
										$time = "month";
										break;
								case "4" :
										$time = "year";
										break;
							}
							$expire_string = "0000-00-00 00:00:00";
							if($plan_values["0"]["duration_type"] != "0")
							{//dowloads
								$expire_int		= strtotime($license_date);
								$expire_int		= strtotime("+".$plan_values["0"]["duration_count"]." ".$time, $expire_int);
								$expire_string	= date("Y-m-d H:i:s", $expire_int);
							}

							$sql = "insert into #__digicom_licenses(userid, productid, orderid, amount_paid, published, purchase_date, expires, plan_id, download_count, renew, renewlicid) "
										. "values ('{$user_id}', '{$item->item_id}', '".$orderid."', '{$price}', ".$published.", '".$license_date."', '".$expire_string."', ".$plan_id.", 0, '{$item->renew}', '{$item->renewlicid}')";
							$database->setQuery($sql);
							$database->query();

							$licid = $database->insertid();
							$licenseid = $licid + 100000000;

							$sql = "update #__digicom_licenses
									set licenseid='".$licenseid."'
									where id = '" . $licid . "'";
							$database->setQuery($sql);
							$database->query();

							$site_config = JFactory::getConfig();
							$tzoffset = $site_config->get('offset');
							$buy_date = date('Y-m-d H:i:s', time() + $tzoffset);
							$sql = "insert into #__digicom_logs (`userid`, `productid`, `buy_date`, `buy_type`)
									values (".$user_id.", ".$item->item_id.", '".$buy_date."', 'new')";
							$database->setQuery($sql);
							$database->query();
						}

						$sql = "update #__digicom_products_emailreminders
								set `send`=0
								where product_id=".intval($item->item_id);
						$database->setQuery($sql);
						$database->query();

						$license_index++;
						$license[$license_index] = new stdClass;
						if(isset($item) && isset($item->productfields) && !empty($item->productfields)){
							$license[$license_index]->productfields = $item->productfields;
						}
						$license[$license_index]->licenseid = $licenseid;

						if($item->usestock == '1'){
							$sql = "update #__digicom_products
									set used=used+1
									where id = '" . $item->item_id . "'";
							$database->setQuery( $sql );
							$database->query();
						}

						if($item->domainrequired == '3'){//if item is package - we have to update its type
							$sql = "update #__digicom_licenses
									set ltype='package'
									where id='" . $license[$license_index]->id . "'";
							$database->setQuery($sql);
							$database->query();
						}
						$sql = '';
					}
				}// if now featured
				else
				{//package
					$site_config = JFactory::getConfig();
					$tzoffset = $site_config->get('offset');
					$buy_date = date('Y-m-d H:i:s', time() + $tzoffset);
					$sql = "insert into #__digicom_logs (`userid`, `productid`, `buy_date`, `buy_type`)
							values (".$user_id.", ".$item->item_id.", '".$buy_date."', 'new')";
					$database->setQuery($sql);
					$database->query();
				}
				$this->addUserToList($user_id, $item->item_id);

				for($i = 0; $i < count($item->featured); $i++)
				{
					for($k = 0; $k < $item->quantity; ++$k)
					{//inserting as many licenses as there are packages in the order
						$lic_date = JFactory::getDate($now);
// 						$lic_date->setOffset($jconfig->get('offset'));
						//$license_date = $lic_date->toSql();
						$license_date = date("Y-m-d H:i:s", $now);

						$sql = "";
						if($item->renew == "0"){//new
							$sql = "select `price`
									from #__digicom_products_plans
									where `product_id`=".intval($item->featured[$i]->id)." and `default`=1";
						}
						else{//renew
							$sql = "select `price`
									from #__digicom_products_renewals
									where `product_id`=".intval($item->featured[$i]->id)." and `default`=1";
						}
						$database->setQuery($sql);
						$database->query();
						$amount_paid = $database->loadResult();
						if(!isset($amount_paid)){// renew but not plan for renew
							$sql = "select `price` from #__digicom_products_plans where `product_id`=".intval($item->featured[$i]->id)." and `default`=1";
							$database->setQuery($sql);
							$database->query();
							$amount_paid = $database->loadResult();
						}

						$expire_date = "0000-00-00 00:00:00";
						if($item->featured[$i]->planid != $unlimited_plan_id){ // not unlimited
							$sql = "select * from #__digicom_plans where id=".intval($item->featured[$i]->planid);
							$database->setQuery($sql);
							$database->query();
							$plan_values = $database->loadAssocList();

							if($plan_values["0"]["duration_type"] != "0"){//dowloads
								$time = "";
								switch($plan_values["0"]["duration_type"]){
									case "1" :
											$time = "hour";
											break;
									case "2" :
											$time = "day";
											break;
									case "3" :
											$time = "month";
											break;
									case "4" :
											$time = "year";
											break;
								}
								$expire_int = strtotime($license_date);
								$expire_int = strtotime("+".$plan_values["0"]["duration_count"]." ".$time, $expire_int);
								$expire_date = date("Y-m-d H:i:s", $expire_int);
							}
						}

						$sql = "insert into #__digicom_licenses (`userid`, `productid`, `orderid`, `amount_paid`, `published`, `ltype`, `package_id`, `purchase_date`, `expires`, `plan_id`, `download_count`, `renew`, `renewlicid`) "
						. "values ('".$user_id."','".$item->featured[$i]->id."', '".$orderid."', '".$amount_paid."', ".$published.", 'package_item', ".$item->item_id.", '".$license_date."', '".$expire_date."', '".$item->featured[$i]->planid."', 0, '".$item->renew."', '".$item->renewlicid."')";
						$database->setQuery($sql);
						$database->query();

						$licid = $database->insertid();
						$licenseid = $licid + 100000000;

						$sql = "update #__digicom_licenses
								set licenseid='".$licenseid."'
								where id = '" . $licid . "'";
						$database->setQuery( $sql );
						$database->query();

						$sql = "update #__digicom_products
								set used=used+1
								where id = '" . $item->featured[$i]->id . "' and usestock='1'";
						$database->setQuery( $sql );
						$database->query();
						
						$this->addLicensePackage( $item->featured[$i]->id, $orderid, $user_id, $published );
					}
					$this->addUserToList($user_id, $item->featured[$i]->id);
				}
			}
		}
// end foreach
		//now we have to fill fields dependencies for each license
		//we no longer need field and option ids - they were
		//required to prevent user from entering something
		//inappropriate in front-end
		//admin should be able to handle license fields himself
		//Log::debug($license);
		foreach($license as $i => $v){
			if(!empty($v->productfields)){
				foreach($v->productfields as $i1 => $v1){
					$options = explode("\n", $v1->options);
					if($v1->optionid >= 0){
						$optionname = $database->getEscaped( trim( $options[$v1->optionid] ) );
					}
					else{
						$optionname = "Nothing Selected";
					}
					$sql = "insert into #__digicom_licensefields(licenseid, fieldname, optioname)
							values ('" . $v->licenseid . "', '" . $database->getEscaped( $v1->name ) . "', '" . $optionname . "');";
					$database->setQuery( $sql );
					$database->query();
					$sql = "";
				}
			}
		}
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
