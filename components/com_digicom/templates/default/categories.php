<?php
/**
 * @package			com_digicom
 * @author			themexpert.com
 * @version			1.0beta1
 * @copyright		Copyright (C) 2010-2015 ThemeXpert. All rights reserved.
 * @license			GNU/GPLv3
*/

defined('_JEXEC') or die;
// Load Jquery
JHtml::_('jquery.framework');
// We'll only load this js if show cart in popup option is set from admin setting
if($this->configs->get('afteradditem',0) == "2"){
	JHTML::_('behavior.modal');
	JFactory::getDocument()->addScript(JURI::base()."media/digicom/assets/js/createpopup.js");
}
$cart_itemid = DigiComHelper::getCartItemid();
$conf = $this->configs;
$prod = $this->prod;
$bsGrid = array(
		1 => 'span12',
		2 => 'span6',
		3 => 'span4',
		4 => 'span3',
		6 => 'span2'
	);

?>
<div id="digicom">
	<div class="digi-categories">
		
		<!-- Category Info -->
		<div class="category-info media">
			<!-- Category Name -->
			<h2 class="page-title"><?php echo $this->category->name; ?></h2>
			<div class="pull-left">
				<img class="img-rounded" src="<?php echo $this->category->image; ?>"/>
			</div>
			<div class="media-body">
				<?php echo $this->category->description; ?>
			</div>
		</div>
		
		<div class="products-list clearfix">
			<div class="row-fluid">
	            <ul class="thumbnails">
	              <?php 
				  $i=0;
				  foreach($this->prods as $key=>$item): 
				 	if(! ($i % $this->configs->get('category_cols')) )  echo '</ul></div><div class="row-fluid"><ul class="thumbnails">';
				  	// echo ( $i == $this->configs->get('category_cols') ) ? '<div class="clearfix"></div>' : '';
				  ?>
				  <li class="<?php echo $bsGrid[$this->configs->get('category_cols')]?>">
	                <div class="thumbnail">
	                	<!-- Product Image -->
	                  	<?php if(!empty($item->images)): ?>
					  	<a href="<?php echo JRoute::_('index.php?option=com_digicom&view=products&cid='.$item->catid.'&pid='.$item->id);?>" class="image"><img alt="Product Image" src="<?php echo $item->images; ?>"></a>
	                  	<?php endif; ?>

						<?php if($item->featured): ?>
	                  	<span class="featured">Featured</span>
						<?php endif; ?>
						
	                  	<?php if(!empty($item->bundle_source)):?>
						<span class="bundle-label"><?php echo JText::sprintf('COM_DIGICOM_CATEGORY_PRODUCT_TYPE_BUNDLE');?></span>
						<?php endif; ?>	                  	
					  
					  	<!-- Product Name & Intro text -->
					  	<div class="caption">
		                    <h3><a href="<?php echo JRoute::_('index.php?option=com_digicom&view=products&cid='.$item->catid.'&pid='.$item->id);?>"><?php echo $item->name; ?></a></h3>
		                    <p class="description"><?php echo $item->description; ?></p>
											
							<!-- <form name="prod" class="input-append" id="product-form" action="<?php echo JRoute::_('index.php?option=com_digicom&view=cart');?>" method="post" style="width:100%;">
								<input id="quantity_<?php echo $item->id; ?>" type="number" name="qty" min="1" class="input-small" value="1" size="2" placeholder="<?php echo JText::_('DSQUANTITY'); ?>">	
								<input type="hidden" name="option" value="com_digicom"/>
								<input type="hidden" name="view" value="cart"/>
								<input type="hidden" name="task" value="add"/>
								
								<input type="hidden" name="pid" value="<?php echo $item->id; ?>"/>
								<input type="hidden" name="cid" value="<?php echo $item->catid; ?>"/>
								<input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>"/>
											
								<?php if($this->configs->get('afteradditem',0) == "2"){ ?>
									<button type="button" class="btn btn-warning" onclick="javascript:createPopUp(<?php echo $item->id; ?>, <?php echo $item->catid; ?>, '<?php echo JURI::root(); ?>', '', '', <?php echo $cart_itemid; ?>, '<?php echo JRoute::_("index.php?option=com_digicom&viewcart&Itemid=".$cart_itemid) ?>');"><i class="ico-shopping-cart"></i> <?php echo JText::_("DSADDTOCART");?></button>
								<?php } else{ ?>
									<button type="submit" class="btn btn-warning"><i class="ico-shopping-cart"></i> <?php echo JText::_("DSADDTOCART");?></button>
								<?php } ?>
							</form> -->

							<!-- Readmore Button -->
		                    <p class="clearfix" style="margin: 0;">
			                    <!-- Product price -->
						  		<span class="price"><?php echo DigiComHelper::format_price2($item->price, $this->configs->get('currency','USD'), true, $this->configs); ?></span>
		                    	<a href="<?php echo JRoute::_('index.php?option=com_digicom&view=products&cid='.$item->catid.'&pid='.$item->id);?>" class="btn btn-primary read-more"><?php echo JText::_('COM_DIGICOM_PRODUCT_DETAILS'); ?></a>
		                    </p> 
	                  	</div>
	                </div>
	              </li>
				  <?php 
				  $i++;
				  endforeach; 
				  ?>
	            </ul>
	          </div>
		</div>
		<div class="pagination"><?php echo $this->pagination->getPagesLinks(); ?></div>
	</div>
	
</div>
<?php
echo DigiComHelper::powered_by();
