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
<form action='<?php echo $vars->action_url ?>' class="form-horizontal" method="post" id="paymentForm">
	<input type="hidden" name="sid" value="<?php echo $vars->sid?>" />
	<input type='hidden' name='invoice_number' value='<?php echo $vars->order_id ?>' >
	<input type='hidden' name='order_id' value='<?php echo $vars->order_id ?>' >
	<input type="hidden" name="merchant_order_id" value="<?php echo $vars->order_id ?>" />

	<!-- we will run the loop to show all product info -->
	<?php 
	$i = 0;
	foreach ($vars->items as $key => $item) : ?>
		<input type='hidden' name='li_<?php echo $i; ?>_type' value='product' >
		<input type='hidden' name='li_<?php echo $i; ?>_name' value='<?php echo $item->item_name ?>' >
		<input type='hidden' name='li_<?php echo $i; ?>_product_id' value='<?php echo $item->item_id ?>' >
		<input type='hidden' name='li_<?php echo $i; ?>__description' value='<?php echo $item->description ?>' >
		<input type='hidden' name='li_<?php echo $i; ?>_price' value='<?php echo $item->price ?>' >
	<?php 
	$i++;
	endforeach; 
	?>
	<!--// end-->
	
	<input type='hidden' name='x_receipt_link_url' value="<?php echo $vars->notify_url;?>" >
	
	<?php if($vars->demo):?>
	<input type="hidden" name="demo" value="<?php echo $vars->demo;?>" />
	<?php endif;?>

	<input type="hidden" name="total" value="<?php echo $vars->amount ?>" />
	<input type="hidden" name="lang" value="<?php echo $vars->lang; ?>" />
	<input type="hidden" name="fixed" value="Y" />
	<input type='hidden' name='mode' value='2CO' >
	<input type='hidden' name='return_url' value="<?php echo $vars->return;?>" >
	<input type="hidden" name="pay_method" value="<?php //echo strtoupper($vars->pay_method); ?>" />
	<input type="hidden" name="id_type" value="1" />

	<div class="form-actions">
		<input name="submit" type="submit" class="btn btn-success btn-large" value="Pay Now" >
	</div>
	
</form>
</div>
