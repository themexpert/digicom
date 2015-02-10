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

$email = $this->email;
$configs = $this->configs;

?>

<form id="adminForm" name="adminForm" action="index.php" method="post">
	<table class="adminlist" style="font-family:Verdana, Arial, Helvetica, sans-serif;" cellpadding="5" cellspacing="5">
		<tr>
			<td valign="top" align="right">
				<?php echo JText::_('VIEWPRODSUBJ'); ?>:
			</td>
			<td>
				<?php echo $email["0"]["subject"]; ?>
			</td>
		</tr>
		<tr>
			<td valign="top" align="right">
				<?php echo JText::_('DSTO'); ?>:
			</td>
			<td>
				<?php echo $email["0"]["to"]; ?>
			</td>
		</tr>
		<tr>
			<td valign="top" align="right">
				<?php echo JText::_('DIGI_BODY'); ?>:
			</td>
			<td>
				<?php echo $email["0"]["body"]; ?>
			</td>
		</tr>
	</table>

	<input type="hidden" name="option" value="com_digicom" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="Logs" />
</form>