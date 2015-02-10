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

$document = JFactory::getDocument();
$document->addStyleSheet("components/com_digicom/assets/css/digicom.css");

?>
<table>
	<tr>
		<td class="header_zone" colspan="4">
			<?php
				echo JText::_("HEADER_PRODFAMILIES");
			?>
		</td>
	</tr>
</table>
<?php
	$k = 0;
	$n = count ($this->pclasses);
	$page = $this->pagination;

	if ($n < 1): 
//		echo JText::_('VIEWCATEGORYNOCAT');
?>

<table class="adminlist table">
<thead>

	<tr>
		<th width="5">
			<input type="checkbox" onclick="checkAll(<?php echo $n; ?>)" name="toggle" value="" />
		</th>
			<th width="20">
			<?php echo JText::_('VIEWPRODUCTCLASSID');?>
		</th>
		<th class="title">
			<?php echo JText::_('VIEWPRODUCTCLASSNAME');?>
		</th>
		<th>
			<?php echo JHTML::_('grid.order',  $this->pclasses ); ?>
		</th>
		<th width="5%">
			<?php echo JText::_('VIEWPRODUCTCLASSPUBLISHING');?>
		</th>

	</tr>
</thead>


<tbody>
	<tr>
		<td colspan="5">
			<?php echo $this->pagination->getListFooter(); ?>
		</td>
	</tr>
</tbody>
</table>

	<form id="adminForm" action="index.php" name="adminForm" method="post">
		<input type="hidden" name="option" value="com_digicom" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="controller" value="ProductClasses" />
	</form>

<?php

	else:

?>
<form id="adminForm" action="index.php" name="adminForm" method="post" >
<div id="editcell">
<table class="adminlist table table-striped">
<thead>
	<tr>
		<th width="5">
			<input type="checkbox" onclick="checkAll(<?php echo $n; ?>)" name="toggle" value="" />
		</th>
			<th width="20">
			<?php echo JText::_('VIEWPRODUCTCLASSID');?>
		</th>
		<th class="title">
			<?php echo JText::_('VIEWPRODUCTCLASSNAME');?>
		</th>
		<th>
			<span style="float:left !important; padding-left: 55%;">
			<?php echo JHTML::_('grid.order',  $this->pclasses ); ?>
			</span>
		</th>
		<th width="5%">
			<?php echo JText::_('VIEWPRODUCTCLASSPUBLISHING');?>
		</th>
	</tr>
</thead>

<tbody>
	<?php
	//	for ($i = 1; $i <= $n; $i++):
	$z = 0;
	$ordering = true;
	foreach ($this->pclasses as $i => $v):

		$pclass = $this->pclasses[$i];
		$id = $pclass->id;
		$checked = JHTML::_('grid.id', $z, $id);
		$link = JRoute::_("index.php?option=com_digicom&controller=productclasses&task=edit&cid[]=".$id);
		$published = JHTML::_('grid.published', $pclass, $z );
	?>
	<tr class="row<?php echo $k;?>">
		<td>
			<?php echo $checked;?>
		</td>

		<td>
			<?php echo $id;?>
		</td>
		<td>
			<a href="<?php echo $link;?>" ><?php echo $pclass->name;?></a>
		</td>
		<td class="order">
			<span><?php echo $page->orderUpIcon( $z, 1, 'orderup', 'Move Up', $ordering); ?></span>
			<span><?php echo $page->orderDownIcon( $z, $n, 1, 'orderdown', 'Move Down', $ordering ); ?></span>
			<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
			<input type="text" name="order[]" size="5" value="<?php echo $pclass->ordering; ?>" <?php echo $disabled; ?> class="text_area" style="text-align: center" />
		</td>
		<td>
			<?php echo $published;?>
		</td>
	</tr>


	<?php
		$z++;
		$k = 1 - $k;
		endforeach;
	?>
	<tr>
		<td colspan="5">
			<?php echo $this->pagination->getListFooter(); ?>
		</td>
	</tr>
</tbody>
</table>

</div>
<input type="hidden" name="option" value="com_digicom" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="ProductClasses" />
</form>

<?php
	endif;
?>