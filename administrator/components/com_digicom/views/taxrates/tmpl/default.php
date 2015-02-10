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
$n = count ($this->rates);
$page = $this->pagination;
$document = JFactory::getDocument();
$document->addStyleSheet("components/com_digicom/assets/css/digicom.css");


?>

<table>
	<tr>
		<td class="header_zone" colspan="4">
			<?php
				echo JText::_("HEADER_TAXRATES");
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

<table><tr>
<td align="left">
	<form action="index.php" name="adminFormsearch" method="post">
		<input type="text" name="searchvalue" value="<?php echo JRequest::getVar('searchvalue', '', 'request');?>" />
		<input type="submit" name="submit" value="<?php echo JText::_("SEARCH");?>" />
	  	<input type="hidden" name="option" value="com_digicom" />
		<input type="hidden" name="task" value="search" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="controller" value="TaxRates" />
	</form>
</td>
<td align="right"><?php echo JText::_("DSUPLOADTAXRATES");?><br />
<!--<form action="index.php" name="pluginFileForm" method="post"	enctype="multipart/form-data" >-->
	<form action="index.php" name="adminFormfile" method="post" enctype="multipart/form-data">
		<input type="file" name="datafile" id="datafile" value="" />
		<input type="submit" name="submit" value="<?php echo JText::_("DSUPLOAD");?>" />
		(<a href="index3.php?option=com_digicom&controller=taxrates&task=viewsample&no_html=1" target="_blank" ><?php echo JText::_("DSVIEWSAMPLE");?></a>)
	  	<input type="hidden" name="option" value="com_digicom" />
		<input type="hidden" name="task" value="upload" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="controller" value="TaxRates" />
	</form>

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
			<?php echo JText::_('VIEWTAXRATEID');?>
		</th>
		<th class="title">
			<?php echo JText::_('VIEWTAXRATENAME');?>
		</th>
		<th>
			<?php echo JText::_('VIEWTAXRATECOUNTRY');?>
		</th>
		<th>
			<?php echo JText::_('VIEWTAXRATEREGION');?>
		</th>

		<th>
			<?php echo JText::_('VIEWTAXRATEZIP');?>
		</th>

		<th>
			<?php echo JText::_('VIEWTAXRATERATE');?>
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
		<input type="hidden" name="controller" value="TaxRates" />
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
			<?php echo JText::_('VIEWTAXRATEID');?>
		</th>
		<th class="title">
			<?php echo JText::_('VIEWTAXRATENAME');?>
		</th>
		<th>
			<?php echo JText::_('VIEWTAXRATECOUNTRY');?>
		</th>
		<th>
			<?php echo JText::_('VIEWTAXRATEREGION');?>
		</th>

		<th>
			<?php echo JText::_('VIEWTAXRATEZIP');?>
		</th>

		<th>
			<?php echo JText::_('VIEWTAXRATERATE');?>
		</th>											 

	</tr>
</thead>

<tbody>

<?php 
//	for ($i = 1; $i <= $n; $i++):
	$z = 0;
	$ordering = true;
	foreach ($this->rates as $i => $v):

		$rate = $this->rates[$i];
		$id = $rate->id;
		$checked = JHTML::_('grid.id', $z, $id);
		$link = JRoute::_("index.php?option=com_digicom&controller=taxrates&task=edit&cid[]=".$id);
		$published = JHTML::_('grid.published', $rate, $z );
?>
	<tr class="row<?php echo $k;?>"> 
		 	<td>
		 			<?php echo $checked;?>
		</td>

		 	<td>
		 			<?php echo $id;?>
		</td>
		 	<td>
		 			<a href="<?php echo $link;?>" ><?php echo $rate->name;?></a>
		</td>

		 	<td>
		 			<?php echo $rate->country;?>
		</td>

		 	<td>
		 			<?php echo $rate->state;?>
		</td>

		 	<td>
		 			<?php echo $rate->zip;?>
		</td>

		 	<td>
		 			<?php echo $rate->rate;?>
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
<input type="hidden" name="controller" value="TaxRates" />
</form>

<?php
	endif;

?>