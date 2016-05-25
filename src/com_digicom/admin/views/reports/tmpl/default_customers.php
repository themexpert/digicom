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
$innertab		= $input->get('report','new_customers');
?>

<ul class="nav nav-pills">
  <li<?php echo ($innertab == 'customers_new' ? ' class="active"' : '');?>>
    <a href="<?php echo JRoute::_('index.php?option=com_digicom&view=reports&tab=customers&report=customers_new');?>" class="current"><?php echo JText::_('COM_DIGICOM_REPORTS_CUSTOMERS_NEW_CUSTOMERS');?></a>
  </li>
  <li<?php echo ($innertab == 'customers_top' ? ' class="active"' : '');?>>
    <a href="<?php echo JRoute::_('index.php?option=com_digicom&view=reports&tab=customers&report=customers_top');?>" class=""><?php echo JText::_('COM_DIGICOM_REPORTS_CUSTOMERS_TOP_CUSTOMERS');?></a>
  </li>
</ul>
<p class="clearfix"></p>

<h3><?php echo JText::_('COM_DIGICOM_REPORTS_CUSTOMERS_STATS');?></h3>

<section class="salesreportsWrapper">
  <?php echo $this->loadTemplate($innertab); ?>
</section>
