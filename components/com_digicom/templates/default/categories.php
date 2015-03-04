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
?>
<div id="digicom" class="digi-categories">
	<!-- Category Name -->
	<h2 class="page-title"><?php echo $this->category->name; ?></h2>
	<!-- Category Info -->
	<div class="category-info media">
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
			  if($i%3 == 0) echo '</ul></div><div class="row-fluid"><ul class="thumbnails">';
			  ?>
			  <li class="span4">
                <div class="thumbnail">
                	<!-- Product Image -->
                  	<?php if(!empty($item->images)): ?>
				  	<img alt="Product Image" src="<?php echo $item->images; ?>">
                  	<?php endif; ?>
                  	<!-- Product price -->
				  	<p class="price"><span class="label label-success"><?php echo DigiComHelper::format_price2($item->price, $this->configs->get('currency','USD'), true, $this->configs); ?></span></p>
				  
				  	<!-- Product Name & Intro text -->
				  	<div class="caption">
	                    <h3><?php echo $item->name; ?></h3>
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
	                    <p>
	                    	<a href="<?php echo JRoute::_('index.php?option=com_digicom&view=products&cid='.$item->catid.'&pid='.$item->id);?>" class="btn btn-primary"><?php echo JText::_('COM_DIGICOM_PRODUCT_DETAILS'); ?></a>
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
	<div class="pagination"><?php echo $this->pagination->getPagesLinks(); ?> </div>
</div>
<?php
echo DigiComHelper::powered_by();
