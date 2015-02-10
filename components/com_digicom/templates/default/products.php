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
JHTML::_('behavior.modal');
JHtml::_('formbehavior.chosen', 'select');
$k = 0;

$n = count($this->prods);
$tp = $this->totalprods;
$lists = $this->lists;

if (count($this->prods)) reset($this->prods);
if($n):
?>

<div class="digicom">
	<div id="dslayout-vert">
		<h4><?php echo $this->category_name; ?></h4>
<?php
	if ($n < 1): // No product available
?>
	<div class="well well-small">
		<?php
			echo JText::_('DSNOPROD')."<br/><br/><a href='".JRoute::_("index.php?option=com_digicom")."'>".JText::_('DSCONTINUESHOPING')."</a>";
		?>
	</div>
<?php
	else:
		
		$orderby = JRequest::getVar('orderby', 'default');
		if($this->configs->get('prodlayoutsort',0) == "0"){
			$controller = JRequest::getVar('controller', "");
			if(trim($controller) == ""){
				$controller = JRequest::getVar('view', "");
			}
	?>
			<div class="well well-small">
				<form class="form-horizontal nomargin" action="index.php" name="digicom_orderby" method="get">
					<?php echo JText::_("DIGI_SORT_BY"); ?>:   
					<input type="hidden" name="option" value="<?php echo JRequest::getVar('option'); ?>"/>
					<input type="hidden" name="controller" value="<?php echo strtolower($controller); ?>"/>
					<input type="hidden" name="task" value="<?php echo JRequest::getVar('task'); ?>"/>
					<input type="hidden" name="cid" value="<?php echo JRequest::getVar('cid'); ?>"/>
					<select class="pull-right" id="orderby" name="orderby" onchange="digicom_orderby.submit()">
						<option value="default"<?php echo ($orderby == 'default') ? " selected='selected'":"" ; ?>><?php echo JText::_("DIGI_SELECT"); ?></option>
						<option value="name"<?php echo ($orderby == 'name') ? " selected='selected'":"" ; ?>><?php echo JText::_("DIGI_PRODUCT_NAME"); ?></option>
						<option value="latest"<?php echo ($orderby == 'latest') ? " selected='selected'":"" ; ?>><?php echo JText::_("DIGI_LATEST_PRODUCTS"); ?></option>
					</select>
					<input type="hidden" name="Itemid" value="<?php echo JRequest::getVar('Itemid'); ?>"/>
				</form>
			</div>
	<?php
		}


	$Itemid = JRequest::getInt("Itemid", "0");
	$cart_itemid = DigiComHelper::getCartItemid();

	if(intval($cart_itemid) == 0){
		$cart_itemid = $Itemid;
	}

	$andItemid = "";
	if($cart_itemid != "0"){
		$andItemid = "&Itemid=".$cart_itemid;
	}
	$product_itemid = DigiComHelper::getProductItemid();
	$andProdItem = "";
	if($product_itemid != "0"){
		$andProdItem = "&Itemid=".$product_itemid;
	}

	$cols = $this->configs->get('prodlayoutcol',3);

	if($cols < 1) $cols = 1;
	if($cols > 10) $cols = 10;
	$style_cols = floor(100/$cols);
	$span = floor(12/$cols);

	$rows = $this->configs->get('prodlayoutrow',3);

	$k = 0;

	for($i = 0; $i < $rows; $i++):
		if($k >= $n)
		{
			break;
		}

		echo '<div class="row-fluid">';
		for($j = 0; $j < $cols; $j++):

			if($k >= $n)
			{
				break;
			}
			$prod = $this->prods[$this->pids[$k]];
			$separator_vert = ' ijd-vertical-separator ';
			if($j == ($cols-1)){
				$separator_vert = ' ';
			}

			$id = $prod->id;
			$link = JRoute::_("index.php?option=com_digicom&controller=products&task=view&cid=".$this->catid."&pid=".$id.$andProdItem);

			$img_align = "align_topcenter";
			if($this->configs->get('grid_image_align',0) == "0"){// top center
				$img_align = "align_topcenter";
			}
			elseif($this->configs->get('grid_image_align',0) == "1"){// left
				$img_align = "align_left";
			}
			elseif($this->configs->get('grid_image_align',0) == "2"){// right
				$img_align = "align_right";
			}

			$db = JFactory::getDBO();
			$sql = "SELECT price FROM #__digicom_products_plans WHERE product_id=".intval($id)." AND `default`=1";
			$db->setQuery($sql);
			$db->query();
			$default_result = $db->loadResult();
			if(isset($default_result)){
				$prod->price = $default_result;
				$prod->price = DigiComHelper::format_price2($prod->price, $this->configs->get('currency','USD'), true, $this->configs);
			} ?>


			<div class="span<?php echo $span; ?>">

				<?php
				if ($this->configs->get('grid_image_align',0) == 0):?>
				<div class="thumbnail"><?php
					$title = $prod->image_title;
					$title = str_replace('"', "&quot;", $title);
					if(trim($title) != "")
					{
						$title = 'title="'.$title.'"';
					}

					if ($prod->articlelinkuse == 0) {
						if (isset($prod->defprodimage)) :
						?>
						<p class="<?php echo $img_align;?>">
							<img <?php echo $title; ?> src="<?php echo ImageHelper::GetProductThumbImageURL($prod->defprodimage); ?>" alt="<?php echo $prod->name; ?>" class="<?php echo $img_align; ?>">
						</p>
						<?php
						endif;
					} elseif ($prod->articlelinkuse == 1) {
						$article_id = intval($prod->articlelinkid);
						$link_article = "";
						if ($article_id != "0") {
							$itemid_string = "";
							$itemid_page = JRequest::getVar("Itemid", "0");
							if($itemid_page != "0" && $itemid_page != ""){
								$itemid_string = "&Itemid=".$itemid_page;
							}
							
							require_once JPATH_SITE . '/components/com_content/helpers/route.php';
							$db = JFactory::getDbo();
							$sql = "SELECT
											co.id,
											concat(co.id, ':', co.alias) AS `slug`,
											concat(ca.id, ':', ca.alias) AS `catslug`
										FROM
											#__content AS co
												INNER JOIN
											#__categories AS ca ON co.catid = ca.id
										WHERE
											co.id =".$prod->articlelinkid;
							$db->setQuery($sql);
							$res = $db->loadObject();
							if($res){
								$link_article = JRoute::_(ContentHelperRoute::getArticleRoute($res->slug, $res->catslug));
							}
							if( !$link_article ) {
								$link_article = JRoute::_('index.php?option=com_digicom&controller=products&task=view&cid='.$prod->catid.'&pid='.$prod->id);
							}
						}
						?>
						<p style="font-weight:bold;" class="<?php echo $img_align;?>">
							<a title="<?php echo $prod->name;?>" href="<?php echo $link_article;?>"><?php echo $prod->name; ?></a>
						</p>
						<?php if (isset($prod->defprodimage)) : ?>
						<p class="<?php echo $img_align;?>">
							<a title="<?php echo $prod->name;?>" href="<?php echo $link_article;?>">
								<img <?php echo $title; ?> src="<?php echo ImageHelper::GetProductThumbImageURL($prod->defprodimage); ?>" alt="<?php echo $prod->name; ?>" class="<?php echo $img_align; ?>">
							</a>
						</p>
						<?php endif; ?>
						<?php
					} elseif ($prod->articlelinkuse == 2) {
						?>
						<p style="font-weight:bold;" class="<?php echo $img_align;?>">
							<a title="<?php echo $prod->name;?>" href="<?php echo trim($prod->articlelink); ?>" target="_blank"><?php echo $prod->name; ?></a>
						</p>
						<p class="<?php echo $img_align;?>">
							<?php if (isset($prod->defprodimage)) : ?>
							<a title="<?php echo $prod->name;?>" href="<?php echo trim($prod->articlelink); ?>" target="_blank">
								<img <?php echo $title; ?> src="<?php echo ImageHelper::GetProductThumbImageURL($prod->defprodimage); ?>" alt="<?php echo $prod->name; ?>" class="<?php echo $img_align; ?>">
							</a>
							<?php endif; ?>
							<?php if(trim($prod->subtitle) != ""): ?>
								<br />
								<?php echo $prod->subtitle;?>
							<?php endif;?>
						</p>
						<?php
					} elseif($prod->articlelinkuse == 3) {
						?>
						<p style="font-weight:bold;" class="<?php echo $img_align;?>">
							<a title="<?php echo $prod->name;?>" href="<?php echo $link; ?>"><?php echo $prod->name; ?></a>
						</p>
						<p class="<?php echo $img_align;?>">
							<?php if (isset($prod->defprodimage)) : ?>
							<a title="<?php echo $prod->name;?>" href="<?php echo $link;?>">
								<img <?php echo $title; ?> src="<?php echo ImageHelper::GetProductThumbImageURL($prod->defprodimage); ?>" alt="<?php echo $prod->name; ?>" class="<?php echo $img_align; ?>">
							</a>
							<?php endif;?>
						<?php if(trim($prod->subtitle) != ""): ?>
							<br />
							<?php echo $prod->subtitle;?>
						<?php endif;?>
						</p><?php
					}
					?>
					<div class="caption">
						<?php
						$link_article = "";
						if($prod->articlelinkuse == 1)
						{
							$article_id = intval($prod->articlelinkid);
							if($article_id != "0")
							{
								$itemid_string = "";
								$itemid_page = JRequest::getInt("Itemid", "0");
								if($itemid_page != "0" && $itemid_page != "")
								{
									$itemid_string = "&Itemid=".$itemid_page;
								}
								// $db = JFactory::getDBO();
								// $sql = "select `alias`, `catid` from #__content where id=".intval($article_id);
								// $db->setQuery($sql);
								// $db->query();
								// $result = $db->loadAssocList();
								// $link_article = JRoute::_("index.php?option=com_content&view=article&id=".$article_id.":".trim($result["0"]["alias"])."&catid=".intval(trim($result["0"]["catid"])).$itemid_string);
								// $link_article = trim($link_article);
								
								require_once JPATH_SITE . '/components/com_content/helpers/route.php';
								$db = JFactory::getDbo();
								$sql = "SELECT
												co.id,
												concat(co.id, ':', co.alias) AS `slug`,
												concat(ca.id, ':', ca.alias) AS `catslug`
											FROM
												#__content AS co
													INNER JOIN
												#__categories AS ca ON co.catid = ca.id
											WHERE
												co.id =".$article_id;
								$db->setQuery($sql);
								$res = $db->loadObject();
								if($res){
									$link_article = JRoute::_(ContentHelperRoute::getArticleRoute($res->slug, $res->catslug));
								}
								if( !$link_article ) {
									$link_article = JRoute::_('index.php?option=com_digicom&controller=products&task=view&cid='.$prod->catid.'&pid='.$prod->id);
								}
							}
						}
						elseif($prod->articlelinkuse == 2)
						{
							$link_article = trim($prod->articlelink);
						}
						elseif($prod->articlelinkuse == 3)
						{
							$link_article = $link;
						}
						?>
						<?php
						if(!is_null($prod->price) ):
							?>
							<span class="ijd-product-price ijd-row ijd-center"><?php echo $prod->price; ?></span>
							<?php
						endif;
						?>
						<p style="text-align:center;"><?php echo DigiComHelper::ShowProdDesc($prod->description, $this->configs, "list"); ?></p><?php

						$validation_js_script = DigiComHelper::addValidation($prod->productfields, $this->pids[$k]);
						$doc = JFactory::getDocument();
						$doc->addScriptDeclaration( $validation_js_script ); ?>

					<?php if (!$prod->usestock || ($prod->usestock && $prod->showstockleft && $prod->stock == 0 && $prod->emptystockact == 0) )
					{ ?>
						<form name="addproduct" method="post" action="index.php" onsubmit="return prodformsubmitA<?php echo $this->pids[$k]; ?>()">
						<table width="100%">
							<tr>
								<td>
									<input type="hidden" name="option" value="com_digicom"/>
									<input type="hidden" name="controller" value="Cart"/>
									<input type="hidden" name="task" value="add"/>
									<?php
										if(JRequest::getVar('layout','none') != 'none') :
									?>
										<!-- <input type="hidden" name="layout" value="<?php //echo JRequest::getVar('layout','list'); ?>"/> -->
									<?php
										endif;
									?>
									<input type="hidden" name="pid" value="<?php echo $id; ?>"/>
									<input type="hidden" name="cid" value="<?php echo $this->catid; ?>"/>
									<input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>"/>
									<div class="<?php echo $img_align; ?>">
										<?php
										if ($prod->cartlinkuse == 1) {
											?>
											<div class="btn-group">
												<?php if ($this->configs->get('showproductdetails',1) == '1') : ?>
												<a href="<?php echo $link_article;?>" class="btn btn-small" title="<?php echo JText::_("DIGI_PRODUCT_DETAILS");?>" <?php echo  ($prod->articlelinkuse == 2 ? 'target="_blank"' : '');?>><i class="ico-folder-open"></i> <?php echo JText::_("DIGI_PRODUCT_DETAILS");?></a>
												<?php endif; ?>
												<?php if ($this->configs->get('catalogue',0) == '0') : ?>
												<a href="<?php echo $prod->cartlink;?>" class="btn btn-warning" target="_blank" style="font-size:12px;color:#333;padding:2px 9px;"><i class="ico-shopping-cart"></i> <?php echo JText::_("DSADDTOCART");?></a>
												<?php endif; ?>
											</div>
											<?php
										} elseif ($this->configs->get('afteradditem',2) == "2") {
												$doc->addScript(JURI::base().DIGICOM_ASSET_PATH . "/js/createpopup.js"); ?>
												
												<div class="btn-group">
													<?php if ($this->configs->get('showproductdetails',1) == '1') : ?>
													<a href="<?php echo $link_article;?>" class="btn btn-small" title="<?php echo JText::_("DIGI_PRODUCT_DETAILS");?>" <?php echo  ($prod->articlelinkuse == 2 ? 'target="_blank"' : '');?>><i class="ico-folder-open"></i> <?php echo JText::_("DIGI_PRODUCT_DETAILS");?></a>
													<?php endif; ?>
													<?php if ($this->configs->get('catalogue',0) == '0') : ?>
													<button type="button" class="btn btn-small btn-warning" onclick="javascript:createPopUp(<?php echo $id; ?>, <?php echo $this->catid; ?>, '<?php echo JURI::root(); ?>', '', '', <?php echo $cart_itemid; ?>, '<?php echo JRoute::_("index.php?option=com_digicom&view=cart&Itemid=".$cart_itemid); ?>');"><i class="ico-shopping-cart"></i> <?php echo JText::_("DSADDTOCART");?></button>
													<?php endif; ?>
												</div>
												<?php
										} else {
											?>
											<div class="btn-group">
												<?php if ($this->configs->get('showproductdetails',1) == '1') : ?>
												<a href="<?php echo $link_article;?>" class="btn btn-small" title="<?php echo JText::_("DIGI_PRODUCT_DETAILS");?>" <?php echo  ($prod->articlelinkuse == 2 ? 'target="_blank"' : '');?>><i class="ico-folder-open"></i> <?php echo JText::_("DIGI_PRODUCT_DETAILS");?></a>
												<?php endif; ?>
												<?php if ($this->configs->get('catalogue',0) == '0') : ?>
												<button type="submit" class="btn btn-small btn-warning"><i class="ico-shopping-cart"></i> <?php echo JText::_("DSADDTOCART");?></button>
												<?php endif; ?>
											</div> <?php
										}
										?>
									</div>
								</td>
							</tr>

							<?php
							if($prod->usestock && $prod->showstockleft && ($prod->used < $prod->stock)){
						?>
								<tr>
									<td>
										<div class="dsremainingstock"><?php echo sprintf(JText::_("DS_NUMBER_IN_STOCK"), $prod->stock - $prod->used); ?></div>
									</td>
								</tr>
							<?php
								}
							?>

							<?php
								if($prod->usestock && $prod->emptystockact && ($prod->used >= $prod->stock)){
							?>
									<tr>
										<td>
											<div class="dssoldout"><?php echo JText::_("DSOUTOFSTOCK"); ?></div>
										</td>
									</tr>
							<?php
								}
							?>

						</table>
						</form>
					<?php
					} else {
					?>
						<?php if ($prod->usestock && ($prod->used < $prod->stock)) : ?>
						<form name="addproduct" method="post" action="index.php" onsubmit="return prodformsubmitA<?php echo $this->pids[$k]; ?>()">
						<?php endif; ?>
						<table width="100%"><tr><td>
						<?php
								//if ($this->configs->showprodshort == 1) echo "<div class='dsdesc'>".DigiComHelper::ShowProdDesc($prod->description, $this->configs)."</div>";
						?>
						 </td></tr>
							<?php if ($prod->usestock && (($prod->stock - $prod->used) > 0)) : ?>
							<?php if ($prod->showqtydropdown == 1) { ?>
							<tr><td><div class="dsqty"><?php echo $lists['qty'][$id];?></div></td></tr>
							<?php } ?>
							<?php if ($this->maxf > 0):?>
							<tr><td><div class="dsattribs">
								<?php echo $lists[$prod->id]['attribs'];?>
								</div>
							</td></tr>
							<?php endif; ?>
							<tr><td><input type="hidden" name="option" value="com_digicom"/>
							<input type="hidden" name="controller" value="Cart"/>
							<input type="hidden" name="task" value="add"/>
							<?php if (JRequest::getVar('layout','none') != 'none') : ?>
							<!-- <input type="hidden" name="layout" value="<?php //echo JRequest::getVar('layout','list'); ?>"/> -->
							<?php endif; ?>
							<input type="hidden" name="pid" value="<?php echo $id; ?>"/>
							<input type="hidden" name="cid" value="<?php echo $this->catid; ?>"/>
							<input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>"/>
							<div class="dsaddtocart">
								<?php
									if($this->configs->get('afteradditem',2) == "2"){
										$doc->addScript(JURI::base().DIGICOM_ASSET_PATH . "/js/createpopup.js");
								?>
										<div class="btn-group">
											<a href="<?php echo $link_article;?>" class="btn btn-small" title="<?php echo JText::_("DIGI_PRODUCT_DETAILS");?>"><i class="ico-folder-open"></i> <?php echo JText::_("DIGI_PRODUCT_DETAILS");?></a>
											<button type="button" class="btn btn-small btn-warning" onclick="javascript:createPopUp(<?php echo $id; ?>, <?php echo $this->catid; ?>, '<?php echo JURI::root(); ?>', '', '', <?php echo $cart_itemid; ?>, '<?php echo JRoute::_("index.php?option=com_digicom&view=cart&Itemid=".$cart_itemid) ?>');"><i class="ico-shopping-cart"></i> <?php echo JText::_("DSADDTOCART");?></button>
										</div>
								<?php
									}
									else{
								?>
										<div class="btn-group">
											<a href="<?php echo $link_article;?>" class="btn btn-small" title="<?php echo JText::_("DIGI_PRODUCT_DETAILS");?>"><i class="ico-folder-open"></i> <?php echo JText::_("DIGI_PRODUCT_DETAILS");?></a>
											<button type="submit" class="btn btn-small btn-warning"><i class="ico-shopping-cart"></i> <?php echo JText::_("DSADDTOCART");?></button>
										</div>
								<?php
									}
								?>

							</div>
							</td></tr>
							<?php endif; ?>

							<?php
								if($prod->usestock && $prod->showstockleft && ($prod->used < $prod->stock)){
							?>
									<tr>
										<td>
											<div class="dsremainingstock"><?php echo sprintf(JText::_("DS_NUMBER_IN_STOCK"), $prod->stock - $prod->used); ?></div>
										</td>
									</tr>
							<?php
								}
							?>

							<?php
								if($prod->usestock && $prod->emptystockact && ($prod->used >= $prod->stock)){
							?>
									<tr>
										<td>
											<div class="dssoldout"><?php echo JText::_("DSOUTOFSTOCK"); ?></div>
										</td>
									</tr>
							<?php
								}
							?>

						</table>
						<?php if ($prod->usestock && ($prod->used < $prod->stock)) : ?>
						</form>
						<?php endif; ?>
						<?php } // end if use stock option ?>

					<!-- End / Add to Cart -->
					</div>
				</div>
				<?php endif;?>
			</div>
<?php
		$k++;

		endfor;

		if ($k <= $n) {
?>
			</div>
<?php
		}

	endfor;
	// if(($rows * $cols) < $this->pagination->limit)
	// {
		// $pagination = new JPagination($this->total, $limitstart, ($rows * $cols));
		// $this->pagination;= $pagination;
		if($this->pagination->getPagesLinks() != ''):?>
			<div class="pagination pagination-centered"><?php echo $this->pagination->getPagesLinks(); ?></div>
			<div style="text-align:center;"><?php echo $this->pagination->getPagesCounter(); ?></div><?php
		endif;
	// }
?>
<?php
endif;
endif;
?>
</div>

<?php echo DigiComHelper::powered_by(); ?>
