<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;
$params 	= $vars['params'];
$product 	= $vars['product'];
$configs 	= $vars['configs'];
$categorieid = $product['catid'];
?>
<form target="index.php" id="prform" name="prform" method="post">
	<h3>
		<?php echo DigiComSiteHelperPrice::format_price($product['price'], $configs->get('currency','USD'), true, $configs);?>
	</h3>

	<input type="submit" name="Button" 
		class="btn btn-foo btn-lg" 
		value="<?php echo JText::_('PLG_CONTENT_DIGICOM_ADD_TO_CART_BTN_LBL');?>"
	>

	<input name="qty" type="hidden" value="1" />
	<input name="pid" type="hidden" id="product_id" value="<?php $replace['id'];?>">
	<input name="cid" type="hidden" value="<?php $categorieid;?>">
	<input type="hidden" name="view" value="cart"/>
	<input type="hidden" name="task" value="cart.add"/>
	<input type="hidden" name="option" value="com_digicom"/>
	<input type="hidden" name="from_add_plugin" value="1"/>

</form>