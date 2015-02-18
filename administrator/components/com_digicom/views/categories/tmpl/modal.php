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
$url = JUri::getInstance();
$function = JRequest::getCmd('function');
$document = JFactory::getDocument();
//$document->addStyleSheet("components/com_digicom/assets/css/digicom.css");

$k = 0;
$n = count ($this->cats);
?>
<h1><?php echo JText::_('VIEWDSADMINCATEGORIES'); ?></h1>
<div class="alert alert-info">
	<?php echo JText::_("HEADER_CATEGORIES"); ?>
</div>

<form id="adminForm" action="<?php echo $url->toString();?>" name="adminForm" method="post" >
	<div id="editcell">
	<table class="adminlist table">
	<thead>
		<tr>
			<th width="5">
				<input type="checkbox" onclick="checkAll(<?php echo $n; ?>)" name="toggle" value="" />
			</th>
				<th width="20">
				<?php echo JText::_('VIEWCATEGORYID');?>
			</th>
			<th class="title">
				<?php echo JText::_('VIEWCATEGORYNAME');?>
			</th>
			<th width="5%">
				<?php echo JText::_('VIEWCATEGORYPUBLISHING');?>
			</th>
		</tr>
	</thead>

	<tbody>
<?php 
if ($n):
	$z = 0;
	$ordering = true;
	foreach ($this->cats as $i => $v):

		$cat	= $this->cats[$i];
		$id		= $cat->id;
		$checked = JHTML::_('grid.id', $z, $id);
		$link = JRoute::_("index.php?option=com_digicom&controller=categories&task=edit&cid[]=".$id);
		$published = JHTML::_('grid.published', $cat, $z );
?>
	<tr class="row<?php echo $k;?>"> 
		 	<td>
		 			<?php echo $checked;?>
		</td>
		<td><?php echo $id;?></td>
		<td>
			<a href="javascript:void(0)" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('<?php echo $id; ?>', '<?php echo $this->escape(addslashes($cat->name)); ?>', '', '', '', '', null);" ><?php echo $cat->treename;?></a>
		</td>
		<td align="center">
			<?php echo $published;?>
		</td>

	</tr>


<?php 
		$z++;
		$k = 1 - $k;
	endforeach;
endif;
?>
	</tbody>

	<tfoot>
		<tr>
			<td colspan="5">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>

	</table>

	</div>
	<input type="hidden" name="option" value="com_digicom" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="controller" value="Categories" />
</form>