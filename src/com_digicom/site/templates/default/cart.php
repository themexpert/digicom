<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;
JHtml::_('formbehavior.chosen', 'select');
$user = JFactory::getUser();
?>
<div id="digicom" class="dc dc-cart">

	<?php if(count($this->items) == 0): ?>
		<p class="alert alert-warning">
			<?php echo JText::_("COM_DIGICOM_CART_IS_EMPTY_NOTICE"); ?>
		</p>
	<?php else: ?>

		<?php echo $this->loadTemplate('steps');?>

		<?php if($user->id != "0"): ?>
		<div class="well well-sm" style="text-align:right;vertical-align:bottom;">
			<?php echo JText::sprintf("COM_DIGICOM_CART_LOGGED_IN_AS",$user->name); ?>
		</div>
		<?php endif; ?>

		<form
			data-digicom-id="cart_form"
			name="dcForm"
			id="dcForm"
			class="form dc-cart-items"
			method="post"
			action="<?php echo JRoute::_("index.php?option=com_digicom&view=cart"); ?>">

			<?php echo $this->loadTemplate('items');?>

			<?php echo $this->loadTemplate('price');?>

			<input name="option" type="hidden" value="com_digicom">
			<input name="view" type="hidden" value="cart">
			<input name="task" type="hidden" data-digicom-id="task" value="cart.checkout">

		</form>

		<?php echo $this->loadTemplate('modals');?>

	<?php endif; ?>

	<?php echo DigiComSiteHelperDigicom::powered_by(); ?>

</div>
