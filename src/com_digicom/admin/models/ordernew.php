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
use Joomla\String\String;

/**
 * Ordernew model.
 *
 * @since  1.0.0
 */
class DigiComModelOrderNew extends JModelAdmin
{
	/**
	 * The type alias for this content type.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public $typeAlias = 'com_digicom.order';

	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $text_prefix = 'COM_DIGICOM_ORDER';

	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $type    The table name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable  A JTable object
	 *
	 * @since   1.0.0
	 */
	public function getTable($type = 'Order', $prefix = 'Table', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Abstract method for getting the form from the model.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 *
	 * @since   1.0.0
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_digicom.order', 'order', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  array  The default data is an empty array.
	 *
	 * @since   1.0.0
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_digicom.edit.order.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		$this->preprocessData('com_digicom.order', $data);

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed  Object on success, false on failure.
	 *
	 * @since   1.0.0
	 */
	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);
		$item->products = array();
		return $item;
	}

	/**
	 * Method to validate the form data.
	 *
	 * @param   JForm   $form   The form to validate against.
	 * @param   array   $data   The data to validate.
	 * @param   string  $group  The name of the field group to validate.
	 *
	 * @return  mixed  Array of filtered data if valid, false otherwise.
	 *
	 * @see     JFormRule
	 * @see     JFilterInput
	 * @since   12.2
	 */
	public function validate($form, $data, $group = null)
	{

		// Filter and validate the form data.
		$data = $form->filter($data);
		$return = $form->validate($data, $group);

		// Check for an error.
		if ($return instanceof Exception)
		{
			$this->setError($return->getMessage());

			return false;
		}

		// Check the validation results.
		if ($return === false)
		{
			// Get the validation messages from the form.
			foreach ($form->getErrors() as $message)
			{
				$this->setError($message);
			}

			return false;
		}

		// $config = JFactory::getConfig();
		// $tzoffset = $config->get('offset');
		// if(isset($data['order_date'])&& $data['order_date']){
		// 	$date = JFactory::getDate($data['order_date']);
		// 	$purchase_date = $date->toSql();
		// 	$order_date = $date->toUNIX();
		// } else{
		// 	$purchase_date = date('Y-m-d H:i:s', time() + $tzoffset);
		// 	$date = JFactory::getDate();
		// 	$order_date = $date->toUNIX();
		// }

		if(isset($data['order_date']) && $data['order_date']){
			$date = JFactory::getDate($data['order_date']);
			$order_date = $date->format('Y-m-d 00:00:0');
		} else{
			$date = JFactory::getDate('now');
			$order_date = $date->format('Y-m-d 00:00:0');
		}

		$data['order_date'] = $order_date;
		$data['promocodeid'] = $this->getPromocodeByCode( $data['discount'] );
		$data['number_of_products'] = count( $data['product_id'] );
		$data['published'] = '1';

		return $data;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since	3.1
	 */
	public function save($data)
	{
		$userid = $data['userid'];
		$table = $this->getTable('Customer');
		$table->loadCustommer($userid);

		if(empty($table->id) or $table->id < 0){
			$user = JFactory::getUser($userid);

			$cust = new stdClass();
			$cust->id = $user->id;
			$cust->name = $user->name;
			$cust->email = $user->email;
			$table->bind($cust);
			$table->create();
		}
		//print_r($table);die;
		// prepare the data
		$status = $data['status'];
		if($status == 'Paid'){
			$data['amount_paid'] = $data['amount'];
			$data['status'] = 'Active';
		}
		$data['price'] = $data['amount'];
		// $data['amount'] = $data['amount'] - $data['discount'];
		$data['promocodeid'] = $this->getPromocodeByCode($data['promocode']);

		//DigiComSiteHelperLicense::addLicenceSubscription($data['product_id'], $data['userid'], 1, $data['status']);
		// print_r($data);die;
		if(parent::save($data)){

			//hook the files here
			$recordId = $this->getState('ordernew.id');
			//we have to add orderdetails now;
			$this->addOrderDetails($data['product_id'], $recordId, $data['userid'], $data['status']);

			$info = array(
				'orderid' => $recordId,
				'status' => $status,
				'now_paid' => $data['amount_paid'],
				'customer' => $cust->name ,
				'username' => JFactory::getUser()->username
			);

			DigiComSiteHelperLog::setLog('purchase', 'admin ordernew save', $recordId, 'Admin created order#'.$recordId.', status: '.$status.', paid: '.$data['amount_paid'], json_encode($info),$status);

			// $orders = $this->getInstance( "Orders", "DigiComModel" );
			// $orders->updateLicensesStatus($data['id'], $type);
			if($data['status'] == 'Active'){
				$type = 'complete_order';
			}else{
				$type = $data['status'];
			}
			DigiComSiteHelperLicense::addLicenceSubscription($data['product_id'], $data['userid'], $recordId, $type);

			DigiComHelperEmail::sendApprovedEmail($recordId, $type, $data['status'], $data['amount_paid']);

			$items = $this->getOrderItems($recordId);
			if($data['status'] == 'Active'){

				$dispatcher = JDispatcher::getInstance();
				$dispatcher->trigger('onDigicomAfterPaymentComplete', array($recordId, $info, $data['processor'], $items, $data['userid']));

			}

			return true;

		}

		return false;

	}
	/*
	* add order details
	*/

	function addOrderDetails($items, $orderid, $customer, $status = "Active")
	{

		if($status != "Pending")
			$published = 1;
		else
			$published = 0;

		$database = JFactory::getDBO();
		$jconfig = JFactory::getConfig();

		$user_id = $customer;

		if($user_id == 0){
			return false;
		}

		$product = $this->getTable('Product');
		// start foreach
		foreach($items as $key=>$item)
		{
			if($key >= 0)
			{
				$product->load($item);
				$price = $product->price;
				$date = JFactory::getDate();
				$purchase_date = $date->toSql();
				$package_type = (!empty($product->bundle_source) ? $product->bundle_source : 'reguler');
				$sql = "insert into #__digicom_orders_details(userid, productid,quantity,price, orderid, amount_paid, published, package_type, purchase_date) "
						. "values ('{$user_id}', '{$item}', '1','{$price}','".$orderid."', '0', ".$published.", '".$package_type."', '".$purchase_date."')";
				//print_r($sql);die;
				$database->setQuery($sql);
				$database->query();
				//
				// $site_config = JFactory::getConfig();
				// $tzoffset = $site_config->get('offset');
				// $buy_date = date('Y-m-d H:i:s', time() + $tzoffset);
				// $sql = "insert into #__digicom_logs (`userid`, `productid`, `buy_date`, `buy_type`)
				// 		values (".$user_id.", ". $item .", '".$buy_date."', 'new')";
				// $database->setQuery($sql);
				// $database->query();


				$sql = "update #__digicom_products set used=used+1 where id = '" . $item . "'";
				$database->setQuery( $sql );
				$database->query();

			}
		}
		// end foreach

		return true;
	}

	public function getOrderItems( $order_id ){

		$configs = $this->getConfigs();
		$db 	= JFactory::getDbo();
		$sql 	= 'SELECT `p`.*, `od`.quantity FROM `#__digicom_products` AS `p` INNER JOIN `#__digicom_orders_details` AS `od` ON (`od`.`productid` = `p`.`id`) WHERE `orderid` ='.$order_id;

		$db->setQuery($sql);
		$items = $db->loadObjectList();

		//change the price of items if needed
		for ( $i = 0; $i < count( $items ); $i++ )
		{
			$item = &$items[$i];
			$item->discount = 0;
			$item->currency = $configs->get('currency','USD');
			$item->price = DigiComSiteHelperPrice::format_price( $item->price, $item->currency, false, $configs ); //sprintf( $price_format, $item->product_price );
			$item->subtotal = $item->price * $item->quantity;
		}

		return $items ;

	}

	function calcPrice($req)
	{
		$configs = JComponentHelper::getComponent('com_digicom')->params;

		$result = array();
		$amount_subtotal = 0;
		$amount = 0;
		$taxvalue = 0;

		//--------------------------------------------------------
		// Promo code
		//--------------------------------------------------------

		$promovalue = 0;
		$addPromo = false;
		$ontotal = false;
		$onProduct = false;
		if($req->promocode !='none'){

			$q = "select * from #__digicom_promocodes where code = '".trim($req->promocode)."'";
			$this->_db->setQuery($q);
			$promo = $this->_db->loadObject();
			//print_r($promo->discount_enable_range);die;
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
			}
		}

		//echo $ontotal;die;

		//$cust_id = $req->customer_id;
		if(isset($req)){
			foreach($req->pids as $item ) {
				if (!empty($item[0])) {
					$sql = "SELECT price FROM #__digicom_products WHERE id = '" . $item[0] . "'";
					$this->_db->setQuery( $sql );
					$plan = $this->_db->loadObject();
					$price = $plan->price;
					$amount_subtotal += $price;
					$amount += $price;
					//$taxvalue += $this->getTax( $product_id, $cust_id, $price );
					// $taxvalue += 0;

					//check promocode on product apply
					if($addPromo && $onProduct){
						// Get product restrictions
						$sql = "SELECT p.`productid` FROM `#__digicom_promocodes_products` AS p WHERE p.`promoid`=" . $promo->id ." and p.`productid`=".$item[0];
						$this->_db->setQuery( $sql );
						$promo->product = $this->_db->loadObject();

						if (count($promo->product) && $promo->aftertax == '0')
						{
							//promo discount should be applied before taxation
							//we get product to calculate discount

							if ($promo->promotype == '0')
							{
								// Use absolute values
								$promovalue += $promo->amount;
							}
							else
							{
								// Use percentage
								$promovalue += $price * $promo->amount / 100;
							}

							$sql = "update #__digicom_promocodes set used=used+1 where id = '" . $promo->id . "'";
							$this->_db->setQuery( $sql );
							$this->_db->query();

						}
					} // end if for: product promo check
				} //end if for empty if check
			} //end foreach for products
		}

		//add tax to total
		// $amount = $amount + $taxvalue;

		if($addPromo && $onProduct){
			$amount -= $promovalue;
		}

		//--------------------------------------------------------
		// Promo code on cart
		//--------------------------------------------------------
		if($addPromo && $ontotal){
			//echo 'apply promo on cart';die;
			//now lets apply promo discounts if there are any
			if($promo->promotype == '0'){//use absolute values
				$amount -= $promo->amount;
				$promovalue = $promo->amount;
			}
			else{ //use percentage
				$promovalue = $amount * $promo->amount / 100;
				$amount *= 1 - $promo->amount / 100;
			}

			$sql = "update #__digicom_promocodes set used=used+1 where id = '" . $promo->id . "'";
			$this->_db->setQuery( $sql );
			$this->_db->query();
		}

		//echo $promovalue;die;
		//--------------------------------------------------------
		$amount_subtotal = $amount_subtotal < 0 ? "0.00" : $amount_subtotal;
		$amount = $amount < 0 ? "0.00" : $amount;

		if($configs->get('enable_taxes',0) && $req->userid)
		{
			$customer = $this->getTable("Customer");
			$customer->load($req->userid);
			$tax_amount = DigiComSiteHelperPrice::get_tax_rate($configs, $customer->country, $customer->state);
			$taxvalue = $amount * $tax_amount;
			$amount = $amount + $taxvalue;
		}
		else
		{
			$taxvalue = 0;
		}


		$result['amount'] = trim( DigiComHelperDigiCom::format_price( $amount_subtotal, $configs->get('currency','USD'), true, $configs ) );
		$result['amount_value'] = trim( DigiComHelperDigiCom::format_price( $amount, $configs->get('currency','USD'), false, $configs ) );
		$result['price_value'] = trim( DigiComHelperDigiCom::format_price( $amount_subtotal, $configs->get('currency','USD'), false, $configs ) );
		$result['tax_value'] = trim( DigiComHelperDigiCom::format_price( $taxvalue, $configs->get('currency','USD'), false, $configs ) );
		$result['tax'] = trim( DigiComHelperDigiCom::format_price( $taxvalue, $configs->get('currency','USD'), true, $configs ) );;
		$result['discount_sign'] = trim( DigiComHelperDigiCom::format_price( $promovalue, $configs->get('currency','USD'), true, $configs ) );
		$result['discount'] = trim( DigiComHelperDigiCom::format_price( $promovalue, $configs->get('currency','USD'), false, $configs ) );
		$result['total'] = trim( DigiComHelperDigiCom::format_price( $amount, $configs->get('currency','USD'), true, $configs ) );
		$result['total_value'] = trim( DigiComHelperDigiCom::format_price( $amount, $configs->get('currency','USD'), false, $configs ) );
		$result['currency'] = $configs->get('currency','USD');
		$result['shipping'] = 0;

		return $result;
	}

	/*
		method to get discount code
	*/
	function getPromocodeByCode($code){
		$sql = "SELECT id FROM #__digicom_promocodes WHERE code = '" . $code . "'";
		$this->_db->setQuery( $sql );
		$promocode_id = $this->_db->loadResult();

		if ( $promocode_id ) {
			return $promocode_id;
		} else {
			return "0";
		}

	}

	function getConfigs() {
		$comInfo = JComponentHelper::getComponent('com_digicom');
		return $comInfo->params;
	}
}
