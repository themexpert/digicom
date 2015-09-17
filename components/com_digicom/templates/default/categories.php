<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;
// Include helper and some js stuffs
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
// Lets cache some variables for grid
$column = $this->configs->get('category_cols','3');
$items = array_chunk($this->items[$this->parent->id], $column);
$grid = 12/$column;
?>
<div id="digicom" class="dc dc-categories-list">
	<?php if($this->params->get('show_page_heading') OR
					 $this->params->get('show_base_description')): ?>
		<div class="dc-head">
			<?php if ($this->params->get('show_page_heading')) : ?>
			<h1>
				<?php echo $this->escape($this->params->get('page_heading')); ?>
			</h1>
			<?php endif; ?>
			<?php if ($this->params->get('show_base_description')) : ?>
				<?php if($this->params->get('categories_description')) : ?>
					<div class="category-desc base-desc">
					<?php echo JHtml::_('content.prepare', $this->params->get('categories_description'), '',  $this->get('extension') . '.categories'); ?>
					</div>
				<?php else : ?>
					<?php //Otherwise get one from the database if it exists. ?>
					<?php  if ($this->parent->description) : ?>
						<div class="category-desc base-desc">
							<?php echo JHtml::_('content.prepare', $this->parent->description, '', $this->parent->extension . '.categories'); ?>
						</div>
					<?php endif; ?>
				<?php endif; ?>
			<?php endif; ?>
		</div> <!-- Category header -->
	<?php endif;?>

	<?php if(count($this->items[$this->parent->id]) > 0 AND $this->maxLevelcat != 0):?>
		<?php foreach($items as $row):?>
			<div class="row">
				<?php foreach($row as $item):?>
					<div class="col-md-<?php echo $grid ?>">
						<div class="thumbnail">
							<?php if($item->getParams()->get('image')) : ?>
								<img src="<?php echo $item->getParams()->get('image'); ?>" alt="<?php echo htmlspecialchars($item->getParams()->get('image_alt')); ?>" />
							<?php endif; ?>
							<div class="caption">
								<h3 class="dc-cat-title">
									<a href="<?php echo JRoute::_(DigiComSiteHelperRoute::getCategoryRoute($item->id));?>">
										<?php echo $this->escape($item->title); ?>
									</a>
									<?php if ($this->params->get('show_cat_num_products_cat') == 1) :?>
										<span class="badge badge-info">
											<?php echo $item->numitems; ?>
										</span>
									<?php endif; ?>
								</h3>
								<?php if (($this->params->get('show_cat_num_products_cat') == 1) AND 						   $item->description ) : ?>
									<div class="dc-cat-desc">
										<?php echo JHtml::_('content.prepare', $item->description, '', 'com_content.categories'); ?>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
				<?php endforeach;?>
			</div>
		<?php endforeach;?>
	<?php endif;?>

<?php echo DigiComSiteHelperDigiCom::powered_by(); ?>

</div>
