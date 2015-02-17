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
JHtml::_('jquery.framework');
if($this->configs->get('afteradditem',0) == "2"){
	JHTML::_('behavior.modal');
	JFactory::getDocument()->addScript(JURI::base()."media/digicom/assets/js/createpopup.js");
}
$cart_itemid = DigiComHelper::getCartItemid();
?>
<div class="digicom-wrapper com_digicom categories">
	<h2 class="page-title category-title"><?php echo $this->category->name; ?></h2>
	<div class="category_info media">
		<div class="pull-left">
			<img class="img-rounded" src="<?php echo $this->category->image; ?>"/>
		</div>
		<div class="media-body">
			<?php echo $this->category->description; ?>
		</div>
	</div>
	
	<div class="products_list clearfix">
		<div class="row-fluid">
            <ul class="thumbnails">
              <?php 
			  $i=0;
			  foreach($this->prods as $key=>$item): 
			  if($i%3 == 0) echo '</ul></div><div class="row-fluid"><ul class="thumbnails">';
			  ?>
			  <li class="span4">
                <div class="thumbnail">
                  <?php if(!empty($item->images)): ?>
				  <img alt="300x200" src="<?php echo $item->images; ?>" style="width: 300px; height: 200px;">
                  <?php endif; ?>
				  
				  <div class="caption">
                    <h3 class="center"><?php echo $item->name; ?></h3>
					<p class="center price"><span class="label label-success"><?php echo DigiComHelper::format_price2($item->price, $this->configs->get('currency','USD'), true, $this->configs); ?></span></p>
                    <p class="description"><?php echo $item->description; ?></p>
					
					
					<form name="prod" class="input-append" id="product-form" action="<?php echo JRoute::_('index.php?option=com_digicom&view=cart');?>" method="post" style="width:100%;">
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
					</form>

					
                    <p class="center"><a href="<?php echo JRoute::_('index.php?option=com_digicom&view=products&cid='.$item->catid.'&pid='.$item->id);?>" class="btn btn-primary"><?php echo JText::_('COM_DIGICOM_PRODUCT_DETAILS'); ?></a></p>
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
