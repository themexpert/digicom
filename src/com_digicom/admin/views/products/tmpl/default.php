<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/html/');

JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

$user		= JFactory::getUser();
$userId		= $user->get('id');
$canOrder	= $user->authorise('core.edit.state', 'com_digicom.component');

$archived	= $this->state->get('filter.published') == 2 ? true : false;
$trashed	= $this->state->get('filter.published') == -2 ? true : false;
$product_type	= $this->state->get('filter.product_type');

$saveOrder	= $listOrder == 'a.ordering';
if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_digicom&view=products&task=products.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'productList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

$assoc		= JLanguageAssociations::isEnabled();

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
		Joomla.tableOrdering(order, dirn, "");
	};
');
?>
<form id="adminForm" action="<?php echo JRoute::_('index.php?option=com_digicom&view=products'); ?>" method="post" name="adminForm" autocomplete="off" class="form-horizontal">
	<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="">
<?php else : ?>
	<div id="j-main-container" class="">
<?php endif;?>
		<div class="dg-alert dg-alert-with-icon">

			<span class="icon-video"></span><?php echo JText::_("COM_DIGICOM_PRODUCTS_HEADER_NOTICE"); ?>
			<a href="#videoTutorialModal" role="button" class="btn btn-primary btn-small pull-right" data-toggle="modal">
				<?php echo JText::_("COM_DIGICOM_GUIDE_VIDEO"); ?> <i class="icon-arrow-right-4"></i>
			</a>

		</div>

		<?php
		// Search tools bar
		echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
		?>

		<div id="editcell" >

			<table class="adminlist table table-striped table-hover" id="productList">

				<thead>

					<tr>
						<th width="1%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
						</th>
						<th width="1%">
							<span><?php echo JHtml::_('grid.checkall'); ?></span>
						</th>
						<th width="1%" style="min-width:55px">
							<?php echo JHtml::_('grid.sort', 'COM_DIGICOM_PRODUCTS_ACTION', 'published', $listDirn, $listOrder); ?>
						</th>
						<th class="nowrap hidden-phone" width="10%">
							<?php echo JHtml::_('grid.sort', 'COM_DIGICOM_PRODUCTS_IMAGE', 'id', $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'name', $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo JHtml::_('grid.sort', 'COM_DIGICOM_PRODUCTS_TYPE', 'product_type', $listDirn, $listOrder); ?>
						</th>
						<th width="10%">
							<?php echo JHtml::_('grid.sort', 'COM_DIGICOM_PRODUCTS_PRICE', 'price', $listDirn, $listOrder); ?>
						</th>
						<th width="1%" class="nowrap hidden-phone">
							<?php echo JText::_('COM_DIGICOM_PRODUCTS_VALIDITY'); ?>
						</th>
						<th width="10%">
							<?php echo JHtml::_('grid.sort', 'COM_DIGICOM_PRODUCTS_STOCK', 'hide_public', $listDirn, $listOrder); ?>
						</th>
						<th width="10%" class="nowrap hidden-phone hide-mediam">
							<?php echo JHtml::_('searchtools.sort',  'JGRID_HEADING_ACCESS', 'a.access', $listDirn, $listOrder); ?>
						</th>
						<th width="1%" class="nowrap hidden-phone hide-mediam">
							<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder); ?>
						</th>
						<th width="1%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'JGLOBAL_HITS', 'a.hits', $listDirn, $listOrder); ?>
						</th>

					</tr>

				</thead>

				<tbody>

				<?php foreach ($this->items as $i => $item) :
					$item->max_ordering = 0;
					$ordering   = ($listOrder == 'a.ordering');
					$canEdit    = $user->authorise('core.edit',       'com_digicom');
					$canCheckin = $user->authorise('core.manage',     'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
					$canChange  = $user->authorise('core.edit.state', 'com_digicom') && $canCheckin;
					?>
					<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->catid; ?>">
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
								<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />
							<?php endif; ?>
						</td>
						<td class="center">
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						</td>
						<td class="center">
							<div class="btn-group">
								<?php //echo $checked; ?>

								<?php echo JHtml::_('jgrid.published', $item->published, $i, 'products.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>

								<?php
								if($canChange){
									echo JHtml::_('featured.featured', $item->featured, $i, 'products.', $canChange, 'cb');

									JHtml::_('actionsdropdown.duplicate', 'cb' . $i, 'products');

									$action = $trashed ? 'untrash' : 'trash';
									JHtml::_('actionsdropdown.' . $action, 'cb' . $i, 'products');

									$action = $archived ? 'unarchive' : 'archive';
									JHtml::_('actionsdropdown.' . $action, 'cb' . $i, 'products');

									echo JHtml::_('actionsdropdown.render', $this->escape($item->name));
								}


								?>
							</div>
						</td>
						<td align="center" class="nowrap hidden-phone">
							<?php if(!empty($item->image_intro)): ?>
								<div class="product-thumb">
									<img src="<?php echo JUri::root() . $item->image_intro; ?>" >
								</div>
							<?php endif; ?>
						</td>
						<td class="has-context">
							<div class="pull-left break-word">
								<?php if ($item->checked_out) : ?>
									<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'products.', $canCheckin); ?>
								<?php endif; ?>
								<?php if ($item->language == '*'):?>
									<?php $language = JText::alt('JALL', 'language'); ?>
								<?php else:?>
									<?php $language = $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
								<?php endif;?>
								<?php if ($canEdit) : ?>
									<a class="hasTooltip" href="<?php echo JRoute::_('index.php?option=com_digicom&view=product&task=product.edit&id=' . $item->id); ?>" title="<?php echo JText::_('JACTION_EDIT'); ?>">
										<?php echo $this->escape($item->name); ?></a>
								<?php else : ?>
									<span title="<?php echo JText::sprintf('JFIELD_ALIAS_LABEL', $this->escape($item->alias)); ?>"><?php echo $this->escape($item->name); ?></span>
								<?php endif; ?>
								<span class="small break-word">
									<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias)); ?>
								</span>
								<div class="small">
									<?php echo JText::_('JCATEGORY') . ": " . $this->escape($item->category_title); ?>
								</div>
							</div>
						</td>
						<td>
							<?php
								switch ( $item->product_type )
								{
									case 'bundle':
										echo JText::_('COM_DIGICOM_PRODUCTS_TYPE_BUNDLE');
										break;
									case 'reguler':
									default:
										echo JText::_('COM_DIGICOM_PRODUCTS_TYPE_SINGLE');
										break;
								}
							?>
						</td>
						<td class="small">
							<?php echo DigiComHelperDigiCom::format_price($item->price, $this->configs->get('currency','USD'), true, $this->configs); ?>
						</td>
						<td class="small hidden-phone">
								<?php echo DigiComSiteHelperPrice::getProductValidityPeriod($item); ?>
						</td>
						<td align="center" style="text-align: center; ">
							<?php echo ($item->hide_public ? '<span class="label label-important">' . JText::_("JNO") . '</span>' : '<span class="label label-success">' . JText::_("JYES") . '</span>' ); ?>
						</td>
						<td class="small hidden-phone hide-mediam">
							<?php echo $this->escape($item->access_level); ?>
						</td>

						<td class="center hidden-phone hide-mediam">
							<?php echo (int) $item->id; ?>
						</td>
						<td class="center hidden-phone">
							<?php echo (int) $item->hits; ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<div class="pagination-centered">
				<?php echo $this->pagination->getListFooter(); ?>
			</div>
		</div>
	</div>

	<input type="hidden" name="option" value="com_digicom" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="view" value="products" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listOrder; ?>" />
	<?php echo JHtml::_('form.token'); ?>

</form>

<?php
	echo JHtml::_(
		'bootstrap.renderModal',
		'videoTutorialModal',
		array(
			'url' => 'https://www.youtube-nocookie.com/embed/pIfktnNwNsU?list=PL5eH3TQ0wUTZXKs632GyKMzGVkxdfPB4f&amp;showinfo=0',//&amp;autoplay=1
			'title' => JText::_('COM_DIGICOM_ABOUT_PRODUCT_USE_VIDEO'),
			'height' => '400px',
			'width' => '1280'
		)
	);
?>


<div class="dg-footer">
	<?php echo JText::_('COM_DIGICOM_CREDITS'); ?>
</div>
