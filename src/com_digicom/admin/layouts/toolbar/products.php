<?php
/**
 * @package		DigiCom
 * @copyright	Copyright (c)2010-2015 ThemeXpert
 * @license 	GNU General Public License version 3, or later
 * @author 		ThemeXpert http://www.themexpert.com
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

// Instantiate a new JLayoutFile instance and render the layout
JHtml::_('bootstrap.popover');
?>
<div class="btn-group">
  <a href="#" class="newproduct btn btn-small btn-success" data-toggle="dropdown" aria-expanded="false">
	<span class="icon-cart"></span>
	<?php echo JText::_('COM_DIGICOM_PRODUCTS_TOOLBAR_ADD_NEW'); ?>
	<span class="caret"></span>
  </a>
  <ul class="dropdown-menu dropdown-menu-right" role="menu">
    <li>
		<a class="hasPopover" title="<?php echo JText::_("COM_DIGICOM_PRODUCTS_TOOLBAR_ADD_SINGLE_PRODUCT"); ?>" data-content="<?php echo JHtml::tooltipText('COM_DIGICOM_PRODUCTS_TOOLBAR_ADD_SINGLE_PRODUCT_TIP'); ?>" data-placement="left"
			href="<?php echo Jroute::_('index.php?option=com_digicom&task=product.add&product_type=reguler'); ?>">
			<i class="icon-file-add"></i>
			<?php echo JText::_("COM_DIGICOM_PRODUCTS_TOOLBAR_ADD_SINGLE_PRODUCT"); ?>
		</a>
	</li>
    <li>
		<a class="hasPopover" title="<?php echo JText::_("COM_DIGICOM_PRODUCTS_TOOLBAR_ADD_BUNDLE_PRODUCT"); ?>" data-content="<?php echo JHtml::tooltipText('COM_DIGICOM_PRODUCTS_TOOLBAR_ADD_BUNDLE_PRODUCT_TIP'); ?>" data-placement="left"
			href="<?php echo JRoute::_('index.php?option=com_digicom&task=product.add&product_type=bundle'); ?>">
			<i class="icon-box-add"></i>
			<?php echo JText::_("COM_DIGICOM_PRODUCTS_TOOLBAR_ADD_BUNDLE_PRODUCT"); ?>
		</a>
	</li>
  </ul>
</div>