<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');

$app = JFactory::getApplication();
$input = $app->input;

$assoc = JLanguageAssociations::isEnabled();

$input->set('layout', 'dgform');

JFactory::getDocument()->addScriptDeclaration('
	Joomla.submitbutton = function(task)
	{
		if (task == "category.cancel" || document.formvalidator.isValid(document.getElementById("adminForm")))
		{
			' . $this->form->getField("description")->save() . '
			Joomla.submitform(task, document.getElementById("adminForm"));
		}
	};
');

$isNew = ($this->item->id == 0);

?>

<form action="<?php echo JRoute::_('index.php?option=com_digicom&extension=' . $input->getCmd('extension', 'com_digicom') . '&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
	<div id="digicom" class="dc digicom">
		<div class="tx-sidebar"><?php echo $this->sidebar; ?></div>
		<div class="tx-main">
			<div class="page-header">
				<h1><?php echo JText::_('COM_DIGICOM_CATEGORY_BASE_' . ($isNew ? 'ADD' : 'EDIT') . '_TITLE') ?></h1>
				<p>In this page you can edit category</p>
				<ul class="nav nav-tabs" role="tablist">
			    <li class="active"><a href="#general" data-toggle="tab"><?php echo JText::_('COM_DIGICOM_PRODUCT_GENERAL_SETTINGS', true); ?></a></li>
			    <li><a href="#assoc" data-toggle="tab"><?php echo JText::_('JGLOBAL_FIELDSET_ASSOCIATIONS', true); ?></a></li>
			  </ul>
			</div> <!-- .page-header -->
			<div class="page-content">
				<div class="row">
					<div class="col-md-8">
						<div class="tab-content">
							<div class="tab-pane active" id="general">
								<div class="panel panel-default">
									<div class="panel-heading">Category Settings</div>
									<div class="panel-body">
										<?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>
										<?php echo JLayoutHelper::render('edit.cat_params', $this); ?>
										<?php echo $this->form->getLabel('description'); ?>
										<?php echo $this->form->getInput('description'); ?>
									</div>
								</div>
							</div> <!-- .tab-pane -->
							<div class="tab-pane" id="assoc">
								<div class="panel panel-default">
									<div class="panel-heading">Permission</div>
									<div class="panel-body">
										<?php if ($assoc) : ?>
											<?php echo $this->loadTemplate('associations'); ?>
										<?php endif; ?>		
										<?php if ($this->canDo->get('core.admin')) : ?>
											<?php echo $this->form->getInput('rules'); ?>
										<?php endif; ?>
									</div>
								</div>
							</div>
						</div> <!-- .tab-content -->
					</div>
					<div class="col-md-4">
						<div class="panel panel-default">
							<div class="panel-heading">Publishing Options</div>
							<div class="panel-body"><?php echo JLayoutHelper::render('sidebars.sidebar_cat', $this); ?></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php echo $this->form->getInput('extension'); ?>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
