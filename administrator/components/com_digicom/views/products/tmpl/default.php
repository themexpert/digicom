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

$app = JFactory::getApplication();
$listOrder	= $app->getUserState('digicom.product.list.ordering');
$listDirn	= $app->getUserState('digicom.product.list.direction');

$k = 0;
$n = count ($this->prods);
$page = $this->pagination;
$configs = $this->configs;
$prc = JRequest::getVar("prc", "-1");
$session = JFactory::getSession();
$search_session = $session->get('digicom.product.search');
$state_filter = JRequest::getVar("state_filter", "-1");

$document = JFactory::getDocument();
//$document->addStyleSheet("components/com_digicom/assets/css/digicom.css");

$limistart = $this->pagination->limitstart;

?>
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
					<input type="text" name="search" placeholder="<?php echo JText::_('DSSEARCH'); ?>" value="<?php echo $search_session; ?>"/>		
					<button type="submit" class="btn hasTooltip" title="" data-original-title="Search">
						<i class="icon-search"></i>
					</button>
				</div>
				
				<div class="btn-wrapper pull-right">
					<?php echo $this->csel; ?>
					
					<select name="state_filter" onchange="document.adminForm.submit();">
						<option value="-1" <?php if($state_filter == "-1"){echo 'selected="selected"'; } ?>><?php echo JText::_("DIGI_SELECT_STATE"); ?></option>
						<option value="1" <?php if($state_filter == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("HELPERPUBLISHED"); ?></option>
						<option value="0" <?php if($state_filter == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("HELPERUNPUBLICHED"); ?></option>
					</select>
				</div>
			</div>
		</div>
		<br>
		<div class="alert alert-info">
			<?php echo JText::_("HEADER_PRODUCTS"); ?>
		</div>
		<div id="editcell" >

			<table class="adminlist table table-striped table-hover">

				<thead>

					<tr>
						<th width="1%">
							<span><?php echo JHtml::_('grid.checkall'); ?></span>
						</th>
						<th width="1%">
							<?php echo JHtml::_('grid.sort', 'VIEWPRODSKU', 'id', $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo JHtml::_('grid.sort', 'VIEWPRODNAME', 'name', $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo JHtml::_('grid.sort', 'VIEWPRODTYPE', 'product_type', $listDirn, $listOrder); ?>
						</th>
						<th width="10%">
							<?php echo JHtml::_('grid.sort', 'PRODUCT_IS_VISIBLE', 'hide_public', $listDirn, $listOrder); ?>
						</th>
						<th width="10%">
							<?php echo JHtml::_('grid.sort', 'VIEWPRODPUBLISHING', 'published', $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo JText::_('VIEWPRODCATEGORY'); ?>
						</th>
						<th width="1%">
							<?php echo JHtml::_('grid.sort', 'VIEWPRODID', 'id', $listDirn, $listOrder); ?>
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
					?>
					<tr class="row<?php echo $k;?>">
						<td>
							<?php echo $checked; ?>
						</td>
						<td align="center">
							<?php echo $prod->id; ?>
						</td>
						<td>
							<a href="<?php echo $link;?>" ><?php echo $prod->name;?></a>
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
							<?php echo ($prod->hide_public ? '<span style="color:#ff0000;">' . JText::_("DSNO") . '</span>' : JText::_("DSYES")); ?>
						</td>
						<td align="center">
							<?php echo $published; ?>
						</td>
						<td align="center">
									<?php foreach( $prod->cats as $j => $z) {
										$clink = JRoute::_("index.php?option=com_digicom&controller=categories&task=edit&cid[]=".$z->id);
										echo '<a href="'.$clink.'" >'.$z->name.'</a><br />';
									}
									?>
						</td>
						<td align="center">
							<?php echo $id; ?>
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

	<input type="hidden" name="prc" value="<?php echo $this->prc; ?>" />
	<input type="hidden" name="option" value="com_digicom" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="controller" value="products" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listOrder; ?>" />

</form>
