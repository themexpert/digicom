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
<h3><?php echo JText::_('COM_DIGICOM_REPORTS_SALES_STATS');?></h3>
<ul class="nav nav-pills">
  <li><a href="<?php echo JRoute::_('index.php?option=com_digicom&view=reports&tab=sales&report=sales_by_date');?>" class="current"><?php echo JText::_('COM_DIGICOM_REPORTS_SALES_DATE');?></a></li>
  <li><a href="<?php echo JRoute::_('index.php?option=com_digicom&view=reports&tab=sales&report=sales_by_product');?>" class=""><?php echo JText::_('COM_DIGICOM_REPORTS_SALES_PRODUCTS');?></a></li>
  <li><a href="<?php echo JRoute::_('index.php?option=com_digicom&view=reports&tab=sales&report=sales_by_category');?>" class=""><?php echo JText::_('COM_DIGICOM_REPORTS_SALES_CATEGORY');?></a></li>
  <li><a href="<?php echo JRoute::_('index.php?option=com_digicom&view=reports&tab=sales&report=coupon_usage');?>" class=""><?php echo JText::_('COM_DIGICOM_REPORTS_SALES_COUPONS_DATE');?></a></li>
</ul>
