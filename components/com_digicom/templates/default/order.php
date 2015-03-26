<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 410 $
 * @lastmodified	$LastChangedDate: 2013-11-14 11:50:41 +0100 (Thu, 14 Nov 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

defined ('_JEXEC') or die ("Go away.");

$n = count ($this->order->products);
$configs = $this->configs;
$order = $this->order;

$date = date( $configs->get('time_format','d M Y'), $order->order_date);
if ($this->order->id < 1){
	echo JText::_('DSEMPTYORDER');
}
?>

<div id="digicom">	

	<?php DigiComSiteHelperDigicom::loadModules('digicom_toolber'); ?>

	<h1 class="digi-page-title"><?php echo JText::_('COM_DIGICOM_ORDER_DETAILS'); ?></h1>

	<table class="table table-striped table-hover table-bordered">
		<thead>
			
			<tr>
				<th><?php echo JText::_('COM_DIGICOM_ORDER_ID'); ?></th>
				<th><?php echo $order->id; ?></th>
			</tr>
		</thead>

		<tbody>

			<tr>
				<td><strong><?php echo JText::_('COM_DIGICOM_STATUS'); ?></strong></td>
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
				<td><strong><?php echo JText::_('COM_DIGICOM_DATE'); ?></strong></td>
				<td><?php echo $date; ?></td>
			</tr>
			
			<tr>
				<td><strong><?php echo JText::_('COM_DIGICOM_DISCOUNT'); ?></strong></td>
				<td><?php echo DigiComSiteHelperDigiCom::format_price($order->promocodediscount, $configs->get('currency','USD'), true, $configs);?></td>
			</tr>

			<tr>
				<td><strong><?php echo JText::_('COM_DIGICOM_AMOUNT_PAID'); ?></strong></td>
				<td><?php echo DigiComSiteHelperDigiCom::format_price($order->amount_paid, $configs->get('currency','USD'), true, $configs);?></td>
			</tr>

		</tbody>
	</table>
	<?php if(strtolower($order->status) === 'pending'): ?>
		<div class="alert alert-warning">
		  <?php echo JText::sprintf('COM_DIGICOM_ORDER_COMPLETE_NOTICE',JRoute::_('index.php?option=com_digicom&view=checkout&order_id='.$order->id.'&processor='.$order->processor.$this->Itemid)); ?>
		</div>
	<?php endif; ?>
	<h3 class="digi-section-title"><?php echo JText::_('COM_DIGICOM_PRODUCTS'); ?></h3>
	<table class="table table-striped table-hover table-bordered">
		<thead>
			<tr>
				<th><?php echo JText::_('COM_DIGICOM_PRODUCTS_IMAGE'); ?></th>
				<th><?php echo JText::_('COM_DIGICOM_PRODUCTS_NAME'); ?></th>
				<th><?php echo JText::_('COM_DIGICOM_PRODUCTS_TYPE'); ?></th>
				<th><?php echo JText::_('COM_DIGICOM_PRODUCTS_PRICE'); ?></th>
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
