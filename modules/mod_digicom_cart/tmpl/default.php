<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

// Total amount added to cart
if (count($list) > 0) {
	$total = 0;
	$number = 0;
	foreach ($list as $key => $item) {
		if ($key >= 0) {
			$currency = $item->currency;
			if (!isset($item->discounted_price)) {
				$total += $item->price * $item->quantity;
			} else {
				$total += $item->discounted_price * $item->quantity;
			}
			$number++;
		}
	}
}
?>
<div class="dg-cart <?php echo $moduleclass_sfx; ?>">
	<?php if(count($list) > 0) :?>
	<ul class="dg-cart-list">
		<?php foreach($list as $index => $item): ?>

			<?php
				// TODO : remove this after issue #52 solve. no false index should be on cart array
				if($index<0) continue;
			?>

			<li class="clearfix">
				<a href="<?php echo JRoute::_(DigiComHelperRoute::getProductRoute($item->id, $item->catid)) ?>">
					<img src="<?php echo DigiComSiteHelperDigiCom::getThumbnail($item->images); ?>" alt="<?php echo $item->name; ?>"/>
					<?php echo $item->name; ?>
				</a>
				<span class="dg-quantity">
					<?php echo $item->quantity;?> x <?php echo $item->price; ?>
				</span>
			</li>
		<?php endforeach; ?>
	</ul>
	<div class="dg-total">
		<p class="dg-amount">
			<strong><?php echo JText::_('COM_DIGICOM_SUBTOTAL')?>:</strong> <?php echo $total; ?>
		</p>

		<a class="btn btn-primary" href="<?php echo JRoute::_('index.php?option=com_digicom&view=cart'.$Itemid)?>">View Cart</a>
	</div>
	<?php else: ?>
		<p><?php echo JText::_('MOD_DIGICOM_CART_EMPTY');?></p>
	<?php endif; ?>
</div>