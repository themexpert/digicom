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

$db = JFactory::getDBO();
$sql = "select `id` from #__menu where `link`='index.php?option=com_digicom&view=licenses' and `menutype`='DigiCom-Menu'";
$db->setQuery($sql);
$db->query();
$Itemid = intval($db->loadResult());

$link = JRoute::_("index.php?option=com_digicom&controller=licenses&task=saveDomain&action=saveDomain&Itemid=".$Itemid);
$dev_domain = trim ($this->license->dev_domain);
$domain = trim($this->license->domain);

?>

<div style="direction: ltr;">
<?php echo JText::_('DS_ENTER_DOMAIN_1'); ?><br />
<span style="color: #FF0000"><?php echo JText::_('DS_ENTER_DOMAIN_2'); ?></span><br />

<form name="domainForm" action="<?php echo $link;?>" method="post" >
	<table>

		<tr>
			<td>
				<?php echo JText::_("DSPRODDOMAIN");?>
			</td>
			<td>
				<input type="text" name="proddomain" <?php echo strlen($domain)>0?'value="'.$domain.'" disabled':'' ;?> />
			</td>
		</tr>

		<tr>
		<td>
			<?php echo JText::_("DSDEVDOMAIN");?>
		</td>

		<td>
			<input type="text" name="devdomain" <?php echo strlen($dev_domain)>0?'value="'.$dev_domain.'" disabled':'' ;?> />
		</td>
		</tr>

	</table>
	<input type="hidden" name="licid" value="<?php echo $this->license->id;?>" />
	<button type="submit" name="submit" class="btn btn-success"><?php echo JText::_('DSSAVE');?></button>
</form>
</div>
<?php echo DigiComHelper::powered_by(); ?>
