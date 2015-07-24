<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

$invisible = 'style="display:none;"';
$k = 0;
$n = count ($this->order->products);
$configs = $this->configs;
$order = $this->order;
$user = $this->customer->_customer;
?>
<div id="digicom">
	<div class="digi-view-order">	
		<div class="container">
			<div class="row-fluid">
				<div class="span12">
					<?php if ($this->order->id < 1): ?>
						<div class="alert alert-danger"><?php echo JText::_('COM_DIGICOM_ORDERS_NO_ORDER_FOUND_NOTICE'); ?></div>
					<?php else: ?>
					
					<form action="index.php" name="adminForm" method="post" style="padding-left: 10px; padding-right:10px; padding-top:100px;">
						<input id="print_button" class="btn" style="float:right;margin-bottom: 10px;" type="button" value="<?php echo JText::_("COM_DIGICOM_PRINT");?>" onclick="document.getElementById('print_button').style.display='none'; javascript:window.print(); return false;" />
						
						<table  class="table" width="100%"  border="0" cellpadding="3" cellspacing="0" bordercolor="#cccccc" style="border-collapse: collapse">
							<tr>
								<td align="left" valign="top">
									<?php
										$store_logo = $configs->get('store_logo','');
										if(trim($store_logo) != ""){
									?>
											<img src="<?php echo JRoute::_($store_logo); ?>" alt="store_logo" border="0">
									<?php
										}
									?>
								</td>
								<td align="right" valign="top" style="text-align: right;font-size: 13px;">
									<address>
										<strong>
											<?php echo trim($configs->get('store_name','DigiCom Store')) != "" ? $configs->get('store_name','DigiCom Store') : ""; ?>
										</strong>
										<br />								
										<?php if(trim($configs->get('address')) != "") { echo $configs->get('address','') . '<br />';} ?>
									
										<?php if(trim($configs->get('store_info')) != "") { echo $configs->get('store_info','') . '<br />'; } ?>
										
										<?php echo trim($configs->get('phone')) != "" ? JText::_("PHONE").":".$configs->get('phone') . '<br />' : ""; ?>
									
										<a href="<?php echo JUri::root(); ?>" title="?php echo $configs->get('store_name','DigiCom Store'); ?>"><?php echo $configs->get('store_name','DigiCom Store'); ?></a>
									</address>
								</td>
							</tr>

							<tr>
								<td align="left">
									<?php if(!empty($user->firstname)) : ?>
									<strong><?php echo $user->firstname?>&nbsp;<?php echo $user->lastname;?></strong><br />
									<?php endif; ?>
									<?php if(!empty($user->address)) : ?>
										<?php echo $user->address?><br />
									<?php endif; ?>
									<?php if(!empty($user->city)) : ?>
									<?php echo $user->city?>,&nbsp;<?php echo $user->state?> <br />
									<?php endif; ?>
									<?php if(!empty($user->zipcode)) : ?>
									<?php echo $user->zipcode;?>,&nbsp;<?php echo $user->country?> <br />
									<?php endif; ?>
								</td>

								<td>

								</td>
							</tr>

							<tr>
								<td align="left">
									<h3 style="text-transform: uppercase;font-size: 30px;"><?php echo JText::_('COM_DIGICOM_ORDER_PRINT_VIEW_INVOICE');?></h3>
									<div>
										<span><?php echo JText::_("COM_DIGICOM_MY_ORDERS");?><span>
										<code><?php echo '#' . $order->id; ?></code>
									</div>
									<div>
										<span><?php echo JText::_("JDATE");?><span>
										<code><?php echo date( $configs->get('time_format','d-m-Y'), $order->order_date); ?></code>
									</div>
									<div>
										<span><?php echo JText::_("COM_DIGICOM_PAYMENT_METHOD");?><span>
										<code><?php echo $order->processor; ?></code>
									</div>
									<div>
										<span><?php echo JText::_("JSTATUS");?><span>
										<code><?php echo ( strtolower($order->status) === 'active' ? JText::_('COM_DIGICOM_PAYMENT_PAID') : $order->status); ?></code>
									</div>

									
								</td>

								<td style="font-weight:normal" align="right">
									
								</td>
							</tr>
						</table>
						
						<table class="table" style="margin-bottom: 40px;">
							<thead>
								<tr style="border-bottom: 3px solid #666;">
									<th>#</th>
									<th class="sectiontableheader"  <?php //if ($configs->showoipurch == 0) echo $invisible;?> >
										<?php echo JText::_('COM_DIGICOM_PRODUCT');?>
									</th>
									<th class="sectiontableheader"  <?php //if ($configs->showoipurch == 0) echo $invisible;?> >
										<?php echo JText::_('COM_DIGICOM_TYPE'); ?>
									</th>
									<th style="text-align: right;">
										<?php echo JText::_('COM_DIGICOM_PRODUCT_PRICE');?>
									</th>
								</tr>

							</thead>

							<tbody>

							<?php
								$total = 0;

								for ($i = 0; $i < $n; $i++):
									$prod = $order->products[$i];
									$id = $order->id;
									if (!isset($prod->currency)) $prod->currency = $configs->get('currency','USD');
							?>
								<tr class="row<?php echo $k;?> sectiontableentry<?php echo ($i%2 + 1);?>">
									<td>
										<?php 
											echo $i+1; 
										?>
									</td>

									<td>
									 	<?php echo $prod->name;?>
									 	<?php if ($configs->get('show_validity',1) == 1) : ?>
										<div class="muted">
											<small><?php echo JText::_('COM_DIGICOM_PRODUCT_VALIDITY'); ?> : <?php echo DigiComSiteHelperPrice::getProductValidityPeriod($order); ?></small>
										</div>
										<?php endif; ?>
									</td>

									<td>
									 	<?php echo ucfirst( $prod->package_type ); ?>
									</td>

									<td style="text-align: right;">
										<?php echo DigiComSiteHelperDigiCom::format_price($prod->price, $prod->currency, true, $configs);?>
									</td>
								</tr>
							<?php
									$total += $prod->price;
									$k = 1 - $k;
								endfor;
							?>	
							</tbody>
						</table>

						<hr>

						<table class="table">
							<tbody>

								<tr style="">
									<td style="font-weight:bold;text-align: right;" width="70%"><?php echo JText::_("COM_DIGICOM_SUBTOTAL");?></td>
									<td style="text-align: right;"><span style="white-space:nowrap;font-weight: bold;"><?php echo DigiComSiteHelperDigiCom::format_price($total, $prod->currency, true, $configs);?></span></td>
								</tr>

								<?php
									if($order->discount > 0){
										$total = $total - $order->discount;
								?>
								<tr>
									<td style="font-weight:bold;text-align: right;" width="70%"><?php echo JText::sprintf("COM_DIGICOM_DISCOUNT",$order->promocode);?></td>
									<td style="text-align: right;"><span style="white-space:nowrap;font-weight: bold;"><?php echo DigiComSiteHelperDigiCom::format_price($order->discount, $prod->currency, true, $configs);?></span></td>
								</tr>
								<?php
									}
								?>

								<tr>
							   		<td style="font-weight:bold;text-align: right;" width="70%"><?php echo JText::_("COM_DIGICOM_TOTAL");?></td>
									<?php
										if($order->amount_paid != "" && $order->amount_paid != "-1" && $order->amount_paid != $total){
											$total = $order->amount_paid;
										}
									?>
									<td style="text-align: right;"><span style="white-space:nowrap;font-weight: bold;font-size: 18px;"><?php echo DigiComSiteHelperDigiCom::format_price($total, $prod->currency, true, $configs);?></span></td>
								</tr>

							</tbody>
						</table>

						<input type="hidden" name="option" value="com_digicom" />
						<input type="hidden" name="task" value="" />
						<input type="hidden" name="boxchecked" value="0" />
						<input type="hidden" name="controller" value="Orders" />
					</form>
					<?php endif; ?>
					<?php echo DigiComSiteHelperDigiCom::powered_by(); ?>
				</div><!-- End of span12 -->
				
			</div><!-- End of row-fluid -->
		</div><!-- End of container -->
	</div> <!-- End of Digi OrderView -->
</div><!-- End of Digicom -->