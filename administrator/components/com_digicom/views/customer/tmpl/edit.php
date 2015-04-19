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

$cust = $this->cust;
$user = $this->user;
$configs = $this->configs;
$app = JFactory::getApplication();
$input = $app->input;

$document = JFactory::getDocument();
$input->set('layout', 'dgform');

?>

<script language="javascript" type="text/javascript">
	<!--
	var request_processed = 0;
	function submitbutton(pressbutton) {
		submitform( pressbutton );
	}

	function populateShipping () {
		var names = Array ('address','zipcode', 'city');
		var i;
		for (i = 0; i < names.length; i++) {
			val = document.getElementById(names[i]).value;
			document.getElementById('ship' + names[i]).value = val;
		}
		idx = document.getElementById('country').selectedIndex;
		document.getElementById('shipcountry').selectedIndex = idx;

		changeProvince_ship();
		request_processed = 1;
	}

	-->
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
<?php if (!empty( $this->sidebar)) : ?>
		<div id="j-sidebar-container" class="">
			<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="">
	<?php else : ?>
		<div id="j-main-container" class="">
	<?php endif;?>
			<div class="row-fluid">
				<div class="span12">
					<?php echo JHtml::_('bootstrap.startTabSet', 'digicomTab', array('active' => 'details')); ?>

					<?php echo JHtml::_('bootstrap.addTab', 'digicomTab', 'details', JText::_('COM_DIGICOM_CUSTOMER_TAB_HEADING_CUSTOMER_INFO', true)); ?>
				
					<div class="form-horizontal">
						<div class="row-fluid">
							<div class="span6">
								<h3><?php echo JText::_( 'COM_DIGICOM_CUSTOMER_TITLE_CUSTOMER_DETAILS' ); ?></h3>
								<div class="control-group">
									<label class="control-label"><?php echo JText::_( 'JGRID_HEADING_ID' ); ?></label>
									<div class="controls">
										<?php echo $cust->id; ?>
									</div>
								</div>
								<div class="control-group">
									<label class="control-label"><?php echo JText::_( 'COM_DIGICOM_USER_NAME' ); ?></label>
									<div class="controls">
										<?php echo ($user["username"] ? $user["username"] : $cust->email ); ?>
									</div>
								</div>

								<div class="control-group">
									<label class="control-label"><?php echo JText::_( "COM_DIGICOM_FULL_NAME" ); ?></label>
									<div class="controls">
										<?php echo $cust->firstname . ' ' . $cust->lastname ?>
									</div>
								</div>

								<div class="control-group">
									<label class="control-label"><?php echo JText::_( "COM_DIGICOM_CUSTOMER_REGISTRATION_DATE" ); ?></label>
									<div class="controls">
										<?php echo $cust->registered; ?>
									</div>
								</div>

								

								<div class="control-group">
									<label class="control-label">
										<?php echo JText::_( 'COM_DIGICOM_EMAIL' );?>
									</label>
									<div class="controls">
										<?php echo $cust->email; ?>
									</div>
								</div>

								
							</div>

							<div class="span6">

								<h3><?php echo JText::_( "COM_DIGICOM_CUSTOMER_TITLE_CUSTOMER_BILLING_ADDRESS" ); ?></h3>
								<div class="control-group">
									<label class="control-label">
										<?php echo JText::_( "COM_DIGICOM_CUSTOMER_TYPE" ); ?>
									</label>
									<div class="controls">	
										<fieldset id="customer_person_select" class="radio btn-group">
											<input type="radio" class="jform_customer_person_select" name="person" id="customer_person_select_1" value="1" <?php echo (($cust->person == '1' || $cust->person === null)?"checked='checked'":"");?> />
											<label class="btn" for="customer_person_select_1"><?php echo JText::_('COM_DIGICOM_CUSTOMER_TYPE_PERSON'); ?></label>
											<input type="radio" class="jform_customer_person_select" name="person" id="customer_person_select_0" value="0" <?php echo (($cust->person == '0') ? "checked='checked'" : "");?> />
											<label class="btn" for="customer_person_select_0"><?php echo JText::_('COM_DIGICOM_CUSTOMER_TYPE_COMPANY'); ?></label>
										</fieldset>
									</div>
									
								</div>
								
								<div class="control-group">
									<label class="control-label"><?php echo JText::_( "COM_DIGICOM_CUSTOMER_TYPE_COMPANY" ); ?><b></b></label>
									<div class="controls">
										<input name="company" type="text" id="company" size="30" value="<?php echo $cust->company; ?>">
									</div>
								</div>

								<div class="control-group">
									<label for="" class="control-label"><?php echo Jtext::_( "COM_DIGICOM_CUSTOMER_ADDRESS" ); ?><span class="error">*</span></label>
									<div class="controls">
										<textarea name="address"><?php echo $cust->address; ?></textarea>
									</div>
								</div>

								<div class="control-group">
									<label for="" class="control-label"><?php echo JText::_( "COM_DIGICOM_CUSTOMER_COUNTRY" ); ?></label>
									<div class="controls">
										<input name="country" type="text" id="country" size="30" value="<?php echo $cust->country; ?>">
									</div>
								</div>

								<div class="control-group">
									<label for="" class="control-label"><?php echo JText::_( "COM_DIGICOM_CUSTOMER_STATE" ); ?></label>
									<div class="controls">
										<input name="state" type="text" id="state" size="30" value="<?php echo $cust->state; ?>">
									</div>
								</div>
								
								<div class="control-group">
									<label for="" class="control-label"><?php echo JText::_( "COM_DIGICOM_CUSTOMER_CITY" ); ?></label>
									<div class="controls">
										<input name="city" type="text" id="city" size="30" value="<?php echo $cust->city; ?>">
									</div>
								</div>
								
								<div class="control-group">
									<label for="" class="control-label"><?php echo JText::_( "COM_DIGICOM_CUSTOMER_TAX_NUMBER" ); ?></label>
									<div class="controls">
										<input name="taxnum" type="text" id="taxnum" size="30" value="<?php echo $cust->taxnum; ?>">
									</div>
								</div>

							</div>
						</div>
						
					</div>

					<?php echo JHtml::_('bootstrap.endTab'); ?>
					<?php echo JHtml::_('bootstrap.addTab', 'digicomTab', 'order_details', JText::_('COM_DIGICOM_CUSTOMER_TAB_HEADING_CUSTOMER_ORDERS', true)); ?>

						<table class="adminlist table table-striped">
							<thead>
								<tr>
									<th width="20">
										<?php echo JText::_( 'JGRID_HEADING_ID' ); ?>
									</th>

									<th>
										<?php echo JText::_( 'COM_DIGICOM_DATE' ); ?>
									</th>
									<th>
										<?php echo JText::_( 'COM_DIGICOM_PRICE' ); ?>
									</th>
									<th>
										<?php echo JText::_( 'COM_DIGICOM_AMOUNT_PAID' ); ?>
									</th>
									<th>
										<?php echo JText::_( 'JSTATUS' ); ?>
									</th>
									<th>
										<?php echo JText::_( 'COM_DIGICOM_CUSTOMER_PAYMENT_METHOD' ); ?>
									</th>
								</tr>
							</thead>

							<tbody>
								<?php 
								$n = count($cust->orders);
								if($n > 0):  
								?>
								<?php
								$z = 0;
								$k = 0;
								for ( $i = 0; $i < $n; $i++ ):
									++$z;
									$order =  $cust->orders[$i];

									$id = $order->id;
									$link = JRoute::_( "index.php?option=com_digicom&controller=licenses&task=list&oid[]=" . $id );
									$olink = JRoute::_( "index.php?option=com_digicom&controller=orders&task=show&cid[]=" . $id );
									$order->published = 1;
									$published = JHTML::_( 'grid.published', $order, $i );

								?>
									<tr class="row<?php echo $k; ?>">
										<td align="center">
											<a href="<?php echo $olink; ?>"><?php echo $id; ?></a>
										</td>
										<td align="center">
											<?php echo date( $configs->get('time_format','DD-MM-YYYY'), $order->order_date ); ?>
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
								<?php else: ?>
									<tr>
										<td colspan="9">
											<?php echo  JText::_('COM_DIGICOM_NO_ORDER_FOUND'); ?>
										</td>
									</tr>
								<?php endif; ?>
							</tbody>
							
						</table>

					<?php echo JHtml::_('bootstrap.endTab'); ?>
					<?php echo JHtml::_('bootstrap.endTabSet'); ?>
				</div>
			</div>


	</div>

	<input type="hidden" name="option" value="com_digicom" />
	<input type="hidden" name="id" value="<?php echo $cust->id; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="customer" />
	<input type="hidden" name="keyword" value="<?php echo $this->keyword; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>

<div class="dg-footer">
	<?php echo JText::_('COM_DIGICOM_CREDITS'); ?>
</div>
