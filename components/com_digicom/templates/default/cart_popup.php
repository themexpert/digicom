<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

JHTML::_('behavior.modal');
$configs 	= $this->configs;
$items 		= $this->items;
$total 		= 0; // sub total for all products
$currency = $configs->get('currency','USD');
?>
<div id="digicom" class="digicom-wrapper com_digicom cart">

	<table class="table table-hover table-striped">
		<thead>
			<tr valign="top">
				<th width="30%"><?php echo JText::_("COM_DIGICOM_IMAGE");?></th>
				<th width="30%"><?php echo JText::_("COM_DIGICOM_PRODUCT");?></th>
				<th><?php echo JText::_("COM_DIGICOM_PRICE_PLAN");?></th>
				<th><?php echo JText::_("COM_DIGICOM_QUANTITY"); ?></th>
				<th><?php echo JText::_("COM_DIGICOM_SUBTOTAL");?></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach($items as $itemnum => $item): ?>

			<tr>
				<!-- Product image -->
				<td width="70">
					<?php if(!empty($item->images)): ?>
						<img height="100" width="100" title="<?php echo $item->name; ?>"
						src="<?php echo  JURI::root() . JRoute::_(DigiComSiteHelperDigiCom::getThumbnail($item->images)); ?>" alt="<?php echo $item->name; ?>"/>
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
					<?php echo DigiComSiteHelperPrice::format_price($item->price, $item->currency, true, $configs); ?>
				</td>
				<!-- /End Price -->

				<td align="center" nowrap="nowrap">
					<span class="digicom_details">
						<strong> <?php echo $item->quantity; ?> </strong>
					</span>
				</td>

				<td nowrap>
					<span id="cart_item_total<?php echo $item->cid; ?>" class="digi_cart_amount"><?php
						echo DigiComSiteHelperPrice::format_price($item->subtotal-(isset($value_discount) ? $value_discount : 0), $item->currency, true, $configs); ?>
					</span>
				</td>
			</tr>
			<?php $total += $item->subtotal; ?>
		<?php endforeach;?>
		</tbody>
		<tfoot>
			<tr class="info">
				<td></td>
				<td colspan="2">
					<strong>
						<?php
						$text = "COM_DIGICOM_ITEM_IN_CART";
						if(count($items) > 1){
							$text = "COM_DIGICOM_ITEMS_IN_CART";
						}
						echo count($items)." ".JText::_($text);
						?>
					</strong>
				</td>
				<td><strong><?php echo JText::_("COM_DIGICOM_SUBTOTAL");?></strong></td>
				<td>
					<strong><?php echo DigiComSiteHelperPrice::format_price($total, $currency, true, $configs); ?></strong>
				</td>
			</tr>
		</tfoot>
	</table>
</div>
<?php JFactory::getApplication()->close(); ?>
