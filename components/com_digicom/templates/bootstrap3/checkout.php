<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;


JHTML::_('behavior.formvalidation');

$pg_plugin = $this->pg_plugin;
$configs = $this->configs;
$data = $this->data;
?>
<div id="digicom">

	<?php
	$this->setLayout('cart');
	echo $this->loadTemplate('steps');
	?>

	<h1 class="digi-page-title"><?php echo JText::sprintf("COM_DIGICOM_CHECKOUT_PAYMENT_DETAILS_PAGE_TITLE", $pg_plugin); ?></h1>

	<div class="cart-items-wrap">
		<h4 class="align-center"><?php echo JText::_("COM_DIGICOM_SUMMARY_YOUR_ORDER");?></h4>

		<table id="digicomcarttable" class="table table-striped table-bordered" width="100%">
		  <thead>
		    <tr>
		      <th><?php echo JText::_("COM_DIGICOM_PRODUCT");?></th>
		      <th><?php echo JText::_("COM_DIGICOM_PRICE_PLAN");?></th>
		      <th><?php echo JText::_("COM_DIGICOM_QUANTITY"); ?></th>
		      <th><?php echo JText::_("COM_DIGICOM_SUBTOTAL");?></th>
		    </tr>
		  </thead>
		  <tbody>
		    <?php foreach($this->items as $itemnum => $item ): ?>
		      <tr>
		        <td>
							<?php echo $item->name; ?>
		          <?php if ($this->configs->get('show_validity',1) == 1) : ?>
		            <div class="muted">
		              <small><?php echo JText::_('COM_DIGICOM_PRODUCT_VALIDITY'); ?> : <?php echo DigiComSiteHelperPrice::getProductValidityPeriod($item); ?></small>
		            </div>
		          <?php endif; ?>
		        </td>
		        <td><?php echo DigiComSiteHelperPrice::format_price($item->price, $item->currency, true, $this->configs); ?></td>
		        <td><?php echo $item->quantity; ?></td>
		        <td><?php echo DigiComSiteHelperPrice::format_price($item->subtotal-(isset($value_discount) ? $value_discount : 0), $item->currency, true, $this->configs); ?></td>
		      </tr>
		      <?php
		    endforeach;
		    ?>
		  </tbody>
		  <tfoot>
				<tr>
					<td colspan="4" style="text-align:right;">
						Total : <strong><?php echo  DigiComSiteHelperPrice::format_price($this->order->amount, $configs->get('currency','USD'), true, $configs);?></strong>
					</td>
				</tr>
		  </tfoot>

		</table>
	</div>

	<?php echo $data[0]; ?>

<?php echo DigiComSiteHelperDigiCom::powered_by(); ?>
