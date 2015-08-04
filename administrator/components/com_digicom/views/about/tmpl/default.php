<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

$mosConfig_absolute_path = JPATH_ROOT;
$document = JFactory::getDocument();
$app = JFactory::getApplication();
$input = $app->input;
$input->set('layout', 'dgtabs');
?>

<form action="<?php echo JRoute::_('index.php?option=com_digicom&controller=about'); ?>" method="post" name="adminForm" autocomplete="off" class="form-validate form-horizontal">
	<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="">
<?php else : ?>
	<div id="j-main-container" class="">
<?php endif;?>

		<?php echo JHtml::_('bootstrap.startTabSet', 'digicomTab', array('active' => 'about')); ?>

			<?php echo JHtml::_('bootstrap.addTab', 'digicomTab', 'about', JText::_('COM_DIGICOM_ABOUT_TAB_TITLE_ABOUT', true)); ?>

			<div class="about-dglogo">
				<a href="#">Digicom Logo</a>
			</div>

			<div class="about-content">
				<?php echo JText::_("COM_DIGICOM_ABOUT_DIGICOM_DETAILS"); ?>
			</div>

			<?php echo JHtml::_('bootstrap.endTab'); ?>

			<?php echo JHtml::_('bootstrap.addTab', 'digicomTab', 'systeminfo', JText::_('COM_DIGICOM_ABOUT_TAB_TITLE_SYSTEMINFO', true)); ?>
				<?php echo $this->loadTemplate('systeminfo'); ?>
			<?php echo JHtml::_('bootstrap.endTab'); ?>


		<?php echo JHtml::_('bootstrap.endTabSet'); ?>


	</div>
</form>
