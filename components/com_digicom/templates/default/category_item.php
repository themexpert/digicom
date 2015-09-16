<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;
?>

<?php
$images = json_decode($this->item->images);
if(!isset($images->image_intro)){
	$images = new stdClass();
	$images->image_intro = $this->item->images;
}
if($this->item->price > 0){
	$price = DigiComSiteHelperPrice::format_price($this->item->price, $this->configs->get('currency','USD'), true, $this->configs);
}else{
	$price = JText::_('COM_DIGICOM_PRODUCT_PRICE_FREE');
}
$link = JRoute::_(DigiComSiteHelperRoute::getProductRoute($this->item->id, $this->item->catid, $this->item->language));
?>

<div class="dc-thumbnail thumbnail">
	<!-- Product Image -->
	<?php if(!empty($images->image_intro)): ?>
		<img itemprop="image" src="<?php echo JURI::root().$images->image_intro; ?>" alt="<?php echo $this->item->name; ?> Image" >
	<?php endif; ?>

	<?php if(!empty($this->item->bundle_source)):?>
	<span class="bundle-label label label-warning"><?php echo JText::sprintf('COM_DIGICOM_PRODUCT_TYPE_BUNDLE');?></span>
	<?php endif; ?>
	<?php if($this->item->featured): ?>
		<span class="featured label label-info">Featured</span>
	<?php endif; ?>

	<!-- Product Name & Intro text -->
	<div class="caption">
		<h3>
			<a itemprop="url" href="<?php echo $link;?>">
				<span itemprop="name"><?php echo $this->item->name; ?></span>
			</a>
		</h3>
		<p class="description" itemprop="description"><?php echo $this->item->introtext; ?></p>

		<!-- Price & Readmore Button -->
		<div class="clearfix input-group input-group-sm" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
		  <span class="input-group-addon product-price" id="product-price" itemprop="price" content="<?php echo $this->item->price; ?>"><?php echo $price; ?></span>
			<div class="input-group-btn">
				<a itemprop="url" href="<?php echo $link;?>" class="btn btn-primary read-more"><?php echo JText::_('COM_DIGICOM_BUTTON_DETAILS'); ?></a>
			</div>
		</div>
	</div>
</div>
