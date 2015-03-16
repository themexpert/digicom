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
$input->set('layout', 'dgform');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'cancel' || document.formvalidator.isValid(document.id('item-form')))
		{
			Joomla.submitform(task, document.getElementById('item-form'));
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_digicom&controller=category&task=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
<?php if (!empty( $this->sidebar)) : ?>
		<div id="j-sidebar-container" class="">
			<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="">
	<?php else : ?>
		<div id="j-main-container" class="">
	<?php endif;?>
			
			<div class="form-horizontal">
				<div class="row-fluid">
					<div class="span9">
						<div class="panel-box">
							<?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>
						<?php echo $this->form->getControlGroup('image'); ?>
						
						<div class="control-group ">
							<?php echo $this->form->getLabel('description'); ?>
							<?php echo $this->form->getInput('description'); ?>
						</div>
						<div class="control-group ">
							<?php echo $this->form->getLabel('fulldescription'); ?>
							<?php echo $this->form->getInput('fulldescription'); ?>
						</div>
						<?php // Do not show the publishing options if the edit form is configured not to. ?>
						</div>
						
					</div>
					<div class="span3">
						<?php echo JLayoutHelper::render('edit.sidebar', $this); ?>
					</div>
				</div>
				
				<input type="hidden" name="option" value="com_digicom" />
				<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="controller" value="category" />
				<input type="hidden" name="return" value="<?php echo $input->getCmd('return'); ?>" />		
				<?php echo JHtml::_('form.token'); ?>


			</div>
		</div>
</form>
