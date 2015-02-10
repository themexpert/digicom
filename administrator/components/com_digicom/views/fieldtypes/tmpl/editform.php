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
		</td>
		</tr>
			   <tr>
				<td>
			<?php echo JText::_("VIEWATTRIBATTROPTIONS");?>
		</td>
		<td>
			<textarea name="options" rows="10" cols="22"><?php echo $attr->options;?></textarea>
		</td>
		</tr>

			   <tr>
				<td>
			<?php echo JText::_("VIEWATTRIBATTRPUBLISH");?>
		</td>
		<td>
			<?php echo JText::_("VIEWATTRIBATTRPUBLISHNO"); ?> <input type="radio" name="published" value="0" <?php echo ($attr->published == 0)?"checked":""; ?> />
			<?php echo JText::_("VIEWATTRIBATTRPUBLISHYES"); ?> <input type="radio" name="published" value="1" <?php echo ($attr->published == 1 || $attr->published != 0)?"checked":""; ?> />

		</td>
		</tr>
			 <tr>
				<td>
			<?php echo JText::_("VIEWATTRIBATTRSIZE");?>
		</td>
		<td>
			<input type="text" name="size" value="<?php echo ($attr->size > 0 ? $attr->size:"") ;?>" />
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
