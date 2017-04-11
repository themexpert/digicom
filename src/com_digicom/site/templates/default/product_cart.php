<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;
?>
<?php if ($this->configs->get('catalogue',0) == '0' and !$this->item->hide_public) : ?>
	<div class="dc-addtocart-bar">
		<form name="prod" class="form" id="product-form" action="<?php echo JRoute::_('index.php?option=com_digicom&view=cart');?>" method="post" style="width:100%;">
			<div class="form-group<?php echo ($configs->get('show_quantity',0) == 1 ? " with-qnty " : ' '); ?>no-padding no-margin">

				<?php if($configs->get('show_quantity',0) == "1") {	?>
					<input data-digicom-id="quantity_<?php echo $this->item->id; ?>" type="number" name="qty" min="1" class="dc-product-qnty form-control" value="1" size="2" placeholder="<?php echo JText::_('COM_DIGICOM_QUANTITY'); ?>">
				<?php } ?>

				<?php if($configs->get('afteradditem',0) == "2") {	?>
					<div type="button" class="btn btn-success btn-md btn-block btn-cart" onclick="Digicom.addtoCart(<?php echo $this->item->id; ?>,'<?php echo JRoute::_("index.php?option=com_digicom&view=cart"); ?>');"><?php echo JText::_("COM_DIGICOM_ADD_TO_CART");?></div>
				<?php }else { ?>
					<button type="submit" class="btn btn-success btn-lg btn-block"> <?php echo JText::_('COM_DIGICOM_ADD_TO_CART'); ?></button>
					<?php } ?>
			</div>

			<input type="hidden" name="option" value="com_digicom"/>
			<input type="hidden" name="view" value="cart"/>
			<input type="hidden" name="task" value="cart.add"/>
			<input type="hidden" name="pid" value="<?php echo $this->item->id; ?>"/>
		</form>
	</div>
<?php endif; ?>