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
JHtml::_('behavior.multiselect');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

$k = 0;
$n = count ($this->prods);
$page = $this->pagination;
$configs = $this->configs;
$prc = JRequest::getVar("prc", "-1");
$search_session = $this->escape($this->state->get('filter.search'));
$state_filter = $this->escape($this->state->get('filter.published'));
$limit = JRequest::getVar("limit", "25");
$limistart = $this->pagination->limitstart;

$user		= JFactory::getUser();
$userId		= $user->get('id');
$canOrder	= $user->authorise('core.edit.state', 'com_digicom.component');

$archived	= $this->state->get('filter.published') == 2 ? true : false;
$trashed	= $this->state->get('filter.published') == -2 ? true : false;

$saveOrder	= $listOrder == 'a.ordering';
if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_digicom&controller=products&task=saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'productList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
$sortFields = $this->getSortFields();

$prodedit = JRequest::getInt('prodedit',0);
$type = JRequest::getVar('type','');
$type = ($type == 'item' ? 'items' : $type);
$type = ($type == 'order' ? 'orders' : $type);
?>
<script type="text/javascript">

	function selectProductLink(id)
	{
		if (document.getElementById(id)) {
			document.getElementById(id).checked = true;
			selectProductID();
		}
	}

	function selectProductID()
	{
		var promotype = '<?php echo $type;?>';
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

		if (promotype != '')
		{
			<?php if(!$prodedit):?>
			window.parent.addProduct('<?php echo $type;?>', '<?php echo JRequest::getVar('id',0); ?>');
			<?php endif;?>
			window.parent.document.getElementById('product_<?php echo $type;?>_name_text_<?php echo JRequest::getVar('id',0); ?>').innerHTML = prod_name;
			window.parent.document.getElementById('product_<?php echo $type;?>_name_text_<?php echo JRequest::getVar('id',0); ?>').style.color = "#999";
			window.parent.document.getElementById('product_<?php echo $type;?>_id<?php echo JRequest::getVar('id',0); ?>').value = parseInt(prod_id);
			window.parent.document.getElementById('product_<?php echo substr($type,0,strlen($type)-1);?>_remove_<?php echo JRequest::getVar('id',0); ?>').style.display = 'block';
		}
		else
		{
			window.parent.document.getElementById('product_name_text_<?php echo JRequest::getVar('id',0); ?>').innerHTML = prod_name;
			window.parent.document.getElementById('product_id<?php echo JRequest::getVar('id',0); ?>').value = parseInt(prod_id);
			window.parent.show_attribute_product('<?php echo JRequest::getVar('id',0); ?>');
		}

		window.parent.SqueezeBox.close();
	}

</script>
<form id="adminForm" action="<?php echo JRoute::_('index.php?option=com_digicom&controller=products'); ?>" method="post" name="adminForm" autocomplete="off" class="form-horizontal">
	<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="">
<?php else : ?>
	<div id="j-main-container" class="">
