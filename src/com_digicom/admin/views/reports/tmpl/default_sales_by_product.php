<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined ('_JEXEC') or die ("Go away.");
$app    		= JFactory::getApplication();
$input  		= $app->input;
$configs		= $this->configs;
$this->range 	= $input->get('range','7day');
$session  = JFactory::getSession();
$productid = $session->get( 'productid', '' );
$rangeDays = DigiComHelperChart::getRangeDayLabel($this->range);
$rangePrices = DigiComHelperChart::getRangePricesLabel($this->range,$rangeDays,true);
$num_of_sale = DigiComHelperChart::getRangeTotalNoSale($this->range);
// echo $num_of_sale;die;
?>
<div class="row-fluid">
	<div class="span3">
		<div class="well well-small">
			<h3 class="nav-header">Search Product</h3>

			<form name="adminFormStatsRange" method="post" action="<?php echo JRoute::_('index.php?option=com_digicom&view=reports'); ?>" class="form-horizontal" style="display: inline-block;">
				<div class="control-group">
					<?php
					$productsList = DigiComHelperDigiCom::getProductsList();
					$arr = array();
					$arr[] = JHTML::_('select.option', '', JText::_('JSELECT') );
					foreach ($productsList as $key => $value) {
						$arr[] = JHTML::_('select.option', $value->id, $value->name );
					}

					echo JHTML::_('select.genericlist', $arr, 'productid', null, 'value', 'text', $productid);
					?>
				</div>
			 	<div class="control-group">
					<input type="submit" class="btn" value="Show">

					<input type="hidden" name="option" value="com_digicom">
					<input type="hidden" name="view" value="reports">
					<input type="hidden" name="tab" value="sales">
					<input type="hidden" name="report" value="sales_by_product">
					<input type="hidden" name="range" value="<?php echo $this->range; ?>">
				</div>

			</form>

			<div class="panel-box dc-block">
			  <h3><?php echo JText::sprintf('COM_DIGICOM_TOTAL_SALE_AMOUNT', $num_of_sale); ?></h3>
			</div>
		</div>
	</div>
	<div class="span9">
		<div><canvas id="myChart" width="400" height="150"></canvas></div>

		<script type="text/javascript">
		  var data = {
		    labels: [<?php echo $rangeDays; ?>],
		    datasets: [

		      {
		        label: "Range Report",
		        fillColor: "#e6f3f9",
		        strokeColor: "#1562AD",
		        pointColor: "#1562AD",
		        pointStrokeColor: "#1562AD",
		        pointHighlightFill: "#e6f3f9",
		        pointHighlightStroke: "#1562AD",
		        data: [<?php echo $rangePrices; ?>]
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
