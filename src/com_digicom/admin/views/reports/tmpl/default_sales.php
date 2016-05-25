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
$session  = JFactory::getSession();
$input    = $app->input;
$this->innertab = $input->get('report','sales_by_date');
$this->range = $input->get('range','7day');
$this->start_date = $input->get('start_date','');
$this->end_date = $input->get('end_date','');

if($this->innertab == 'sales_by_product'){
  
  $productid = $input->get('productid','');
  
  if(empty($productid)){
    $productid = $session->get( 'productid', '' );  
  }
  $session->set( 'productid', $productid );
}
?>

<div class="navbar">
  <div class="navbar-inner">
    <ul class="nav">
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
  </div>
</div>

<p class="clearfix"></p>

<div class="well well-small">
  
      <ul class="nav nav-pills">
        <li<?php echo ($this->range == 'year' ? ' class="active"' : '');?>>
          <a href="<?php echo JRoute::_('index.php?option=com_digicom&view=reports&tab=sales&report='.$this->innertab.'&range=year');?>" class="current">
            <?php echo JText::_('Year');?>
          </a>
        </li>
        <li<?php echo ($this->range == 'last_month' ? ' class="active"' : '');?>>
          <a href="<?php echo JRoute::_('index.php?option=com_digicom&view=reports&tab=sales&report='.$this->innertab.'&range=last_month');?>" class="current">
            <?php echo JText::_('Last Month');?>
          </a>
        </li>
        <li<?php echo ($this->range == 'month' ? ' class="active"' : '');?>>
          <a href="<?php echo JRoute::_('index.php?option=com_digicom&view=reports&tab=sales&report='.$this->innertab.'&range=month');?>" class="current">
            <?php echo JText::_('This Month');?>
          </a>
        </li>
        <li<?php echo ($this->range == '7day' ? ' class="active"' : '');?>>
          <a href="<?php echo JRoute::_('index.php?option=com_digicom&view=reports&tab=sales&report='.$this->innertab.'&range=7day');?>" class="current">
            <?php echo JText::_('Last 7 Days');?>
          </a>
        </li>
        <li<?php echo ($this->range == 'custom' ? ' class="custom active"' : ' class="custom"');?> style="padding: 4px 10px 2px;">
          Custom:
          <form name="adminFormStatsRange" method="post" action="<?php echo JRoute::_('index.php?option=com_digicom&view=reports'); ?>" class="form-inline" style="display: inline-block;">

            <div>
              <?php 
              echo JHTML::calendar($this->start_date,'start_date', 'start_date', '%Y-%m-%d',array('size'=>'8','maxlength'=>'10','class'=>'validate',));
              echo JHTML::calendar($this->end_date,'end_date', 'end_date', '%Y-%m-%d',array('size'=>'8','maxlength'=>'10','class'=>'validate',));
              ?>
              <input type="submit" class="btn" value="Go">

              <input type="hidden" name="option" value="com_digicom">
              <input type="hidden" name="view" value="reports">
              <input type="hidden" name="tab" value="sales">
              <input type="hidden" name="report" value="<?php echo $this->innertab;?>">
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

