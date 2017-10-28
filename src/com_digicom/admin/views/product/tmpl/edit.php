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
JHtml::_('formbehavior.chosen');

$app = JFactory::getApplication();
$input = $app->input;
$input->set('layout', 'edit');

// Get product type
$product_type = $this->item->product_type;
if(empty($product_type)){
	$product_type = $this->form->getData()->get('product_type');
}
// New/edit 
$isNew		= ($this->item->id == 0);
?>
<script type="text/javascript">

	Joomla.submitbutton = function(task)
	{
		setFormSubmitting();
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
		if (task == 'product.cancel' || document.formvalidator.isValid(document.id('adminForm')))
		{
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
	}

var formSubmitting = false;
var setFormSubmitting = function() { formSubmitting = true; };

window.onload = function() {
    window.addEventListener("beforeunload", function (e) {
        var confirmationMessage = 'It looks like you have been editing something. ';
        confirmationMessage += 'If you leave before saving, your changes will be lost.';

        if (formSubmitting) {
            return undefined;
        }

        (e || window.event).returnValue = confirmationMessage; //Gecko + IE
        return confirmationMessage; //Gecko + Webkit, Safari, Chrome etc.
    });
};
</script>

<form action="<?php echo JRoute::_('index.php?option=com_digicom&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
	<div id="digicom" class="dc digicom">
		<div class="tx-sidebar">
			<?php echo $this->sidebar; ?>
		</div>
		<div class="tx-main">
			<div class="page-header">
				<?php if($isNew):?>
					<h1><?php echo JText::_('COM_DIGICOM_MANAGER_PRODUCT_NEW_TITLE'); ?></h1>
				<?php else:?>
					<h1><?php echo JText::_('COM_DIGICOM_MANAGER_PRODUCT_EDIT_TITLE'); ?></h1>
				<?php endif; ?>
				<ul class="nav nav-tabs" role="tablist">
			    <li class="active"><a href="#general" data-toggle="tab"><?php echo JText::_('COM_DIGICOM_PRODUCT_GENERAL_SETTINGS', true); ?></a></li>
			    <li><a href="#files" data-toggle="tab">Files</a></li>
			  </ul>
			</div> <!-- .page-header end -->
			<div class="page-content">
				<div class="tab-content">
					<div class="tab-pane active" id="general">
						<div class="row">
							<div class="col-md-8">
								<div class="panel panel-default">
									<div class="panel-heading">Basic</div>
									<div class="panel-body">
										<?php echo JLayoutHelper::render('edit.title_alias_price', $this); ?>
									</div>
								</div>
								<div class="panel panel-default">
									<div class="panel-heading">Content</div>
									<div class="panel-body">
										<div class="product-short-desc control-group">
											<?php echo $this->form->getLabel('introtext'); ?>
											<?php echo $this->form->getInput('introtext'); ?>
										</div>
										<div class="product-full-desc control-group ">
											<?php echo $this->form->getLabel('fulltext'); ?>
											<?php echo $this->form->getInput('fulltext'); ?>
										</div>
									</div>
								</div>
							</div> <!-- general > main -->
							<div class="col-md-4">
								<div class="panel panel-default">
									<div class="panel-heading">
										Product Images
										<ul class="nav nav-tabs" role="tablist">
									    <li class="active"><a href="#intro-image" data-toggle="tab"><?php echo JText::_('Thumbnail', true); ?></a></li>
									    <li><a href="#full-image" data-toggle="tab"><?php echo JText::_('Full Image', true); ?></a></li>
									  </ul>
									</div> <!-- panel heading -->
									<div class="panel-body">
										<div class="tab-content">
											<div class="tab-pane active" id="intro-image">
												<div class="product-image">
														<?php echo $this->form->getControlGroup('image_intro'); ?>
												</div>
											</div> <!-- #intro-image -->
											<div class="tab-pane" id="full-image">
												<div class="product-image">
														<?php echo $this->form->getControlGroup('image_full'); ?>
												</div>
											</div> <!-- #full-image -->
										</div>
									</div> <!-- panel-body -->
								</div> <!-- sidebar > image panel -->
								<div class="panel panel-default">
									<div class="panel-heading">Publishing Options</div>
									<div class="panel-body">
										<?php echo JLayoutHelper::render('sidebars.sidebar', $this); ?>
									</div>
								</div>
							</div> <!-- general > sidebar -->
						</div>
					</div> <!-- general tab-pane -->
					<div class="tab-pane" id="files">
						<?php if($product_type == 'reguler') : ?>
						<div class="panel panel-default">
							<div class="panel-heading"><?php echo JText::_('COM_DIGICOM_PRODUCT_REGULAR_FILES_SELECTION', true);?></div>
							<div class="panel-body"><?php echo JLayoutHelper::render('edit.files', $this); ?></div>
						</div> <!-- .panel regular files -->
						<?php else: ?>
						<div class="panel panel-default">
							<div class="panel-heading"><?php echo JText::_('COM_DIGICOM_PRODUCT_BUNDLE_FILES_SELECTION', true);?></div>
							<div class="panel-body"><?php echo JLayoutHelper::render('edit.bundle', $this); ?></div>
						</div> <!-- .panel regular files -->
						<?php endif; ?>
						<!-- Params -->
						<?php echo $this->loadTemplate('params'); ?>
					</div>
				</div>
			</div> <!-- .page-content end -->
		</div> <!-- .tx-main -->
	</div> <!-- #digicom -->
	
	<input type="hidden" name="option" value="com_digicom" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="jform[product_type]" value="<?php echo $product_type; ?>" />
	<input type="hidden" name="view" value="product" />
	<input type="hidden" name="state_filter" value="<?php echo JRequest::getVar("state_filter", "-1"); ?>" />
	<input type="hidden" name="return" value="<?php echo $input->getCmd('return'); ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
<?php
	echo JHtml::_(
		'bootstrap.renderModal',
		'videoTutorialModal',
		array(
			'url' => 'https://www.youtube-nocookie.com/embed/pIfktnNwNsU?list=PL5eH3TQ0wUTZXKs632GyKMzGVkxdfPB4f&amp;showinfo=0',//&amp;autoplay=1
			'title' => JText::_('COM_DIGICOM_ABOUT_PRODUCT_USE_VIDEO'),
			'height' => '400px',
			'width' => '1280'
		)
	);
?>
