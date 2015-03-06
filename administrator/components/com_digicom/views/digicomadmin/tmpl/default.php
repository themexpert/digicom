<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 440 $
 * @lastmodified	$LastChangedDate: 2013-11-20 04:53:55 +0100 (Wed, 20 Nov 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

defined ('_JEXEC') or die ("Go away.");
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
		<h2>DigiCom Dashboard</h2>
		<p class="lead">Revulationary Joomla Extension</p>
		<p class="text-right alert alert-info">Report of the Month <span class="label label-info"><?php echo date('F'); ?></span> </p>

		<div class="row-fluid sales-overview">
			 <div class="span3">
			 	<div class="panel-box">			 		
				 	<span class="icon-briefcase"></span>
				 	<p><strong><?php echo DigiComAdminHelper::format_price($this->totalOrder, $configs->get('currency','USD'), true, $configs);?></strong><br>Total Sale</p>
			 	</div>
			 </div>

			 <div class="span3">
			 	<div class="panel-box">			 		
				 	<span class="icon-cart"></span>
				 	<p><strong><?php echo $this->reportOrders['total']; ?></strong><br>Total Orders</p>
			 	</div>
			 </div>

			 <div class="span3">
			 	<div class="panel-box">
			 		<span class="icon-warning"></span>
				 	<p><strong><?php echo $this->reportOrders['pending']; ?></strong><br>Pending Orders</p>
			 	</div>			 	
			 </div>

			 <div class="span3">
			 	<div class="panel-box">			 		
				 	<span class="icon-users"></span>
				 	<p><strong><?php echo $this->reportCustomer; ?></strong><br>New Customers</p>
			 	</div>
			 </div>
		</div>

		<div class="panel">
			<div class="panel-header clearfix">
				<h3 class="panel-title"><span class="icon-bars"></span> Sales Analytics</h3>
			</div>
			<?php
				$chart = DigiComChart::test();
				$monthlyDay = DigiComChart::getMonthLabelDay();
				
				$monthlyPrice = DigiComChart::getMonthLabelPrice($monthlyDay);
			?>
			<div class="panel-content"> 
				<div><canvas id="myChart" width="945" height="200"></canvas></div>
			  
			  	<script type="text/javascript">
			  		var data = {
					    labels: [<?php echo $monthlyDay; ?>],
					    datasets: [

					        {
					            label: "Monthly Report",
					            fillColor: "rgba(151,187,205,0.2)",
					            strokeColor: "rgba(151,187,205,1)",
					            pointColor: "rgba(151,187,205,1)",
					            pointStrokeColor: "#555",
					            pointHighlightFill: "#555",
					            pointHighlightStroke: "rgba(151,187,205,1)",
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
		
		<div class="row-fluid">
			<div class="span6 panel">
				<div class="panel-header clearfix">
					<h3 class="panel-title"><span class="icon-star-empty"></span><?php echo JText::_('DIGICOM_LATESTORDERS'); ?></h3>
				</div>
				<div class="panel-content">
					<table class="table table-striped">
					<thead>
						<tr>
							<th>#</th>
							<th>Buyer</th>
							<th>Amount</th>
							<th>Date</th>
						</tr>
					</thead>
					<?php foreach($this->latest_orders AS $order) : ?>

						<tr>
							<td>
								<span class="label label-ds hasTip" title="" data-original-title="Order ID">
									<a href="index.php?option=com_digicom&controller=orders&task=show&cid[]=<?php echo $order->id; ?>"><?php echo JText::_('VIEWLICLICORDERID').$order->id; ?></a>
								</span>
							</td>
							<td>
								<strong class="row-title">
									<a href="index.php?option=com_digicom&controller=customers&task=edit&cid[]=<?php echo $order->userid;?>">
										<?php echo $order->firstname.' '.$order->lastname;?>
									</a>
								</strong>
							</td>
							<td><span class="small pull-right"><?php echo DigiComAdminHelper::format_price($order->amount, $order->currency, true, $configs); ?></span></td>
							<td><span class="small"><i class="icon-calendar"></i> <?php echo date("Y-m-d", $order->order_date); ?></span></td>
						</tr>
						
					<?php endforeach; ?>
					</table>
					<a href="index.php?option=com_digicom&controller=orders"><?php echo JText::_('COM_DIGICOM_ALL_ORDERS'); ?></a>
				</div>
			</div>
			<div class="span6 panel">
				<div class="panel-header clearfix">
					<h3 class="panel-title"><span class="icon-download"></span>Most Sold Product</h3>
				</div>
				<div class="panel-content">
					<table class="table table-striped" style="text-align: center;">
						<thead>
						<tr>
							<th>Name</th>
							<th>Type</th>
							<th>Price</th>
							<th>Num Sold</th>
						</tr>
						</thead>
						<?php foreach($this->most_sold AS $product) : ?>
						<tr>
							<td><a href="index.php?option=com_digicom&controller=products&task=edit&cid[]=<?php echo $product->productid;?>"><?php echo $product->name;?></a></td>
							<td><?php echo ($product->package_type =='reguler' ? ucfirst($product->package_type) : JText::sprintf('COM_DIGICOM_BUNDLE',ucfirst($product->package_type)));?></td>
							<td><?php echo $product->price;?></td>
							<td><?php echo $product->total;?></td>
						</tr>
						<?php endforeach; ?>
					</table>
					<a href="index.php?option=com_digicom&controller=products"><?php echo JText::_('DIGICOM_RECENTPROD'); ?></a>
				</div>
			</div>
		</div>
		
	</div>
</form>

<div class="alert alert-info text-center">
	<?php echo JText::_('DIGICOM_CREDITS'); ?>
</div>