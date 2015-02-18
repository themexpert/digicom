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

$k = 0;
$n = count ($this->plains);
$page = $this->pagination;
$configs = $this->configs;

$document = JFactory::getDocument();
$document->addScript(JURI::root() . 'components/com_digicom/assets/js/jquery.digicom.js');
$document->addScript(JURI::root() . 'components/com_digicom/assets/js/jquery.noconflict.digicom.js');
//$document->addStyleSheet("components/com_digicom/assets/css/digicom.css");

$document->addScriptDeclaration("
	jQuery(function() {
		var unlimited = jQuery('.row0').filter(function(index) {
		  var $ = jQuery,
		  cond_1 = $.trim($(this).find('td:eq(3)').text()) === 'Unlimited',
		  cond_2 = $.trim($(this).find('td:eq(2) a').text()) === 'Unlimited';
		  return ( cond_1 && cond_2 );
		}).find('td:first input');

		unlimited.prop('disabled', true);
		unlimited.parents('tr:first').find('td:eq(2) a').removeAttr('href');

		var old = checkAll;
		checkAll = function() {
			var ret = old.apply(this, arguments);
			unlimited.prop('checked', false);
		}
	});
");
?>

<table>
	<tr>
		<td class="header_zone" colspan="4">
			<?php
				echo JText::_("HEADER_PLAINS");
			?>
		</td>
	</tr>
	<tr>
		<td align="right">
			<a class="modal digi_video" rel="{handler: 'iframe', size: {x: 750, y: 435}}" href="index.php?option=com_digicom&controller=about&task=vimeo&id=38440227">
				<img src="<?php echo JURI::base(); ?>components/com_digicom/assets/images/icon_video.gif" class="video_img" />
				<?php echo JText::_("COM_DIGICOM_VIDEO_PLAINS_MANAGER"); ?>				  
			</a>
		</td>
	</tr>
</table>

<?php

if ($n < 1):

	?>


<form id="adminForm" action="index.php" name="adminForm" method="post">

	<table class="adminlist table">

		<thead>
			<tr>
				<th width="5">
					<input type="checkbox" onclick="checkAll(<?php echo $n; ?>)" name="toggle" value="" />
				</th>
				<th width="20">
						<?php echo JText::_('VIEWPLAINID');?>
				</th>
				<th>
						<?php echo JText::_('VIEWPLAINNAME');?>
				</th>

				<th>
						<?php echo JText::_('VIEWPLAINTERMS');?>
				</th>

				<th>
						<?php echo JText::_("VIEWPLAINORDERING");?>
						<?php echo JHTML::_('grid.order',  $this->plains ); ?>
				</th>

				<th>
						<?php echo JText::_("VIEWPLAINPUBLISH");?>
				</th>

			</tr>
		</thead>

		<tbody>
		</tbody>

	</table>

	<input type="hidden" name="option" value="com_digicom" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="controller" value="Plans" />
</form>

<?php

else:

?>

<form id="adminForm" action="index.php" name="adminForm" method="post">

	<table class="adminlist table">

		<thead>
			<tr>
				<th width="5">
					<input type="checkbox" onclick="checkAll(<?php echo $n; ?>)" name="toggle" value="" />
				</th>
				<th width="20">
						<?php echo JText::_('VIEWPLAINID');?>
				</th>
				<th>
						<?php echo JText::_('VIEWPLAINNAME');?>
				</th>

				<th>
						<?php echo JText::_('VIEWPLAINTERMS');?>
				</th>

				<th>
						<?php echo JText::_("VIEWPLAINORDERING");?>
						<?php echo JHTML::_('grid.order',  $this->plains ); ?>
				</th>

				<th>
						<?php echo JText::_("VIEWPLAINPUBLISH");?>
				</th>

			</tr>
		</thead>

		<tbody>

				<?php
				JHTML::_("behavior.tooltip");

				$ordering = true;

				for ($i = 0; $i < $n; $i++):
					$plain = $this->plains[$i];
					$id = $plain->id;
					$checked = JHTML::_('grid.id', $i, $id);
					$link = JRoute::_("index.php?option=com_digicom&controller=plans&task=edit&cid[]=".$id);
					$published = JHTML::_('grid.published', $plain, $i );
					?>
			<tr class="row<?php echo $k;?>">

				<td>
							<?php echo $checked;?>
				</td>

				<td align="right"><?php echo $plain->id; //echo $i+1;?></td>

				<td>
					<a href="<?php echo $link;?>" ><?php echo $plain->name;?></a>
				</td>

				<td>
							<?php
							if ($plain->duration_count != -1) {
								echo DigiComAdminHelper::getDurationType($plain->duration_count, $plain->duration_type);
							} else {
								echo JText::_("DS_UNLIMITED");
							}
							?>
				</td>
				<td class="order">
					<span><?php echo $page->orderUpIcon( $i, true, 'orderup', 'Move Up', $ordering); ?></span>
					<span><?php echo $page->orderDownIcon( $i, $n, true, 'orderdown', 'Move Down', $ordering ); ?></span>
							<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
					<input type="text" name="order[]" size="5" value="<?php echo $plain->ordering; ?>" <?php echo $disabled; ?> class="text_area" style="text-align: center" />
				</td>

				<td align="center">
					<a href="javascript: void(0);" onClick="return listItemTask('cb<?php  echo $i;?>','<?php  echo $plain->published ? "unpublish" : "publish";?>')">
								<?php //echo JHTML::tooltip("<table>".$times."</table>", $alt, "../../../administrator/images/".$img);?>
								<?php echo $published; ?>
					</a>
				</td>

			</tr>


<?php
				$k = 1 - $k;
				endfor;
?>

		</tbody>

		<tfoot>
			<tr>
				<td colspan="9">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
	</table>

	<input type="hidden" name="option" value="com_digicom" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="controller" value="Plans" />
</form>


<?php

endif;

?>
