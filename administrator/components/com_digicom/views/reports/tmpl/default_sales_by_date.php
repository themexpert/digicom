<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined ('_JEXEC') or die ("Go away.");

$configs = $this->configs;
$monthlyDay = DigiComHelperChart::getMonthLabelDay();
$monthlyPrice = DigiComHelperChart::getMonthLabelPrice($monthlyDay);
?>

<div><canvas id="myChart" height="200"></canvas></div>

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
