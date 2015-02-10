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

global $Itemid;
$link = JRoute::_("index.php?option=com_digicom&controller=licenses&task=saveDomain"."&Itemid=".$Itemid);
$dev_domain = trim ($this->license->dev_domain);
$domain = trim($this->license->domain);

?>

<form id="adminForm" name="domainForm" action="<?php echo $link;?>" method="post" >
	<table>

		<tr>
		<td>
			<?php echo JText::_("DSPRODDOMAIN");?>
		</td>

		<td>
			<input type="text" name="proddomain" <?php echo strlen($domain)>0?'value="'.$domain.'" ':'' ;?> />
		</td>
		</tr>

		<tr>
		<td>
			<?php echo JText::_("DSDEVDOMAIN");?>
		</td>

		<td>
			<input type="text" name="devdomain" <?php echo strlen($dev_domain)>0?'value="'.$dev_domain.'" ':'' ;?> />
		</td>
		</tr>

	</table>
	<input type="hidden" name="cid[]" value="<?php echo $this->license->id;?>" />
	<input type="submit" name="submit" value="<?php echo JText::_('DSSAVE');?>" />
</form>