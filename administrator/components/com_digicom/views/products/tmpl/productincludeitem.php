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

JHTML::_('behavior.modal');
$include = $this->newinclude;

?>

<div id="product_include_box_<?php echo $include['id']; ?>" style="border-bottom:1px solid #ccc;margin:15px;padding:10px;">
	<table width="100%">
		<tr>
			<td style="" width="30%"><?php echo JText::_( 'Product' ); ?></td>
			<td style="">
				<div style="float:left">
					<span id="product_include_name_text_<?php echo $include['id']; ?>" style="line-height: 17px;padding: 0.2em; border: 1px solid rgb(204, 204, 204); display: block; width: 250px;"><?php echo $include['name']; ?></span>
					<input type="hidden" value="" id="product_include_id<?php echo $include['id']; ?>" name="products_bundle[<?php echo $include['id']; ?>]"/>
				</div>
				<div class="button2-left">
					<div class="blank input-append" style="margin-left: -1px;">
						<input type="button" class="btn btn-small" onclick="javascript:grayBoxiJoomla('index.php?option=com_digicom&controller=products&task=selectProductInclude&id=<?php echo $include['id']; ?>&tmpl=component', 600, 400)" value="<?php echo JText::_('DIGI_SELECT')?>" />
					</div>
				</div>
			</td>
			<td style="">
				<a id="product_include_remove_1" class="btn btn-small btn-danger" href="javascript:void(0)" onclick="remove_product_include('<?php echo $include['id']; ?>');">Remove</a>
			</td>
		</tr>
		
	</table>
</div>