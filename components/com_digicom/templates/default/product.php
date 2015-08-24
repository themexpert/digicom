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

					<?php if(!empty($this->item->images)): ?>
						<img src="<?php echo $this->item->images; ?>" class="img-responsive"/>
					<?php endif; ?>

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

						<div class="addtocart-bar<?php echo ($conf->get('show_quantity',0) == 1 ? " input-append" : ''); ?>">
							<a
								href="<?php echo JRoute::_('index.php?option=com_digicom&view=cart&task=cart.add&from=ajax&pid='.$this->item->id);?>"
								role="button"
								class="btn btn-small btn-primary"
								data-toggle="modal"
								data-target="#cartPopup">
									<i class="icon-cart"></i>
									<?php echo JText::_('COM_DIGICOM_ADD_TO_CART'); ?>
							</a>
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
		'cartPopup',
		array(
			'title' 	=> JText::_('COM_DIGICOM_CART_ITEMS'),
			'height' 	=> '400px',
			'width'	 	=> '1280',
			'footer'	=> '<button type="button" class="btn btn-default" data-dismiss="modal">'.JText::_('COM_DIGICOM_CONTINUE').'</button> <a href="'.JRoute::_("index.php?option=com_digicom&view=cart").'" class="btn btn-warning"><i class="ico-ok-sign"></i> '.JText::_("COM_DIGICOM_CHECKOUT").'</a>'
		)
	);
	?>
</div>
