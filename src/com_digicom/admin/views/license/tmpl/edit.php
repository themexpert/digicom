<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$app = JFactory::getApplication();
$document = JFactory::getDocument();
$input = $app->input;
?>
<div id="digicom" class="dc digicom">
<form action="index.php" method="post" name="adminForm" id="adminForm">
<?php if (!empty( $this->sidebar)) : ?>
		<div id="j-sidebar-container" class="">
			<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="">
	<?php else : ?>
		<div id="j-main-container" class="">
	<?php endif;?>
			<div class="row-fluid">
				<div class="span12">

					<div class="form-horizontal">
						<?php echo $this->form->getControlGroup('id'); ?>
						<?php echo $this->form->getControlGroup('licenseid'); ?>
						<?php echo $this->form->getControlGroup('orderid'); ?>
						<?php echo $this->form->getControlGroup('userid'); ?>
						<?php echo $this->form->getControlGroup('productid'); ?>
						<?php echo $this->form->getControlGroup('purchase'); ?>
						<?php echo $this->form->getControlGroup('expires'); ?>
						<?php echo $this->form->getControlGroup('active'); ?>
					</div>
					
				</div>
			</div>

	</div>

	<input type="hidden" name="option" value="com_digicom" />
	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="license" />
	<?php echo JHtml::_('form.token'); ?>
</form>
<?php
	// echo JHtml::_(
	// 	'bootstrap.renderModal',
	// 	'videoTutorialModal',
	// 	array(
	// 		'url' => 'https://www.youtube-nocookie.com/embed/oJ9MmXisEU8?list=PL5eH3TQ0wUTZXKs632GyKMzGVkxdfPB4f&amp;showinfo=0',//&amp;autoplay=1
	// 		'title' => JText::_('COM_DIGICOM_CUSTOMER_VIDEO_INTRO'),
	// 		'height' => '400px',
	// 		'width' => '1280'
	// 	)
	// );
?>
<div class="dg-footer">
	<?php echo JText::_('COM_DIGICOM_CREDITS'); ?>
</div>
</div>
