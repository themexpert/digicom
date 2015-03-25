<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 341 $
 * @lastmodified	$LastChangedDate: 2013-10-10 14:28:28 +0200 (Thu, 10 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

defined ('_JEXEC') or die ("Go away.");

$configs = $this->configs;
$Itemid = JRequest::getInt("Itemid", 0);
$processor = JRequest::getVar("processor", '');
if($configs->get('shopping_cart_style','') == "1"){
	JRequest::setVar("tmpl", "component");
}

?>

<?php
	if($configs->get('show_steps',1) == 1){
?>
		<div class="pagination pagination-centered">
			<ul>
				<li class="disabled"><span><?php echo JText::_("COM_DIGICOM_BUYING_PROCESS_STEP_ONE"); ?></span></li>
				<li class="disabled"><span><?php echo JText::_("COM_DIGICOM_BUYING_PROCESS_STEP_TWO"); ?></span></li>
				<li class="active"><span><?php echo JText::_("COM_DIGICOM_BUYING_PROCESS_STEP_THREE"); ?></span></li>
			</ul>
		</div>
<?php
	}
?>

<span id="cart_wrapper_component">
<?php

	$customer = $this->customer;
	$items = $this->items;
	$total = 0;//$this->total;//0;
	$discount = $this->discount;//0;
	$cat_url = $this->cat_url;
 	$totalfields = 0;
 	$shippingexists = 0;

	$invisible = 'style="display:none;"';
		if ( count ( $items ) == 0 ) {
		   	$formlink = JRoute::_("index.php?option=com_digicom"."&Itemid=".$Itemid);
			//echo _CART_EMPTY . "<br>";
			?>
<!-- 
			<form name="cart_form" method="post" action="<?php echo $formlink;?>">
			<input type="button"  class="button" name="continue" value="<?php echo JText::_("COM_DIGICOM_CONTINUE_SHOPPING");?>" onClick="window.location='<?php
				//echo DigiComSiteHelperDigiCom::DisplayContinueUrl($configs,$cat_url);
			 ?>';">
			</form> -->

			<?php
			return;
		}

	$login_link = JRoute::_("index.php?option=com_digicom&view=profile&layout=login&returnpage=cart&Itemid=".$Itemid);
	$cart_url = JRoute::_("index.php?option=com_digicom&view=cart&Itemid=".$Itemid);
	$checkout_url = JRoute::_("index.php?option=com_digicom&task=cart.checkout&fromsum=1&processor=".$processor."&Itemid=".$Itemid);
	//print_r($customer);
		?>

 <?php if (!isset($customer->_user->id) || !$customer->_user->id ) {?><p style="font-size: 18px"><?php echo JText::_("COM_DIGICOM_SUMMARY_RETURN_CUSTMERS_CLICK")?>
 <a href="<?php echo $login_link; ?>"><?php echo JText::_("COM_DIGICOM_SUMMARY_HERE");?></a><?php echo ' '; ?><?php echo JText::_("COM_DIGICOM_SUMMARY_TO_LOGIN");?></p>
	<?php }
?>


<?php $formlink = JRoute::_("index.php?option=com_digicom&controller=cart"."&Itemid=".$Itemid); ?>

<table width="100%" cellspacing="0" id="digi_table">
<h1><?php echo JText::_("COM_DIGICOM_SUMMARY_YOUR_ORDER")?></h1>
	<tr ><?php // class was cart_heading ?>
	  <th class="summary_header">
	  	 <?php echo JText::_("DSPROD");?>
	  </th>
	  <th class="summary_header" <?php if ($configs->get('showcam',1) == 0) echo $invisible;?> >
		  <?php echo JText::_("COM_DIGICOM_QUANTITY");?>
	  </th>
	  <th class="summary_header">
		<?php echo JText::_("COM_DIGICOM_PRODUCT_PRICE");?>
	  </th>
	  <th class="summary_header" <?php if ($discount!=1) echo 'style="display:none"'?> >
		<?php echo JText::_("COM_DIGICOM_DISCOUNT");?>
	  </th>
	  <th class="summary_header" <?php echo ($shippingexists >0 )?"":'style="display:none;"';?>>
		<?php echo JText::_("COM_DIGICOM_SHIPPING_COST"); ?>
	  </th>


	  <?php if ($totalfields > 0  ){ ?>
	  <th class="summary_header"><?php echo JText::_("COM_DIGICOM_ATTRIBUTES"); ?></th>
	  <?php } ?>
	  <th nowrap class="summary_header">
		<?php echo JText::_("COM_DIGICOM_SUBTOTAL");?>
	  </th>
	</tr>
	<?php $k=1;foreach ( $items as $itemnum => $item ) {
		if ($itemnum < 0) continue;
	?>
	<tr class="item_row">

	  <td class="item_column">
		  <?php echo $item->name; ?>
		  <!-- <br/>
		  <?php echo DigiComSiteHelperDigiCom::format_price2($item->price, $item->currency, true, $configs); ?> -->
	  </td>
	  <!-- Quantity -->
		<td class="center" <?php if ($configs->get('showcam',1) == 0) echo $invisible;?> nowrap="nowrap">
			<span class="digicom_details">
				<strong>
					<?php echo $item->quantity; ?>
				</strong>
			</span>
		</td>
		<!-- /End Quantity -->
		<td class="item_column" style="text-align:center;"> <?php
			echo  DigiComSiteHelperDigiCom::format_price($item->price, $item->currency, true, $configs);//sprintf($price_format,$item->product_price)." ".$item->currency;
		?></td>
		<td class="item_column" style="text-align:center; <?php if ($discount!=1) echo 'display:none;'?>"><?php
			//echo (isset($item->discount)) ? $item->discount." %" : "N/A" ;
			echo (isset($item->percent_discount)) ? $item->percent_discount  : "N/A" ;
		?></td>
		<td class="item_column"  <?php echo ($shippingexists >0 )?"style='text-align:center;'":'style="text-align:center; display:none;"';?>><?php
		if ($configs->shipping_price == 1) $item->shipping += $item->itemtax;
			echo (isset($item->shipping)&&$item->domainrequired==2? DigiComSiteHelperDigiCom::format_price($item->shipping, $item->currency, true, $configs):"N/A");
		?></td>


	  <?php if ($totalfields > 0  ){ ?>
	 	 <td  class="item_column" style="text-align:center;" nowrap="nowrap"><?php
		 	 $i = 0;

			echo $lists[$item->cid]['attribs'];
		//   add_selector_to_cart($item, $optlen, $select_only, $i);

		?></td>
		  <?php } ?>
	  	<td class="item_column" nowrap style="text-align:center;"><?php
		echo  DigiComSiteHelperDigiCom::format_price($item->subtotal, $item->currency, true, $configs);
		
	  ?>
		</td>
		</tr>
		<?php
			$total += $item->subtotal;//+$item->product_price;
			$k = 1 - $k;}?>
	<tr>

	  <td colspan="<?php
	  	$span = 6;
	  	if ($totalfields > 0 && $shippingexists > 0) ;//echo '7';
	  	else if ($totalfields > 0 || $shippingexists > 0) $span--;//echo'6';
	  	else $span-=2;
	  	if ($discount != 1) $span--;
		if ($configs->get('showcam',1) == 0) $span--;
		if ($configs->get('showcremove',1) == 0) $span--;

	  echo $span;

	  ?>" valign="top" class="item_column_right" nowrap  >
	<span
	  <?php
	if (strlen(trim($this->promocode)))
	  	echo JText::_("COM_DIGICOM_DISCOUNT_CODE"). ":  ";
	  ?><?php echo $this->promocode; ?>
</span>
<span style="display:none" >
<?php
	if ($shippingexists) {
		echo " <input type='radio' name='shipto' value='2' checked  />";
	}
?>
</span>
	  </td>
	  <td class="item_column_right"><?php

		$tax = $this->tax;
		if ($configs->get('tax_summary',0) == 1){
				   if ($tax['promo'] > 0 && $tax['promoaftertax'] == '0'):
				?>
				<?php echo JText::_("COM_DIGICOM_DISCOUNT"); ?><br />
				<?php endif; ?>

			  <?php
			  
				if ($tax['value'] > 0 && $customer->_user->id > 0) {
				  echo $tax['type'];
				}
				if ($tax['shipping'] > 0 && $customer->_user->id > 0):
				?>
				<?php echo JText::_("COM_DIGICOM_SHIPPING"); ?><br />
				<?php endif; ?>
				<?php
				 if ($tax['promo'] > 0 && $tax['promoaftertax'] == '1'):
				?>
				<?php echo JText::_("COM_DIGICOM_DISCOUNT_CODE"); ?><br />
				<?php endif;
		}
		 ?>
		<?php echo JText::_("COM_DIGICOM_TOTAL");?><br />
	  </td>
		  <td class="item_column_right" nowrap style="text-align:center; padding-top:10px;">

			<?php
				if ($configs->get('tax_summary',0) == 1){
				if ($tax['promo'] > 0 && $tax['promoaftertax'] == '0')
					echo DigiComSiteHelperDigiCom::format_price($tax['promo'], $tax['currency'], true, $configs)."<br />";
				?>
								  <?php
				if ($tax['value'] > 0 && $customer->_user->id > 0)
					echo DigiComSiteHelperDigiCom::format_price($tax['value'], $tax['currency'], true, $configs)."<br />";
				?>

				<?php
				if ($tax['shipping'] > 0 && $customer->_user->id > 0)
					echo DigiComSiteHelperDigiCom::format_price($tax['shipping'], $tax['currency'], true, $configs)."<br />";
				?>

				<?php
				if ($tax['promo'] > 0 && $tax['promoaftertax'] == '1')
				echo DigiComSiteHelperDigiCom::format_price($tax['promo'], $tax['currency'], true, $configs)."<br />";
				}
			?>
		<?php
			  echo DigiComSiteHelperDigiCom::format_price($tax['taxed'], $tax['currency'], true, $configs)."<br />";
		?></td>
	</tr>
	<tr>
		<td><?php echo JText::_("COM_DIGICOM_PAYMENT_METHOD"); ?></td>
		<td><?php echo ucfirst($processor); ?></td>
	</tr>
	<tr>
	  <td height="30" colspan="10" width="100%">
	  <table width="100%"  border="0" cellspacing="0" cellpadding="2" style="margin-top: 15px;">
		<tr>
		  <td>
		  	<a href="<?php echo $cart_url;?>" class="digicom_cancel btn btn-warning"><?php echo JText::_("COM_DIGICOM_EDIT_ORDER")?></a>
		  	<a href="<?php echo $checkout_url;?>" class="digicom_cancel btn btn-success" style="margin-left: 5px;"><?php echo JText::_("COM_DIGICOM_PLACE_ORDER")?></a>
		  </td>
		  <td class="item_column_right">
		  </td>
		</tr>
	  </table>

	 </td>
	</tr>
  </table>

</span>

<?php //echo DigiComSiteHelperDigiCom::powered_by(); ?>
