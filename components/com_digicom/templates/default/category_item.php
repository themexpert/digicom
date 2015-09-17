<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

$images = json_decode($this->item->images);
// Legacy code
// TODO : remove from 1.1 or so
if(!isset($images->image_intro)){
	$images = new stdClass();
	$images->image_intro = $this->item->images;
}
// Set Price value or free label
if($this->item->price > 0){
	$price = DigiComSiteHelperPrice::format_price($this->item->price, $this->configs->get('currency','USD'), true, $this->configs);
}else{
	$price = JText::_('COM_DIGICOM_PRODUCT_PRICE_FREE');
}
$link = JRoute::_(DigiComSiteHelperRoute::getProductRoute($this->item->id, $this->item->catid, $this->item->language));
?>

<div class="dc-item thumbnail" itemscope itemtype="http://schema.org/Product" data-digicom-item data-id="<?php echo $this->item->id?>">

	<?php if(!empty($images->image_intro)): ?>
	<figure>
		<a itemprop="url" href="<?php echo $link;?>">
			<img itemprop="image" src="<?php echo JURI::root().$images->image_intro; ?>" alt="<?php echo $this->item->name; ?> Image" >
		</a>

		<figcaption class="caption">
			<?php if(!empty($this->item->bundle_source)):?>
			<span class="label label-info label--bundle"><?php echo JText::sprintf('COM_DIGICOM_PRODUCT_TYPE_BUNDLE');?></span>
			<?php endif; ?>
			<?php if($this->item->featured): ?>
				<span class="label label-info label--featured">Featured</span>
			<?php endif; ?>

			<h2>
				<a itemprop="url" href="<?php echo $link;?>">
					<span itemprop="name"><?php echo $this->item->name; ?></span>
				</a>
			</h2>
			<p class="dc-item-desc" itemprop="description"><?php echo $this->item->introtext; ?></p>
			<a itemprop="url" href="<?php echo $link;?>" class="btn btn-primary read-more"><?php echo JText::_('COM_DIGICOM_BUTTON_DETAILS'); ?></a>
		</figcaption>
	</figure>
	<?php endif; ?>
</div>
