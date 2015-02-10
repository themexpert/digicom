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

JHtml::_('behavior.tooltip');

$document = JFactory::getDocument();
$document->addStyleSheet("components/com_digicom/assets/css/digicom.css");

?>
<script type="text/javascript">

		function checkUnlimited(el) {

			//var durationcount = document.getElementById('duration_count');
			var durationtype = document.getElementById('duration_type');

			if (el.selectedIndex == 0) {
				durationtype.style.display = 'none';
			} else {
				durationtype.style.display = 'inline';
			}
		}

</script>

<form id="adminForm" action="index.php" name="adminForm" method="post">

	<fieldset>

		<legend><?php echo $this->action. " " . JText::_('SUBACTIONNAME') ?></legend>
		<table>
			<tr>
				<td align="right">
					<a class="modal digi_video" rel="{handler: 'iframe', size: {x: 750, y: 435}}" href="index.php?option=com_digicom&controller=about&task=vimeo&id=38440227">
						<img src="<?php echo JURI::base(); ?>components/com_digicom/assets/images/icon_video.gif" class="video_img" />
						<?php echo JText::_("COM_DIGICOM_VIDEO_PLAINS_MANAGER"); ?>				  
					</a>
				</td>
			</tr>
		</table>

		<table>
			<tr>
				<td width="30"><?php echo JText::_('PLAINNAME'); ?></td>
				<td>
					<input type="text" name="name" value="<?php echo $this->plain->name; ?>"/>
					<?php
						echo JHTML::tooltip(JText::_("COM_DIGICOM_PLANSNAME_TIP"), '', '',  "<img src=".JURI::root()."administrator/components/com_digicom/assets/images/tooltip.png />", '', '', 'hasTip');
					?>
				</td>
			</tr>
			<tr>
				<td><?php echo JText::_('PLAINDURCOUNT'); ?></td>
				<td>
					<?php  echo $this->lists['duration_count']; ?> <?php  echo $this->lists['duration_type']; ?>
					<?php
						echo JHTML::tooltip(JText::_("COM_DIGICOM_PLANSTERMS_TIP"), '', '',  "<img src=".JURI::root()."administrator/components/com_digicom/assets/images/tooltip.png />", '', '', 'hasTip');
					?>
				</td>
			</tr>
			<tr>
				<td><?php echo JText::_('PLAINPUBLISHED'); ?></td>
				<td>
					<?php  echo $this->lists['published']; ?>
					<?php
						echo JHTML::tooltip(JText::_("COM_DIGICOM_PLANSPUBLISH_TIP"), '', '',  "<img src=".JURI::root()."administrator/components/com_digicom/assets/images/tooltip.png />", '', '', 'hasTip');
					?>
				</td>
			</tr>
		</table>

	</fieldset>

	<input type="hidden" name="id" value="<?php echo $this->plain->id; ?>"/>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_digicom" />
	<input type="hidden" name="controller" value="Plans" />

</form>