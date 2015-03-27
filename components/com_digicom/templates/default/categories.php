<?php
/**
 * @package			com_digicom
 * @author			themexpert.com
 * @version			1.0beta1
 * @copyright		Copyright (C) 2010-2015 ThemeXpert. All rights reserved.
 * @license			GNU/GPLv3
*/

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
JHtml::_('behavior.caption');
$class = 'first ';
JHtml::_('bootstrap.tooltip');
$lang	= JFactory::getLanguage();
$bsGrid = array(
	1 => 'span12',
	2 => 'span6',
	3 => 'span4',
	4 => 'span3',
	6 => 'span2'
);

?>

<div id="digicom" class="categories-list<?php echo $this->pageclass_sfx;?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
	<h1>
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h1>
	<?php endif; ?>

	<?php if ($this->params->get('show_base_description')) : ?>
		<?php //If there is a description in the menu parameters use that; ?>

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

	<div class="row-fluid">
        <ul class="thumbnails">
        	<?php
			if (count($this->items[$this->parent->id]) > 0 && $this->maxLevelcat != 0) :
				$i=0;
				foreach($this->items[$this->parent->id] as $id => $item) : 
					if($i !=0 && !($i % $this->configs->get('category_cols')) )  echo '</ul></div><div class="row-fluid"><ul class="thumbnails">';
					?>
						
						<?php
					if ($this->params->get('show_empty_categories_cat') || $item->numitems) :
						if (!isset($this->items[$this->parent->id][$id + 1]))
						{
							$class = 'last ';
						}
						?>
					<li class="<?php echo $class; ?><?php echo $bsGrid[$this->configs->get('category_cols','3')]?>">
						<div class="thumbnail text-center">
							<?php $class = ''; ?>
							
							<?php if($item->getParams()->get('image')) : ?>
								<img src="<?php echo $item->getParams()->get('image'); ?>" alt="<?php echo htmlspecialchars($item->getParams()->get('image_alt')); ?>" />
							<?php endif; ?>
							<h3 class="item-title">
								<a href="<?php echo JRoute::_(DigiComHelperRoute::getCategoryRoute($item->id));?>">
								<?php echo $this->escape($item->title); ?></a>
								<?php if ($this->params->get('show_cat_num_articles_cat') == 1) :?>
									<span class="badge badge-info tip hasTooltip" title="<?php echo JHtml::tooltipText('COM_DIGICOM_NUM_ITEMS'); ?>">
										<?php echo $item->numitems; ?>
									</span>
								<?php endif; ?>
								
							</h3>
							<?php if ($item->description) : ?>
								<div class="category-desc">
									<?php echo JHtml::_('content.prepare', $item->description, '', 'com_content.categories'); ?>
								</div>
							<?php endif; ?>

						</div>
					</li>
				<?php endif; ?>
			<?php  $i++;endforeach; ?>
			<?php endif; ?>

		</ul>
	</div>
</div>

<?php
echo DigiComSiteHelperDigiCom::powered_by();
