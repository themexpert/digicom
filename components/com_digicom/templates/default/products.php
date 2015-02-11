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

echo $r;
$addtocart = '<input type="submit" value="'.(JText::_("DSADDTOCART")).'" class="btn"/> ';
//$price =  $prod->price;
$i = $this->i;
$validation_js_script = DigiComHelper::addValidation($prod->productfields, $i);
$doc = JFactory::getDocument();
$doc->addScriptDeclaration( $validation_js_script );
// $doc->addScript(JURI::root()."components/com_digicom/assets/js/SqueezeBox.js");
$doc->addScript(JURI::root()."components/com_digicom/assets/js/createpopup.js");
// $doc->addStyleSheet(JURI::root()."components/com_digicom/assets/css/SqueezeBox.css");

$gray_size = $conf->prodlayoutlightimgprev;
$gray_size_w = $gray_size + 100;
$gray_size_h = $gray_size + 100;

?>

<div id="dslayout-viewproduct">

<?php if (!$prod->usestock || ($prod->usestock && $prod->showstockleft && $prod->stock == 0 && $prod->emptystockact == 0) || ($prod->usestock && (($prod->stock - $prod->used) > 0)) )  : ?>

<script language="javascript" type="text/javascript">
	function displayImage(key, type){
		to_be_replaced = "";
		if(type == "image"){
			to_be_replaced = 'slide';
		}
		else if(type == "prev"){
			to_be_replaced = 'prev_div';
		}
		else if(type == "next"){
			to_be_replaced = 'next_div';
		}

		var url = 'index.php?option=com_digicom&controller=Products&task=getimage&type='+type+'&format=raw&position='+key+'&pid='+<?php echo intval($prod->id); ?>;
		var req = new Request.HTML({
			method: 'get',
			url: url,
			data: { 'do' : '1' },
			update: $(to_be_replaced),
			onComplete: function(response){
			}
		}).send();

		document.getElementById("count").innerHTML = (parseInt(key)+1)+"/<?php echo count($prodimages); ?>";

		return true;
	}

	function nextImage(){
		var pre = document.prod.prev_pos.value;
		var next = document.prod.next_pos.value;
		document.prod.prev_pos.value = parseInt(pre) + 1;
		document.prod.next_pos.value = parseInt(next) + 1;
		displayImage(next, 'prev');
		displayImage((parseInt(next) + 1), 'next');
		displayImage(next, 'image');
		document.getElementById("prev").style.display = 'block';
		if((parseInt(next) + 1) == <?php echo count($prodimages); ?>){
			document.getElementById("next").style.display = "none";
		}
	}

	function prevImage(){
		var pre = document.prod.prev_pos.value;
		var next = document.prod.next_pos.value;
		document.prod.prev_pos.value = parseInt(pre) - 1;
		document.prod.next_pos.value = parseInt(next) - 1;
		displayImage(parseInt(pre) - 1, 'prev');
		displayImage(pre, 'next');
		displayImage(parseInt(pre) - 1, 'image');
		if(parseInt(pre) - 1 <= 0){
			document.getElementById("prev").style.display = "none";
		}
		document.getElementById("next").style.display = "block";
	}

	function changeGrayBoxSize(width, height){
		document.getElementById('sbox-window').style.height = parseInt(height) + "px";
		document.getElementById('sbox-window').style.width = parseInt(width) + "px";
	}
</script>

