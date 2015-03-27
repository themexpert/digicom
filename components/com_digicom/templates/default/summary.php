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
if($configs->get('show_steps',1) == 1){ ?>
<div class="pagination pagination-centered">
	<ul>
		<li class="disabled"><span><?php echo JText::_("COM_DIGICOM_BUYING_PROCESS_STEP_ONE"); ?></span></li>
		<li class="disabled"><span><?php echo JText::_("COM_DIGICOM_BUYING_PROCESS_STEP_TWO"); ?></span></li>
		<li class="active"><span><?php echo JText::_("COM_DIGICOM_BUYING_PROCESS_STEP_THREE"); ?></span></li>
	</ul>
</div>
<?php } ?>

<span id="cart_wrapper_component">
<?php

$customer = $this->customer;
$items = $this->items;
$total = 0;//$this->total;//0;
$discount = $this->discount;//0;
$cat_url = $this->cat_url;
$invisible = 'style="display:none;"';

$login_link = JRoute::_("index.php?option=com_digicom&view=profile&layout=login&returnpage=cart&Itemid=".$Itemid);
$cart_url = JRoute::_("index.php?option=com_digicom&view=cart&Itemid=".$Itemid);
$checkout_url = JRoute::_("index.php?option=com_digicom&task=cart.checkout&fromsum=1&processor=".$processor."&Itemid=".$Itemid);
?>

<?php if (!isset($customer->_user->id) || !$customer->_user->id ) { ?>
	
	<p style="font-size: 18px">
		<?php echo JText::_("COM_DIGICOM_SUMMARY_RETURN_CUSTMERS_CLICK")?>
 		<a href="<?php echo $login_link; ?>"><?php echo JText::_("COM_DIGICOM_SUMMARY_HERE");?></a>
 		<?php echo ' '; ?>
 		<?php echo JText::_("COM_DIGICOM_SUMMARY_TO_LOGIN");?>
 	</p>

<?php } ?>

<h1><?php echo JText::_("COM_DIGICOM_SUMMARY_YOUR_ORDER")?></h1>
<table width="100%" cellspacing="0" id="digi_table" class="table table-bordered">
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
	  <th nowrap class="summary_header">
		<?php echo JText::_("COM_DIGICOM_SUBTOTAL");?>
	  </th>
	</tr>

	<?php $k=1;foreach ( $items as $itemnum => $item ) 
	{
		if ($itemnum < 0) continue;
		?>
		<tr class="item_row">

			<td class="item_column">
				<?php echo $item->name; ?>
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
			<td class="item_column" style="text-align:center;"> 
				<?php echo  DigiComSiteHelperDigiCom::format_price($item->price, $item->currency, true, $configs);?>
			</td>

			<td class="item_column" style="text-align:center; <?php if ($discount!=1) echo 'display:none;'?>">
				<?php echo (isset($item->percent_discount)) ? $item->percent_discount  : "N/A" ; ?>
			</td>

		  	<td class="item_column" nowrap style="text-align:center;">
		  		<?php echo  DigiComSiteHelperDigiCom::format_price($item->subtotal, $item->currency, true, $configs); ?>
			</td>
		</tr>
		<?php
		$total += $item->subtotal;//+$item->product_price;
		$k = 1 - $k;
	} 
	$tax = $this->tax;
	?>
	<tr>
		<td colspan="<?php echo ($discount ? '4' : '3'); ?>" valign="top" class="item_column_right" nowrap >
			<?php echo JText::_("COM_DIGICOM_SUBTOTAL"); ?>
  		</td>

		<td class="item_column_right">
	  		<?php echo DigiComSiteHelperDigiCom::format_price($total, $tax['currency'], true, $configs);	?>
		</td>
	</tr>

	<?php 
	if (strlen(trim($this->promocode))): 
	?>
	<tr>
		<td colspan="<?php echo ($discount ? '4' : '3'); ?>" valign="top" class="item_column_right" nowrap >
			<?php echo JText::sprintf("COM_DIGICOM_DISCOUNT",$this->promocode); ?>
  		</td>

		<td class="item_column_right">
	  		<?php echo DigiComSiteHelperDigiCom::format_price($tax['promo'], $tax['currency'], true, $configs);	?>
		</td>
	</tr>
	<?php endif; ?>

	<tr>
		<td colspan="<?php echo ($discount ? '4' : '3'); ?>" valign="top" class="item_column_right" nowrap >
			<?php echo JText::_("COM_DIGICOM_TOTAL");?><br />
		</td>
		<td class="item_column_right" nowrap style="text-align:center; padding-top:10px;">
			<?php echo DigiComSiteHelperDigiCom::format_price($tax['value'], $tax['currency'], true, $configs); ?>
		</td>
	</tr>

	<tr>
		<td colspan="<?php echo ($discount ? '4' : '3'); ?>"><?php echo JText::_("COM_DIGICOM_PAYMENT_METHOD"); ?></td>
		<td><?php echo ucfirst($processor); ?></td>
	</tr>
	<tr>
		<td height="30" colspan="<?php echo ($discount ? '5' : '4'); ?>" width="100%">
			<a href="<?php echo $cart_url;?>" class="digicom_cancel btn btn-warning"><?php echo JText::_("COM_DIGICOM_EDIT_ORDER")?></a>
			<a href="<?php echo $checkout_url;?>" class="digicom_cancel btn btn-success" style="margin-left: 5px;"><?php echo JText::_("COM_DIGICOM_PLACE_ORDER")?></a>
		</td>
	</tr>
  </table>

</span>

<?php echo DigiComSiteHelperDigiCom::powered_by(); ?>
