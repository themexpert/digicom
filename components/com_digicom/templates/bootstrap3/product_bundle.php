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
<div class="bundled-products">
	<h3><?php echo JText::_('COM_DIGICOM_PRODUCT_BUNDLE_ITEMS_TITLE');?></h3>
	<ul class="list-group">
			<?php
			foreach($this->item->bundleitems as $key=>$bitem):
		  	$link = JRoute::_(DigiComSiteHelperRoute::getProductRoute($bitem->id,$bitem->catid, $bitem->language));
			?>
			<li class="list-group-item">
				<a href="<?php echo $link; ?>"><?php echo $bitem->name; ?></a>
				<span class="badge"><?php echo DigiComSiteHelperPrice::format_price($bitem->price, $this->configs->get('currency','USD'), true, $this->configs); ?></span>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
