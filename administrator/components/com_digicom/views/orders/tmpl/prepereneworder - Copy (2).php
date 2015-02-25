<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 341 $
 * @lastmodified	$LastChangedDate: 2013-10-10 14:28:28 +0200 (Thu, 10 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

defined ('_JEXEC') or die ("Go away.");

JHtml::_('behavior.multiselect');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
?>

<form action="<?php echo JRoute::_('index.php?option=com_digicom&controller=orders'); ?>" method="post" name="adminForm" id="item-form" class="form-validate">

	<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="">
	<?php else : ?>
	<div id="j-main-container" class="">
	<?php endif;?>
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_DIGICOM_NEW_ORDER_ID');?></legend>
			<p class="alert alert-info"><?php echo JText::_('HEADER_ORDERS_ADD');?></p>
			
			<div class="form-horizontal">
				
				<?php echo $this->form->getControlGroup('userid'); ?>
			
				<?php echo $this->form->getControlGroup('order_date'); ?>
								
				<?php echo $this->form->getControlGroup('currency'); ?>
				
				<?php echo $this->form->getControlGroup('status'); ?>
				
				<?php echo $this->form->getControlGroup('promocode'); ?>
				
				<?php echo $this->form->getControlGroup('amount_paid'); ?>
				
				<?php echo $this->form->getControlGroup('processor'); ?>
				
				<?php echo $this->form->getControlGroup('published'); ?>
				
			</div>
			
		</fieldset>
	</div>
</form>