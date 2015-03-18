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
	<?php echo JText::_('COM_DIGICOM_PRODUCTS_TOOLBAR_ADD_NEW'); ?>
	<span class="caret"></span>
  </a>
  <ul class="dropdown-menu dropdown-menu-right" role="menu">
    <li>
		<a class="hasPopover" title="<?php echo JText::_("COM_DIGICOM_PRODUCTS_TOOLBAR_ADD_SINGLE_PRODUCT"); ?>" data-content="<?php echo JHtml::tooltipText('COM_DIGICOM_PRODUCTS_TOOLBAR_ADD_SINGLE_PRODUCT_TIP'); ?>"
			href="<?php echo Jroute::_('index.php?option=com_digicom&task=product.add&product_type=reguler'); ?>">
			<i class="icon-download"></i>
			<?php echo JText::_("COM_DIGICOM_PRODUCTS_TOOLBAR_ADD_SINGLE_PRODUCT"); ?>
		</a>
	</li>
    <li>
		<a class="hasPopover" title="<?php echo JText::_("COM_DIGICOM_PRODUCTS_TOOLBAR_ADD_BUNDLE_PRODUCT"); ?>" data-content="<?php echo JHtml::tooltipText('COM_DIGICOM_PRODUCTS_TOOLBAR_ADD_BUNDLE_PRODUCT_TIP'); ?>" 
			href="<?php echo JRoute::_('index.php?option=com_digicom&task=product.add&product_type=bundle'); ?>">
			<i class="icon-box-add"></i>
			<?php echo JText::_("COM_DIGICOM_PRODUCTS_TOOLBAR_ADD_BUNDLE_PRODUCT"); ?>
		</a>
	</li>
  </ul>
</div>