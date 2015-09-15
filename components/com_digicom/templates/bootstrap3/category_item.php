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
if(!isset($images->thumb_image)){
	$images = new stdClass();
	$images->thumb_image = $this->item->images;
}
if($this->item->price > 0){
	$price = DigiComSiteHelperPrice::format_price($this->item->price, $this->configs->get('currency','USD'), true, $this->configs);
}else{
	$price = JText::_('COM_DIGICOM_PRODUCT_PRICE_FREE');
}
$link = JRoute::_(DigiComSiteHelperRoute::getProductRoute($this->item->id, $this->item->catid, $this->item->language));
?>
<div class="<?php echo $this->bsGrid[$this->column]?>">
	<div class="thumbnail">
		<!-- Product Image -->
		<?php if(!empty($images->thumb_image)): ?>
			<a href="<?php echo $link;?>" class="image" title="<?php echo $this->item->name; ?>">
				<img alt="<?php echo $this->item->name; ?> Image" src="<?php echo $images->thumb_image; ?>">
			</a>
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
				<a href="<?php echo $link;?>"><?php echo $this->item->name; ?></a>
			</h3>
			<p class="description"><?php echo $this->item->introtext; ?></p>

			<!-- Price & Readmore Button -->
			<div class="clearfix input-group input-group-sm">
			  <span class="input-group-addon product-price" id="product-price"><?php echo $price; ?></span>
				<div class="input-group-btn">
					<a href="<?php echo $link;?>" class="btn btn-primary read-more"><?php echo JText::_('COM_DIGICOM_BUTTON_DETAILS'); ?></a>
				</div>
			</div>
		</div>
	</div>
</div>
