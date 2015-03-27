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

$invisible = 'style="display:none;"';
$k = 0;
$n = count ($this->order->products);
$configs = $this->configs;
$order = $this->order;
$user = $this->customer->_customer;
global $Itemid;

if ($this->order->id < 1):

	echo JText::_('DSEMPTYORDER');
?>

	<form action="<?php echo JRoute::_("index.php?option=com_digicom&controller=orders&task=list"."&Itemid=".$Itemid); ?>" name="adminForm" method="post">
	  	<input type="hidden" name="option" value="com_digicom" />
		<input type="submit" value="<?php echo JText::_("DSVIEWORDERS");?>" />
	</form>

<?php
	else:
?>

<div id="digicom">
	<div class="digi-view-order">	
		<div class="container">
			<div class="row-fluid">
				<div class="span12">
					
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
									<b><?php echo trim($configs->get('store_name','DigiCom Store')) != "" ? $configs->get('store_name','DigiCom Store') : ""; ?></b>
									<br />								
									<?php if(trim($configs->get('address')) != "") {
										echo $configs->get('address','');
										} ?>
									<br />
									<?php if(!empty($configs->city)) : ?>
									<?php echo $configs->city;?>,&nbsp;
									<?php endif; ?>
									<?php if(trim($configs->get('state','')) != ""){
										echo $configs->get('state','');
									} ?>,&nbsp;
									<?php if(!empty($configs->zip)) : ?>
									<?php echo $configs->zip;?>,&nbsp;
									<?php endif; ?>
									<?php if(trim($configs->get('country','')) != ""){ 
										echo $configs->get('country','');
									} ?>
									<br />																
									<?php echo trim($configs->get('phone')) != "" ? JText::_("PHONE").":".$configs->get('phone') : ""; ?>
									<br />
									<?php echo trim($configs->get('fax')) != "" ? JText::_("FAX").":".$configs->get('fax') : ""; ?>
									<br />
									<?php echo trim($configs->get('store_url','') != "") ? "".$configs->get('store_url','') : ""; ?>
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
									<?php echo JText::_("COM_DIGICOM_MY_ORDERS")." #".$order->id; ?>
									<br />
									<?php echo JText::_("JDATE")." ".date( $configs->get('time_format','d-m-Y'), $order->order_date);?>
								</td>

								<td style="font-weight:normal" align="right">
									
								</td>
							</tr>

							<!-- <tr>
							
								<td align="left">
									
									<table>
										
										<tr>
											<td style="border: 0;">
												<?php echo JText::_('DSADDRESS');?>:</td><td> <?php ?>
											</td>
										</tr>
										
										<tr>
											<td style="border: 0;"></td>
											<td style="border: 0;">
												
												<?php ?>
											</td>
										</tr>
										<?php
										
							
										
										?>
										<tr>
											<td style="border: 0;"></td>
											<td style="border: 0;">
												
											</td>
										</tr>
										<?php
									
										?>
									</table>
									
								</td>
							
								<td></td>
							</tr> -->

		

							<!-- <tr>
								<th align="left">
									<?php echo JText::_("DSBILLEDTO");?>
								</th>
							
								<th align="right">
							
								</th>
							</tr> -->
							
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
								$total = "0";

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

									<td <?php //if($configs->showoipurch == 0) echo $invisible;?>>
									 	<?php echo $prod->name;?>
									</td>

									<td <?php //if($configs->showoipurch == 0) echo $invisible;?>>
									 	<?php echo ucfirst( $prod->package_type ); ?>
									</td>

									<td style="text-align: right;">
										<?php echo $prod->price;?>
									</td>
								</tr>
							<?php
									$k = 1 - $k;
								endfor;

								$colspan=5;
								$colspan--;
							?>	
							</tbody>
						</table>

						<hr>

						<table class="table">
							<tbody>

								<tr style="">
									<td style="font-weight:bold;text-align: right;" width="70%"><?php echo JText::_("COM_DIGICOM_SUBTOTAL");?></td>
									<td style="text-align: right;"><span style="white-space:nowrap;font-weight: bold;"><?php echo DigiComSiteHelperDigiCom::format_price($order->amount, $prod->currency, true, $configs);?></span></td>
								</tr>

								<?php
									if($order->promocodediscount > 0){
										$total = $total - $order->promocodediscount;
								?>
								<tr>
									<td style="font-weight:bold;text-align: right;" width="70%"><?php echo JText::_("COM_DIGICOM_DISCOUNT");?></td>
									<td style="text-align: right;"><span style="white-space:nowrap;font-weight: bold;"><?php echo DigiComSiteHelperDigiCom::format_price($order->promocodediscount, $prod->currency, true, $configs);?></span></td>
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
					<?php

					endif;

					echo DigiComSiteHelperDigiCom::powered_by(); ?>


				</div><!-- End of span12 -->
				
			</div><!-- End of row-fluid -->
		</div><!-- End of container -->
	</div> <!-- End of Digi OrderView -->
</div><!-- End of Digicom -->