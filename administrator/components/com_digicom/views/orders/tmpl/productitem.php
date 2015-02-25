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

?>
<div id="product_item_<?php echo $id_rand; ?>" class="add-product">
	<table class="table">
		<tr>
			
			<td style="">
				<div style="float:left">
					<span id="product_name_text_<?php echo $id_rand; ?>" class="product-name">Select a Product</span>
					<input type="hidden" value="" id="product_id<?php echo $id_rand; ?>" name="product_id[<?php echo $id_rand; ?>]"/>
				</div>
				<div>
					<div class="blank btn-group" style="padding:0">
						<input type="button" class="btn" onclick="javascript:grayBoxiJoomla('index.php?option=com_digicom&controller=products&task=selectProducts&id=<?php echo $id_rand; ?>&tmpl=component', 600, 400)" value="<?php echo JText::_('DIGI_SELECT')?>" />
						<a class="btn btn-danger" href="javascript:void(0)" id="product_item_remove_1" onclick="remove_product('<?php echo $id_rand; ?>');">x</a>
					</div>
				</div>
			</td>
		</tr>
	</table>


</div>