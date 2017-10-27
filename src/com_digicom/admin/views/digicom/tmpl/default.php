<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$configs = $this->configs;
$document = JFactory::getDocument();
$document->addScript( JURI::root(true)."/media/com_digicom/js/chart.min.js");

$monthlyDay = DigiComHelperChart::getMonthLabelDay();
$monthlyPrice = DigiComHelperChart::getMonthLabelPrice($monthlyDay);
?>

<form action="<?php echo JRoute::_('index.php?option=com_digicom'); ?>" class="clearfix" method="post" name="adminForm" id="adminForm">
	<div id="digicom" class="dc digicom">
		<div class="tx-sidebar">
			<?php echo $this->sidebar; ?>
		</div>
		<div class="tx-main">
			<div class="page-header">
				<h1>Dashboard</h1>
				<p><?php echo JText::sprintf('COM_DIGICOM_REPORTS_OF_THE_MONTH', date('F'));?></p>
			</div>
			<div class="page-content">
				<?php //checking if installed joomla version is less  3.0
				if ( version_compare( JVERSION, '3.4', '<' ) == 1){
				?>
				<p class="alert alert-error nomargin-top">
					<?php echo JText::sprintf('COM_DIGICOM_ERROR_JVERSION_NEED_UPGADE', JVERSION);?>
				</p>
				<?php } ?>
				<div class="row">
					<div class="col-md-3">
						<div class="panel panel-default text-center">
							<div class="panel-body">
								<h3><?php echo DigiComHelperDigiCom::format_price($this->totalOrder, $configs->get('currency','USD'), true, $configs, 1);?></h3>
							</div>
							<div class="panel-footer"><?php echo JText::_('COM_DIGICOM_REPORTS_TOTAL_SALES'); ?></div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="panel panel-default text-center">
							<div class="panel-body">
								<h3>
									<?php echo $this->reportOrders['total']; ?> 
									<small>(
										<span class="text-success"><?php echo $this->reportOrders['paid']; ?> <?php echo JText::_('COM_DIGICOM_PAID'); ?></span> / <span class="muted"><?php echo $this->reportOrders['free']; ?> <?php echo JText::_('COM_DIGICOM_FREE'); ?></span>
									)</small>
								</h3>
							</div>
							<div class="panel-footer"><?php echo JText::_('COM_DIGICOM_REPORTS_TOTAL_ORDERS'); ?></div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="panel panel-default text-center">
							<div class="panel-body"><h3><?php echo $this->reportOrders['pending']; ?></h3></div>
							<div class="panel-footer"><?php echo JText::_('COM_DIGICOM_REPORTS_PENDING_ORDERS'); ?></div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="panel panel-default text-center">
							<div class="panel-body"><h3><?php echo $this->reportCustomer; ?></h3></div>
							<div class="panel-footer"><?php echo JText::_('COM_DIGICOM_REPORTS_NEW_CUSTOMERS'); ?></div>
						</div>
					</div>
				</div> <!-- sales overview 4 box end -->
				<div class="row">
					<div class="col-md-12">
						<h4><?php echo JText::_('COM_DIGICOM_REPORTS_SALES_ANALYTICS'); ?></h4>
						<div class="panel panel-default">
							<div class="panel-body">
								<div><canvas id="myChart" height="80px"></canvas></div>
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
				<div class="row">
					<div class="col-md-6">
						<div class="panel panel-default">
							<div class="panel-heading">
								<?php echo JText::_('COM_DIGICOM_REPORTS_LATEST_ORDERS'); ?>
								<div class="pull-right"><a href="index.php?option=com_digicom&view=orders"><?php echo JText::_('COM_DIGICOM_ALL_ORDERS'); ?></a></div>
							</div>
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
								<tbody>
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
													<?php echo $order->name;?>
												</a>
											</strong>
										</td>
										<td><span class="small pull-right"><?php echo DigiComHelperDigiCom::format_price($order->amount, $order->currency, true, $configs); ?></span></td>
										<td><span class="small"><?php echo $order->order_date; ?></span></td>
									</tr>
								<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					</div>
					<div class="col-md-6">
						<div class="panel panel-default">
							<div class="panel-heading">
								 <?php echo JText::_('COM_DIGICOM_REPORTS_MOST_SOLD_PRODUCTS'); ?>
								 <div class="pull-right"><a href="<?php echo JRoute::_('index.php?option=com_digicom&view=products');?>"><?php echo JText::_('COM_DIGICOM_RECENT_PRODUCTS'); ?></a></div>
							</div>
							<table class="table table-striped" style="text-align: center;">
								<thead>
								<tr>
									<th width="15%"><?php echo JText::_('COM_DIGICOM_NAME'); ?></th>
									<th><?php echo JText::_('COM_DIGICOM_PRODUCTS_TYPE'); ?></th>
									<th><?php echo JText::_('COM_DIGICOM_PRICE'); ?></th>
									<th><?php echo JText::_('COM_DIGICOM_TOTAL_SOLD'); ?></th>
								</tr>
								</thead>
								<tbody>
								<?php foreach($this->most_sold AS $product) : ?>
								<tr>
									<td><a href="<?php echo JRoute::_('index.php?option=com_digicom&task=product.edit&id='.$product->productid);?>"><?php echo $product->name;?></a></td>
									<td><span class="label label-ds"><?php echo ($product->package_type =='reguler' ? ucfirst($product->package_type) : JText::sprintf('COM_DIGICOM_BUNDLE',ucfirst($product->package_type)));?></span></td>
									<td><?php echo DigiComHelperDigiCom::format_price($product->price, $configs->get('currency','USD'), true, $configs);?></td>
									<td><?php echo $product->total;?></td>
								</tr>
								<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<footer class="dg-footer text-right">
					<?php echo JText::_('COM_DIGICOM_CREDITS'); ?>
				</footer>
			</div> <!-- .page-content end -->
		</div> <!-- #tx-main end -->
	</div> <!-- #digicom end -->
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
