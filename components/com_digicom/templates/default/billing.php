<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
?>
<div id="digicom" class="dc dc-billing">
	<form name="adminForm" id="adminForm"
		action="<?php echo JRoute::_('index.php?option=com_digicom&view=billing'); ?>"
		method="post"
		class="form-horizontal"
		enctype="multipart/form-data">

		<?php foreach ($this->form->getFieldsets() as $fieldset): // Iterate through the form fieldsets and display each one.?>
			<?php $fields = $this->form->getFieldset($fieldset->name);?>
			<?php if (count($fields)):?>
				<fieldset class="<?php echo $fieldset->name; ?>">
				<?php if (isset($fieldset->label)):// If the fieldset has a label set, display it as the legend.?>
					<legend><?php echo JText::_($fieldset->label);?></legend>
				<?php endif;?>
				<?php foreach ($fields as $field) :// Iterate through the fields in the set and display them.?>
					<?php if ($field->hidden):// If the field is hidden, just display the input.?>
						<?php echo $field->input;?>
					<?php else: ?>
						<div class="control-group group-<?php echo $field->class;?>">
							<div class="control-label">
								<?php echo $field->label; ?>
							</div>
							<div class="controls">
								<?php echo $field->input;?>
							</div>
						</div>
					<?php endif;?>
				<?php endforeach;?>
				</fieldset>
			<?php endif;?>
		<?php endforeach;?>

		<div class="control-group">
			<div class="controls">
				<button type="submit" class="btn btn-primary validate"><?php echo JText::_('COM_DIGICOM_PROFILE_UPDATE');?></button>
				<a class="btn" href="<?php echo JRoute::_('');?>" title="<?php echo JText::_('JCANCEL');?>"><?php echo JText::_('JCANCEL');?></a>
			</div>
		</div>

		<input type="hidden" name="option" value="com_digicom" />
		<input type="hidden" name="view" value="billing" />
		<input type="hidden" name="task" value="profile.billing" />
		<input type="hidden" name="return" value="<?php echo JFactory::getApplication()->input->get("return", base64_encode(JRoute::_("index.php?option=com_digicom&view=cart")), "base64"); ?>" />
		<?php echo JHtml::_('form.token');?>
	</form>

	<?php echo DigiComSiteHelperDigiCom::powered_by(); ?>
	
</div>
