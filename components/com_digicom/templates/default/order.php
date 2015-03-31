<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

$n = count ($this->order->products);
$configs = $this->configs;
$order = $this->order;

$date = date( $configs->get('time_format','d M Y'), $order->order_date);
if ($this->order->id < 1){
	echo JText::_('DSEMPTYORDER');
}
$params = json_decode($this->order->params);
?>

<div id="digicom">	

	<?php DigiComSiteHelperDigicom::loadModules('digicom_toolber'); ?>

	<h1 class="digi-page-title"><?php echo JText::_('COM_DIGICOM_ORDER_DETAILS'); ?></h1>

	<table class="table table-striped table-hover table-bordered">
		<thead>
			
			<tr>
				<th><?php echo JText::_('JGRID_HEADING_ID'); ?></th>
				<th><?php echo $order->id; ?></th>
			</tr>
		</thead>

		<tbody>

			<tr>
				<td><strong><?php echo JText::_('JSTATUS'); ?></strong></td>
				<td>
					<?php
						$labelClass = '';
						if ( strtolower($order->status) === 'active') $labelClass = 'label-success';
						elseif ( strtolower($order->status) === 'pending') $labelClass = 'label-warning';
					?>
					<span class="label <?php echo $labelClass; ?>"><?php echo $order->status; ?></span>
				</td>
			</tr>

			
			<tr>
				<td><strong><?php echo JText::_('COM_DIGICOM_PAYMENT_METHOD'); ?></strong></td>
				<td><?php echo ucfirst( $order->processor ); ?></td>
			</tr>
			<tr>
				<td><strong><?php echo JText::_('JDATE'); ?></strong></td>
				<td><?php echo $date; ?></td>
			</tr>
			
			<tr>
				<td><strong><?php echo JText::_('COM_DIGICOM_DISCOUNT'); ?></strong></td>
				<td><?php echo DigiComSiteHelperDigiCom::format_price($order->promocodediscount, $configs->get('currency','USD'), true, $configs);?></td>
			</tr>

			<tr>
				<td><strong><?php echo JText::_('COM_DIGICOM_TOTAL'); ?></strong></td>
				<td><?php echo DigiComSiteHelperDigiCom::format_price($order->amount, $configs->get('currency','USD'), true, $configs);?></td>
			</tr>

			<tr>
				<td><strong><?php echo JText::_('COM_DIGICOM_TOTAL_PAID'); ?></strong></td>
				<td><?php echo DigiComSiteHelperDigiCom::format_price($order->amount_paid, $configs->get('currency','USD'), true, $configs);?></td>
			</tr>
			
			<tr>
				<td><strong><?php echo JText::_('COM_DIGICOM_ORDER_PAYMENT_INFORMATION'); ?></strong></td>
				<td><p class="alert alert-info"><?php echo $order->comment;?></p></td>
			</tr>

			<?php if(!empty($params->warning)): ?>
			<tr>
				<td><strong><?php echo JText::_('COM_DIGICOM_ORDER_PAYMENT_WARNING'); ?></strong></td>
				<td><p class="alert alert-danger"><?php echo $params->warning;?></p></td>
			</tr>
			<?php endif; ?>

		</tbody>
	</table>
	<?php if(strtolower($order->status) === 'pending'): 
	$u = JURI::getInstance();
	$item = JFactory::getApplication()->getMenu()->getItems('link', 'index.php?option=com_digicom&view=checkout', true);
	$Itemid = isset($item->id) ? $item->id : '';
	?>
		<div class="alert alert-warning">
  			<p><?php echo JText::sprintf('COM_DIGICOM_ORDER_COMPLETE_NOTICE'); ?></p>
			<form method="get" class="well well-small form-inline" action="<?php echo $u->toString(); ?>">
  				<input type="hidden" name="option" value="com_digicom">
				<input type="hidden" name="view" value="checkout">
				<input type="hidden" name="order_id" value="<?php echo $order->id; ?>">

				<?php echo DigiComSiteHelperDigicom::getPaymentPlugins($configs); ?>

				<button class="btn pull-right" type="submit">Pay Now</button>
				<input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>">
 			</form>
 		</div>

	<?php endif; ?>
	<h3 class="digi-section-title"><?php echo JText::_('COM_DIGICOM_PRODUCTS'); ?></h3>
	<table class="table table-striped table-hover table-bordered">
		<thead>
			<tr>
				<th><?php echo JText::_('COM_DIGICOM_IMAGE'); ?></th>
				<th><?php echo JText::_('JGLOBAL_TITLE'); ?></th>
				<th><?php echo JText::_('COM_DIGICOM_TYPE'); ?></th>
				<th><?php echo JText::_('COM_DIGICOM_PRODUCT_PRICE'); ?></th>
			</tr>
		</thead>

		<tbody>
			<?php 
			foreach($order->products as $key=>$product): 
			$productlink = JRoute::_(DigiComHelperRoute::getProductRoute($product->id, $product->catid, $product->language));
			?>
			<tr>
				<td><img width="64" height="64" src="<?php echo JUri::root().$product->images; ?>" alt="<?php echo $product->name; ?>" /></td>
				<td><strong><a href="<?php echo $productlink; ?>" target="_blank"><?php echo $product->name; ?></a></strong></td>
				<td><?php echo ucfirst( $product->package_type ); ?></td>
				<td><?php echo DigiComSiteHelperDigiCom::format_price($product->price, $configs->get('currency','USD'), true, $configs);?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>


	<a href="<?php echo JRoute::_("index.php?option=com_digicom&view=downloads"); ?>" class="btn btn-success">
		<i class="icon-out"></i><?php echo JText::_('COM_DIGICOM_GO_DOWNLOAD'); ?>
	</a>

	<a class="btn btn-info" target="_blank" href="<?php echo JRoute::_("index.php?option=com_digicom&view=order&layout=invoice&id=".$order->id."&tmpl=component"); ?>">
		<i class="icon-printer"></i> <?php echo JText::_('COM_DIGICOM_ORDER_PRINT'); ?>
	</a>

	<?php DigiComSiteHelperDigicom::loadModules('digicom_footer','xhtml'); ?>

</div>

<?php echo DigiComSiteHelperDigiCom::powered_by(); ?>
