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
$n = count ($this->languages);
$mn = count ($this->mlanguages);

$document = JFactory::getDocument();
$document->addStyleSheet("components/com_digicom/assets/css/digicom.css");

if ($n < 1): 
//		echo JText::_('VIEWLANGNOLANG');
?>

	<form id="adminForm" action="index.php" name="adminForm" method="post">
	  	<input type="hidden" name="option" value="com_digicom" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="controller" value="Languages" />
	</form>

<?php

	else:

?>
<script language="javascript" type="text/javascript" >
<!--
	function checkLangFile () {
		var file = document.getElementById("langfile");
		if (file.value.length < 1) {
			alert ('<?php echo JText::_("VIEWLANGNOLANGSELECT");?>');
			return false;
		}

	}

-->
</script>

<table>
	<tr>
		<td class="header_zone" colspan="4">
			<?php
				echo JText::_("HEADER_LANGUAGES");
			?>
		</td>
	</tr>
	<tr>
		<td align="right">
			<a class="modal digi_video" rel="{handler: 'iframe', size: {x: 750, y: 435}}" href="index.php?option=com_digicom&controller=about&task=vimeo&id=38437490">
				<img src="<?php echo JURI::base(); ?>components/com_digicom/assets/images/icon_video.gif" class="video_img" />
				<?php echo JText::_("COM_DIGICOM_VIDEO_LANG_MANAGER"); ?>				  
			</a>
		</td>
	</tr>
</table>

<form id="adminForm" action="index.php" name="adminForm" method="post">
<div id="editcell" >
<table class="adminlist table">
<caption><?php echo JText::_("DSCOMPONENTTRANS");?></caption>

<thead>

	<tr>
		<th width="5">
			<input type="checkbox" onclick="checkAll(<?php echo $n; ?>)" name="toggle" value="" />
		</th>
			<th width="20">
			<?php echo JText::_('VIEWLANGID');?>
		</th>
		<th>
			<?php echo JText::_('VIEWLANGTITLE');?>
		</th>

		<th colspan="2"><?php echo JText::_("EDITLANG");?>
		</th>

	</tr>
</thead>

<tbody>

<?php 
	for ($i = 0; $i < $n; $i++):
		$language = $this->languages[$i];
		if (empty($language)) continue;
		$id = $language->id;
		$checked = JHTML::_('grid.id', $i, $id);
		$link_fe = JRoute::_("index.php?option=com_digicom&controller=languages&task=editFE&cid[]=".$id);
		$link_be = JRoute::_("index.php?option=com_digicom&controller=languages&task=editBE&cid[]=".$id);
//		$published = JHTML::_('grid.published', $language, $i );
?>
	<tr class="row<?php echo $k;?>"> 
		 	<td>
		 			<?php echo $checked;?>
		</td>

		 	<td>
		 			<?php echo $id;?>
		</td>

		 	<td>
		 			<!--<a href="<?php echo $link;?>" >-->
			<?php echo $language->name;?>
			<!--</a>-->
		</td>
		 	<td>
		 			<a href="<?php echo $link_fe;?>"><?php echo JText::_("EDITFRONTENDLANG");?></a>
		</td>

		 	<td>
		 			<a href="<?php echo $link_be;?>"><?php echo JText::_("EDITBACKENDLANG");?></a>
		</td>


	</tr>


<?php 
		$k = 1 - $k;
	endfor;
?>
</tbody>


</table>

</div>

<input type="hidden" name="option" value="com_digicom" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="Languages" />
</form>
<br />




<?php
	endif;

?>
<!--
<form action="index.php" name="pluginFileForm" method="post" enctype="multipart/form-data" onsubmit="return checkLangFile();">
<div id="editcell" >
<table class="adminlist table">
<tr>
<td nowrap>
<input id="langfile" type="file" name="langfile" /> <input type="submit" name="submit" value="Upload language"  />
</td>

</tr>
</table>

</div>

<input type="hidden" name="option" value="com_digicom" />
<input type="hidden" name="task" value="upload" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="Languages" />
</form>
-->

<div id="editcell" >
<table class="adminlist table">
<caption><?php echo JText::_("DSMODULETRANS");?></caption>
<thead>

	<tr>
<!--		<th width="5">
			<input type="checkbox" onclick="checkAll(<?php echo $n; ?>)" name="toggle" value="" />

		</th>
-->
			<th width="20">
			<?php echo JText::_('VIEWLANGID');?>
		</th>
		<th>
			<?php echo JText::_('VIEWLANGTITLE');?>
		</th>

		<th ><?php echo JText::_("EDITLANG");?>
		</th>

	</tr>
</thead>

<tbody>

<?php 
	for ($i = 0; $i < $mn; $i++):
		$language = $this->mlanguages[$i];
		if (empty($language)) continue;
		$id = $i;
		$fefilename = explode (".", $language->fefilename);
		$fefilename = $fefilename[1];

		$link_fe = JRoute::_("index.php?option=com_digicom&controller=languages&task=editML&cid[]=".strtolower($language->fefilename));
?>
	<tr class="row<?php echo $k;?>"> 
<!--		 	<td>
		 			<?php //echo $checked;?>
		</td>
-->

		 	<td>
		 			<?php echo $id;?>
		</td>

		 	<td>
		 			<!--<a href="<?php echo $link;?>" >-->
			<?php echo $language->name;?>
			<!--</a>-->
		</td>
		 	<td align="center">
		 			<a href="<?php echo $link_fe;?>"><?php echo $fefilename; //JText::_("EDITFRONTENDLANG");?></a>
		</td>

	<!--	 	<td>
		 			<a href="<?php echo $link_be;?>"><?php echo JText::_("EDITBACKENDLANG");?></a>
		</td>
-->


	</tr>


<?php 
		$k = 1 - $k;
	endfor;
?>
</tbody>


</table>

</div>
