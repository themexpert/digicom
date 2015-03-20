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
JHTML::_('behavior.tooltip');
?>

<?php

$document= JFactory::getDocument();
$document->addScript(JURI::root()."administrator/components/com_digicom/assets/js/digicom.js");
//$document->addStyleSheet("components/com_digicom/assets/css/digicom.css");

?>

<table>
	<tr>
		<td align="right">
			<a class="modal digi_video" rel="{handler: 'iframe', size: {x: 750, y: 435}}" href="index.php?option=com_digicom&controller=about&task=vimeo&id=38437487">
				<img src="<?php echo JURI::base(); ?>components/com_digicom/assets/images/icon_video.gif" class="video_img" />
				<?php echo JText::_("COM_DIGICOM_VIDEO_CUST_WIZARD"); ?>				  
			</a>
		</td>
	</tr>
</table>

<form id="adminForm" name="adminForm" method="post">
	<input type="radio" checked="checked" name="author_type" onclick="javascript:hideUsername();" value="0"/><?php echo JText::_("DIGI_NEW_CUSTOMER"); ?>
	<span class="editlinktip hasTip" title="<?php echo JText::_("DIGI_NEW_CUSTOMER")."::".JText::_("DIGI_NEW_CUSTOMER_TIP"); ?>" >
		<img border="0" src="components/com_digicom/assets/images/icons/tooltip.png">
	</span>
	<br/>
	<input type="radio" name="author_type" onclick="javascript:showUsername();" value="1"/><?php echo JText::_("DIGI_OLD_CUSTOMER"); ?>
	<span class="editlinktip hasTip" title="<?php echo JText::_("DIGI_OLD_CUSTOMER")."::".JText::_("DIGI_OLD_CUSTOMER_TIP"); ?>" >
		<img border="0" src="components/com_digicom/assets/images/icons/tooltip.png">
	</span>
	<div id="user_name" style="display:none; ">
	   <table> 
		   <tr>
			  <td style="padding: 7px" width="15%">
					<?php echo JText::_("VIEWCUSTOMERUSER"); ?>&nbsp;&nbsp;<input type="text" name="username" value=""> 
			  </td> 
		   </tr>
	   </table>
   </div>
	<input type="hidden" name="option" value="com_digicom" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="hidemainmenu" value="0" />
	<input type="hidden" name="controller" value="Customers" />
</form>
