<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;
?>
<fieldset class="adminform">
	<legend><?php echo JText::_('COM_DIGICOM_ABOUT_SYSTEMINFO_SYSTEM_INFORMATION'); ?></legend>
	<table class="table table-striped">
		<thead>
			<tr>
				<th width="25%">
					<?php echo JText::_('COM_DIGICOM_ABOUT_SYSTEMINFO_SETTING'); ?>
				</th>
				<th>
					<?php echo JText::_('COM_DIGICOM_ABOUT_SYSTEMINFO_VALUE'); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="2">&#160;</td>
			</tr>
		</tfoot>
		<tbody>
			<tr>
				<td>
					<strong><?php echo JText::_('COM_DIGICOM_ABOUT_SYSTEMINFO_PHP_BUILT_ON'); ?></strong>
				</td>
				<td>
					<?php echo $this->info['php']; ?>
				</td>
			</tr>
			<tr>
				<td>
					<strong><?php echo JText::_('COM_DIGICOM_ABOUT_SYSTEMINFO_DATABASE_VERSION'); ?></strong>
				</td>
				<td>
					<?php echo $this->info['dbversion']; ?>
				</td>
			</tr>
			<tr>
				<td>
					<strong><?php echo JText::_('COM_DIGICOM_ABOUT_SYSTEMINFO_DATABASE_COLLATION'); ?></strong>
				</td>
				<td>
					<?php echo $this->info['dbcollation']; ?>
				</td>
			</tr>
			<tr>
				<td>
					<strong><?php echo JText::_('COM_DIGICOM_ABOUT_SYSTEMINFO_PHP_VERSION'); ?></strong>
				</td>
				<td>
					<?php echo $this->info['phpversion']; ?>
				</td>
			</tr>
			<tr>
				<td>
					<strong><?php echo JText::_('COM_DIGICOM_ABOUT_SYSTEMINFO_WEB_SERVER'); ?></strong>
				</td>
				<td>
					<?php echo !empty($this->info['server']) ? $this->info['server'] : JText::_('COM_DIGICOM_ABOUT_SYSTEMINFO_NA'); ?>
				</td>
			</tr>
			<tr>
				<td>
					<strong><?php echo JText::_('COM_DIGICOM_ABOUT_SYSTEMINFO_WEBSERVER_TO_PHP_INTERFACE'); ?></strong>
				</td>
				<td>
					<?php echo $this->info['sapi_name']; ?>
				</td>
			</tr>
			<tr>
				<td>
					<strong><?php echo JText::_('COM_DIGICOM_ABOUT_SYSTEMINFO_JOOMLA_VERSION'); ?></strong>
				</td>
				<td>
					<?php echo $this->info['version']; ?>
				</td>
			</tr>
			<tr>
				<td>
					<strong><?php echo JText::_('COM_DIGICOM_ABOUT_SYSTEMINFO_PLATFORM_VERSION'); ?></strong>
				</td>
				<td>
					<?php echo $this->info['platform']; ?>
				</td>
			</tr>
			<tr>
				<td>
					<strong><?php echo JText::_('COM_DIGICOM_ABOUT_SYSTEMINFO_USER_AGENT'); ?></strong>
				</td>
				<td>
					<?php echo htmlspecialchars($this->info['useragent']); ?>
				</td>
			</tr>
		</tbody>
	</table>
</fieldset>

<fieldset class="adminform">
	<legend><?php echo JText::_('COM_DIGICOM_ABOUT_SYSTEMINFO_DIRECTORY_PERMISSIONS'); ?></legend>
	<table class="table table-striped">
		<thead>
			<tr>
				<th width="650">
					<?php echo JText::_('COM_DIGICOM_ABOUT_SYSTEMINFO_DIRECTORY'); ?>
				</th>
				<th>
					<?php echo JText::_('COM_DIGICOM_ABOUT_SYSTEMINFO_STATUS'); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="2">&#160;</td>
			</tr>
		</tfoot>
		<tbody>
			<?php foreach ($this->directory as $dir => $info) : ?>
				<tr>
					<td>
						<?php echo JHtml::_('directory.message', $dir, $info['message']); ?>
					</td>
					<td>
						<?php echo JHtml::_('directory.writable', $info['writable']); ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</fieldset>
