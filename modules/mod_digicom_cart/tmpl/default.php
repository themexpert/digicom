<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;
$doc 				= JFactory::getDocument();
// Load style file
$doc->addStyleSheet( JUri::root(true). '/modules/mod_digicom_cart/assets/css/mod_digicom_cart.css');
?>
<div data-digicom-id="mod_digicom_cart_wrap" class="dg-cart <?php echo $moduleclass_sfx; ?>">
	<?php if(count($list) > 0) :?>
	<ul class="dg-cart-list">
		<?php
			foreach($list as $index => $item):
				$images = json_decode($item->images);
				if(!isset($images->thumb_image)){
					$images = new stdClass();
					$images->thumb_image = $item->images;
				}
			?>

			<li class="clearfix">
				<a href="<?php echo JRoute::_(DigiComSiteHelperRoute::getProductRoute($item->id, $item->catid)) ?>">
					<?php if($item->images): ?><img src="<?php echo JURI::root() . $images->thumb_image; ?>" alt="<?php echo $item->name; ?>"/><?php endif; ?>
					<?php echo $item->name; ?>
				</a>
				<span class="dg-quantity">
					<?php echo $item->quantity;?> x <?php echo $item->price; ?>
				</span>
			</li>
		<?php endforeach; ?>
	</ul>
	<div class="dg-total">
		<!-- <?php if($tax['promo'] > 0): ?>
		<p class="dg-amount-discount">
			<strong><?php echo JText::_('MOD_DIGICOM_CART_PROMO_DISCOUNT')?>:</strong>
			<?php echo DigiComSiteHelperPrice::format_price($tax["promo"], $tax["currency"], true, $configs); ?>
		</p>
		<?php endif; ?> -->

		<p class="dg-amount">
			<strong><?php echo JText::_('MOD_DIGICOM_CART_PRICE_TOTAL')?>:</strong>
			<?php echo DigiComSiteHelperPrice::format_price($tax["payable_amount"], $tax["currency"], true, $configs); ?>
		</p>


		<a class="btn btn-primary" href="<?php echo JRoute::_('index.php?option=com_digicom&view=cart'.$Itemid)?>">View Cart</a>
	</div>
	<?php else: ?>
		<p><?php echo JText::_('MOD_DIGICOM_CART_EMPTY_CART');?></p>
	<?php endif; ?>
</div>
