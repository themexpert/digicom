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
//echo $listOrder;die;
$listDirn	= $this->escape($this->state->get('list.direction'));

$state_filter = $this->escape($this->state->get('filter.published'));
$search_filter = $this->escape($this->state->get('filter.search'));

$k = 0;
$n = count ($this->cats);

$user		= JFactory::getUser();
$userId		= $user->get('id');
$canOrder	= $user->authorise('core.edit.state', 'com_digicom.component');

$saveOrder	= $listOrder == 'a.ordering';
if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_digicom&controller=categories&task=saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'categoryList', 'adminForm', strtolower($listDirn), $saveOrderingUrl, false, true);
}

$sortFields = $this->getSortFields();

JFactory::getDocument()->addScriptDeclaration('
	Joomla.orderTable = function()
	{
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != "' . $listOrder . '")
		{
			dirn = "asc";
		}
		else
		{
			dirn = direction.options[direction.selectedIndex].value;
		}
		console.log(order);
		console.log(dirn);
		Joomla.tableOrdering(order, dirn, "");
	};
');
?>
<form action="<?php echo JRoute::_('index.php?option=com_digicom&controller=categories'); ?>" id="adminForm" method="post" name="adminForm" class="form-horizontal">
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
					<input id="filter_search" type="text" name="search" placeholder="<?php echo JText::_('DSSEARCH'); ?>" value="<?php echo $search_filter; ?>"/>		
					<button type="submit" class="btn hasTooltip" title="" data-original-title="Search">
						<i class="icon-search"></i>
					</button>
					<button type="button" class="btn hasTooltip js-stools-btn-clear" onclick="document.id('filter_search').value='';this.form.submit();">
						<i class="icon-remove"></i>	
					</button>
				</div>
				
				<div class="btn-wrapper pull-right">
					
					
					<select name="filter_published" onchange="document.adminForm.submit();">
						<option value="" <?php if($state_filter == ""){echo 'selected="selected"'; } ?>><?php echo JText::_("JALL"); ?></option>
						<option value="1" <?php if($state_filter == "1"){echo 'selected="selected"'; } ?>><?php echo JText::_("HELPERPUBLISHED"); ?></option>
						<option value="0" <?php if($state_filter == "0"){echo 'selected="selected"'; } ?>><?php echo JText::_("HELPERUNPUBLICHED"); ?></option>
					</select>
					
					
					<div class="btn-group pull-right">
						<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
						<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
							<option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
							<?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder);?>
						</select>
					</div>
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
		<div class="alert alert-info"> <?php echo JText::_("HEADER_CATEGORIES"); ?> </div>

		<div id="editcell">
			<table class="adminlist table table-striped table-hover" id="categoryList">
				<thead>
					<tr>
						<th width="1%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
						</th>
						<th width="5">
							<input type="checkbox" onclick="checkAll(<?php echo $n; ?>)" name="toggle" value="" />
						</th>
						<th width="5%">
							<?php echo JText::_('VIEWCATEGORYPUBLISHING');?>
						</th>
						<th width="1%">
							<?php echo JText::_('COM_DIGICOM_PRODUCTS_IMG');?>
						</th>
						
						<th class="title">
							<?php echo JText::_('VIEWCATEGORYNAME');?>
						</th>
						<!--
						<th width="10%">
							<?php //echo JHTML::_('grid.order',  $this->cats ); ?>
						</th>
						-->
						
						</th>
							<th width="20">
							<?php echo JText::_('VIEWCATEGORYID');?>
						</th>
						
					</tr>
				</thead>

				<tbody>
			<?php 
			if ($n):
				$z = 0;
				$ordering = true;
				foreach ($this->cats as $i => $v):

					$cat = $this->cats[$i];
					$id = $cat->id;
					$checked = JHTML::_('grid.id', $z, $id);
					$link = JRoute::_("index.php?option=com_digicom&controller=categories&task=edit&cid[]=".$id);
					$published = JHTML::_('grid.published', $cat, $z );

					$canCreate  = $user->authorise('core.create',     'com_digicom.component');
					$canEdit    = $user->authorise('core.edit',       'com_digicom.component');
					$canCheckin = $user->authorise('core.manage',     'com_checkin') || $cat->checked_out == $user->get('id') || $cat->checked_out == 0;
					$canChange  = $user->authorise('core.edit.state', 'com_digicom.component') && $canCheckin;
					
					// Get the parents of item for sorting
					if ($cat->level > 1)
					{
						$parentsStr = "";
						$_currentParentId = $cat->parent_id;
						$parentsStr = " " . $_currentParentId;
						for ($i2 = 0; $i2 < $cat->level; $i2++)
						{
							foreach ($this->ordering as $k => $v)
							{
								$v = implode("-", $v);
								$v = "-" . $v . "-";
								if (strpos($v, "-" . $_currentParentId . "-") !== false)
								{
									$parentsStr .= " " . $k;
									$_currentParentId = $k;
									break;
								}
							}
						}
					}
					else
					{
						$parentsStr = "";
					}
			?>
				<tr class="row<?php echo $k; ?>" sortable-group-id="<?php echo $cat->parent_id; ?>" item-id="<?php echo $cat->id ?>" parents="<?php echo $parentsStr ?>" level="<?php echo $cat->level ?>"> 

					<td class="order nowrap center hidden-phone">
							<?php
							$iconClass = '';
							if (!$canChange)
							{
								$iconClass = ' inactive';
							}
							elseif (!$saveOrder)
							{
								$iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
							}
							?>
							<span class="sortable-handler<?php echo $iconClass ?>">
								<i class="icon-menu"></i>
							</span>
							<?php if ($canChange && $saveOrder) : ?>
								<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $cat->ordering;?>" class="width-20 text-area-order " />
							<?php endif; ?>
					</td>
					<td>
						<?php echo $checked;?>
					</td>
					<td align="center">
						<?php echo $published;?>
					</td>
					<td align="center">
						<?php if(!empty($cat->image)): ?>
						<img src="<?php echo JUri::root() . $cat->image; ?>" height="48" width="48">
						<?php endif; ?>
					</td>
					
					<td>
						<?php echo str_repeat('<span class="gi">&mdash;</span>', $cat->level - 1) ?>
							<?php if ($cat->checked_out) : ?>
								<?php echo JHtml::_('jgrid.checkedout', $i, $cat->editor, $cat->checked_out_time, 'categories.', $canCheckin); ?>
							<?php endif; ?>
							<?php if ($canEdit || $canEditOwn) : ?>
								<a href="<?php echo $link; ?>">
									<?php echo $this->escape($cat->name); ?></a>
							<?php else : ?>
								<?php echo $this->escape($cat->name); ?>
							<?php endif; ?>
							<span class="small" title="<?php echo $this->escape($cat->path); ?>">
								<?php if (empty($cat->note)) : ?>
									<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($cat->alias)); ?>
								<?php else : ?>
									<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS_NOTE', $this->escape($cat->alias), $this->escape($cat->note)); ?>
								<?php endif; ?>
							</span>
						<!--		<a href="<?php echo $link;?>" >
									<?php
										//echo str_repeat('<span class="gi">|&mdash;</span>', $cat->level).$cat->name;
										//echo $cat->treename;
									?>
								</a>
						-->
					</td>
					<!--
					<td class="order">
						<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
						<input type="text" name="order[]" size="5" value="<?php echo $cat->ordering; ?>" <?php echo $disabled; ?> class="text_area" style="text-align: center" />
					</td>
					-->
					<td>
								<?php echo $id;?>
					</td>
					

				</tr>


			<?php 
					$z++;
					$k = 1 - $k;
				endforeach;
			endif;
			?>
				</tbody>

				<tfoot>
					<tr>
						<td colspan="5">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>

			</table>

		</div>
	</div>
	<input type="hidden" name="option" value="com_digicom" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="controller" value="categories" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listOrder; ?>" />
</form>