<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;
// TODO : Refactor - Codestandard
$showManager = false;
$mosConfig_absolute_path = JPATH_ROOT;
$max_upload = ini_get('upload_max_filesize');
$canDo = JHelperContent::getActions('com_digicom', 'component');
if($canDo->get('core.create') && $canDo->get('core.delete'))
{
	$showManager = true;
}
$tmpl = JFactory::getApplication()->input->get('tmpl','');
?>
<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="">
<?php else : ?>
	<div id="j-main-container" class="">
<?php endif;?>
		<?php if($tmpl != 'component') :?>
		<div class="dg-alert dg-alert-with-icon">
			<span class="icon-info"></span><span class="text-warning"><?php echo JText::_("COM_DIGICOM_FILE_MANAGER_NOTICE_CREATE_INDEX_HTML"); ?></span>
		</div>
		<div class="dg-alert dg-alert-with-icon">
			<span class="icon-flag"></span><?php echo JText::sprintf("COM_DIGICOM_FILE_MANAGER_HEADER_NOTICE",ini_get('upload_max_filesize')); ?>
		</div>
	<?php endif; ?>
					<?php if($showManager){?>
					<script type="text/javascript">
						jQuery(document).ready(function() {
							var basePath = '<?php echo JURI::root(true); ?>';
							var elf = jQuery('#elfinder').elfinder({
								url : '<?php echo JURI::base(true); ?>/index.php?option=com_digicom&view=filemanager&task=filemanager.connector&no_html=1',
								<?php if($this->mimes): ?>
								onlyMimes: [<?php echo $this->mimes; ?>],
								<?php endif; ?>
								<?php if($this->fieldID): ?>
								getFileCallback : function(path) {
									value = path.replace(basePath, '');
									parent.elFinderUpdate('<?php echo $this->fieldID; ?>', value);
								}
								<?php else: ?>
								height: 600
								<?php endif; ?>
							}).elfinder('instance');
						});
					</script>
					<?php } ?>
					<form action="<?php echo JRoute::_('index.php?option=com_digicom&view=filemanager&no_html=1'); ?>" method="post" class="clearfix" name="adminForm" id="adminForm">

			<div id="">
							<!--// Start Elfinder-->
							<div id="elfinder"></div>

							<input type="hidden" name="task" value="" />
							<input type="hidden" name="boxchecked" value="0" />
							<?php echo JHtml::_('form.token'); ?>
						</div>
					</form>

	</div>

		<div class="dg-footer">
			<?php echo JText::_('COM_DIGICOM_CREDITS'); ?>
		</div>
