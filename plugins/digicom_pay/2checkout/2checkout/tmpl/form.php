<?php 
/**
 * @package Social Ads
 * @copyright Copyright (C) 2009 -2010 foobla.com, Tekdi Web Solutions . All rights reserved.
 * @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     http://www.foobla.com.com
 */
defined('_JEXEC') or die('Restricted access');

 ?>


<div class="akeeba-bootstrap">
<!--
<form action="<?php echo $vars->action_url ?>" class="form-horizontal" method="post" id="paymentForm">

	<input type="hidden" name="sid" value="<?php echo $vars->sid?>" />
	<input type="hidden" name="cart_order_id" value="<?php echo $vars->order_id ?>" />
	<input type="hidden" name="total" value="<?php echo $vars->amount ?>" />

	<input type="hidden" name="demo" value="<?php echo  $vars->demo; ?>" />
	<input type="hidden" name="merchant_order_id" value="<?php echo $vars->order_id ?>" />
	<input type="hidden" name="fixed" value="Y" />
	<input type="hidden" name="lang" value="<?php echo $vars->lang; ?>" />
	<input type='hidden' name='return_url' value="<?php echo $vars->return;?>" >
	
	<input type="hidden" name="pay_method" value="<?php echo strtoupper($vars->pay_method); ?>" />
	<input type="hidden" name="id_type" value="1" />
	
	<div class="form-actions">
		<input name="submit" type="submit" class="btn btn-success btn-large" value="Pay Now" >
	</div>
</form>
-->
<form action='<?php echo $vars->action_url ?>' class="form-horizontal" method="post" id="paymentForm">
	<input type="hidden" name="sid" value="<?php echo $vars->sid?>" />
	<input type='hidden' name='mode' value='2CO' >
	<input type='hidden' name='order_id' value='<?php echo $vars->order_id ?>' >
	<input type="hidden" name="merchant_order_id" value="<?php echo $vars->order_id ?>" />
	
	<!-- we will run the loop to show all product info -->
	<input type='hidden' name='li_0_type' value='product' >
	<input type='hidden' name='li_0_name' value='<?php echo $vars->item_name ?>' >
	<input type='hidden' name='li_0_product_id' value='9999999999999999999999999' >
	<input type='hidden' name='li_0__description' value='Example Product Description' >
	<input type='hidden' name='li_0_price' value='<?php echo $vars->amount ?>' >
	<!--// end-->
	
	<input type='hidden' name='card_holder_name' value='Checkout Shopper' >
	<input type='hidden' name='street_address' value='123 Test St' >
	<input type='hidden' name='street_address2' value='Suite 200' >
	<input type='hidden' name='city' value='Columbus' >
	<input type='hidden' name='state' value='OH' >
	<input type='hidden' name='zip' value='43228' >
	<input type='hidden' name='country' value='USA' >
	<input type='hidden' name='email' value='example@2co.com' >
	<input type='hidden' name='phone' value='614-921-2450' >
	<input type='hidden' name='phone_extension' value='197' >
	<input type='hidden' name='purchase_step' value='payment-method' >
	
	<input type='hidden' name='x_receipt_link_url' value="<?php echo $vars->notify_url;?>" >
	
	<div class="form-actions">
		<input name="submit" type="submit" class="btn btn-success btn-large" value="Pay Now" >
	</div>
	
</form>
</div>
