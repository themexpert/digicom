<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

$column = $this->category->params->get('category_cols', 3);
$items = array_chunk($this->items, $column);
$grid = 12/$column;
?>
<div id="digicom" class="dc dc-category">

	<?php
		if($this->params->get('show_page_heading') OR
			$this->category->params->get('show_cat_title') OR
			$this->category->params->get('show_cat_intro') OR
			($this->category->params->get('show_cat_image')  AND $this->category->params->get('image') !== NULL )


		):
	?>
	 <header class="dc-category">
	 		<?php if ($this->params->get('show_page_heading')) : ?>
   			<h1> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
   		<?php endif; ?>

			<?php if($this->category->params->get('show_cat_image', 1) AND ($this->category->params->get('image') !== NULL ) ): ?>
				<!-- Category Info -->
				<div class="dc-cat-head clearfix">
					<div class="dc-cat-media pull-left">
						<img class="img-responsive" src="<?php echo $this->category->params->get('image'); ?>" />
					</div>
				</div>
			<?php endif; ?>

			<?php
			if(
				($this->category->params->get('show_cat_intro',1) && !empty($this->category->description))
				or
				($this->params->get('show_cat_tags', 1) && !empty($this->category->tags->itemTags) && count($this->category->tags->itemTags) > 0)
				or
				($this->category->params->get('show_cat_title',1) && !empty($this->category->title))
			): ?>

				<div class="dc-cat-body">

					<?php if($this->category->params->get('show_cat_title',1) && !empty($this->category->title)): ?>
					<!-- Category Name -->
					<h1><?php echo $this->category->title; ?></h1>
					<?php endif; ?>

					<?php if ($this->params->get('show_cat_tags', 1) && !empty($this->category->tags->itemTags)) : echo 44;?>
						<!-- Category Tags -->
						<?php $this->category->tagLayout = new JLayoutFile('joomla.content.tags'); ?>
						<?php echo $this->category->tagLayout->render($this->category->tags->itemTags); ?>
					<?php endif; ?>

					<?php if($this->category->params->get('show_cat_intro',1) && !empty($this->category->description)): ?>
					<div class="dc-cat-desc">
						<?php echo $this->category->description; ?>
					</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
	</header>
	<?php endif;?>

	<div class="dc-items" data-digicom-items>
		<?php foreach($items as $row) :?>
		<div class="row">
			<?php foreach($row as $item) :?>
				<div class="col-md-<?php echo $grid?>">
					<?php
						// Load item template
						$this->item = $item;
						echo $this->loadTemplate('item');
					?>
				</div>
			<?php endforeach;?>
		</div>
		<?php endforeach;?>
	</div>

	<div class="dc-pagination pagination">
		<?php echo $this->pagination->getPagesLinks(); ?>
	</div>

	<?php echo DigiComSiteHelperDigicom::powered_by(); ?>

</div>
