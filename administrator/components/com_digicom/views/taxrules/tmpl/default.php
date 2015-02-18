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
$n = count ($this->rules);
$page = $this->pagination;
$document = JFactory::getDocument();
//$document->addStyleSheet("components/com_digicom/assets/css/digicom.css");
?>

<table>
	<tr>
		<td class="header_zone" colspan="4">
			<?php
				echo JText::_("HEADER_TAXRULES");
			?>
		</td>
	</tr>
	<tr>
		<td align="right">
			<a class="modal digi_video" rel="{handler: 'iframe', size: {x: 750, y: 435}}" href="index.php?option=com_digicom&controller=about&task=vimeo&id=38448863">
				<img src="<?php echo JURI::base(); ?>components/com_digicom/assets/images/icon_video.gif" class="video_img" />
				<?php echo JText::_("COM_DIGICOM_VIDEO_SETTING_TAX"); ?>				  
			</a>
		</td>
	</tr>
</table>

<?php
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
			<?php echo JText::_('VIEWTAXRULEID');?>
		</th>
		<th class="title">
			<?php echo JText::_('VIEWTAXRULENAME');?>
		</th>

		<th>
			<?php echo JHTML::_('grid.order',  $this->rules ); ?>
		</th>
		<th width="5%">
			<?php echo JText::_('VIEWTAXRULEPUBLISHING');?>
		</th>											 


	</tr>
</thead>


<tbody>
	<tr>
<td colspan="7"><?php echo $this->pagination->getListFooter(); ?></td>

</td>
</tr>
</tbody>
</table>

	<form id="adminForm" action="index.php" name="adminForm" method="post">
	  	<input type="hidden" name="option" value="com_digicom" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="controller" value="TaxRules" />
	</form>

<?php

	else:

?>
<form id="adminForm" action="index.php" name="adminForm" method="post" >
<div id="editcell">
<table class="adminlist table">
<thead>

	<tr>
		<th width="5">
			<input type="checkbox" onclick="checkAll(<?php echo $n; ?>)" name="toggle" value="" />
		</th>
			<th width="20">
			<?php echo JText::_('VIEWTAXRULEID');?>
		</th>
		<th class="title">
			<?php echo JText::_('VIEWTAXRULENAME');?>
		</th>

		<th>
			<?php echo JHTML::_('grid.order',  $this->rules ); ?>
		</th>
		<th width="5%">
			<?php echo JText::_('VIEWTAXRULEPUBLISHING');?>
		</th>											 

	</tr>
</thead>

<tbody>

<?php 
//	for ($i = 1; $i <= $n; $i++):
	$z = 0;
	$ordering = true;
	foreach ($this->rules as $i => $v):

		$rule = $this->rules[$i];
		$id = $rule->id;
		$checked = JHTML::_('grid.id', $z, $id);
		$link = JRoute::_("index.php?option=com_digicom&controller=taxrules&task=edit&cid[]=".$id);
		$published = JHTML::_('grid.published', $rule, $z );
?>
	<tr class="row<?php echo $k;?>"> 
		 	<td>
		 			<?php echo $checked;?>
		</td>

		 	<td>
		 			<?php echo $id;?>
		</td>
		 	<td>
		 			<a href="<?php echo $link;?>" ><?php echo $rule->name;?></a>
		</td>

		<td class="order">
			<span><?php echo $page->orderUpIcon( $z, 1, 'orderup', 'Move Up', $ordering); ?></span>
			<span><?php echo $page->orderDownIcon( $z, $n, 1, 'orderdown', 'Move Down', $ordering ); ?></span>
			<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
			<input type="text" name="order[]" size="5" value="<?php echo $rule->ordering; ?>" <?php echo $disabled; ?> class="text_area" style="text-align: center" />
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
</tbody>

	<tfoot>
		<tr>
			<td colspan="9">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>

</table>

</div>
<input type="hidden" name="option" value="com_digicom" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="TaxRules" />
</form>

<?php
	endif;

?>