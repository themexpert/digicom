<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

JHtml::_('jquery.framework');
JHTML::_('behavior.formvalidation');
$configs = JComponentHelper::getComponent('com_digicom')->params;

$document = JFactory::getDocument();
if($vars->custom_email==""){
	$email = JText::_('PLG_DIGICOM_PAY_OFFLINE_AFTER_PAYMENT_CONTACT_NO_ADDRS');
}
else{
	$email = $vars->custom_email;
}
$preview_image = $this->params->get('preview_image');
$buy_image = $this->params->get('buy_image');
?>

<div class="digicom-payment-form">
	<form action="<?php echo $vars->url; ?>" name="adminForm" id="adminForm" class="form-validate form-horizontal"  method="post">
		<div>
			<?php if($preview_image): ?>
			<div class="control-group text-center center align-center">
				<img src="<?php echo $preview_image; ?>" />
			</div>
			<?php endif; ?>
			<div class="control-group">
				<label for="cardfname" class="control-label"><?php  echo JText::_( 'PLG_DIGICOM_PAY_OFFLINE_ORDER_INFORMATION_LABEL' );?></label>
				<div class="controls">	<?php  echo JText::sprintf( 'PLG_DIGICOM_PAY_OFFLINE_ORDER_INFO', $vars->custom_name);?></div>
			</div>

			<div class="control-group">
				<label for="cardlname" class="control-label"><?php echo JText::_( 'PLG_DIGICOM_PAY_OFFLINE_PAYABLE_AMOUNT' ); ?></label>
				<div class="controls"><span class="label label-success"><?php echo DigiComSiteHelperPrice::format_price( $vars->amount, $configs->get('currency','USD'), true, $configs ); ?></span></div>
			</div>

			<div class="control-group">
				<label for="cardlname" class="control-label"><?php echo JText::_( 'PLG_DIGICOM_PAY_OFFLINE_COMMENT' ); ?></label>
				<div class="controls"><textarea id='comment' name='comment' class="inputbox required" rows='3' maxlength='135' size='28'></textarea></div>
			</div>

			<div class="control-group">
				<label for="cardaddress1" class="control-label"><?php echo JText::_( 'PLG_DIGICOM_PAY_OFFLINE_AFTER_PAYMENT_CONTACT_LABEL' ) ?></label>
				<div class="controls"><?php  echo $email;?>
					<input type='hidden' name='mail_addr' value="<?php echo $email;?>" />
				</div>
			</div>

			<div class="form-actions">

				<input type='hidden' name='option' value="com_digicom" />
				<input type='hidden' name='task' value="cart.processPayment" />
				<input type='hidden' name='processor' value="offline" />

				<input type='hidden' name='order_id' value="<?php echo $vars->order_id;?>" />
				<input type='hidden' name="total" value="<?php //echo sprintf('%02.2f',$vars->amount) ?>" />
				<input type="hidden" name="user_id" size="10" value="<?php echo $vars->user_id;?>" />
				<input type='hidden' name='return' value="<?php echo $vars->return;?>" >
				<input type="hidden" name="plugin_payment_method" value="onsite" />

				<?php if($buy_image) : ?>
					<input type="image" name="submit"
						src="<?php echo $this->params->get('buy_image'); ?>" 
						value="<?php echo JText::_('PLG_DIGICOM_PAY_OFFLINE_SUBMIT'); ?>" 
						alt="<?php echo $this->params->get('title'); ?>" 
						class="btn"
						/>
				<?php else: ?>
					<input type="submit" name="submit"
						class="btn btn-success btn-large btn-lg"
						value="<?php echo JText::_('PLG_DIGICOM_PAY_OFFLINE_SUBMIT'); ?>" 
						alt="<?php echo $this->params->get('title'); ?>" />
				<?php endif; ?>
			</div>
		</div>
		<?php echo JHtml::_('form.token');?>
	</form>
</div>
