<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

$app = JFactory::getApplication();
$template = $app->getTemplate();
$document = JFactory::getDocument();
$app = JFactory::getApplication();
$input = $app->input;
$input->set('layout', 'dgform');
//$document->addStyleSheet("components/com_digicom/assets/css/digicom.css");
// Load the tooltip behavior.
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (document.formvalidator.isValid(document.id('component-form')))
		{
			Joomla.submitform(task, document.getElementById('component-form'));
		}
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_digicom&view=configs'); ?>" id="component-form" method="post" name="adminForm" autocomplete="off" class="form-validate form-horizontal">
	<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container">
	<?php else : ?>
	<div id="j-main-container">
	<?php endif;?>

		<div class="row-fluid">
			<div class="span12">
				<ul class="nav nav-tabs" id="configTabs">
					<?php $fieldSets = $this->form->getFieldsets(); ?>
					<?php foreach ($fieldSets as $name => $fieldSet) : ?>
						<?php $label = empty($fieldSet->label) ? 'COM_DIGICOM_SETTINGS_' . $name . '_FIELDSET_LABEL' : $fieldSet->label; ?>
						<li><a href="#<?php echo $name; ?>" data-toggle="tab"><?php echo JText::_($label); ?></a></li>
					<?php endforeach; ?>
				</ul>
				<div class="tab-content">
					<?php $fieldSets = $this->form->getFieldsets(); ?>
					<?php 
					foreach ($fieldSets as $name => $fieldSet) : 
					?>
						<div class="tab-pane" id="<?php echo $name; ?>">
							<?php
							if($name != 'email_settings'):
							
								if (isset($fieldSet->description) && !empty($fieldSet->description))
								{
									echo '<p class="tab-description">' . JText::_($fieldSet->description) . '</p>';
								}
								?>
								<?php 
								foreach ($this->form->getFieldset($name) as $field) : 
								
								?>
									<?php
									/*
									$fieldname = str_replace( ']', '', str_replace('jform[', '', $field->name) );
									$class = '';
									$rel = '';
									if ($showon = $field->getAttribute('showon'))
									{
										JHtml::_('jquery.framework');
										JHtml::_('script', 'jui/cms.js', false, true);
										$id = $this->form->getFormControl();
										$showon = explode(':', $showon, 2);
										$class = ' showon_' . implode(' showon_', explode(',', $showon[1]));
										$rel = ' rel="showon_' . $id . '[' . $showon[0] . ']"';
									}
									
									
									<?php echo $this->form->getControlGroup('images'); ?>
									<?php foreach ($this->form->getGroup('images') as $field) : ?>
										<?php echo $field->getControlGroup(); ?>
									<?php endforeach; ?>
									
									
									*/
									?>
									<div class="control-group">
										<div class="control-label">
											<?php echo $field->label; ?>
										</div>
										<div class="controls">
											<?php echo $field->input; ?>
										</div>
									</div>										
								<?php endforeach; ?>
							<?php else: ?>
							
								<ul class="nav nav-tabs" id="emailTabs">
									<li class=""><a href="#COMMON" data-toggle="tab">Common</a></li>
									<li><a href="#NEW_ORDER" data-toggle="tab">New Order</a></li>
								</ul>
								
								<div class="tab-content">
								
									<div class="tab-pane" id="COMMON">
										<?php foreach ($this->form->getGroup('email_settings') as $field) : ?>
											<?php echo $field->getControlGroup(); ?>
										<?php endforeach; ?>
									</div>
									
									<div class="tab-pane" id="NEW_ORDER">
										<?php foreach ($this->form->getGroup('new_order') as $field) : 
											$name = $field->getAttribute('name');
											if($name != 'template'):
											?>
											<?php echo $field->getControlGroup(); ?>
											<?php else: ?>
											<?php echo $field->input; ?>
											<?php endif; ?>
										<?php endforeach; ?>
									</div>
									
									
								</div>
								
							<?php endif; ?>
						
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
	<div>
		<input type="hidden" name="id" value="<?php echo $this->component->id; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->component->option; ?>" />
		<input type="hidden" name="view" value="configs" />
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
<script type="text/javascript">
	jQuery('#configTabs a:first').tab('show'); // Select first tab
</script>
<div class="dg-footer">
	<?php echo JText::_('COM_DIGICOM_CREDITS'); ?>
</div>