<?php endif;?>
		
		<div class="js-stools">
			<div class="clearfix">
				<div class="btn-wrapper input-append">
					<input id="filter_search" type="text" name="filter_search" placeholder="<?php echo JText::_('DSSEARCH'); ?>" value="<?php echo $search_session; ?>"/>		
					<button type="submit" class="btn hasTooltip" title="" data-original-title="Search">
						<i class="icon-search"></i>
					</button>
					<button type="button" class="btn hasTooltip js-stools-btn-clear" onclick="document.id('filter_search').value='';this.form.submit();">
						<i class="icon-remove"></i>	
					</button>
				</div>
				
				<div class="btn-wrapper pull-right">
					<?php echo $this->csel; ?>					
					
					<div class="btn-group pull-right hidden-phone">
						<label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
						<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
							<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
							<option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING');?></option>
							<option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');?></option>
						</select>
					</div>
					<div class="btn-group pull-right hidden-phone">
						<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
						<?php echo $this->pagination->getLimitBox(); ?>
					</div>
				</div>
			</div>
		</div>
		<br>
		<div class="alert alert-info">
			<?php echo JText::_("COM_DIGICOM_PRODUCTS_SELECT"); ?>
		</div>
		<div id="editcell" >

			<table class="adminlist table table-striped table-hover" id="productList">

				<thead>

					<tr>
						<th>
							<?php echo JHtml::_('grid.sort', 'VIEWPRODNAME', 'name', $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo JHtml::_('grid.sort', 'COM_DIGICOM_PRODUCTS_TYPE', 'product_type', $listDirn, $listOrder); ?>
						</th>
						<th width="10%">
							<?php echo JHtml::_('grid.sort', 'COM_DIGICOM_PRODUCTS_PRICE', 'hide_public', $listDirn, $listOrder); ?>
						</th>
						<th width="10%">
							<?php echo JHtml::_('grid.sort', 'PRODUCT_IS_VISIBLE', 'hide_public', $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo JText::_('VIEWPRODCATEGORY'); ?>
						</th>
					</tr>

				</thead>

				<tbody>

				<?php
				JHTML::_("behavior.tooltip");
				$ordering = true;
				$cselected = "";
				$poz = $limistart + 1;
				if ($prc > 0) $cselected .= "&prc=".$prc;
				if ($state_filter != "-1") $cselected .= "&state_filter=".$state_filter;
				else $cselected = '';
				for ($i = 0; $i < $n; $i++):
					$prod = $this->prods[$i];
					$id = $prod->id;
					$checked = JHTML::_('grid.id', $i, $id);
					$link = JRoute::_("index.php?option=com_digicom&controller=products&task=edit&cid[]=".$id.$cselected);
					$published = JHTML::_('grid.published', $prod->published, $i);
					DigiComAdminHelper::publishAndExpiryHelper($img, $alt, $times, $status, $prod->publish_up, $prod->publish_down, $prod->published, $this->configs);
					
					$canCreate  = $user->authorise('core.create',     'com_digicom.component');
					$canEdit    = $user->authorise('core.edit',       'com_digicom.component');
					$canCheckin = $user->authorise('core.manage',     'com_checkin') || $prod->checked_out == $user->get('id') || $prod->checked_out == 0;
					$canChange  = $user->authorise('core.edit.state', 'com_digicom.component') && $canCheckin;
					

					?>
					<tr class="row<?php echo $k;?>">
						<td>
							<span class="hide"><input id="<?php echo $id; ?>_id_product" type="radio" name="product_id" value="<?php echo $prod->name; ?>" onclick="selectProductID()"/></span>
							<a href="#" onclick="selectProductLink('<?php echo $id; ?>_id_product')"><?php echo $prod->name;?></a>
						</td>
						<td>
							<?php
								switch ( $prod->product_type )
								{
									case 'bundle':
										echo JText::_('VIEWPRODPRODTYPEPAK');
										break;
									case 'reguler':
									default:
										echo JText::_('VIEWPRODPRODTYPEDNR');
										break;
								}
							?>
						</td>
						<td align="center">
							<?php echo $prod->price; ?>
						</td>
						<td align="center" style="text-align: center; ">
							<?php echo ($prod->hide_public ? '<span class="label label-important">' . JText::_("DSNO") . '</span>' : '<span class="label label-success">' . JText::_("DSYES") . '</span>' ); ?>
						</td>
						
						<td align="center">
							<a href="<?php echo JRoute::_("index.php?option=com_digicom&controller=categories&task=edit&cid[]=".$prod->catid); ?>" ><?php echo $prod->category_title; ?></a>
									<?php //foreach( $prod->cats as $j => $z) {
										//$clink = JRoute::_("index.php?option=com_digicom&controller=categories&task=edit&cid[]=".$z->id);
										//echo '<a href="'.$clink.'" >'.$z->name.'</a><br />';
									//}
									?>
						</td>
					</tr>
							<?php
							$k = 1 - $k;
						endfor;
						?>
				</tbody>

				<tfoot>
					<tr>
						<td colspan="8">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>

	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listOrder; ?>" />

	<input type="hidden" name="prc" value="<?php echo $this->prc; ?>" />
	<input type="hidden" name="option" value="com_digicom" />
	<input type="hidden" name="task" value="selectProducts" />
	<input type="hidden" name="controller" value="products" />
	<input type="hidden" name="id" value="<?php echo JRequest::getVar('id',0); ?>" />
	<input type="hidden" name="tmpl" value="component" />

</form>
