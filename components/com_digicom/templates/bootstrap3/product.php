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
$images  = json_decode($this->item->images);
if(!isset($images->full_image)){
	$images = new stdClass();
	$images->full_image = $this->item->images;
}
if($this->item->price > 0){
	$price = '<span>'.JText::_('COM_DIGICOM_PRODUCT_PRICE').": ".DigiComSiteHelperPrice::format_price($this->item->price, $conf->get('currency','USD'), true, $conf).'</span>';
}else{
	$price = '<span>'.JText::_('COM_DIGICOM_PRODUCT_PRICE_FREE').'</span>';
}
?>
<div id="digicom">

	<div class="digi-products">

			<h1 class="digi-page-title">
			<?php echo $this->item->name; ?>

				<?php if($this->item->featured):?>
					<span class="label label-important"><?php echo JText::_('JFEATURED');?></span>
				<?php endif; ?>

				<?php if(!empty($this->item->bundle_source)):?>
					<span class="label"><?php echo JText::sprintf('COM_DIGICOM_PRODUCT_TYPE_BUNDLE');?></span>
				<?php endif; ?>
			</h1>

			<?php if (!empty($this->item->tags->itemTags)) : ?>
				<?php $this->item->tagLayout = new JLayoutFile('joomla.content.tags'); ?>
				<?php echo $this->item->tagLayout->render($this->item->tags->itemTags); ?>
			<?php endif; ?>

			<p class="intro">
				<?php echo $this->item->introtext; ?>
			</p>

			<?php if(!empty($images->full_image)): ?>
				<img src="<?php echo $images->full_image; ?>" class="img-responsive"/>
			<?php endif; ?>

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
				<div class="product-price pull-left">
					<?php echo $price; ?>
					<br/>
					<?php if ($this->configs->get('show_validity',1) == 1) : ?>
					<span>
						<small><?php echo JText::_('COM_DIGICOM_PRODUCT_VALIDITY'); ?> : <?php echo DigiComSiteHelperPrice::getProductValidityPeriod($this->item); ?></small>
					</span>
					<?php endif; ?>
				</div>

				<div class="addtocart-bar pull-right">
					<form name="prod" class="form-inline" id="product-form" action="<?php echo JRoute::_('index.php?option=com_digicom&view=cart');?>" method="post" style="width:100%;">
						<div class="<?php echo ($conf->get('show_quantity',0) == 1 ? "input-group" : ''); ?>">

							<?php if($conf->get('show_quantity',0) == "1") {	?>
								<input data-digicom-id="quantity_<?php echo $this->item->id; ?>" type="number" name="qty" min="1" class="input-group-addon" value="1" size="2" placeholder="<?php echo JText::_('COM_DIGICOM_QUANTITY'); ?>">
							<?php } ?>

							<div class="input-group-btn">
								<?php if($conf->get('afteradditem',0) == "2") {	?>
									<div type="button" class="btn btn-primary" onclick="Digicom.addtoCart(<?php echo $this->item->id; ?>,'<?php echo JRoute::_("index.php?option=com_digicom&view=cart"); ?>');"><i class="glyphicon glyphicon-cart"></i> <?php echo JText::_("COM_DIGICOM_ADD_TO_CART");?></div>
								<?php }else { ?>
									<div type="submit" class="btn btn-primary"><i class="glyphicon glyphicon-cart"></i> <?php echo JText::_('COM_DIGICOM_ADD_TO_CART'); ?></div>
									<?php } ?>

							</div>
						</div>

						<input type="hidden" name="option" value="com_digicom"/>
						<input type="hidden" name="view" value="cart"/>
						<input type="hidden" name="task" value="cart.add"/>
						<input type="hidden" name="pid" value="<?php echo $this->item->id; ?>"/>
					</form>
				</div>
			</div>
			<?php endif; ?>

		<?php echo DigiComSiteHelperDigicom::powered_by(); ?>
	</div>
	<?php
		$layoutData = array(
			'selector' => 'digicomCartPopup',
			'params'   => array(
											'title' 	=> JText::_('COM_DIGICOM_CART_ITEMS'),
											'height' 	=> '400',
											'width'	 	=> '1280',
											'footer'	=> '<button type="button" class="btn btn-default" data-dismiss="modal">'.JText::_('COM_DIGICOM_CONTINUE').'</button> <a href="'.JRoute::_("index.php?option=com_digicom&view=cart").'" class="btn btn-warning"><i class="ico-ok-sign"></i> '.JText::_("COM_DIGICOM_CHECKOUT").'</a>'
										),
			'body'     => ''
		);
		echo JLayoutHelper::render('bt3.modal.main', $layoutData);
	?>
</div>
