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
?>
<div id="digicom">

	<?php if(count($this->items) == 0): ?>
		<div class="alert alert-warning">
			<?php echo JText::_("COM_DIGICOM_CART_IS_EMPTY_NOTICE"); ?>
		</div>
	<?php else: ?>
			<?php echo $this->loadTemplate('steps');?>

		<div class="digi-cart">
			<?php
			$user = JFactory::getUser();
			if($user->id != "0"){
			?>
			<div class="row-fluid">
				<div class="span12" style="text-align:right;vertical-align:bottom;">
					<?php echo JText::sprintf("COM_DIGICOM_CART_LOGGED_IN_AS",$user->name); ?>
				</div>
			</div>
			<?php } ?>


			<form data-digicom-id="cart_form" name="cart_form" method="post" action="<?php echo JRoute::_("index.php?option=com_digicom&view=cart"); ?>">

				<?php echo $this->loadTemplate('items');?>

				<?php echo $this->loadTemplate('price');?>

				<input name="view" type="hidden" value="cart">
				<input name="task" type="hidden" data-digicom-id="task" value="cart.checkout">
				<input name="returnpage" type="hidden" value="">

			</form>
		</div>

		<?php echo $this->loadTemplate('modals');?>

<?php endif; ?>

	<?php echo DigiComSiteHelperDigicom::powered_by(); ?>
</div>
