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

$attr = $this->attr;
$configs = $this->configs;
?>
<script language="javascript" type="text/javascript">
<!--

function submitbutton(pressbutton) {
	submitform( pressbutton );
}
-->
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<fieldset class="adminform">
	<legend><?php echo JText::_('VIEWATTRIBATTRSETTINGS');?></legend>
				<table class="admintable">

			<tr>
				<td>
			<?php echo JText::_("VIEWATTRIBATTRNAME");?>
		</td>
		<td>
			<input type="text" name="name" value="<?php echo $attr->name;?>" />
			<?php
				echo JHTML::tooltip(JText::_("COM_DIGICOM_ATTRIBNAME_TIP"), '', '',  "<img src=".JURI::root()."administrator/components/com_digicom/assets/images/tooltip.png />", '', '', 'hasTip');
			?>
		</td>
		</tr>
			   <tr>
				<td>
			<?php echo JText::_("VIEWATTRIBATTROPTIONS");?>
		</td>
		<td>
			<textarea name="options" rows="10" cols="22"><?php echo $attr->options;?></textarea>
			<?php
				echo JHTML::tooltip(JText::_("COM_DIGICOM_ATTRIBOPTION_TIP"), '', '',  "<img src=".JURI::root()."administrator/components/com_digicom/assets/images/tooltip.png />", '', '', 'hasTip');
			?>
		</td>
		</tr>

		<tr>
		<td>
			<?php echo JText::_("VIEWATTRIBATTRPUBLISH");?>
		</td>
		<td>
			<select name="published" class="span2">
				<option value="0" <?php echo ($attr->published == 0)?"selected":""; ?>><?php echo JText::_("DSNO"); ?></option>
				<option value="1" <?php echo ($attr->published == 1 || $attr->published != 0)?"selected":""; ?>><?php echo JText::_("DSYES"); ?></option>
			</select>
			<?php
				echo JHTML::tooltip(JText::_("COM_DIGICOM_ATTRIBPUBLISH_TIP"), '', '',  "<img src=".JURI::root()."administrator/components/com_digicom/assets/images/tooltip.png />", '', '', 'hasTip');
			?>
		</td>
		</tr>
			 <tr>
				<td>
			<?php echo JText::_("VIEWATTRIBATTRSIZE");?>
		</td>
		<td>
			<input class="span2" type="text" name="size" value="<?php echo ($attr->size > 0 ? $attr->size:"") ;?>" />
			<?php
				echo JHTML::tooltip(JText::_("COM_DIGICOM_ATTRIBSIZE_TIP"), '', '',  "<img src=".JURI::root()."administrator/components/com_digicom/assets/images/tooltip.png />", '', '', 'hasTip');
			?>
		</td>
		</tr>
		 </table>
	</fieldset>

		<input type="hidden" name="images" value="" />
			<input type="hidden" name="option" value="com_digicom" />
			<input type="hidden" name="id" value="<?php echo $attr->id; ?>" />
			<input type="hidden" name="task" value="" />
		<input type="hidden" name="controller" value="Attributes" />
		</form>
