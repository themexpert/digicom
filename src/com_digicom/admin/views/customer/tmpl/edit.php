<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$cust = $this->cust;
$user = $this->user;
$configs = $this->configs;
$app = JFactory::getApplication();
$input = $app->input;

$document = JFactory::getDocument();
$input->set('layout', 'edit');
$total_orders = count($cust->orders);
?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div id="digicom" class="dc digicom">
		<div class="tx-sidebar"><?php echo $this->sidebar; ?></div>
		<div class="tx-main">
			<div class="page-header">
				<h1><?php echo JText::_('COM_DIGICOM_CUSTOMER_TAB_HEADING_CUSTOMER_INFO', true); ?></h1>
				<p>Update customer billing info and see their orders here</p>
			</div> <!-- .page-header -->
			<div class="page-content">
				<div class="row">
					<div class="col-md-6">
						<div class="panel panel-default">
							<div class="panel-heading"><?php echo JText::_( 'COM_DIGICOM_CUSTOMER_TITLE_CUSTOMER_DETAILS' ); ?></div>
							<table class="table table-striped table-hover">
								<tbody>
									<tr>
										<td><strong><?php echo JText::_( 'JGRID_HEADING_ID' ); ?></strong></td>
										<td><?php echo $cust->id; ?></td>
									</tr>
									<tr>
										<td><strong><?php echo JText::_( 'COM_DIGICOM_USER_NAME' ); ?></strong></td>
										<td><?php echo ($user["username"] ? $user["username"] : $cust->email ); ?></td>
									</tr>
									<tr>
										<td><strong><?php echo JText::_( "COM_DIGICOM_FULL_NAME" ); ?></strong></td>
										<td><?php echo $cust->name ?></td>
									</tr>
									<tr>
										<td><strong><?php echo JText::_( "COM_DIGICOM_CUSTOMER_REGISTRATION_DATE" ); ?></strong></td>
										<td><?php echo $cust->registered; ?></td>
									</tr>
									<tr>
										<td><strong><?php echo JText::_( 'COM_DIGICOM_EMAIL' );?></strong></td>
										<td><?php echo $cust->email; ?></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="col-md-6">
						<div class="panel panel-default">
							<div class="panel-heading"><?php echo JText::_( "COM_DIGICOM_CUSTOMER_TITLE_CUSTOMER_BILLING_ADDRESS" ); ?></div>
							<div class="panel-body">
								<div class="form-group clearfix">
									<label class="col-sm-4 control-label"><?php echo JText::_( "COM_DIGICOM_CUSTOMER_TYPE" ); ?></label>
									<div class="col-sm-8">
										<fieldset id="customer_person_select" class="radio btn-group" style="margin: 0;">
											<input type="radio" class="jform_customer_person_select" name="person" id="customer_person_select_1" value="1" <?php echo (($cust->person == '1' || $cust->person === null)?"checked='checked'":"");?> />
											<label class="btn" for="customer_person_select_1"><?php echo JText::_('COM_DIGICOM_CUSTOMER_TYPE_PERSON'); ?></label>
											<input type="radio" class="jform_customer_person_select" name="person" id="customer_person_select_0" value="0" <?php echo (($cust->person == '0') ? "checked='checked'" : "");?> />
											<label class="btn" for="customer_person_select_0"><?php echo JText::_('COM_DIGICOM_CUSTOMER_TYPE_COMPANY'); ?></label>
										</fieldset>
									</div>
								</div> <!-- .form-group -->
								<div class="form-group clearfix">
									<label class="col-sm-4 control-label"><?php echo JText::_( "COM_DIGICOM_CUSTOMER_TYPE_COMPANY" ); ?></label>
									<div class="col-sm-8">
										<input name="company" type="text" id="company" size="30" value="<?php echo $cust->company; ?>">
									</div>
								</div> <!-- .form-group -->
								<div class="form-group clearfix">
									<label class="col-sm-4 control-label"><?php echo JText::_( "COM_DIGICOM_CUSTOMER_TAX_NUMBER" ); ?></label>
									<div class="col-sm-8">
										<input name="taxnum" type="text" id="taxnum" size="30" value="<?php echo $cust->taxnum; ?>">
									</div>
								</div> <!-- .form-group -->
								<div class="form-group clearfix">
									<label class="col-sm-4 control-label"><?php echo Jtext::_( "COM_DIGICOM_CUSTOMER_ADDRESS" ); ?><span class="error">*</span></label>
									<div class="col-sm-8">
										<textarea name="address"><?php echo $cust->address; ?></textarea>
									</div>
								</div> <!-- .form-group -->
								<?php echo $this->form->renderField('country'); ?>
								<?php echo $this->form->renderField('state'); ?>
								<div class="form-group clearfix">
									<label class="col-sm-4 control-label"><?php echo JText::_( "COM_DIGICOM_CUSTOMER_CITY" ); ?></label>
									<div class="col-sm-8">
										<input name="city" type="text" id="city" size="30" value="<?php echo $cust->city; ?>">
									</div>
								</div> <!-- .form-group -->
							</div> <!-- .panel-body -->
						</div> <!-- .panel -->
					</div> <!-- .col-md-6 -->
				</div> <!-- .row -->
				<div class="row">
					<div class="col-md-12">
						<div class="panel panel-default">
							<div class="panel-heading"><?php echo JText::_('COM_DIGICOM_CUSTOMER_TAB_HEADING_CUSTOMER_ORDERS', true); ?></div>
							<?php if($total_orders > 0): ?>
							<table class="adminlist table table-striped table-bordered">
								<thead>
									<tr>
										<th width="20"><?php echo JText::_( 'JGRID_HEADING_ID' ); ?></th>
										<th><?php echo JText::_( 'COM_DIGICOM_DATE' ); ?></th>
										<th><?php echo JText::_( 'COM_DIGICOM_PRICE' ); ?></th>
										<th><?php echo JText::_( 'COM_DIGICOM_AMOUNT_PAID' ); ?></th>
										<th><?php echo JText::_( 'JSTATUS' ); ?></th>
										<th><?php echo JText::_( 'COM_DIGICOM_CUSTOMER_PAYMENT_METHOD' ); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php
									$z = 0;
									$k = 0;
									for ( $i = 0; $i < $total_orders; $i++ ):
										++$z;
										$order =  $cust->orders[$i];

										$id = $order->id;
										$olink = JRoute::_( "index.php?option=com_digicom&task=order.edit&id=" . $id );
										$order->published = 1;
										$published = JHTML::_( 'grid.published', $order, $i );

									?>
										<tr class="row<?php echo $k; ?>">
											<td align="center">
												<a href="<?php echo $olink; ?>" target="_blank">#<?php echo $id; ?></a>
											</td>
											<td align="center">
												<?php echo $order->order_date; ?>
											</td>
											<td align="center">
												<?php
													echo DigiComHelperDigiCom::format_price($order->amount, $configs->get('currency','USD'), true, $configs);
												?>
											</td>
											<td align="center">
												<?php

													if ($order->amount_paid == "-1") $order->amount_paid = $order->amount;
													$refunds = DigiComHelperDigiCom::getRefunds($order->id);
													$chargebacks = DigiComHelperDigiCom::getChargebacks($order->id);
													$order->amount_paid = $order->amount_paid - $refunds - $chargebacks;
													echo DigiComHelperDigiCom::format_price($order->amount_paid, $configs->get('currency','USD'), true, $configs);

												?>
											</td>
											<td align="center">
												<?php
													$a_style = "";
													if($order->status == "Pending"){
														$a_style = ' label-warning';
													}else{
														$a_style = ' label-success';
													}
												?>
												<span class="label<?php echo $a_style; ?>"><?php echo (trim( $order->status ) != "in_progres" ? $order->status : "Active"); ?></span>
											</td>
											<td align="center">
												<?php echo $order->processor; ?>
											</td>

										</tr>
										<?php
										$k = 1 - $k;
									endfor;
										?>
								</tbody>
							</table>
							<?php else: ?>
								<div class="panel-body">
									<div class="well well-lg text-center muted">
										<?php echo  JText::_('COM_DIGICOM_ORDERS_NOTICE_NO_ORDER_FOUND'); ?>
									</div>
								</div>
							<?php endif; ?>
						</div> <!-- .col-md-12 -->
					</div> <!-- .row -->
				</div>
			</div> <!-- .page-content -->
		</div>
	</div>

	<input type="hidden" name="option" value="com_digicom" />
	<input type="hidden" name="id" value="<?php echo $cust->id; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="customer" />
	<?php echo JHtml::_('form.token'); ?>
</form>
