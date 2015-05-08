<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 341 $
 * @lastmodified	$LastChangedDate: 2013-10-10 14:28:28 +0200 (Thu, 10 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

defined ('_JEXEC') or die ("Go away.");

$configs = $this->configs;
$document = JFactory::getDocument();
$app = JFactory::getApplication();
$document->addScript( JURI::root(true)."/media/digicom/assets/js/chart.min.js");
$input = $app->input;
?>

<script language="javascript" type="text/javascript">
	function changereport(report){
		if(report == "custom"){
			document.getElementById("td_date").style.display = "block";
		}
		else{
			document.getElementById("td_date").style.display = "none";
			document.adminFormStats.submit();
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_digicom&view=reports'); ?>" method="post" name="adminFormStats" autocomplete="off" class="form-validate form-horizontal">
	<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="">
<?php else : ?>
	<div id="j-main-container" class="">
<?php endif;?>
		<p class="dg-alert dg-alert-danger">
			<?php echo JText::_(' Reports is now on the reactor and we need additional flux capacitor in ThemeXpert to generate 1.21 gigawatts reporting feature. Its coming with next Beta version.'); ?>
		</p>
		<?php echo JHtml::_('bootstrap.startTabSet', 'digicomTab', array('active' => 'sales')); ?>

			<?php echo JHtml::_('bootstrap.addTab', 'digicomTab', 'sales', JText::_('Sales', true)); ?>
			<div class="well">
				<div class="pull-left">
					<h3>Sales Overview</h3>
				</div>
				<div class="pull-right">
					<?php 
						$today = strtotime('today');
						$daybefore30 = strtotime("30 days",$today);
						$startdate = $input->get('startdate', date("Y-m-d", $daybefore30));
						$enddate = $input->get('enddate', date('Y-m-d'));

						echo JText::_("VIEWSTATFROM")."&nbsp;";
						echo JHTML::_("calendar", $startdate, 'startdate', 'startdate', "%Y-%m-%d")."&nbsp;";
						echo JText::_("VIEWSTATTO")."&nbsp;";
						echo JHTML::_("calendar", $enddate, 'enddate', 'enddate', "%Y-%m-%d")."&nbsp;&nbsp;";
						echo '<input type="submit" name="Submit" value="'.JText::_("VIEWSTATGO").'" class="btn" />';
					?>
				</div>
			</div
			<ul class="nav nav-pills">
			  <li class="active">
			  	<a href="#">Daily</a>
			  </li>
			  <li>
			  	<a href="#">Weekly</a>
			  </li>
			  <li>
			  	<a href="#">Monthly</a>
			  </li>
			  <li>
			  	<a href="#">Yearly</a>
			  </li>
			  <li>
			  	<a href="#">Custom</a>
			  </li>
			</ul>

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
				</div>
				<div class="panel-content"> 
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
				
			</div>

					

			<?php echo JHtml::_('bootstrap.endTab'); ?>

			<?php echo JHtml::_('bootstrap.addTab', 'digicomTab', 'downloads', JText::_('Downloads', true)); ?>

			<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JHtml::_('bootstrap.endTabSet'); ?>
		

		<input type="hidden" name="view" value="reports" />
		<input type="hidden" name="option" value="com_digicom" />
		<input type="hidden" name="task" value="showStats" />
	</div>
</form>

<div class="dg-footer">
	<?php echo JText::_('COM_DIGICOM_CREDITS'); ?>
</div>
