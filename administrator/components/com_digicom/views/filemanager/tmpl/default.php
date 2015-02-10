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

$mosConfig_absolute_path = JPATH_ROOT;
?>
<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container" class="span12">
<?php endif;?>
		<table class="adminform table">
			<tr>
				<td>
					<script type="text/javascript">
						jQuery(document).ready(function() {
							var basePath = '<?php echo JURI::root(true); ?>';
							var elf = jQuery('#elfinder').elfinder({
								url : '<?php echo JURI::base(true); ?>/index.php?option=com_digicom&controller=filemanager&task=connector&no_html=1',
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
					<form action="<?php echo JRoute::_('index.php?option=com_digicom&controller=filemanager&no_html=1'); ?>" method="post" class="clearfix" name="adminForm" id="adminForm">
						
						<div id="j-main-container">
							<!--// Start Elfinder-->
							<div id="elfinder"></div>
							
							<input type="hidden" name="task" value="" />
							<input type="hidden" name="boxchecked" value="0" />
							<?php echo JHtml::_('form.token'); ?>
						</div>
					</form>
				</td>
			</tr>
		</table>
	</div>
