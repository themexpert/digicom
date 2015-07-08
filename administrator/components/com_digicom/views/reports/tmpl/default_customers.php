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
<h3><?php echo JText::_('COM_DIGICOM_REPORTS_CUSTOMERS_STATS');?></h3>

<ul class="nav nav-pills">
  <li><a href="<?php echo JRoute::_('index.php?option=com_digicom&view=reports&tab=customers&report=new_customers');?>" class="current"><?php echo JText::_('COM_DIGICOM_REPORTS_CUSTOMERS_NEW_CUSTOMERS');?></a></li>
  <li><a href="<?php echo JRoute::_('index.php?option=com_digicom&view=reports&tab=customers&report=top_customers');?>" class=""><?php echo JText::_('COM_DIGICOM_REPORTS_CUSTOMERS_TOP_CUSTOMERS');?></a></li>
</ul>
