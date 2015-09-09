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

$processor 		= $this->session->get('processor');
$table_column = 4;
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


			<form id="cart_form" name="cart_form" method="post" action="<?php echo JRoute::_("index.php?option=com_digicom&view=cart"); ?>">

				<?php echo $this->loadTemplate('items');?>

				<?php echo $this->loadTemplate('price');?>

				<input name="view" type="hidden" id="view" value="cart">
				<input name="task" type="hidden" id="task" value="cart.checkout">
				<input name="returnpage" type="hidden" value="">

			</form>
		</div>

		<?php echo $this->loadTemplate('modals');?>

		<script>
			jQuery('.action-agree').click(function() {
			    jQuery('input[name="agreeterms"]').attr('checked', 'checked');
			});

			<?php
			$agreeterms = JFactory::getApplication()->input->get("agreeterms", "");
			if ($agreeterms != '')
			{
				echo 'jQuery("#agreeterms").attr("checked","checked");';
			}

			if ($processor != '')
			{
				echo 'jQuery("#processor").val("' . $processor . '");';
			}
			?>
			function ShowTermsAlert()
			{
				if (document.cart_form.agreeterms.checked != true)
				{
					jQuery('#termsAlertModal').modal('show');
				}
				else
				{
					return true;
				}
			}

			function ShowPaymentAlert()
			{

				jQuery('#paymentAlertModal').modal('show');
			}

			if(jQuery(window).width() > jQuery("#digicomcarttable").width() && jQuery(window).width() < 550)
			{
				jQuery(".digicom table select").css("width", (jQuery("#digicomcarttable").width()-30)+"px");
			}
		</script>
<?php endif; ?>

	<?php echo DigiComSiteHelperDigicom::powered_by(); ?>
</div>
