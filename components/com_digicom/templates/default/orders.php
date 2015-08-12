<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;
$app=JFactory::getApplication();
$input = $app->input;
$configs = $this->configs;
$k = 0;
$n = count ($this->orders);
?>

<div id="digicom">

	<?php DigiComSiteHelperDigicom::loadModules('digicom_toolber'); ?>

	<h1 class="digi-page-title"><?php echo JText::_("COM_DIGICOM_ORDERS_PAGE_TITLE"); ?></h1>

	<form action="<?php echo JRoute::_('index.php?options=com_digicom&view=orders'); ?>" name="adminForm" method="post">

		<div class="input-append">
			<input type="text" id="dssearch" name="search" class="digi-textbox"  value="<?php echo trim($input->get('search', '')); ?>" size="30" />
			<button type="submit" class="btn"><?php echo JText::_("COM_DIGICOM_SEARCH"); ?></button>
		</div>

		<table class="table table-bordered table-striped">
			<thead>
				<tr>
					<th><?php echo JText::_("JGRID_HEADING_ID"); ?></th>
					<th><?php echo JText::_("JDATE"); ?></th>
					<th><?php echo JText::_("JSTATUS"); ?></th>
					<th><?php echo JText::_("COM_DIGICOM_PRICE"); ?></th>
					<th><?php echo JText::_("COM_DIGICOM_TOTAL_PAID"); ?></th>
					<th><?php echo JText::_("COM_DIGICOM_ACTION"); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php
			$i = 0;
			if(count($n) > 0){
				foreach($this->orders as $key=>$order){
					//print_r($order);die;
					$id = $order->id;

					$order_link = JRoute::_("index.php?option=com_digicom&view=order&id=".$id);
					$order_link = '<a class="btn btn-success" href="'.$order_link.'">'.JText::_('COM_DIGICOM_ORDER_DETAILS').'</a>';

					$rec_link = JRoute::_("index.php?option=com_digicom&view=order&layout=invoice&id=".$id."&tmpl=component");
					$rec_link = '<a class="btn btn-info" href="'.$rec_link.'" target="_blank">'.JText::_('COM_DIGICOM_VIEW_AND_PRINT').'</a>';

					// Price
					$order_price = DigiComSiteHelperPrice::format_price($order->amount_paid, $order->currency, true, $configs);
					?>
					<tr>
						<td>
							#<?php echo $order->id; ?>
						</td>
						<td>
							<?php echo $order->order_date;?>
						</td>
						<td>
							<?php
							$labelClass = '';
							if ( strtolower($order->status) === 'active') $labelClass = 'label-success';
							elseif ( strtolower($order->status) === 'pending') $labelClass = 'label-warning';
							?>
							<span class="label <?php echo $labelClass; ?>"><?php echo $order->status; ?></span>
						</td>
						<td>
							<?php echo DigiComSiteHelperPrice::format_price($order->amount, $order->currency, true, $configs); ?>
						</td>
						<td>
							<?php echo $order_price; ?>
						</td>
						<td>
							<?php echo $order_link . ' ' .$rec_link; ?>
						</td>
					</tr>
					<?php
					$i++;
				}
			}else{ ?>
				<tr>
					<td colspan="5">
						<?php echo JText::_('COM_DIGICOM_ORDERS_NO_ORDER_FOUND_NOTICE'); ?>
					</td>
				</tr>
			<?php } ?>

			</tbody>
		</table>

	</form>

	<?php DigiComSiteHelperDigicom::loadModules('digicom_footer','xhtml'); ?>

	<input type="hidden" name="option" value="com_digicom" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="view" value="orders" />
</div>

<?php echo DigiComSiteHelperDigiCom::powered_by(); ?>
