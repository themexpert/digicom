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

<form action="<?php echo JRoute::_('index.php?option=com_digicom&view=configs'); ?>" id="component-form" method="post" name="adminForm" autocomplete="off" class="form-validate form-horizontal digicom-config">
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
						<li<?php echo ($name == 'GENERAL' ? ' class="active"' : '');?>><a href="#<?php echo $name; ?>" data-toggle="tab"><?php echo JText::_($label); ?></a></li>
					<?php endforeach; ?>
				</ul>
				<div class="tab-content">
					<?php $fieldSets = $this->form->getFieldsets(); ?>
					<?php
					foreach ($fieldSets as $name => $fieldSet) :
					?>
						<div class="tab-pane<?php echo ($name == 'GENERAL' ? ' active' : '');?>" id="<?php echo $name; ?>">
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
									<div class="control-group">
										<?php
										if($field->getAttribute('type') == 'spacer') :?>
											<?php echo '<h3>'.JText::_($field->getAttribute('label')).'</h3>'; ?>
										<?php else: ?>
											<div class="control-label">
												<?php echo $field->label; ?>
											</div>
											<div class="controls">
												<?php echo $field->input; ?>
											</div>
										<?php endif; ?>

									</div>
								<?php endforeach; ?>
							<?php else: ?>

								<ul class="nav nav-tabs" id="emailTabs">
									<li class="active"><a href="#COMMON" data-toggle="tab"><?php echo JText::_('COM_DIGICOM_CONFIG_EMAIL_TEMPLATES_COMMON');?></a></li>
									<li><a href="#NEW_ORDER" data-toggle="tab"><?php echo JText::_('COM_DIGICOM_CONFIG_EMAIL_TEMPLATES_NEW_ORDER');?></a></li>
									<li><a href="#COMPLETE_ORDER" data-toggle="tab"><?php echo JText::_('COM_DIGICOM_CONFIG_EMAIL_TEMPLATES_COMPLETE_ORDER');?></a></li>
									<li><a href="#PROCESS_ORDER" data-toggle="tab"><?php echo JText::_('COM_DIGICOM_CONFIG_EMAIL_TEMPLATES_PROCESS_ORDER');?></a></li>
									<li><a href="#CANCEL_ORDER" data-toggle="tab"><?php echo JText::_('COM_DIGICOM_CONFIG_EMAIL_TEMPLATES_CANCEL_ORDER');?></a></li>
								</ul>

								<div class="tab-content">

									<div class="tab-pane active" id="COMMON">
										<?php foreach ($this->form->getGroup('email_settings') as $field) : ?>
											<div class="control-group">
												<?php if($field->getAttribute('type') == 'spacer'):?>
													<?php echo '<p class="'.$field->getAttribute('class','alert').'">'.JText::_($field->getAttribute('label')).'</p>'; ?>
												<?php else: ?>
													<div class="control-label">
														<?php echo $field->label; ?>
													</div>
													<div class="controls">
														<?php echo $field->input; ?>
													</div>
												<?php endif; ?>
											</div>
										<?php endforeach; ?>
									</div>

									<div class="tab-pane" id="NEW_ORDER">
										<?php foreach ($this->form->getGroup('new_order') as $field) :
											$name = $field->getAttribute('name');
											if($name != 'template'):
												?>
												<?php //echo $field->getControlGroup(); ?>
												<div class="control-group">
													<?php if($field->getAttribute('type') == 'spacer'):?>
														<?php echo '<p class="'.$field->getAttribute('class','').'">'.JText::_($field->getAttribute('label')).'</p>'; ?>
													<?php else: ?>
														<div class="control-label">
															<?php echo $field->label; ?>
														</div>
														<div class="controls">
															<?php echo $field->input; ?>
														</div>
													<?php endif; ?>
												</div>
											<?php else: ?>
											<?php echo $field->input; ?>
											<?php endif; ?>
										<?php endforeach; ?>
									</div>
									<div class="tab-pane" id="COMPLETE_ORDER">
										<?php foreach ($this->form->getGroup('complete_order') as $field) :
											$name = $field->getAttribute('name');
											if($name != 'template'):
											?>
												<?php //echo $field->getControlGroup(); ?>
												<div class="control-group">
													<?php if($field->getAttribute('type') == 'spacer'):?>
														<?php echo '<p class="'.$field->getAttribute('class','').'">'.JText::_($field->getAttribute('label')).'</p>'; ?>
													<?php else: ?>
														<div class="control-label">
															<?php echo $field->label; ?>
														</div>
														<div class="controls">
															<?php echo $field->input; ?>
														</div>
													<?php endif; ?>
												</div>
											<?php else: ?>
											<?php echo $field->input; ?>
											<?php endif; ?>
										<?php endforeach; ?>
									</div>
									<div class="tab-pane" id="PROCESS_ORDER">
										<?php foreach ($this->form->getGroup('process_order') as $field) :
											$name = $field->getAttribute('name');
											if($name != 'template'):
											?>
												<?php //echo $field->getControlGroup(); ?>
												<div class="control-group">
													<?php if($field->getAttribute('type') == 'spacer'):?>
														<?php echo '<p class="'.$field->getAttribute('class','').'">'.JText::_($field->getAttribute('label')).'</p>'; ?>
													<?php else: ?>
														<div class="control-label">
															<?php echo $field->label; ?>
														</div>
														<div class="controls">
															<?php echo $field->input; ?>
														</div>
													<?php endif; ?>
												</div>
											<?php else: ?>
											<?php echo $field->input; ?>
											<?php endif; ?>
										<?php endforeach; ?>
									</div>
									<div class="tab-pane" id="CANCEL_ORDER">
										<?php foreach ($this->form->getGroup('cancel_order') as $field) :
											$name = $field->getAttribute('name');
											if($name != 'template'):
											?>
												<?php //echo $field->getControlGroup(); ?>
												<div class="control-group">
													<?php if($field->getAttribute('type') == 'spacer'):?>
														<?php echo '<p class="'.$field->getAttribute('class','').'">'.JText::_($field->getAttribute('label')).'</p>'; ?>
													<?php else: ?>
														<div class="control-label">
															<?php echo $field->label; ?>
														</div>
														<div class="controls">
															<?php echo $field->input; ?>
														</div>
													<?php endif; ?>
												</div>
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
