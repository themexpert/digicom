<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;
// Add specific helper files for html generation
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
$input = JFactory::getApplication()->input;
?>
<?php if($input->get('format','html') != 'attachment'): ?>
<p class="text-right">
	<a class="btn" href="<?php echo JRoute::_('index.php?option=com_digicom&view=about&tmpl=component&format=attachment'); ?>">
		<?php echo JText::_('COMDIGICOM_ABOUT_SYSTEMINFO_DOWNLOAD_BTN'); ?>
	</a>
</p>
<div class="clearfix"></div>
<pre>
<?php endif; ?>

<?php echo JText::_('COM_DIGICOM_ABOUT_SYSTEMINFO_SYSTEM_DIGICOM'); ?>
<?php echo "\n"; ?>
===================================
<?php echo "\n"; ?>
<?php echo JText::_('COM_DIGICOM_ABOUT_SYSTEMINFO_DIGICOM_VERSION'); ?><?php echo "\n"; ?>
	<?php //echo $this->info['digicom']->version; ?>


<?php echo JText::_('COM_DIGICOM_ABOUT_SYSTEMINFO_SYSTEM_INFORMATION'); ?>
<?php echo "\n"; ?>
===================================
<?php echo "\n"; ?>
<?php echo JText::_('COM_DIGICOM_ABOUT_SYSTEMINFO_PHP_BUILT_ON'); ?><?php echo "\n"; ?>
	<?php echo $this->info['php']; ?>
<?php echo "\n"; ?>
<?php echo JText::_('COM_DIGICOM_ABOUT_SYSTEMINFO_DATABASE_VERSION'); ?><?php echo "\n"; ?>
	<?php echo $this->info['dbversion']; ?>
<?php echo "\n"; ?>
<?php echo JText::_('COM_DIGICOM_ABOUT_SYSTEMINFO_DATABASE_COLLATION'); ?><?php echo "\n"; ?>
	<?php echo $this->info['dbcollation']; ?>
<?php echo "\n"; ?>
<?php echo JText::_('COM_DIGICOM_ABOUT_SYSTEMINFO_PHP_VERSION'); ?><?php echo "\n"; ?>
	<?php echo $this->info['phpversion']; ?>
<?php echo "\n"; ?>
<?php echo JText::_('COM_DIGICOM_ABOUT_SYSTEMINFO_WEB_SERVER'); ?><?php echo "\n"; ?>
	<?php echo !empty($this->info['server']) ? $this->info['server'] : JText::_('COM_DIGICOM_ABOUT_SYSTEMINFO_NA'); ?>
<?php echo "\n"; ?>
<?php echo JText::_('COM_DIGICOM_ABOUT_SYSTEMINFO_WEBSERVER_TO_PHP_INTERFACE'); ?><?php echo "\n"; ?>
	<?php echo $this->info['sapi_name']; ?>
<?php echo "\n"; ?>
<?php echo JText::_('COM_DIGICOM_ABOUT_SYSTEMINFO_JOOMLA_VERSION'); ?><?php echo "\n"; ?>
	<?php echo $this->info['version']; ?>
<?php echo "\n"; ?>
<?php echo JText::_('COM_DIGICOM_ABOUT_SYSTEMINFO_PLATFORM_VERSION'); ?><?php echo "\n"; ?>
	<?php echo $this->info['platform']; ?>
<?php echo "\n"; ?>
<?php echo JText::_('COM_DIGICOM_ABOUT_SYSTEMINFO_USER_AGENT'); ?><?php echo "\n"; ?>
	<?php echo htmlspecialchars($this->info['useragent']); ?>
<?php echo "\n"; ?>


<?php echo JText::_('COM_DIGICOM_ABOUT_SYSTEMINFO_DIRECTORY_PERMISSIONS'); ?>
<?php echo "\n"; ?>
===================================
<?php echo "\n"; ?>
<?php foreach ($this->directory as $dir => $info) : ?>
<?php echo JHtml::_('directory.message', $dir, $info['message'], true, $input->get('format','html') === 'attachment' ? true : false); ?> : <?php echo JHtml::_('directory.writable', $info['writable'],($input->get('format') === 'attachment' ? 'true' : false)); ?>
<?php echo "\n"; ?>
<?php endforeach; ?>

<?php if($input->get('format','attachment') != 'attachment'): ?>
</pre>
<?php endif; ?>
