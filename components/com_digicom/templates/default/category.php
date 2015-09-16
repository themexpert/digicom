<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

$this->bsGrid = array(1 => 'col-md-12', 2 => 'col-md-6', 3 => 'col-md-4', 4 => 'col-md-3', 6 => 'col-md-2');
$this->column = $this->category->params->get('category_cols', 3);
?>
<div id="digicom" class="dc dc-category">

	<?php if ($this->params->get('show_page_heading')) : ?>
		<div class="page-header">
			<h1> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
		</div>
	<?php endif; ?>

	<div class="dc-category-item">

		<?php if($this->category->params->get('show_cat_title',1) or $this->category->params->get('show_cat_image',1) or $this->category->params->get('show_cat_intro',1)): ?>
		<!-- Category Info -->
		<div class="category-info clearfix">
			<!-- Category Name -->
			<?php if($this->category->params->get('show_cat_title',1) && !empty($this->category->title)): ?>
			<h1 class="page-title"><?php echo $this->category->title; ?></h1>
			<?php endif; ?>

			<?php if ($this->params->get('show_cat_tags', 1) && !empty($this->category->tags->itemTags)) : ?>
				<?php $this->category->tagLayout = new JLayoutFile('joomla.content.tags'); ?>
				<?php echo $this->category->tagLayout->render($this->category->tags->itemTags); ?>
			<?php endif; ?>

			<?php if($this->category->params->get('show_cat_image',1) AND ($this->category->params->get('image') !== NULL ) ): ?>
			<div class="pull-left">
				<img class="img-rounded" src="<?php echo $this->category->params->get('image'); ?>" />
			</div>
			<?php endif; ?>

			<?php if($this->category->params->get('show_cat_intro',1) && !empty($this->category->description)): ?>
			<div class="category-desc">
				<?php echo $this->category->description; ?>
			</div>
			<?php endif; ?>
		</div>
		<?php endif; ?>

		<?php
		$itemscount = (count($this->items));
		$counter = 0;
		?>
		<?php if (!empty($this->items)) : ?>
			<div class="dc-products-list clearfix">
			<?php foreach ($this->items as $key => &$item) : ?>
				<?php $rowcount = ((int) $key % (int) $this->column) + 1; ?>
				<?php if ($rowcount == 1) : ?>
					<?php $row = $counter / $this->column; ?>
					<div class="dc-products-row row clearfix">
				<?php endif; ?>
				<div class="<?php echo $this->bsGrid[$this->column]?>">
					<div class="dc-product column-<?php echo $rowcount; ?><?php echo $item->published == 0 ? ' system-unpublished' : null; ?>"
						itemscope itemtype="http://schema.org/Product">
						<?php
						$this->item = & $item;
						echo $this->loadTemplate('item');
						?>
					</div>
					<!-- end item -->
					<?php $counter++; ?>
				</div><!-- end column class -->
				<?php if (($rowcount == $this->column) or ($counter == $itemscount)) : ?>
					</div><!-- end row -->
				<?php endif; ?>
			<?php endforeach; ?>

			</div>

			<div class="dc-pagination pagination"><?php echo $this->pagination->getPagesLinks(); ?></div>

		<?php endif; ?>

	</div>

	<?php echo DigiComSiteHelperDigicom::powered_by(); ?>

</div>
