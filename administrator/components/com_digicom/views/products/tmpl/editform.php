<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('jquery.framework');
JHtml::_('jquery.ui');
JHtml::_('jquery.ui', array('sortable'));
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');

$app = JFactory::getApplication();
$input = $app->input;
$document = JFactory::getDocument();
$document->addStyleSheet("components/com_digicom/assets/css/redactor.css");
$document->addScript("components/com_digicom/assets/js/redactor.min.js");

?>
<script type="text/javascript">
	
	jQuery(function(){
		jQuery(".useredactor").redactor();
		jQuery(".redactor_useredactor").css("height","200px");
	});


	Joomla.submitbutton = function(task)
	{
		if (task == 'cancel' || document.formvalidator.isValid(document.id('item-form')))
		{
			Joomla.submitform(task, document.getElementById('item-form'));
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_digicom&controller=products&task=edit&cid[]=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
<?php if (!empty( $this->sidebar)) : ?>
		<div id="j-sidebar-container" class="">
			<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="">
	<?php else : ?>
		<div id="j-main-container" class="">
	<?php endif;?>
	<?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>
	
	<div class="form-inline form-inline-header">
		<?php echo $this->form->getControlGroup('price'); ?>
	</div>
	
	<div class="form-horizontal">
		<div class="row-fluid">
			<div class="span9">
			<?php echo JHtml::_('bootstrap.startTabSet', 'digicomTab', array('active' => 'general')); ?>

				<?php echo JHtml::_('bootstrap.addTab', 'digicomTab', 'general', JText::_('COM_DIGICOM_PRODUCT_CONTENT', true)); ?>
				
				<?php echo $this->form->getControlGroup('images'); ?>
				
				<div class="control-group ">
					<?php echo $this->form->getLabel('description'); ?>
					<?php echo $this->form->getInput('description'); ?>
				</div>
				<div class="control-group ">
					<?php echo $this->form->getLabel('fulldescription'); ?>
					<?php echo $this->form->getInput('fulldescription'); ?>
				</div>
				<?php echo JHtml::_('bootstrap.endTab'); ?>
				<?php // Do not show the publishing options if the edit form is configured not to. ?>
				
				<?php 
				if($this->item->product_type == 'reguler'): ?>
				
				<?php echo JHtml::_('bootstrap.addTab', 'digicomTab', 'files', JText::_('COM_DIGICOM_FILES_SELECTION', true)); ?>
					<?php echo JLayoutHelper::render('edit.files', $this->item->file); ?>
				<?php echo JHtml::_('bootstrap.endTab'); ?>
				<?php else: ?>
				<?php echo JHtml::_('bootstrap.addTab', 'digicomTab', 'bundle', JText::_('COM_DIGICOM_BUNDLE_SELECTION', true)); ?>
					<?php echo JLayoutHelper::render('edit.bundle', array($this->item,$this->cats)); ?>
				<?php echo JHtml::_('bootstrap.endTab'); ?>
				
				<?php endif; ?>
			<?php echo JHtml::_('bootstrap.endTabSet'); ?>
			</div>
			<div class="span3">
				<?php echo JLayoutHelper::render('edit.sidebar', $this); ?>
			</div>
		</div>
		
		<input type="hidden" name="option" value="com_digicom" />
		<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="controller" value="products" />
		<input type="hidden" name="state_filter" value="<?php echo JRequest::getVar("state_filter", "-1"); ?>" />
		<input type="hidden" name="return" value="<?php echo $input->getCmd('return'); ?>" />		
		<?php echo JHtml::_('form.token'); ?>


	</div>
</div>
</form>
