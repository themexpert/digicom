<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;
JHtml::_('formbehavior.chosen', 'select');

$configs 			= $this->configs;
$processor 		= $this->session->get('processor');
$items 				= $this->items;
$tax 					= $this->tax;
$table_column = 4;
?>
<div id="digicom">

	<?php if(count($items) == 0): ?>
		<div class="alert alert-warning">
			<?php echo JText::_("COM_DIGICOM_CART_IS_EMPTY_NOTICE"); ?>
		</div>
	<?php else: ?>
		<?php if($configs->get('show_steps',1) == 1){ ?>
		<div class="pagination pagination-centered">
			<ul>
				<li class="active"><span><?php echo JText::_("COM_DIGICOM_BUYING_PROCESS_STEP_ONE"); ?></span></li>
				<li><span><?php echo JText::_("COM_DIGICOM_BUYING_PROCESS_STEP_TWO"); ?></span></li>
				<li><span><?php echo JText::_("COM_DIGICOM_BUYING_PROCESS_STEP_THREE"); ?></span></li>
			</ul>
		</div>
		<?php } ?>
		<div class="digi-cart">
			<form id="cart_form" name="cart_form" method="post" action="<?php echo JRoute::_("index.php?option=com_digicom&view=cart"); ?>">
				<?php
				$user = JFactory::getUser();
				if($user->id != "0"){
				?>
				<div class="row-fluid">
					<div class="span12" style="text-align:right;vertical-align:bottom;">
						<?php echo JText::sprintf("COM_DIGICOM_CART_LOGGED_IN_AS",$user->name); ?>
					</div>
				</div>
				<?php } ?>

				<table id="digicomcarttable" class="table table-striped table-bordered" width="100%">
					<thead>
						<tr valign="top">
							<th width="30%">
								<?php echo JText::_("COM_DIGICOM_PRODUCT");?>
							</th>
							<th>
								<?php echo JText::_("COM_DIGICOM_PRICE_PLAN");?>
							</th>

							<th>
								<?php echo JText::_("COM_DIGICOM_QUANTITY"); ?>
							</th>

							<?php if ($tax['item_discount']){?>
							<th>
								<?php echo JText::_("COM_DIGICOM_PROMO_DISCOUNT"); ?>
							</th>
							<?php } ?>

							<th><?php echo JText::_("COM_DIGICOM_SUBTOTAL");?></th>

							<th><?php echo JText::_("COM_DIGICOM_CART_REMOVE_ITEM");?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach($items as $itemnum => $item ):
							$item_link = JRoute::_(DigiComSiteHelperRoute::getProductRoute($item->id, $item->catid, $item->language));
							?>
							<tr>
								<td>

									<a href="<?php echo $item_link; ?>" target="blank"><?php echo $item->name; ?></a>
									<?php if ($this->configs->get('show_validity',1) == 1) : ?>
										<div class="muted">
											<small><?php echo JText::_('COM_DIGICOM_PRODUCT_VALIDITY'); ?> : <?php echo DigiComSiteHelperPrice::getProductValidityPeriod($item); ?></small>
										</div>
									<?php endif; ?>
								</td>

								<td nowrap="nowrap">
									<span id="cart_item_price<?php echo $item->cid; ?>">
										<?php echo DigiComSiteHelperPrice::format_price($item->price, $item->currency, true, $configs); ?>
									</span>
								</td>

								<td align="center" nowrap="nowrap">
									<span class="digicom_details">
										<strong>
											<?php if($configs->get('show_quantity',0) == "1") { ?>
												<input id="quantity<?php echo $item->cid; ?>" type="number" onchange="update_cart(<?php echo $item->cid; ?>);" name="quantity[<?php echo $item->cid; ?>]" min="1" class="input-small" value="<?php echo $item->quantity; ?>" size="2" placeholder="<?php echo JText::_('COM_DIGICOM_QUANTITY'); ?>">
											<?php } else {
												echo $item->quantity;
											} ?>
										</strong>
									</span>
								</td>

								<?php if($tax['item_discount']) : ?>
								<td style="text-align:center;" nowrap="nowrap">
									<span id="cart_item_discount<?php echo $item->cid; ?>" class="digi_cart_amount">
										<?php
										$value_discount = 0;
										if ( $item->discount > 0)
										{
											$value_discount = $item->discount;
										}
										elseif ( isset($item->percent_discount) && $item->percent_discount > 0)
										{
											$value_discount = ($item->price * $item->percent_discount) / 100;
										}
										echo DigiComSiteHelperPrice::format_price($value_discount, $item->currency, true, $configs);?>
									</span>
								</td>
								<?php endif; ?>

								<td nowrap>
									<span id="cart_item_total<?php echo $item->cid; ?>" class="digi_cart_amount">
										<?php echo DigiComSiteHelperPrice::format_price($item->subtotal-(isset($value_discount) ? $value_discount : 0), $item->currency, true, $configs); ?>
									</span>
								</td>

								<td nowrap="nowrap">
									<a href="#" class="btn btn-small btn-danger" onclick="deleteFromCart(<?php echo $item->cid;?>);"><i class="icon-trash icon-white"></i></a>
								</td>
							</tr>
							<?php
						endforeach;
						?>
					</tbody>
				</table>

				<table id="digicomcartpromo" width="100%">
					<tr valign="top">
						<td class="general_text" colspan="<?php echo $table_column; ?>" valign="bottom">
							<?php echo JText::_("COM_DIGICOM_CART_IF_PROMOCODE_LABEL"); ?>
						</td>

						<?php if ($configs->get('tax_summary',0) == 1) { ?>
						<td nowrap="nowrap" style="text-align: center; padding-top:15px;">
							<ul class="unstyled">

								<?php if ($tax['promo'] > 0 && $tax['promoaftertax'] == '0') : ?>
								<li class="digi_cart_amount" style="text-align:right;" id="digicom_cart_discount"><?php echo DigiComSiteHelperPrice::format_price($tax['promo'], $tax['currency'], true, $configs) ?></li>
								<?php endif;?>

								<?php if (($tax['value'] > 0 || $configs->get('tax_zero',1) == 1) && $this->customer->_user->id > 0) : ?>
								<li class="digi_cart_amount" style="text-align:right;" id="digicom_cart_tax"><?php echo DigiComSiteHelperPrice::format_price($tax['value'], $tax['currency'], true, $configs); ?></li>
								<?php endif; ?>

								<?php if ($tax['shipping'] > 0 && $this->customer->_user->id > 0) : ?>
								<li class="digi_cart_amount" style="text-align:right;"><?php echo DigiComSiteHelperPrice::format_price($tax['shipping'], $tax['currency'], true, $configs); ?></li>
								<?php endif; ?>

								<?php if ($tax['promo'] > 0 && $tax['promoaftertax'] == '1') : ?>
									<li class="digi_cart_amount" style="text-align:right;"><?php echo DigiComSiteHelperPrice::format_price($tax['promo'], $tax['currency'], true, $configs); ?></li>
								<?php endif; ?>
							</ul>
						</td>
						<?php } else { ?>
							<td>&nbsp;</td>
						<?php } ?>
					</tr>

					<tr valign="top">
						<td colspan="<?php echo $table_column - 1; ?>" >
							<div class="input-append">
								<input type="text" id="promocode" name="promocode" size="15" value="<?php echo $this->promocode; ?>" />
								<button type="submit" class="btn" onclick="document.getElementById('task').value='cart.updateCart';"><i class="ico-gift"></i> <?php echo JText::_("COM_DIGICOM_CART_PROMOCODE_APPLY"); ?></button>
							</div>

						</td>
						<td nowrap="nowrap" style="text-align: center;">
							<ul style="margin: 0; padding: 0;list-style-type: none;">
								<?php if ($tax['discount_calculated']): ?>
									<li class="digi_cart_subtotal_title" style="font-size: 15px;text-align:right;">
										<?php echo JText::_("COM_DIGICOM_SUBTOTAL");?>
									</li>
									<li class="digi_cart_discount_title" style="font-size: 15px;text-align:right;">
									<?php echo JText::_("COM_DIGICOM_PROMO_DISCOUNT");?>
								</li>
								<?php endif; ?>

								<li class="digi_cart_total_title" style="font-weight: bold;font-size: 18px;text-align:right;">
									<?php echo JText::_("COM_DIGICOM_TOTAL");?>
								</li>
							</ul>
						</td>
						<td nowrap="nowrap" style="text-align: center;">
							<ul style="margin: 0; padding: 0;list-style-type: none;">
								<?php if ($tax['discount_calculated']): ?>
								<li class="digi_cart_subtotal_price" id="cart_total" style="font-size: 15px;text-align:right;">
									<?php echo DigiComSiteHelperPrice::format_price($tax['price'], $tax['currency'], true, $configs); ?>
								</li>
								<li class="digi_cart_discount_price" id="cart_total" style="font-size: 15px;text-align:right;">
									<?php echo DigiComSiteHelperPrice::format_price($tax['promo'], $tax['currency'], true, $configs); ?>
								</li>
								<?php endif; ?>

								<li class="digi_cart_total_price" id="cart_total" style="font-weight: bold;font-size: 18px;text-align:right;">
									<?php echo DigiComSiteHelperPrice::format_price($tax['taxed'], $tax['currency'], true, $configs); ?>
								</li>
							</ul>
						</td>
					</tr>
				</table>


				<div id="digicomcartcontinue" class="row-fluid continue-shopping">
					<div class="span8" style="margin-bottom:10px;">
						<?php if($configs->get('askterms',0) == '1' && ($configs->get('termsid') > 0)):?>
							<div class="accept-terms">
								<input type="checkbox" name="agreeterms" id="agreeterms" style="margin-top: 0;"/><?php
								$db = JFactory::getDBO();
								$sql = "select `title`, `alias`, `catid`, `introtext`
												from #__content
												where id=".intval($configs->get('termsid'));
								$db->setQuery($sql);
								$db->query();
								$result = $db->loadAssocList();
								$terms_title = $result["0"]["title"];
								$terms_content = $result["0"]["introtext"];
								$alias = $result["0"]["alias"];
								$catid = $result["0"]["catid"]; ?>
								<a href="javascript:;" onclick="jQuery('#termsShowModal').modal('show');"><?php echo JText::_("COM_DIGICOM_CART_AGREE_TERMS"); ?></a>
							</div>
						<?php endif;?>
					</div>
					<div class="span4" style="margin-bottom: 10px;">
						<p><strong><?php echo JText::_('COM_DIGICOM_PAYMENT_METHOD'); ?></strong></p>
						<?php
						$onclick = "console.log(jQuery('#processor').val());";
						$onclick .= "if(jQuery('#processor').val() === null){ ShowPaymentAlert(); return false; }";
						$onclick.= "jQuery('#returnpage').val('checkout');";

						if($configs->get('askterms',0) == '1')
						{
							$onclick.= "if(ShowTermsAlert()) {" . $onclick . " jQuery('#cart_form').submit(); }else{ return false; }";
						}
						else
						{
							$onclick.= "jQuery('#cart_form').submit();";
						}

						?>

						<?php echo DigiComSiteHelperDigicom::getPaymentPlugins($configs); ?>

						<div id="html-container"></div>
						<button type="button" class="btn btn-warning" style="float:right;margin-top:10px;" onclick="<?php echo $onclick; ?> "><?php echo JText::_('COM_DIGICOM_CHECKOUT');?> <i class="ico-ok-sign"></i></button>
					</div>
				</div>


				<input name="view" type="hidden" id="view" value="cart">
				<input name="task" type="hidden" id="task" value="cart.checkout">
				<input name="returnpage" type="hidden" value="">

			</form>
		</div>

		<?php
			echo JHtml::_(
				'bootstrap.renderModal',
				'paymentAlertModal',
				array(
					'title' => JText::_("COM_DIGICOM_WARNING"),
					'height' => '400px',
					'width' => '1280',
					'footer' => '<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>'
				),
				JText::_("COM_DIGICOM_CART_PAYMENT_METHOD_REQUIRED_NOTICE")
			);

			if($configs->get('askterms',0) == '1' && ($configs->get('termsid',0) > 0)):

				echo JHtml::_(
					'bootstrap.renderModal',
					'termsAlertModal',
					array(
						'title' => JText::_("COM_DIGICOM_WARNING"),
						'height' => '400px',
						'width' => '1280',
						'footer' => '<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>'
					),
					JText::_("COM_DIGICOM_CART_ACCEPT_TERMS_CONDITIONS_REQUIRED_NOTICE")
				);

				echo JHtml::_(
					'bootstrap.renderModal',
					'termsShowModal',
					array(
						'title' => $terms_title,
						'height' => 'auto',
						'width' => '1280',
						'footer' => '<button class="action-agree btn btn-success" data-dismiss="modal" aria-hidden="true">' . JText::_("COM_DIGICOM_CART_AGREE_TERMS_BUTTON") . '</button> <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>'

					),
					$terms_content
				);
			endif;
			?>

		<script>
			jQuery('.action-agree').click(function() {
			    jQuery('input[name="agreeterms"]').attr('checked', 'checked');
			});

			<?php
			$agreeterms = JFactory::getApplication()->input->get("agreeterms", "");
			if ($agreeterms != '')
			{
				echo 'jQuery("#agreeterms").attr("checked","checked");';
			}

			if ($processor != '')
			{
				echo 'jQuery("#processor").val("' . $processor . '");';
			}
			?>
			function ShowTermsAlert()
			{
				if (document.cart_form.agreeterms.checked != true)
				{
					jQuery('#termsAlertModal').modal('show');
				}
				else
				{
					return true;
				}
			}

			function ShowPaymentAlert()
			{

				jQuery('#paymentAlertModal').modal('show');
			}

			if(jQuery(window).width() > jQuery("#digicomcarttable").width() && jQuery(window).width() < 550)
			{
				jQuery(".digicom table select").css("width", (jQuery("#digicomcarttable").width()-30)+"px");
			}
		</script>
<?php endif; ?>
	<?php echo DigiComSiteHelperDigicom::powered_by(); ?>
</div>
