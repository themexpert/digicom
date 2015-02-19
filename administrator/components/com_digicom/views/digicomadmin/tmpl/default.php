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
$document = JFactory::getDocument();

$document->addScript( JURI::root(true)."/media/digicom/assets/js/chart.min.js");
////$document->addStyleSheet("components/com_digicom/assets/css/digicom.css");
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
		
		<div class="row-fluid sales-overview">
			 <div class="span3">
			 	<div class="panel-box">			 		
				 	<span class="icon-briefcase"></span>
				 	<p><strong>$20000.89</strong><br>Total Sale</p>
			 	</div>
			 </div>

			 <div class="span3">
			 	<div class="panel-box">			 		
				 	<span class="icon-cart"></span>
				 	<p><strong>200</strong><br>Total Orders</p>
			 	</div>
			 </div>

			 <div class="span3">
			 	<div class="panel-box">
			 		<span class="icon-warning"></span>
				 	<p><strong>200</strong><br>Pending Orders</p>
			 	</div>			 	
			 </div>

			 <div class="span3">
			 	<div class="panel-box">			 		
				 	<span class="icon-users"></span>
				 	<p><strong>200</strong><br>Total Customers</p>
			 	</div>
			 </div>
		</div>

		<div class="panel">
			<div class="panel-header clearfix">
				<h3 class="panel-title"><span class="icon-bars"></span> Sales Analytics</h3>
				<div class="pull-right">
					<div class="btn-group">
						<a href="#" class="btn">Day</a>
						<a href="#" class="btn">Month</a>
						<a href="#" class="btn">Year</a>
					</div>
				</div>
			</div>
			<div class="panel-content">
				<ul class="nav nav-charts clearfix" id="myTab">
				  <li class="active"><a href="#sales">Sales</a></li>
				  <li><a href="#profile">Profile</a></li>
				  <li><a href="#messages">Messages</a></li>
				  <li><a href="#settings">Settings</a></li>
				</ul>
 
				<div class="tab-content">
				  <div class="tab-pane active" id="sales">
				  	<canvas id="myChart" width="945" height="200"></canvas>
				  	<script type="text/javascript">
				  		var data = {
						    labels: ["January", "February", "March", "April", "May", "June", "July"],
						    datasets: [

						        {
						            label: "My Second dataset",
						            fillColor: "rgba(151,187,205,0.2)",
						            strokeColor: "rgba(151,187,205,1)",
						            pointColor: "rgba(151,187,205,1)",
						            pointStrokeColor: "#fff",
						            pointHighlightFill: "#fff",
						            pointHighlightStroke: "rgba(151,187,205,1)",
						            data: [28, 48, 40, 19, 86, 27, 90]
						        }
						    ]
						};
						var ctx = document.getElementById("myChart").getContext("2d");
						var myLineChart = new Chart(ctx).Line(data);
				  	</script>
				  </div>
				  <div class="tab-pane" id="profile">
				  	
				  </div>
				  <div class="tab-pane" id="messages"></div>
				  <div class="tab-pane" id="settings"></div>
				</div>
			</div>
		</div>
		
	</div>
</form>

<div class="alert alert-info text-center">
	<?php echo JText::_('DIGICOM_CREDITS'); ?>
</div>