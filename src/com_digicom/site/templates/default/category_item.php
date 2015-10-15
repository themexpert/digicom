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
if($this->item->price > 0){
	$price = DigiComSiteHelperPrice::format_price($this->item->price, $this->configs->get('currency','USD'), true, $this->configs).'</span>';
}else{
	$price = '<span>'.JText::_('COM_DIGICOM_PRODUCT_PRICE_FREE').'</span>';
}?>

<div class="dc-item thumbnail" itemscope itemtype="http://schema.org/Product" data-digicom-item data-id="<?php echo $this->item->id?>">

	<figure>
		<?php if(!empty($images->image_intro)): ?>
			<a itemprop="url" href="<?php echo $link;?>">
				<img itemprop="image" src="<?php echo JURI::root().$images->image_intro; ?>" alt="<?php echo $this->item->name; ?> Image" >
			</a>
		<?php endif; ?>

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
			<div class="dc-price text-muted" itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
				<meta itemprop="priceCurrency" content="<?php echo $this->configs->get('currency','USD'); ?>">
				<strong itemprop="price">
					<?php echo $price; ?>
				</strong>
				
				<?php if($this->configs->get('enable_taxes','0') && $this->configs->get('display_tax_with_price','0')):?>
					<span class="text-info">
						<?php echo JLayoutHelper::render('tax.price', array('config' => $this->configs, 'item' => $this->item)); ?>
					</span>
				<?php endif; ?>
			</div>
			<p class="dc-item-desc" itemprop="description"><?php echo $this->item->introtext; ?></p>

			<a itemprop="url" href="<?php echo $link;?>" class="btn btn-primary read-more"><?php echo JText::_('COM_DIGICOM_BUTTON_DETAILS'); ?></a>
		</figcaption>
	</figure>
</div>
