<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined ('_JEXEC') or die ("Go away.");
$app      = JFactory::getApplication();
$input    = $app->input;
$this->innertab = $input->get('report','sales_by_date');
$this->range = $input->get('range','7day');
?>

<ul class="nav nav-pills">
  <li<?php echo ($this->innertab == 'sales_by_date' ? ' class="active"' : '');?>>
    <a href="<?php echo JRoute::_('index.php?option=com_digicom&view=reports&tab=sales&report=sales_by_date');?>" class="current"><?php echo JText::_('COM_DIGICOM_REPORTS_SALES_DATE');?></a>
  </li>
  <li<?php echo ($this->innertab == 'sales_by_product' ? ' class="active"' : '');?>>
    <a href="<?php echo JRoute::_('index.php?option=com_digicom&view=reports&tab=sales&report=sales_by_product');?>" class=""><?php echo JText::_('COM_DIGICOM_REPORTS_SALES_PRODUCTS');?></a>
  </li>
  <!--
  <li<?php echo ($this->innertab == 'sales_by_category' ? ' class="active"' : '');?>>
    <a href="<?php echo JRoute::_('index.php?option=com_digicom&view=reports&tab=sales&report=sales_by_category');?>" class=""><?php echo JText::_('COM_DIGICOM_REPORTS_SALES_CATEGORY');?></a>
  </li>
  <li<?php echo ($this->innertab == 'sales_coupon_usage' ? ' class="active"' : '');?>>
    <a href="<?php echo JRoute::_('index.php?option=com_digicom&view=reports&tab=sales&report=sales_coupon_usage');?>" class=""><?php echo JText::_('COM_DIGICOM_REPORTS_SALES_COUPONS_DATE');?></a>
  </li>
  -->
</ul>
<p class="clearfix"></p>

<div class="well well-small">
  <h3 class="module-title nav-header"><?php echo JText::_('COM_DIGICOM_REPORTS_SALES_STATS');?></h3>
  <ul class="nav nav-tabs">
    <li<?php echo ($this->range == 'year' ? ' class="active"' : '');?>>
      <a href="<?php echo JRoute::_('index.php?option=com_digicom&view=reports&tab=sales&report=sales_by_date&range=year');?>" class="current">
        <?php echo JText::_('Year');?>
      </a>
    </li>
    <li<?php echo ($this->range == 'last_month' ? ' class="active"' : '');?>>
      <a href="<?php echo JRoute::_('index.php?option=com_digicom&view=reports&tab=sales&report=sales_by_date&range=last_month');?>" class="current">
        <?php echo JText::_('Last Month');?>
      </a>
    </li>
    <li<?php echo ($this->range == 'month' ? ' class="active"' : '');?>>
      <a href="<?php echo JRoute::_('index.php?option=com_digicom&view=reports&tab=sales&report=sales_by_date&range=month');?>" class="current">
        <?php echo JText::_('This Month');?>
      </a>
    </li>
    <li<?php echo ($this->range == '7day' ? ' class="active"' : '');?>>
      <a href="<?php echo JRoute::_('index.php?option=com_digicom&view=reports&tab=sales&report=sales_by_date&range=7day');?>" class="current">
        <?php echo JText::_('Last 7 Days');?>
      </a>
    </li>
    <li<?php echo ($this->range == 'custom' ? ' class="custom active"' : ' class="custom"');?>>
      Custom:
      <form name="adminFormStatsRange" method="post" action="<?php echo JRoute::_('index.php?option=com_digicom&view=reports'); ?>" class="form-inline" style="display: inline-block;">

        <div>
          <input type="text" size="9" placeholder="yyyy-mm-dd" value="" name="start_date" class="range_datepicker from hasDatepicker" id="dp1438523397955">
          <input type="text" size="9" placeholder="yyyy-mm-dd" value="" name="end_date" class="range_datepicker to hasDatepicker" id="dp1438523397956">
          <input type="submit" class="button" value="Go">

          <input type="hidden" name="option" value="com_digicom">
          <input type="hidden" name="view" value="reports">
          <input type="hidden" name="tab" value="sales">
          <input type="hidden" name="report" value="sales_by_date">
          <input type="hidden" name="range" value="custom">

        </div>
      </form>
    </li>

  </ul>
  <div class="report-content">
    <section class="salesreportsWrapper">
      <?php echo $this->loadTemplate($this->innertab); ?>
    </section>
  </div>
</div>

