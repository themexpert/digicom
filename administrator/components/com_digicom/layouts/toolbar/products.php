<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_messages
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
// Instantiate a new JLayoutFile instance and render the layout
JHtml::_('bootstrap.popover');
?>
<div class="btn-group">
  <a href="#" class="newproduct btn btn-small btn-primary" data-toggle="dropdown" aria-expanded="false">
	<span class="icon-cart"></span>
	<?php echo JText::_('DIGICOM_ADDNEWPRODUCT'); ?>
	<span class="caret"></span>
  </a>
  <ul class="dropdown-menu dropdown-menu-right" role="menu">
    <li>
		<a class="hasPopover" title="<?php echo JText::_("DIGI_DONWNLOADABLE"); ?>" data-content="<?php echo JHtml::tooltipText(JText::_('DIGI_DONWNLOADABLE_TIP')); ?>"
			href="<?php echo Jroute::_('index.php?option=com_digicom&task=product.add&product_type=reguler'); ?>">
			<i class="icon-download"></i>
			<?php echo JText::_("DIGI_DONWNLOADABLE"); ?>
		</a>
	</li>
    <li>
		<a class="hasPopover" title="<?php echo JText::_("DIGI_PACKAGE_NO_UPLOAD"); ?>" data-content="<?php echo JHtml::tooltipText(JText::_('COM_DIGICOM_PRODPACKAGE_TIP')); ?>" 
			href="<?php echo JRoute::_('index.php?option=com_digicom&task=product.add&product_type=bundle'); ?>">
			<i class="icon-box-add"></i>
			<?php echo JText::_("DIGI_PACKAGE_NO_UPLOAD"); ?>
		</a>
	</li>
  </ul>
</div>