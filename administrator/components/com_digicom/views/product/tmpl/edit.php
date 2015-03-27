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
		if(task== 'product.save' || task == 'product.save2new' || task== 'product.apply'){
			var product_type = jQuery("input[name='jform[product_type]']").val();
			if(product_type == 'bundle'){
				var product_source = jQuery("input:radio[name='jform[bundle_source]']:checked").val();
				switch(product_source){
					case 'category':
						var bundleinfo = jQuery('#jform_bundle_category').val();
						break;
					case 'paroduct':
					default:
						var bundleinfo = jQuery("input[id^='product_include_id']").val();
						break;
				}
				if(bundleinfo === null || bundleinfo === undefined || bundleinfo === '' ){
					message = Joomla.JText._('JLIB_FORM_FIELD_INVALID');
					error = {};
					error.error = [];
					label = '<?php echo JText::_('COM_DIGICOM_PRODUCT_BUNDLE_SOURCE_INFO_REQUIRED'); ?>';
					error.error[0] = message + label;
					
					Joomla.renderMessages(error);
					return false;
				}
			}
		}
		if (task == 'product.cancel' || document.formvalidator.isValid(document.id('item-form')))
		{
			Joomla.submitform(task, document.getElementById('item-form'));
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_digicom&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
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
			<?php echo JHtml::_('bootstrap.startTabSet', 'digicomTab', array('active' => 'general')); ?>

				<?php echo JHtml::_('bootstrap.addTab', 'digicomTab', 'general', JText::_('COM_DIGICOM_PRODUCT_GENERAL_SETTINGS', true)); ?>
				
				<div class="row-fluid">
					<div class="span4">
						<div class="form-inline form-inline-header product-image">
							<?php echo $this->form->getControlGroup('images'); ?>
						</div>						
					</div>
					<div class="span8">
						<?php echo JLayoutHelper::render('edit.title_alias_price', $this); ?>

						<div class="product-short-desc">
							<?php echo $this->form->getLabel('description'); ?>
							<?php echo $this->form->getInput('description'); ?>
						</div>
					</div>
					
				</div>
				
				
				<div class="control-group ">
					<?php echo $this->form->getLabel('fulldescription'); ?>
					<?php echo $this->form->getInput('fulldescription'); ?>
				</div>
				<?php echo JHtml::_('bootstrap.endTab'); ?>
				<?php // Do not show the publishing options if the edit form is configured not to. ?>
				
				<?php 
				$product_type = $this->item->product_type;
				if(empty($product_type)){
					$product_type = $this->form->getData()->get('product_type');
				}

				if($product_type == 'reguler'): ?>
				
					<?php echo JHtml::_('bootstrap.addTab', 'digicomTab', 'files', JText::_('COM_DIGICOM_PRODUCT_REGULAR_FILES_SELECTION', true)); ?>
						<?php echo JLayoutHelper::render('edit.files', $this->item); ?>
					<?php echo JHtml::_('bootstrap.endTab'); ?>

				<?php else: ?>

					<?php echo JHtml::_('bootstrap.addTab', 'digicomTab', 'bundle', JText::_('COM_DIGICOM_PRODUCT_BUNDLE_FILES_SELECTION', true)); ?>
						<?php echo JLayoutHelper::render('edit.bundle', $this); ?>
					<?php echo JHtml::_('bootstrap.endTab'); ?>
				
				<?php endif; ?>
			<?php echo JHtml::_('bootstrap.endTabSet'); ?>
			</div>
			<div class="span3">
				<?php echo JLayoutHelper::render('edit.sidebar', $this); ?>
			</div>
		</div>
		
		<input type="hidden" name="option" value="com_digicom" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="jform[product_type]" value="<?php echo $product_type; ?>" />
		<input type="hidden" name="view" value="product" />
		<input type="hidden" name="state_filter" value="<?php echo JRequest::getVar("state_filter", "-1"); ?>" />
		<input type="hidden" name="return" value="<?php echo $input->getCmd('return'); ?>" />		
		<?php echo JHtml::_('form.token'); ?>

	</div>
</div>
</form>

<div class="dg-footer">
	<?php echo JText::_('COM_DIGICOM_CREDITS'); ?>
</div>
