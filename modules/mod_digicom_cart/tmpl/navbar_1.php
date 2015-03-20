<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_login
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
echo '<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">';
?>

<li id="mod_digicom_cart_wrap" class="mod_digicom_cart<?php echo $class_sfx; ?> mod_digicom_cart_wrap dropdown">
	<a class="dropdown-toggle" data-toggle="dropdown" href="<?php echo JRoute::_('index.php?option=com_digicom&view=cart&task=showCart'.$and_itemid, false)?>">
		<i class="fa fa-shopping-cart fa-fw"></i>
		Cart
		<b class="caret"></b>
	</a>
	<ul class="dropdown-menu">
		<li>
			<div class="panel panel-info">
				<div class="panel-heading">
					<?php echo JText::_('MOD_DIGICOM_CART_HEADING'); ?>
				</div>
				<div class="panel-body text-center">
	<?php
	
	if (count($items) > 0) {
		$module_title = JText::_("_SHOPPING_CART");
		$total = 0;
		$number = 0;
		foreach ($items as $key=>$item) {
			if ($key >= 0) {
				$currency = $item->currency;
				if (!isset($item->discounted_price)) {
					$total += $item->price * $item->quantity;
				} else{
					$total += $item->discounted_price * $item->quantity;
				}
				$number ++;
			}
		}
		
		?>
	
		<?php
			if($number == 1){
				echo $number." ".JText::_("NR_ITEM");
			}
			else{
				echo $number." ".JText::_("NR_ITEMS");
			}
		?>
		<?php echo DigiComHelper::format_price2($total, $currency, true, $configs); ?>
		<?php 
	} else {
		$module_title = '_BUY_NOW';
		if($params->get('modbuynow', '') == '0'){
			?>
			<a href="<?php echo JRoute::_($cat_url); ?>" style="text-align:center; display:block;" class="btn btn-warning">
				<?php echo JText::_('CARTEMPTY');?>
			</a>
			<?php 
		}  elseif($params->get('modbuynow', '') == '1'){
			?>
			<?php echo JText::_('CARTEMPTY'); ?>
			<?php
		} else{
		}
	}
?>
				</div>
				<div class="panel-footer text-right">
					<a class="btn btn-foo" href="<?php echo JRoute::_('index.php?option=com_digicom&view=cart&task=showCart'.$and_itemid, false)?>" <?php echo (count($items) == 0) ? 'disabled="disabled"':''; ?>><i class="fa fa-shopping-cart fa-fw"></i> <?php echo JText::_('MOD_DIGICOM_CART_CHECKOUT'); ?></a>
				</div>
			</div>
		</li>
	</ul>
</li>