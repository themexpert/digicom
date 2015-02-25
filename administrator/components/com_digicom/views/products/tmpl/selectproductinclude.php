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
$n = count ($this->prods);
$page = $this->pagination;
$configs = $this->configs;
$prc = $this->prc;

$search = JRequest::getVar('search', '');
?>

<script type="text/javascript">

	function selectProductID() {

		var productids = document.adminForm;
		var prod_id = '0';
		var prod_name = '0';
		for(var i=0; i<productids.length; i++) {
			var inputtype = productids.elements[i].type;
			if((inputtype == "radio") && (productids.elements[i].checked)) {
				prod_name = productids.elements[i].value;
				prod_id = productids.elements[i].id;
			}
		}

		window.parent.document.getElementById('product_include_name_text_<?php echo JRequest::getVar('id',0); ?>').innerHTML = prod_name;
		window.parent.document.getElementById('product_include_id<?php echo JRequest::getVar('id',0); ?>').value = parseInt(prod_id);
		//window.parent.show_plan_for_product_include( parseInt(prod_id), '<?php echo JRequest::getVar('id',0); ?>' );
		window.parent.SqueezeBox.close();
	}

</script>

<fieldset class="adminform">
	<legend><?php echo JText::_( 'Select Product for Include' ); ?></legend>
<form id="adminForm" action="index.php?option=com_digicom&controller=products" name="adminForm" method="post">

	<div id="editcell" >

		<table>
			<tr>
				<td colspan="9" nowrap align="left">
					Search: 
					<div class="input-append">
						<input type="text" name="search" value="<?php echo $search; ?>"/>
						<input type="submit" value="Go" class="btn"/>
					</div>
				</td>
				<td colspan="9" nowrap align="right"><?php echo JText::_("DSCATEGORY").": ".$this->csel; ?></td>
			</tr>
		</table>

		<br/>

		<table class="adminlist table">

			<thead>

				<tr>
					<th width="10%">
							<span style="white-space:nowrap;"><?php echo JText::_('VIEWPRODID');?></nobr></span>
					</th>
					<th>
							<?php echo JText::_('VIEWPRODNAME');?>
					</th>
					<!-- th>
							<?php echo JText::_('VIEWPRODPRICE');?>
					</th -->
					<th align="left">
							<?php echo JText::_('VIEWPRODCATEGORY');?>
					</th>
					<th width="1%"></th>
				</tr>

			</thead>

			<tbody>
<?php

		for ($i = 0; $i < $n; $i++):

			$prod = $this->prods[$i];
			$id = $prod->id;
			$name = $prod->name;
?>
				<tr class="row<?php echo $k;?>">

					<td align="center">
								<?php echo $id;?>
					</td>

					<td>
						<?php echo $prod->name;?>
					</td>

					<!-- td align="center">
						<?php echo $prod->price;?>
					</td -->

					<td align="left">
<?php 
						$categories = array();
						foreach( $prod->cats as $j => $z) {
							$categories[] = $z->name;
						}
						echo implode(', ',$categories);
?>
					</td>

					<td>
						<button id="<?php echo $id; ?>_id_product" name="product_id" value="<?php echo $name; ?>" onclick="selectProductID()" class="btn btn-success"><?php echo JText::_('COM_DIGICOM_PRODUCT_SELECT');?></button>
					</td>

				</tr>
<?php
			$k = 1 - $k;

		endfor;
?>
			</tbody>
		</table>

		<div style="padding:1em 0;">
			<?php echo $this->pagination->getListFooter(); ?>
		</div>

	</div>

	<input type="hidden" name="prc" value="<?php echo $this->prc; ?>" />
	<input type="hidden" name="option" value="com_digicom" />
	<input type="hidden" name="task" value="selectProductInclude" />
	<input type="hidden" name="controller" value="products" />
	<input type="hidden" name="tmpl" value="component" />
	<input type="hidden" name="id" value="<?php echo JRequest::getVar('id',0); ?>" />

</form>

</fieldset>
