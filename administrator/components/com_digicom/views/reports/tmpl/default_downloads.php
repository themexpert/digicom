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
<h3><?php echo JText::_('COM_DIGICOM_REPORTS_DOWNLOADS_STATS');?></h3>

<ul class="nav nav-pills">
  <li><a href="<?php echo JRoute::_('index.php?option=com_digicom&view=reports&tab=downloads&report=top_downloads');?>" class="current"><?php echo JText::_('COM_DIGICOM_REPORTS_DOWNLOADS_TOP_DOWNLOADS');?></a></li>
  <li><a href="<?php echo JRoute::_('index.php?option=com_digicom&view=reports&tab=downloads&report=products_downloads');?>" class=""><?php echo JText::_('COM_DIGICOM_REPORTS_DOWNLOADS_PRODUCTS_DOWNLOADS');?></a></li>
  <li><a href="<?php echo JRoute::_('index.php?option=com_digicom&view=reports&tab=downloads&report=users_downloads');?>" class=""><?php echo JText::_('COM_DIGICOM_REPORTS_DOWNLOADS_USERS_DOWNLOADS');?></a></li>
  <li><a href="<?php echo JRoute::_('index.php?option=com_digicom&view=reports&tab=downloads&report=latest_downloads');?>" class=""><?php echo JText::_('COM_DIGICOM_REPORTS_DOWNLOADS_LATEST_DOWNLOADS');?></a></li>
</ul>
