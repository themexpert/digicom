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
										<fieldset id="person" class="radio btn-group btn-group-yesno">
											<label for="person1" name="person" class="btn<?php echo (($cust->person != 0 or $cust->person == 1 or $cust->person == '') ? " active btn-success" : ""); ?>">
												<input type="radio" id="person1" value="1" <?php echo (($cust->person != 0 or $cust->person == 1 or $cust->person == '') ? "checked='checked'" : ""); ?>>
												<?php echo JText::_( "VIEWCUSTOMERIMPERSON" ); ?>
											</label>
											
											<label for="person0" name="person" class="btn<?php echo (($cust->person == 0) ? " active btn-success" : ""); ?>">
												<input type="radio" id="person0" value="0" <?php echo (($cust->person == 0) ? "checked='checked'" : ""); ?>>
												<?php echo JText::_( "VIEWCUSTOMERIMCOMPANY" ); ?>
											</label>											
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
										<textarea><?php echo $cust->address; ?></textarea>
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
	<input type="hidden" name="controller" value="Customers" />
	<input type="hidden" name="keyword" value="<?php echo $this->keyword; ?>" />
</form>