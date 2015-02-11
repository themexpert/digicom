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
error_reporting(E_ALL); ini_set("display_errors", 1);
$cart_itemid = DigiComHelper::getCartItemid();
$conf = $this->configs;
$prod = $this->prod;
$date_today = time();
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

<form name="prod" id="product-form" action="<?php echo JRoute::_('index.php?option=com_digicom&view=cart');?>" method="post" style="width:100%;">

	<div class="ijd-box ijd-rounded row-fluid">
		<?php if(!empty($prod->images)): ?>
		<div class="span4">	
			<div class="pull-left">
				<img src="<?php echo $prod->images; ?>" class="img-responsive img-rounded"/>
			</div>
		</div>
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
					<br />
					
					<label for="quantity23" class="quantity_box">
						<?php echo JText::_('DSPRICE'); ?>:&nbsp;
						<span class="label"><?php echo $prod->price; ?> <?php echo $conf->get('currency','USD'); ?> </span>
					</label>
					
					<label for="quantity23" class="quantity_box">
						<?php echo JText::_('DSQUANTITY'); ?>:&nbsp;
						<input id="quantity_<?php echo $prod->id; ?>" type="number" name="qty" min="1" class="input-small" value="1" size="2" placeholder="<?php echo JText::_('DSQUANTITY'); ?>">
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