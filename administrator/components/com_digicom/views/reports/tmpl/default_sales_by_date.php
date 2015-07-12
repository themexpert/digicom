<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined ('_JEXEC') or die ("Go away.");
?>

<div id="chart"></div>

<script type="text/javascript">
var chart = c3.generate({
  bindto: '#chart',
  data: {
    columns: [
      ['Money', 30, 200, 100, 400, 150, 250],
      ['Sales', 50, 20, 10, 40, 15, 25]
    ]
  }
});
</script>
