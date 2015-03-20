<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_login
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<li id="mod_digicom_cart_wrap" class="mod_digicom_cart<?php echo $class_sfx; ?> mod_digicom_cart_wrap">
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
				} else {
					$total += $item->discounted_price * $item->quantity;
				}
				$number ++;
			}
		}
		
		?>
		<table>
			<tr>
				<td>
					<a href="<?php echo JRoute::_('index.php?option=com_digicom&view=cart&task=showCart'.$and_itemid, false)?>">
						<i class="icon-shopping-cart"></i>
					</a>
				</td>
				<td>
					&nbsp;&nbsp;&nbsp;
					<a href="<?php echo JRoute::_('index.php?option=com_digicom&view=cart&task=showCart'.$and_itemid, false)?>">
					<?php
						if($number == 1){
							echo $number." ".JText::_("NR_ITEM");
						}
						else{
							echo $number." ".JText::_("NR_ITEMS");
						}
					?>
					</a>
				</td>
				<td>
					&nbsp;&nbsp;&nbsp;<?php echo DigiComHelper::format_price2($total, $currency, true, $configs); ?>
				</td>
			</tr>
		</table>
		<?php 
	} else{
		$module_title = '_BUY_NOW';
		if($params->get('modbuynow', '') == '0'){
		?>
			<a href="<?php echo JRoute::_($cat_url); ?>" style="text-align:center; display:block;" class="btn btn-warning">
				<?php echo JText::_('CARTEMPTY');?>
			</a>
		<?php 
		} elseif ($params->get('modbuynow', '') == '1') {
		?>
			<table width="100%">
				<tr>
					<td width="100%" align="center">
						<?php echo JText::_('CARTEMPTY'); ?>
					</td>
				</tr>
			</table>
		<?php
		} else{
		}
	}
	?>
</li>