<form name="prod" id="product-form" action="<?php echo JUri::root().'index.php';?>" method="post" style="width:100%;" onsubmit="return prodformsubmitA<?php echo $i; ?>()">
<?php endif; ?>

	<?php if ($prod->usestock && $prod->showstockleft && ($prod->used < $prod->stock)) : ?>
	<?php $lists['qty'] .= "<div class='dsremainingstock'>" . sprintf(JText::_("DS_NUMBER_IN_STOCK"), $prod->stock - $prod->used) . "</div>"; ?>
	<?php endif; ?>

	<?php if ($prod->usestock && $prod->emptystockact && ($prod->used >= $prod->stock)) :?>
	<?php $addtocart = ""; ?>
	<?php $lists['qty'] = "<div class='dssoldout'>".JText::_("DSOUTOFSTOCK")."</div>"; ?>
	<?php $lists['attribs'] = ""; ?>
	<?php endif; ?>
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
			if ($conf->showtwitter == 1 || $conf->showfacebook == 1)
			{ ?>
				<table align="right" width="100%">
					<tr>
						<td>
							<?php
							if($conf->showtwitter == 1){
								?>
								<div id="tweet-zone2">
									<a href="http://twitter.com/share" class="twitter-share-button" data-url="<?php echo $url; ?>" data-count="horizontal">Tweet</a>
									<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
								</div>
								<?php
							}

							if($conf->showfacebook == 1){
								?>
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
								<?php
							}
							?>
						</td>
					</tr>
				</table>
				<?php
			} ?>

			<h1 class="ijd-single-product-title"><?php echo $prod->name; ?></h1>
			<?php
				if(trim($prod->subtitle) != ""){
			?>
					<span class="ijd-product-subtitle" style="margin-left:0px !important;"><?php echo $prod->subtitle; ?></span>
			<?php
				}
			?>

			<?php
				if($conf->showshortdescription == "1"){
			?>
					<span class="<?php echo $conf->prod_short_desc_class; ?>"><?php echo $prod->description; ?></span>
			<?php
				}
				if($conf->showlongdescription == "1"){
			?>
					<span class="<?php echo $conf->prod_long_desc_class; ?>"><?php echo $prod->fulldescription; ?></span>
			<?php
				}
			?>



			<?php if ($this->configs->catalogue == '0') : ?>
			<div class="ijd-add-to-cart ijd-row">

				<?php echo $lists['attribs']; ?>

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
						<?php
							if ($lists['pp']) {
								if ($this->prod_plans_len == 1) {
									$price_or_plan = 'DSPRICE';
								} else {
									$price_or_plan = 'DSTERMS';
								}
						?>
						<label for="quantity23" class="<?php echo $qb; ?>"><?php echo JText::_($price_or_plan); ?>:&nbsp;</label>
						<?php echo $lists['pp']; ?>
						<br />
						<?php } ?>
						<br />

						<?php
							if(($prod->usestock == 1) && ($prod->emptystockact == 0 || $prod->emptystockact == 2) && ($prod->used >= $prod->stock)){
								//do nothing
							}
							elseif(($prod->usestock == 1) && ($prod->emptystockact == 1) && ($prod->used >= $prod->stock)){
								echo '<div class="dssoldout">'.JText::_("DSOUTOFSTOCK").'</div>';
							}
							else{
								if ($prod->cartlinkuse == 1)
								{ ?>
									<div class="btn-group">
										<a href="<?php echo $link_article;?>" class="btn btn-small" title="<?php echo JText::_("DIGI_PRODUCT_DETAILS");?>"><i class="ico-folder-open"></i> <?php echo JText::_("DIGI_PRODUCT_DETAILS");?></a>
										<a href="<?php echo $prod->cartlink;?>" class="btn btn-warning" target="_blank"><i class="ico-shopping-cart"></i> <?php echo JText::_("DSADDTOCART");?></a>
									</div><?php
								}
								else
								{
									if($conf->afteradditem == "2")
									{
										$doc->addScript(JURI::base()."components/com_digicom/assets/js/createpopup.js"); ?>
										<button type="button" class="btn btn-warning" onclick="javascript:createPopUp(<?php echo $prod->id; ?>, <?php echo JRequest::getVar("cid", "0"); ?>, '<?php echo JURI::root(); ?>', '', '', <?php echo $cart_itemid; ?>, '<?php echo JRoute::_("index.php?option=com_digicom&view=cart&Itemid=".$cart_itemid); ?>');"><i class="ico-shopping-cart"></i> <?php echo JText::_("DSADDTOCART");?></button><?php
									}
									else
									{ ?>
										<button type="submit" class="btn btn-warning"><i class="ico-shopping-cart"></i> <?php echo JText::_('DSADDTOCART'); ?></button><?php
									}
								}
							}
						?>
						<?php
							if($prod->usestock && $prod->showstockleft && ($prod->used < $prod->stock)){
								echo '<div class="dsremainingstock">'.sprintf(JText::_("DS_NUMBER_IN_STOCK"), $prod->stock - $prod->used).'</div>';
							}
						?>
				</div><!--add to cart-->
			</div>
			<?php endif; ?>
		</div>
	</div>
	
	<p id="hidden_dst">
		<a id="change_cb">#</a><br>
		<a id="close_cb">#</a>
	</p>

<?php if (!$prod->usestock || ($prod->usestock && $prod->showstockleft && $prod->stock == 0 && $prod->emptystockact == 0) || ($prod->usestock && (($prod->stock - $prod->used) > 0)) )  : ?>
	<input type="hidden" name="option" value="com_digicom"/>
	<input type="hidden" name="controller" value="Cart"/>
	<input type="hidden" name="task" value="add"/>
	<input type="hidden" name="pid" value="<?php echo $prod->id; ?>"/>
	<input type="hidden" name="cid" value="<?php echo JRequest::getInt("cid", "0"); ?>"/>
	<input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>"/>
</form>
<?php endif; ?>

</div>

<?php
	include ('related_include.php');
?>

<?php echo DigiComHelper::powered_by(); ?>
