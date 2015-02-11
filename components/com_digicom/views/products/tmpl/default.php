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
//error_reporting(E_ALL); ini_set("display_errors", 1);
$cart_itemid = DigiComHelper::getCartItemid();
$conf = $this->configs;
$prod = $this->prod;
$date_today = time();
$k = 0;
?>
<div class="digicom-wrapper com_digicom products">

	<?php if(!$prod->id): ?>
	<div class="alert alert-warning">
		<p><?php echo JText::_('DSPRODNOTAVAILABLE'); ?></p>
		<p><a href="<?php echo JRoute::_("index.php?option=com_digicom&view=categories&id=0"); ?>"><?php echo JText::_("DSCONTINUESHOPING"); ?></a></p>
	</div>
	<?php return true; ?>
	<?php endif; ?>
	
	<?php if(($prod->publish_up > $date_today) || ($prod->publish_down != 0 && $prod->publish_down < $date_today)): ?>
	<div class="alert alert-warning">
		<?php echo JText::_('COM_DIGICOM_PRODUCT_PUBLISH_DOWN'); ?>
	</div>
	<?php return true; ?>
	<?php endif; ?>

	<?php if($prod->published == 0): ?>
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

<form name="prod" id="product-form" action="<?php echo JRoute::_('index.php?option=com_digicom&view=cart');?>" method="post" style="width:100%;" onsubmit="return prodformsubmitA<?php echo $prod->id; ?>()">

	<div class="ijd-box ijd-rounded row-fluid">
		<?php if(isset($prodimages) && count($prodimages) > 0) : ?>
		<!-- Images Showcase -->
		<div class="span4">
			<div id="total_slide">
				<?php
					if (isset($prodimages["0"])) {
						$src = ImageHelper::GetProductThumbImageURL($prodimages["0"], "prev");
						$size = @getimagesize($src);
						$style = "";
						if (isset($size)) {
							$style .= "min-height:".($size["1"]+10)."px;";
						}
					} else {
						$style = '';
					}
				?>
				<div id="slide" style="<?php echo $style; ?>">
					<?php
						if(isset($prodimages["0"])){
							$src = ImageHelper::GetProductThumbImageURLBySize($prodimages["0"], $gray_size);
							$size = @getimagesize($src);
							if(isset($size)){
								//$gray_size_w = $size["0"]+100;
								//$gray_size_h = $size["1"]+100;
							}
						}
					?>
					<?php
						if(isset($prodimages["0"])){
					?>
							<a onclick="javascript:grayBoxiJoomla('index.php?option=com_digicom&controller=Products&task=previwimage&tmpl=component&position=0&pid=<?php echo intval($prod->id); ?>', <?php echo $gray_size_w; ?>, <?php echo $gray_size_h; ?>)">
								<img class="product_image_gallery" src="<?php echo ImageHelper::GetProductThumbImageURL($prodimages["0"], "prev"); ?>"/>
							</a>
					<?php
						}
					?>
				</div>
				<div id="count" style="margin-bottom: 10px; margin-top: 10px; color:#CCCCCC; <?php echo $conf->gallery_style == 1 ? 'display:none;' : ""; ?>">
					<?php
						if(count($prodimages) > 0 && trim($prodimages["0"]) != ""){
							echo "1/".count($prodimages);
						}
					?>
				</div>
				<?php
					if(count($prodimages) > 1){
						if($conf->gallery_style == 0){
							echo DigiComHelper::getGalleryScroller($prod, $prodimages, $conf);
						}
						else{
							echo DigiComHelper::getGallerySimple($prod, $prodimages, $conf);
						}
					}
				?>
				<input type="hidden" name="prev_pos" value="0" />
				<input type="hidden" name="next_pos" value="1" />
			</div>
		</div>
		
		<!-- Details & Cart -->
		<div class="span8">
		<?php else: ?>
		
		<!-- Details & Cart -->
		<div class="span12">
		<?php endif; ?>
			<?php
				$url = $this->getPageURL();
			?>
				<table align="right" width="100%">
					<tr>
						<td>
							
								<div id="tweet-zone2">
									<a href="http://twitter.com/share" class="twitter-share-button" data-url="<?php echo $url; ?>" data-count="horizontal">Tweet</a>
									<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
								</div>
							
								<div id="fb-zone">
									<div>
										<div id="fb-root"></div>
										<div style="float:left;">
											<fb:like href="<?php echo $url; ?>" layout="button_count" show_faces="false" send="true" width="" action="like" font="" colorscheme="light">
											</fb:like>
											<a name="fb_share" type="button_count" href="http://www.facebook.com/sharer.php?u=<?php echo $url; ?>">
												Share
											</a>
											<script src="http://static.ak.fbcdn.net/connect.php/js/FB.Share" type="text/javascript"></script>
										</div>
									</div>
								</div>
								
						</td>
					</tr>
				</table>
				<?php
			?>

			<h1 class="ijd-single-product-title"><?php echo $prod->name; ?></h1>
			<div class="description"><?php echo $prod->fulldescription; ?></div>



			<?php if ($this->configs->get('catalogue',0) == '0') : ?>
			<div class="ijd-add-to-cart ijd-row">

				<div class="ijd-addtocartbutton ijd-pad5">
					<?php if (isset($prod->showqtydropdown) && ($prod->showqtydropdown > 0) && ($prod->domainrequired != 3)) { ?>
						<label for="quantity23" class="quantity_box"><?php echo JText::_('DSQUANTITY'); ?>:&nbsp;</label>
						<?php echo JHTML::_('select.integerlist', 0, 25, 1, 'qty', 'class="inputbox"', 1); ?>
						<br />
					<?php
							$qb = "quantity_box";
						} else {
							$qb = "";
						}
					 ?>
						
						<label for="quantity23" class="<?php echo $qb; ?>">
							<?php echo JText::_('DSPRICE'); ?>:&nbsp;
						
							<span class="label"><?php echo $prod->price; ?> <?php echo $conf->get('currency','USD'); ?> </span>
						</label>
						
						<?php
							
							if($conf->get('afteradditem',2) == "2")
							{
								$doc->addScript(JURI::base()."components/com_digicom/assets/js/createpopup.js"); ?>
								<button type="button" class="btn btn-warning" onclick="javascript:createPopUp(<?php echo $prod->id; ?>, <?php echo JRequest::getVar("cid", "0"); ?>, '<?php echo JURI::root(); ?>', '', '', <?php echo $cart_itemid; ?>, '<?php echo JRoute::_("index.php?option=com_digicom&view=cart&Itemid=".$cart_itemid); ?>');"><i class="ico-shopping-cart"></i> <?php echo JText::_("DSADDTOCART");?></button><?php
							}
							else
							{ ?>
								<button type="submit" class="btn btn-warning"><i class="ico-shopping-cart"></i> <?php echo JText::_('DSADDTOCART'); ?></button><?php
							}
								
							
						?>
						
				</div><!--add to cart-->
			</div>
			<?php endif; ?>
		</div>
	</div>
	<input type="hidden" name="option" value="com_digicom"/>
	<input type="hidden" name="view" value="cart"/>
	<input type="hidden" name="task" value="add"/>
	<input type="hidden" name="pid" value="<?php echo $prod->id; ?>"/>
	<input type="hidden" name="cid" value="<?php echo JFactory::getApplication()->input->get('cid',0);?>"/>
	<input type="hidden" name="Itemid" value="<?php echo JFactory::getApplication()->input->get('Itemid',$cart_itemid); ?>"/>
</form>

</div>


<?php echo DigiComHelper::powered_by(); ?>