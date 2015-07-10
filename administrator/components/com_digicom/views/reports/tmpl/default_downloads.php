<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined ('_JEXEC') or die ("Go away.");
$app		= JFactory::getApplication();
$input	= $app->input;
$innertab		= $input->get('report','sales_by_date');
?>
<h3><?php echo JText::_('COM_DIGICOM_REPORTS_DOWNLOADS_STATS');?></h3>

<ul class="nav nav-pills">
  <li><a href="<?php echo JRoute::_('index.php?option=com_digicom&view=reports&tab=downloads&report=downloads_top');?>" class="current"><?php echo JText::_('COM_DIGICOM_REPORTS_DOWNLOADS_TOP_DOWNLOADS');?></a></li>
  <li><a href="<?php echo JRoute::_('index.php?option=com_digicom&view=reports&tab=downloads&report=downloads_products');?>" class=""><?php echo JText::_('COM_DIGICOM_REPORTS_DOWNLOADS_PRODUCTS_DOWNLOADS');?></a></li>
  <li><a href="<?php echo JRoute::_('index.php?option=com_digicom&view=reports&tab=downloads&report=downloads_users');?>" class=""><?php echo JText::_('COM_DIGICOM_REPORTS_DOWNLOADS_USERS_DOWNLOADS');?></a></li>
  <li><a href="<?php echo JRoute::_('index.php?option=com_digicom&view=reports&tab=downloads&report=downloads_latest');?>" class=""><?php echo JText::_('COM_DIGICOM_REPORTS_DOWNLOADS_LATEST_DOWNLOADS');?></a></li>
</ul>
<p class="clearfix"></p>

<section class="salesreportsWrapper">
  <?php echo $this->loadTemplate($innertab); ?>
</section>
