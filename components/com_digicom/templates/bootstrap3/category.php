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
$this->column = $this->category->params->get('category_cols',3);
?>
<div id="digicom">

	<?php if ($this->params->get('show_page_heading')) : ?>
		<div class="page-header">
			<h1> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
		</div>
	<?php endif; ?>

	<div class="digi-categories">

		<?php if($this->category->params->get('show_cat_title',1) or $this->category->params->get('show_cat_image',1) or $this->category->params->get('show_cat_intro',1)): ?>
		<!-- Category Info -->
		<div class="category-info clearfix">
			<!-- Category Name -->
			<?php if($this->category->params->get('show_cat_title',1) && !empty($this->category->title)): ?>
			<h1 class="digi-page-title"><?php echo $this->category->title; ?></h1>
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

		<div class="products-list clearfix">
        <div class="row">
        	<?php
				  $i=0;
				  foreach($this->items as $key=>$item):
					 	if(! ($i % $this->column)  && $i != '0' )  echo '</div><div class="row">';
					 	?>
					 	<?php
					 	$this->item = & $item;
					 	echo $this->loadTemplate('item');
					 	?>
				  	<?php
				  	$i++;
				  endforeach;
				  ?>
        </div>
		</div>
		<div class="pagination"><?php echo $this->pagination->getPagesLinks(); ?></div>
	</div>

</div>
<?php
echo DigiComSiteHelperDigicom::powered_by();
