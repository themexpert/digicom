<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_digicom_products
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$items = array_chunk($list, 4);
$configs = JComponentHelper::getComponent('com_digicom')->params;
$delay = 0;
$column = $params->get('column', 4);
$i = 0;
?>

<?php foreach($items as $item):?>
<div class="row-fluid clearfix">
	<?php foreach($item as $key=>$product):?>
		<?php
			$delay = $delay + 0.2;
			$images = json_decode($product->images);
			// Set Price value or free label
			if($product->price > 0){
				$price = DigiComSiteHelperPrice::format_price($product->price, $configs->get('currency','USD'), true, $configs);
			}else{
				$price = JText::_('COM_DIGICOM_PRODUCT_PRICE_FREE');
			}
			$link = JRoute::_(DigiComSiteHelperRoute::getProductRoute($product->id, $product->catid, $product->language));
		?>
		<?php if($i != 0 && $i%$column == 0) echo '</div><div class="row-fluid clearfix">';?>
		<div class="col-md-<?php echo 12/$column;?> col-xs-6 mod-product-item">
			<div class="block product wow zoomIn" itemscope itemtype="http://schema.org/Product" data-wow-delay="<?php echo $delay, 's'; ?>">
				<figure>
					<a href="<?php echo $product->link; ?>" itemprop="url">
						<div class="img-wrapper atvImg">
							<img class="img-responsive" src="<?php echo $images->image_intro?>" alt="<?php echo $product->name?>" />
							<div class="atvImg-layer" data-img="<?php echo JUri::root() . $images->image_intro?>"></div>
						</div>
					</a>
					<?php if($product->price <= 0) :?>
						<figcaption>
								<span class="product-type-free">
									<?php echo JText::_('COM_DIGICOM_PRODUCT_PRICE_FREE'); ?>
								</span>
						</figcaption>
					<?php endif;?>
				</figure>
				<div class="details">
					<h4 itemprop="name"><?php echo $product->name; ?></h4>
					<meta itemprop="description" content="<?php echo $product->introtext?>" />
					<a class="btn btn-primary" itemprop="url" href="<?php echo $product->link; ?>">
						Buy Now
						<span class="hidden-sm">
						 	| <?php echo $price; ?>
						</span>
					</a>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
</div>
<?php endforeach;?>
<?php
$more_link = $params->get('more_link', '');
$route = JRoute::_('index.php?Itemid='.$more_link);
if(!empty($more_link)):?>
	<div class="text-center masthead no-padding-bottom">
		<a href="<?php echo $route; ?>" class="btn btn-primary btn-outline btn-lg"><?php echo $params->get('cat_text', JText::_('MOD_DIGICOM_PRODUCTS_MORE_PRODUCTS')); ?></a>
	</div>

<?php endif; ?>
