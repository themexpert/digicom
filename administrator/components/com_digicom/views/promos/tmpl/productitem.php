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

JHtml::_('behavior.tooltip');
$id_rand = $this->id_rand;
$user_id = JRequest::getVar('userid',0);
$type = JRequest::getVar('type','items');

$document = JFactory::getDocument();
$document->addStyleSheet("components/com_digicom/assets/css/digicom.css");

?>
<div id="product_<?php echo substr($type,0,strlen($type)-1); ?>_<?php echo $id_rand; ?>">
	<table width="100%">
		<tr>
			<td style="border-top:1px solid #ccc;padding-top:5px;" width="30%">
				<?php echo JText::_( 'Product' ); ?>
			</td>
			<td style="border-top:1px solid #ccc;padding-top:5px;">
				<div style="float:left">
					<span id="product_<?php echo $type; ?>_name_text_<?php echo $id_rand; ?>" style="line-height: 17px;padding: 0.2em; border: 1px solid rgb(204, 204, 204); display: block; width: 250px; overflow: visible;"><?php echo JText::_("SELECT_PRODUCT");?></span>
					<input type="hidden" value="" id="product_<?php echo $type; ?>_id<?php echo $id_rand; ?>" name="<?php echo $type; ?>_product_id[<?php echo $id_rand; ?>]"/>
				</div>
				<div><div class="blank" style="padding:0">
					<input type="button" class="btn btn-small" onclick="javascript:grayBoxiJoomla('index.php?option=com_digicom&controller=products&task=selectProducts&id=<?php echo $id_rand; ?>&userid=<?php echo $user_id; ?>&tmpl=component&type=<?php echo $type; ?>', 600, 400)" value="<?php echo JText::_('DIGI_SELECT')?>" />
					<?php
					echo JHTML::tooltip(JText::_("COM_DIGICOM_ORDERPRODUCT_TIP"), '', '',  "<img src=".JURI::root()."administrator/components/com_digicom/assets/images/tooltip.png />", '', '', 'hasTip');
					?>
				</div></div>
			</td>
			<td style="border-top:1px solid #ccc;padding-top:5px;" class="product_<?php echo substr($type,0,strlen($type)-1); ?>_remove_btn">
				<a href="javascript:void(0)" id="product_<?php echo substr($type,0,strlen($type)-1); ?>_remove_<?php echo $id_rand; ?>" onclick="remove_product('<?php echo $id_rand; ?>', '<?php echo substr($type,0,strlen($type)-1); ?>');" style="display:none;">Remove</a>
			</td>
		</tr>
	</table>
</div>