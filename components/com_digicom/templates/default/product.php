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
if(!isset($images->image_full)){
	$images = new stdClass();
	$images->image_full = $this->item->images;
}
if($this->item->price > 0){
	$price = '<span>'.JText::_('COM_DIGICOM_PRODUCT_PRICE').": ".DigiComSiteHelperPrice::format_price($this->item->price, $conf->get('currency','USD'), true, $conf).'</span>';
}else{
	$price = '<span>'.JText::_('COM_DIGICOM_PRODUCT_PRICE_FREE').'</span>';
}
$link = JRoute::_(DigiComSiteHelperRoute::getProductRoute($this->item->id, $this->item->catid, $this->item->language));
?>

<div id="digicom" class="dc dc-product">

	<div class="product-page<?php echo $this->pageclass_sfx; ?>" itemscope itemtype="http://schema.org/Product">

		<meta itemprop="inLanguage" content="<?php echo ($this->item->language === '*') ? JFactory::getConfig()->get('language') : $this->item->language; ?>" />
		<meta itemprop="url" content="<?php echo $link; ?>" />

		<?php if ($this->params->get('show_page_heading')) : ?>
		<div class="page-header">
			<h1> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
		</div>
		<?php endif;?>
		<header>
			<h2 class="product-title">
				<span itemprop="name">
					<?php echo $this->item->name; ?>
				</span>
			</h2>

			<?php if($this->item->featured):?>
				<span class="label label-info"><?php echo JText::_('JFEATURED');?></span>
			<?php endif; ?>

			<?php if(!empty($this->item->bundle_source)):?>
				<span class="label label-warning"><?php echo JText::sprintf('COM_DIGICOM_PRODUCT_TYPE_BUNDLE');?></span>
			<?php endif; ?>

			<?php if (!empty($this->item->tags->itemTags)) : ?>
				<?php $this->item->tagLayout = new JLayoutFile('joomla.content.tags'); ?>
				<?php echo $this->item->tagLayout->render($this->item->tags->itemTags); ?>
			<?php endif; ?>
		</header>
		<div class="product-details clearfix">
			<?php if(!empty($images->image_full)): ?>
				<div class="text-center">
					<img itemprop="image" src="<?php echo JURI::root().$images->image_full; ?>" alt="<?php echo $this->item->name; ?>" class="img-responsive img-thumbnail"/>
				</div>
			<?php endif; ?>

			<div class="description" itemprop="description">
				<?php echo $this->item->text; ?>
			</div>

			<?php
			if(!empty($this->item->bundle_source)):
				echo $this->loadTemplate('bundle');
			endif;
			?>

			<?php if ($this->configs->get('catalogue',0) == '0' and !$this->item->hide_public) : ?>
			<div class="well clearfix" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
				<meta itemprop="priceCurrency" content="<?php $conf->get('currency','USD');?>" />

				<div class="product-price pull-left">
					<span itemprop="price" content="<?php echo $this->item->price; ?>"><?php echo $price; ?></span>
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

		</div>

	</div>

	<?php
		if($conf->get('afteradditem',0) == "2"):
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
		endif;
	?>

	<?php echo DigiComSiteHelperDigicom::powered_by(); ?>

</div>
