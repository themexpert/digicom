<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

// TODO : Remvoe JRequest and cleanup code, naming convention

JHTML::_('behavior.modal');
$user = JFactory::getUser();
$document=JFactory::getDocument();
$app=JFactory::getApplication();
$input = $app->input;
$configs = $this->configs;
$agreeterms = $input->get("agreeterms", "");
$processor = $input->get("processor", "");
$Itemid = $input->get("Itemid", 0);
$items = $this->items;
?>
<div id="digicom" class="digicom-wrapper com_digicom cart">
<?php
$button_value = "COM_DIGICOM_CHECKOUT";
$onclick = "document.getElementById('returnpage').value='checkout'; document.getElementById('type_button').value='checkout';";

if($user->id == 0 || $this->customer->_customer->country == "")
{
	$button_value = "DSSAVEPROFILE";
	$onclick = "document.getElementById(\'returnpage\').value=\'login_register\'; document.getElementById(\'type_button\').value=\'checkout\';";
}

$url="index.php?option=com_digicom&controller=cart&task=gethtml&tmpl=component&format=raw&processor=";

$total = 0;//$this->total;//0;
$discount = $this->discount;//0;
$cat_url = $this->cat_url;
$totalfields = 0;
$shippingexists = 0;
$from = $input->get("from", "");
$nr_columns = 4;
$tax = $this->tax;
$formlink = JRoute::_("index.php?option=com_digicom&view=cart&Itemid=".$Itemid);
$currency = $configs->get('currency','USD');
?>

<form name="cart_form" method="post" action="<?php echo $formlink?>" onSubmit="return cartformsubmit();">
	<table class="table table-hover table-striped">
		<thead>
		<tr valign="top">
			<th width="30%">
				<?php echo JText::_("COM_DIGICOM_IMAGE");?>
			</th>
			<th width="30%">
				<?php echo JText::_("COM_DIGICOM_PRODUCT");?>
			</th>
			<th>
				<?php echo JText::_("COM_DIGICOM_PRICE_PLAN");?>
			</th>

			<th>
				<?php echo JText::_("COM_DIGICOM_QUANTITY"); ?>
			</th>
			<?php if ($tax['discount_calculated']){?>
			<th>
				<?php echo JText::_("COM_DIGICOM_PROMO_DISCOUNT"); ?>
			</th>
			<?php } ?>

			<th><?php echo JText::_("COM_DIGICOM_SUBTOTAL");?></th>

			<th><?php echo JText::_("COM_DIGICOM_CART_REMOVE_ITEM");?></th>
		</tr>
	</thead>
	<tbody><?php
	$k = 0;
	foreach($items as $itemnum => $item){
		if($itemnum < 0){
			continue;
		}
	?>
		<tr>
			<!-- Product image -->
			<td width="70">
				<?php if(!empty($item->images)): ?>
					<img height="100" width="200" title="<?php echo $item->name; ?>" src="<?php echo JRoute::_(JURI::root().$item->images); ?>" alt="<?php echo $item->name; ?>"/>
				<?php endif; ?>
			</td>
			<!-- /End Product image -->

			<!-- Product name -->
			<td style="text-align:left;" class="digicom_product_name">
				<?php echo $item->name;?>
				<?php if ($this->configs->get('show_validity',1) == 1) : ?>
				<div class="muted">
					<small><?php echo JText::_('COM_DIGICOM_PRODUCT_VALIDITY'); ?> : <?php echo DigiComSiteHelperPrice::getProductValidityPeriod($item); ?></small>
				</div>
				<?php endif; ?>
			</td>
			<!-- /End Product name -->

			<!-- Price -->
			<td align="center" style="vertical-align:top;">
				<?php
					echo DigiComSiteHelperDigiCom::format_price2($item->price, $item->currency, true, $configs);
					$currency = $item->currency;
				?>
			</td>
			<!-- /End Price -->

			<td align="center" nowrap="nowrap">
				<span class="digicom_details">
					<strong> <?php echo $item->quantity; ?> </strong>
				</span>
			</td>

			<td nowrap>
				<span id="cart_item_total<?php echo $item->cid; ?>" class="digi_cart_amount"><?php
					echo DigiComSiteHelperDigiCom::format_price2($item->subtotal-(isset($value_discount) ? $value_discount : 0), $item->currency, true, $configs); ?>
				</span>
			</td>

			<!-- Remove -->
			<td align="center" style="vertical-align:top;width:80px;">
				<a href="#" onclick="javascript:deleteFromCart(<?php echo $item->cid; ?>);"><i class="icon-remove"></i></a>
			</td>
			<!-- /End Remove -->
		</tr>
	<?php
		$total += $item->subtotal;
		$k++;
	}
	?>
		</tbody>
		<tfoot>
		<tr class="info">
			<td></td>
			<td colspan="3">
				<b><?php
					$text = "COM_DIGICOM_ITEM_IN_CART";
					if($k > 1){
						$text = "COM_DIGICOM_ITEMS_IN_CART";
					}
					echo $k." ".JText::_($text);
				?></b>
			</td>
			<td><b><?php echo JText::_("COM_DIGICOM_SUBTOTAL");?></b></td>
			<td>
				<b><?php echo DigiComSiteHelperDigiCom::format_price2($total, $currency, true, $configs); ?></b>
			</td>
		</tr>
		</tfoot>
	</table>

	<input name="controller" type="hidden" id="controller" value="Cart">
	<input name="task" type="hidden" id="task" value="updateCart">
	<input name="returnpage" type="hidden" id="returnpage" value="">
	<input name="Itemid" type="hidden" value="<?php global $Itemid; echo $Itemid; ?>">
	<input name="promocode" type="hidden" value="" />
	<input type="hidden" name="processor" id="processor" value="paypaypal">
</form>
</div>
<?php JFactory::getApplication()->close(); ?>
