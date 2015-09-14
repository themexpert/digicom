<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;
$conf = $this->configs;
if($this->item->price > 0){
	$price = '<span>'.JText::_('COM_DIGICOM_PRODUCT_PRICE').": ".DigiComSiteHelperPrice::format_price($this->item->price, $conf->get('currency','USD'), true, $conf).'</span>';
}else{
	$price = '<span>'.JText::_('COM_DIGICOM_PRODUCT_PRICE_FREE').'</span>';
}
?>
<div id="digicom">

	<div class="digi-products">

			<div class="row-fluid">

				<!-- Details & Cart -->
				<div class="span12">

					<h1 class="digi-page-title">
					<?php echo $this->item->name; ?>

						<?php if($this->item->featured):?>
							<span class="label label-important"><?php echo JText::_('JFEATURED');?></span>
						<?php endif; ?>

						<?php if(!empty($this->item->bundle_source)):?>
							<span class="label"><?php echo JText::sprintf('COM_DIGICOM_PRODUCT_TYPE_BUNDLE');?></span>
						<?php endif; ?>
					</h1>
					
					<?php if(!empty($this->item->images)): ?>
						<img src="<?php echo $this->item->images; ?>" class="img-responsive"/>
					<?php endif; ?>


					<?php if (!empty($this->item->tags->itemTags)) : ?>
						<?php $this->item->tagLayout = new JLayoutFile('joomla.content.tags'); ?>
						<?php echo $this->item->tagLayout->render($this->item->tags->itemTags); ?>
					<?php endif; ?>

					<p class="intro">
						<?php echo $this->item->introtext; ?>
					</p>

					<div class="description">
						<?php echo $this->item->text; ?>
					</div>

					<?php
					if(!empty($this->item->bundle_source)):
						echo $this->loadTemplate('bundle');
					endif;
					?>

					<?php if ($this->configs->get('catalogue',0) == '0' and !$this->item->hide_public) : ?>
					<div class="well clearfix">
						<div class="product-price">
							<?php echo $price; ?>
							<br/>
							<?php if ($this->configs->get('show_validity',1) == 1) : ?>
							<span>
								<small><?php echo JText::_('COM_DIGICOM_PRODUCT_VALIDITY'); ?> : <?php echo DigiComSiteHelperPrice::getProductValidityPeriod($this->item); ?></small>
							</span>
							<?php endif; ?>
						</div>

						<div class="addtocart-bar<?php echo ($conf->get('show_quantity',0) == 1 ? " input-append input-prepend" : ''); ?>">
							<form name="prod" id="product-form" action="<?php echo JRoute::_('index.php?option=com_digicom&view=cart');?>" method="post" style="width:100%;">

								<?php if($conf->get('show_quantity',0) == "1") {	?>
									<input data-digicom-id="quantity_<?php echo $this->item->id; ?>" type="number" name="qty" min="1" class="input-small" value="1" size="2" placeholder="<?php echo JText::_('COM_DIGICOM_QUANTITY'); ?>">
								<?php } ?>

								<?php if($conf->get('afteradditem',0) == "2") {	?>
									<button type="button" class="btn btn-warning" onclick="Digicom.addtoCart(<?php echo $this->item->id; ?>,'<?php echo JRoute::_("index.php?option=com_digicom&view=cart"); ?>');"><i class="icon-cart"></i> <?php echo JText::_("COM_DIGICOM_ADD_TO_CART");?></button>
								<?php }else { ?>
									<button type="submit" class="btn btn-warning"><i class="icon-cart"></i> <?php echo JText::_('COM_DIGICOM_ADD_TO_CART'); ?></button>
								<?php } ?>

								<input type="hidden" name="option" value="com_digicom"/>
								<input type="hidden" name="view" value="cart"/>
								<input type="hidden" name="task" value="cart.add"/>
								<input type="hidden" name="pid" value="<?php echo $this->item->id; ?>"/>
							</form>
						</div>
					</div>
					<?php endif; ?>

				</div>
			</div>

		<?php echo DigiComSiteHelperDigicom::powered_by(); ?>
	</div>
	<?php
	echo JHtml::_(
		'bootstrap.renderModal',
		'digicomCartPopup',
		array(
//			'url' 		=> JRoute::_('index.php?option=com_digicom&view=cart&layout=cart_popup&tmpl=component'),
			'title' 	=> JText::_('COM_DIGICOM_CART_ITEMS'),
			'height' 	=> '400',
			'width'	 	=> '1280',
			'footer'	=> '<button type="button" class="btn btn-default" data-dismiss="modal">'.JText::_('COM_DIGICOM_CONTINUE').'</button> <a href="'.JRoute::_("index.php?option=com_digicom&view=cart").'" class="btn btn-warning"><i class="ico-ok-sign"></i> '.JText::_("COM_DIGICOM_CHECKOUT").'</a>'
		)
	);
	?>
</div>
