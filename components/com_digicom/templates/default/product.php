<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 341 $
 * @lastmodified	$LastChangedDate: 2013-10-10 14:28:28 +0200 (Thu, 10 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

defined ('_JEXEC') or die ("Go away.");
$cart_itemid = DigiComSiteHelperDigicom::getCartItemid();
$itemid = JFactory::getApplication()->input->get('Itemid',$cart_itemid);
$conf = $this->configs;
$date_today = time();

if($this->configs->get('afteradditem',0) == "2"){
	JHTML::_('behavior.modal');
	JFactory::getDocument()->addScript(JURI::base()."media/digicom/assets/js/createpopup.js");
}
?>
<div id="digicom">
	
	<div class="digi-products">

		<?php if(!$this->item->id): ?>
		<div class="alert alert-warning">
			<p><?php echo JText::_('DSPRODNOTAVAILABLE'); ?></p>
			<p><a href="<?php echo JRoute::_(DigiComHelperRoute::getCategoryRoute($this->item->catid, $this->item->language)); ?>"><?php echo JText::_("DSCONTINUESHOPING"); ?></a></p>
		</div>
		<?php return true; ?>
		<?php endif; ?>
		
		<?php if(($this->item->publish_up > $date_today) || ($this->item->publish_down != 0 && $this->item->publish_down < $date_today)): ?>
		<div class="alert alert-warning">
			<?php echo JText::_('COM_DIGICOM_PRODUCT_PUBLISH_DOWN'); ?>
		</div>
		<?php return true; ?>
		<?php endif; ?>

		<?php if($this->item->state == 0): ?>
		<div class="alert alert-warning">
			<p><?php echo JText::_('DIGI_PRODUCT_UNPUBLISHED'); ?></p>
			<p><a href="<?php echo JRoute::_("index.php?option=com_digicom&view=categories&id=0"); ?>"><?php echo JText::_("DIGI_HOME_PAGE"); ?></a></p>
		</div>
		<?php return true; ?>
		<?php endif; ?>

		<?php

		$addtocart = '<input type="submit" value="'.(JText::_("DSADDTOCART")).'" class="btn"/> ';
		?>

		<div id="dslayout-viewproduct">

			<form name="prod" id="product-form" action="<?php echo JRoute::_('index.php?option=com_digicom&view=cart');?>" method="post" style="width:100%;">

				<div class="row-fluid">
					
					<!-- Details & Cart -->
					<div class="span12">
					
						<?php if(!empty($this->item->images)): ?>
						<img src="<?php echo $this->item->images; ?>" class="img-responsive img-rounded"/>
						<?php endif; ?>

						<h1 class="product-title"><?php echo $this->item->name; ?></h1>
						<?php if(!empty($this->item->bundle_source)):?>
						<p class="alert alert-success"><?php echo JText::sprintf('COM_DIGICOM_PRODUCT_TYPE_BUNDLE_TYPE_'.strtoupper($this->item->bundle_source),$this->item->bundle_source);?></p>			
						<?php endif; ?>
						<div class="short-desc"><?php echo $this->item->description; ?></div>
						<div class="description"><?php echo $this->item->fulldescription; ?></div>
						
						<?php if(!empty($this->item->bundle_source)):?>
						<div class="bundled-products">
							<h3><?php echo JText::_('COM_DIGICOM_BUNDLE_ITEMS');?></h3>
							<ul>
								<?php foreach($this->item->bundleitems as $key=>$bitem): ?>
									<li>
										<a href="<?php echo JRoute::_(DigiComHelperRoute::getProductRoute($bitem->id,$bitem->catid, $bitem->language)); ?>"><?php echo $bitem->name; ?></a>
										<span class="label"><?php echo DigiComSiteHelperDigicom::format_price2($bitem->price, $conf->get('currency','USD'), true, $conf); ?></span>
									</li>
								<?php endforeach; ?>
							</ul>	
						</div>						
							
						<?php endif; ?>
						<?php 
						if ($this->configs->get('catalogue',0) == '0') : ?>
						<div class="price-addtocart clearfix">	
							<div class="product-price">
								<?php echo JText::_('DSPRICE'); ?>:
								<span><?php echo DigiComSiteHelperDigicom::format_price2($this->item->price, $conf->get('currency','USD'), true, $conf); ?></span>
							</div>						
							
							<div class="addtocart-bar">
								<!-- <label for="quantity23" class="quantity_box">
									<?php echo JText::_('DSQUANTITY'); ?>:&nbsp;									
								</label> -->

								<div class="input-append">
									<input id="quantity_<?php echo $this->item->id; ?>" type="number" name="qty" min="1" class="input-small" value="1" size="2" placeholder="<?php echo JText::_('DSQUANTITY'); ?>">
									<?php if($conf->get('afteradditem',2) == "2") {	?>
									<button type="button" class="btn btn-warning" onclick="javascript:createPopUp(<?php echo $this->item->id; ?>, <?php echo JRequest::getVar("cid", "0"); ?>, '<?php echo JURI::root(); ?>', '', '', <?php echo $cart_itemid; ?>, '<?php echo JRoute::_("index.php?option=com_digicom&view=cart&Itemid=".$cart_itemid); ?>');"><i class="icon-cart"></i> <?php echo JText::_("DSADDTOCART");?></button>
									<?php }else { ?>
									<button type="submit" class="btn btn-warning"><i class="icon-cart"></i> <?php echo JText::_('DSADDTOCART'); ?></button>
									<?php } ?>
								</div>								
							</div>	
							
							<!-- <br />
							<div class="input-append">	
								
							</div> -->
						</div>
						<?php endif; ?>
					</div>
				</div>
				<input type="hidden" name="option" value="com_digicom"/>
				<input type="hidden" name="view" value="cart"/>
				<input type="hidden" name="task" value="cart.add"/>
				<input type="hidden" name="pid" value="<?php echo $this->item->id; ?>"/>
				<input type="hidden" name="cid" value="<?php echo JFactory::getApplication()->input->get('cid',0);?>"/>
				<input type="hidden" name="Itemid" value="<?php $itemid; ?>"/>
			</form>
		</div>

		<?php echo DigiComSiteHelperDigicom::powered_by(); ?>
	</div>
</div>