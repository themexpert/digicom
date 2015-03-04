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
JHtml::_('formbehavior.chosen', 'select');

$cust = $this->cust;
$user = $this->user["0"];
$configs = $this->configs;
$app = JFactory::getApplication();
$input = $app->input;

$document = JFactory::getDocument();
//$document->addStyleSheet("components/com_digicom/assets/css/digicom.css");
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

					<?php echo JHtml::_('bootstrap.addTab', 'digicomTab', 'details', JText::_('COM_DIGICOM_CUSTOMERS_DETAILS', true)); ?>
				
					<div class="form-horizontal">
						<div class="row-fluid">
							<div class="span6">
								<h3><?php echo JText::_( 'CUSTOMER_DETAILS' ); ?></h3>
								<div class="control-group">
									<label class="control-label"><?php echo JText::_( 'VIEWCUSTOMERID' ); ?></label>
									<div class="controls">
										<?php echo $cust->id; ?>
									</div>
								</div>
								<div class="control-group">
									<label class="control-label"><?php echo JText::_( 'VIEWCUSTOMERUSERNAME' ); ?></label>
									<div class="controls">
										<?php echo $user["username"]; ?>
									</div>
								</div>

								<div class="control-group">
									<label class="control-label"><?php echo JText::_( "VIEWCUSTOMERNAME" ); ?></label>
									<div class="controls">
										<?php echo $cust->firstname . ' ' . $cust->lastname ?>
									</div>
								</div>

								<div class="control-group">
									<label class="control-label"><?php echo JText::_( "VIEWCUSTOMERREGISTERED" ); ?></label>
									<div class="controls">
										<?php echo $cust->registerDate; ?>
									</div>
								</div>

								

								<div class="control-group">
									<label class="control-label">
										<?php echo JText::_( 'VIEWCUSTOMEREMAIL' );?>
									</label>
									<div class="controls">
										<?php echo $user["email"]; ?>
									</div>
								</div>

								
							</div>

							<div class="span6">

								<h3><?php echo JText::_( "VIEWCUSTOMERBILLING" ); ?></h3>
								<div class="control-group">
									<label class="control-label">
										<?php echo JText::_( "VIEWCUSTOMERPOC" ); ?>
									</label>
									<div class="controls">	
										<fieldset id="customer_person_select" class="radio btn-group">
											<input type="radio" class="jform_customer_person_select" name="person" id="customer_person_select_1" value="1" <?php echo (($cust->person == '1' || $cust->person === null)?"checked='checked'":"");?> />
											<label class="btn" for="customer_person_select_1"><?php echo JText::_('VIEWCUSTOMERIMPERSON'); ?></label>
											<input type="radio" class="jform_customer_person_select" name="person" id="customer_person_select_0" value="0" <?php echo (($cust->person == '0') ? "checked='checked'" : "");?> />
											<label class="btn" for="customer_person_select_0"><?php echo JText::_('VIEWCUSTOMERIMCOMPANY'); ?></label>
										</fieldset>
									</div>
									
								</div>
								
								<div class="control-group">
									<label class="control-label"><?php echo JText::_( "VIEWCUSTOMERCOMPANY" ); ?><b></b></label>
									<div class="controls">
										<input name="company" type="text" id="company" size="30" value="<?php echo $cust->company; ?>">
									</div>
								</div>

								<div class="control-group">
									<label for="" class="control-label"><?php echo Jtext::_( "VIEWCONFIGADDRESS" ); ?><span class="error">*</span></label>
									<div class="controls">
										<textarea name="address"><?php echo $cust->address; ?></textarea>
									</div>
								</div>

								<div class="control-group">
									<label for="" class="control-label"><?php echo JText::_( "VIEWCUSTOMERCOUNTRY" ); ?></label>
									<div class="controls">
										<?php echo $this->lists['country_option']; ?>
									</div>
								</div>
								
								<div class="control-group">
									<label for="" class="control-label"><?php echo JText::_( "VIEWCONFIGTAXNUM" ); ?></label>
									<div class="controls">
										<input name="taxnum" type="text" id="taxnum" size="30" value="<?php echo $cust->taxnum; ?>">
									</div>
								</div>

							</div>
						</div>
						
					</div>

					<?php echo JHtml::_('bootstrap.endTab'); ?>
					<?php echo JHtml::_('bootstrap.addTab', 'digicomTab', 'order_details', JText::_('COM_DIGICOM_CUSTOMERS_ORDER_DETAILS', true)); ?>

						<?php 
						//show custommers order 
						//print_r($cust->orders);
						?>
						<table class="adminlist table table-striped">
							<thead>
								<tr>
									<th width="20">
										<?php echo JText::_( 'VIEWORDERSID' ); ?>
									</th>

									<th>
										<?php echo JText::_( 'VIEWORDERSDATE' ); ?>
									</th>
									<th>
										<?php echo JText::_( 'VIEWORDERSPRICE' ); ?>
									</th>
									<th>
										<?php echo JText::_( 'VIEWORDERSSTATUS' ); ?>
									</th>
									<th>
										<?php echo JText::_( 'VIEWORDERSPAYMETHOD' ); ?>
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
												
												if ($order->amount_paid == "-1") $order->amount_paid = $order->amount;
												//$refunds = DigiComAdminModelOrder::getRefunds($order->id);
												//$chargebacks = DigiComAdminModelOrder::getChargebacks($order->id);
												//$order->amount_paid = $order->amount_paid - $refunds - $chargebacks;
												$order->amount_paid = $order->amount_paid;
												echo DigiComAdminHelper::format_price($order->amount_paid, $configs->get('currency','USD'), true, $configs); 
												
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
	
		
			<script>
<?php
					sajax_show_javascript();


?>

				function changeProvince_cb(province_option) {
					//alert(province_option+'MYYYYYYYYYYYY');

					document.getElementById("province").innerHTML = province_option;
				}

				function changeProvince() {
					// get the folder name
					var country;
					country = document.getElementById('country').value;
					//alert(country);
					x_phpchangeProvince(country, 'main', changeProvince_cb);
				}
				var request_processed = 0;
				function changeProvince_cb_ship(province_option) {
					//alert(province_option+'MYYYYYYYYYYYY');

					document.getElementById("shipprovince").innerHTML = province_option;
					if (request_processed == 1) {
						idx = document.getElementById('sel_province').selectedIndex;
						document.getElementById('shipsel_province').selectedIndex = idx;
					}
					request_processed = 0;
				}

				function changeProvince_ship() {
					// get the folder name
					var country;
					country = document.getElementById('shipcountry').value;
					//alert(country);
					x_phpchangeProvince(country, 'ship', changeProvince_cb_ship);
				}
			</script>

	</div>

	<input type="hidden" name="images" value="" />
	<input type="hidden" name="option" value="com_digicom" />
	<input type="hidden" name="id" value="<?php echo $user["id"]; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="customers" />
	<input type="hidden" name="keyword" value="<?php echo $this->keyword; ?>" />
</form>