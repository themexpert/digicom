<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

$configs = $this->configs;
$document = JFactory::getDocument();
$document->addScript( JURI::root(true)."/media/digicom/assets/js/chart.min.js");
?>
<form action="<?php echo JRoute::_('index.php?option=com_digicom'); ?>" class="clearfix" method="post" name="adminForm" id="adminForm">
<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="">
<?php else : ?>
	<div id="j-main-container" class="">
<?php endif;?>
		<p class="text-right alert alert-info">Report of the Month <span class="label label-info"><?php echo date('F'); ?></span> </p>

		<div class="row-fluid sales-overview">
			<div class="span3">
				<div class="panel-box">
					<span class="icon-briefcase"></span>
					<p><strong><?php echo DigiComHelperDigiCom::format_price($this->totalOrder, $configs->get('currency','USD'), true, $configs);?></strong><br><?php echo JText::_('COM_DIGICOM_REPORTS_TOTAL_SALES'); ?></p>
				</div>
			</div>

			<div class="span3">
				<div class="panel-box">
					<span class="icon-cart"></span>
					<p><strong><?php echo $this->reportOrders['total']; ?></strong><br><?php echo JText::_('COM_DIGICOM_REPORTS_TOTAL_ORDERS'); ?></p>
				</div>
			</div>

			<div class="span3">
				<div class="panel-box">
					<span class="icon-warning"></span>
					<p><strong><?php echo $this->reportOrders['pending']; ?></strong><br><?php echo JText::_('COM_DIGICOM_REPORTS_PENDING_ORDERS'); ?></p>
				</div>
			</div>

			<div class="span3">
				<div class="panel-box">
					<span class="icon-users"></span>
					<p><strong><?php echo $this->reportCustomer; ?></strong><br><?php echo JText::_('COM_DIGICOM_REPORTS_NEW_CUSTOMERS'); ?></p>
				</div>
			</div>
		</div>


		<div class="row-fluid">
			<div class="span12">

				<div class="panel">
					<div class="panel-header clearfix">
						<h3 class="panel-title"><span class="icon-bars"></span> <?php echo JText::_('COM_DIGICOM_REPORTS_SALES_ANALYTICS'); ?></h3>
					</div>
					<?php
					$monthlyDay = DigiComHelperChart::getMonthLabelDay();

					$monthlyPrice = DigiComHelperChart::getMonthLabelPrice($monthlyDay);
					?>
					<div class="panel-content">
						<div><canvas id="myChart" height="150"></canvas></div>

						<script type="text/javascript">
							var data = {
								labels: [<?php echo $monthlyDay; ?>],
								datasets: [

									{
										label: "Monthly Report",
										fillColor: "#e6f3f9",
										strokeColor: "#1562AD",
										pointColor: "#1562AD",
										pointStrokeColor: "#1562AD",
										pointHighlightFill: "#e6f3f9",
										pointHighlightStroke: "#1562AD",
										data: [<?php echo $monthlyPrice; ?>]
									}
								]
							};
							options ={
								animation: true,
								scaleShowLabels: true,
								responsive: true,
								tooltipTemplate: "<%if (label){%><%}%><%= value %> <?php echo $configs->get('currency','USD')?>",
							}
							var ctx = document.getElementById("myChart").getContext("2d");
							var myLineChart = new Chart(ctx).Line(data,options);
						</script>
					</div>

				</div>
			</div>
		</div>

		<div class="row-fluid">
			<div class="span6 panel">
				<div class="panel-header clearfix">
					<h3 class="panel-title"><span class="icon-star-empty"></span><?php echo JText::_('COM_DIGICOM_REPORTS_LATEST_ORDERS'); ?></h3>
				</div>
				<div class="panel-content">
					<table class="table table-striped">
					<thead>
						<tr>
							<th><?php echo JText::_('COM_DIGICOM_ID');?></th>
							<th><?php echo JText::_('COM_DIGICOM_STATUS');?></th>
							<th><?php echo JText::_('COM_DIGICOM_BUYER');?></th>
							<th><?php echo JText::_('COM_DIGICOM_AMOUNT');?></th>
							<th><?php echo JText::_('JDATE'); ?></th>
						</tr>
					</thead>
					<?php foreach($this->latest_orders AS $order) : ?>

						<tr>
							<td>
								<span class="hasTip" title="" data-original-title="Order ID">
									<a href="<?php echo JRoute::_('index.php?option=com_digicom&task=order.edit&id='.$order->id); ?>"><?php echo $order->id; ?></a>
								</span>
							</td>
							<td>
								<span class="label label-ds"><?php echo $order->status; ?></span>
							</td>
							<td>
								<strong class="row-title">
									<a href="<?php echo JRoute::_('index.php?option=com_digicom&task=customer.edit&id='.$order->userid);?>">
										<?php echo $order->firstname.' '.$order->lastname;?>
									</a>
								</strong>
							</td>
							<td><span class="small pull-right"><?php echo DigiComHelperDigiCom::format_price($order->amount, $order->currency, true, $configs); ?></span></td>
							<td><span class="small"><?php echo date("Y-m-d", $order->order_date); ?></span></td>
						</tr>
						
					<?php endforeach; ?>
					</table>
					<a href="index.php?option=com_digicom&view=orders"><?php echo JText::_('COM_DIGICOM_ALL_ORDERS'); ?></a>
				</div>
			</div>
			<div class="span6 panel">
				<div class="panel-header clearfix">
					<h3 class="panel-title"><span class="icon-download"></span><?php echo JText::_('COM_DIGICOM_REPORTS_MOST_SOLD_PRODUCTS'); ?></h3>
				</div>
				<div class="panel-content">
					<table class="table table-striped" style="text-align: center;">
						<thead>
						<tr>
							<th><?php echo JText::_('COM_DIGICOM_NAME'); ?></th>
							<th><?php echo JText::_('COM_DIGICOM_PRODUCTS_TYPE'); ?></th>
							<th><?php echo JText::_('COM_DIGICOM_PRICE'); ?></th>
							<th><?php echo JText::_('COM_DIGICOM_TOTAL_SOLD'); ?></th>
						</tr>
						</thead>
						<?php foreach($this->most_sold AS $product) : ?>
						<tr>
							<td><a href="<?php echo JRoute::_('index.php?option=com_digicom&task=product.edit&id='.$product->productid);?>"><?php echo $product->name;?></a></td>
							<td><span class="label label-ds"><?php echo ($product->package_type =='reguler' ? ucfirst($product->package_type) : JText::sprintf('COM_DIGICOM_BUNDLE',ucfirst($product->package_type)));?></span></td>
							<td><?php echo DigiComHelperDigiCom::format_price($product->price, $configs->get('currency','USD'), true, $configs);?></td>
							<td><?php echo $product->total;?></td>
						</tr>
						<?php endforeach; ?>
					</table>
					<a href="<?php echo JRoute::_('index.php?option=com_digicom&view=products');?>"><?php echo JText::_('COM_DIGICOM_RECENT_PRODUCTS'); ?></a>
				</div>
			</div>
		</div>
		
	</div>
</form>
<?php 
	echo JHtml::_(
		'bootstrap.renderModal',
		'videoTutorialModal',
		array(
			'url' => 'https://www.youtube-nocookie.com/embed/4rp239TFgXc?list=PL5eH3TQ0wUTZXKs632GyKMzGVkxdfPB4f&amp;showinfo=0',//&amp;autoplay=1
			'title' => JText::_('COM_DIGICOM_DASHBOARD_VIDEO_INTRO'),
			'height' => '400px',
			'width' => '1280'
		)
	); 
?>
<div class="dg-footer">
	<?php echo JText::_('COM_DIGICOM_CREDITS'); ?>
</div>
