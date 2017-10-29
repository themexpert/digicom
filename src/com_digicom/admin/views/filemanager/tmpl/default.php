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

<form id="adminForm" action="<?php echo JRoute::_('index.php?option=com_digicom&view=filemanager'); ?>" method="post" name="adminForm" autocomplete="off">
	<div id="digicom" class="dc digicom">
		<div class="tx-sidebar"><?php echo $this->sidebar; ?></div> <!-- .tx-sidebar -->
		<div class="tx-main">
			<div class="page-header">
				<h1>File Manager</h1>
				<p>Manage all your store files from here.</p>
			</div> <!-- .page-header -->
			<div class="page-content">
				<?php if($tmpl != 'component') :?>
				<div class="alert alert-info">
					<?php echo JText::_("COM_DIGICOM_FILE_MANAGER_NOTICE_CREATE_INDEX_HTML"); ?>
				</div>
				<div class="alert">
					<?php echo JText::sprintf("COM_DIGICOM_FILE_MANAGER_HEADER_NOTICE",ini_get('upload_max_filesize')); ?>
				</div>
				<?php endif; ?>
				<div class="panel panel-default">
					<div class="panel-body">
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
							<!--// Start Elfinder-->
							<div id="elfinder"></div>
							<input type="hidden" name="task" value="" />
							<input type="hidden" name="boxchecked" value="0" />
							<?php echo JHtml::_('form.token'); ?>
						</form>
					</div>
				</div>
			</div> <!-- .page-content -->
		</div> <!-- .tx-main -->
	</div>
</form>