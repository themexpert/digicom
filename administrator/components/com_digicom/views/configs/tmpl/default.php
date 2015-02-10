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

$app = JFactory::getApplication();
$template = $app->getTemplate();
$document = JFactory::getDocument();
$document->addStyleSheet("components/com_digicom/assets/css/digicom.css");
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
<form action="<?php echo JRoute::_('index.php?option=com_digicom&controller=configs'); ?>" id="component-form" method="post" name="adminForm" autocomplete="off" class="form-validate form-horizontal">
	<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container" class="span12">
<?php endif;?>

		<div class="row-fluid">
			<div class="span12">
				<ul class="nav nav-tabs" id="configTabs">
					<?php $fieldSets = $this->form->getFieldsets(); ?>
					<?php foreach ($fieldSets as $name => $fieldSet) : ?>
						<?php $label = empty($fieldSet->label) ? 'COM_CONFIG_' . $name . '_FIELDSET_LABEL' : $fieldSet->label; ?>
						<li><a href="#<?php echo $name; ?>" data-toggle="tab"><?php echo JText::_($label); ?></a></li>
					<?php endforeach; ?>
				</ul>
				<div class="tab-content">
					<?php $fieldSets = $this->form->getFieldsets(); ?>
					<?php foreach ($fieldSets as $name => $fieldSet) : ?>
						<div class="tab-pane" id="<?php echo $name; ?>">
							<?php
							if (isset($fieldSet->description) && !empty($fieldSet->description))
							{
								echo '<p class="tab-description">' . JText::_($fieldSet->description) . '</p>';
							}
							?>
							<?php foreach ($this->form->getFieldset($name) as $field) : ?>
								<?php
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
								?>
								<div class="control-group<?php echo $class; ?>"<?php echo $rel; ?>>
									<?php if (!$field->hidden && $name != "permissions") : ?>
										<div class="control-label">
											<?php echo $field->label; ?>
										</div>
									<?php endif; ?>
									<div class="<?php if ($name != "permissions") : ?>controls<?php endif; ?>">
										<?php echo $field->input; ?>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
	<div>
		<input type="hidden" name="id" value="<?php echo $this->component->id; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->component->option; ?>" />
		<input type="hidden" name="controller" value="configs" />
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
<script type="text/javascript">
	jQuery('#configTabs a:first').tab('show'); // Select first tab
</script>
