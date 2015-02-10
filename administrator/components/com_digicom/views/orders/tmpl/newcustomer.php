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

$cust = $this->cust;

$document = JFactory::getDocument();
$document->addStyleSheet("components/com_digicom/assets/css/digicom.css");

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
<script language="javascript" type="text/javascript">
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

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'Create a customer profile' ); ?></legend>
		<table class="admintable">
			<tr>
				<td width="50%"><?php echo JText::_( "VIEWCUSTOMERFIRSTNAME" ); ?><span class="error">*</span></td>
				<td><input name="firstname" type="text" id="firstname" size="30" value="<?php echo $cust->firstname ?>"><b>&nbsp;</b></td>
			</tr>
			<tr>
				<td><?php echo JText::_( "VIEWCUSTOMERLASTNAME" ); ?><span class="error">*</span></td>
				<td><input name="lastname" type="text" id="lastname" size="30" value="<?php echo $cust->lastname; ?>"><b>&nbsp;</b></td>
			</tr>
			<tr>
				<td><?php echo JText::_( "VIEWCUSTOMERCOMPANY" ); ?><b></b></td>
				<td><input name="company" type="text" id="company" size="30" value="<?php echo $cust->company; ?>"></td>
			</tr>
			<tr>
				<td><?php echo JText::_( 'VIEWCUSTOMEREMAIL' ); ?><span class="error">*</span></td>
				<td><input name="email" type="text" <?php if ( $cust->id ) { ?>disabled <?php } ?>
									   id="email" size="30" value="<?php echo $cust->email; ?>"><b>&nbsp;</b></td>
			</tr>

			<tr>
				<td colspan="2"><h3><?php echo JText::_( "Login information" ); ?></h3></td>
			</tr>
			<tr>
				<td><?php echo JText::_( 'VIEWCUSTOMERUSERNAME' ); ?><span class="error">*</span></td>
				<td><input name="username" <?php if ( $cust->id ) { ?> disabled <?php } ?> type="text" id="username" size="30" value="<?php echo $cust->username; ?>"><b>&nbsp;</b></td>
			</tr>
<?php			if ( !$cust->id ) { ?>
				<tr>
					<td ><?php echo JText::_( "VIEWCUSTOMERPASSWORD" ); ?><span class="error">*</span></td>
					<td><input name="password" type="password" id="password" size="30" ><b>&nbsp;</b></td>
				</tr>
				<tr>
					<td><?php echo JText::_( "VIEWCUSTOMERPASSWORDCONFIRM" ); ?><span class="error">*</span></td>
					<td><input name="password_confirm" type="password" id="password_confirm" size="30"><b>&nbsp;</b></td>
				</tr>
<?php			}					?>

			<tr>
				<td colspan="2"><h3><?php echo JText::_( "VIEWCUSTOMERBILLING" ); ?></h3></td>
			</tr>
			<tr>
				<td><?php echo Jtext::_( "VIEWCONFIGADDRESS" ); ?><span class="error">*</span></td>
				<td><input name="address" type="text" id="address" size="30" value="<?php echo $cust->address; ?>"><b>&nbsp;</b></td>
			</tr>
			<tr>
				<td><?php echo JText::_( "VIEWCUSTOMERCOUNTRY" ); ?><span class="error">*</span></td>
				<td><?php echo $this->lists['country_option']; ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_( "VIEWCUSTOMERSTATE" ); ?><span class="error">*</span></td>
				<td><?php
					echo $this->lists['customerlocation'];
				?></td>
			</tr>
			<tr>
				<td><?php echo JText::_( "VIEWCUSTOMERCITY" ); ?><span class="error">*</span></td>
				<td>
					<div>
						<input id="city" type="text" value="<?php echo $cust->city; ?>" name="city" size="40" />
					</div>
				</td>
			</tr>
			<tr>
				<td><?php echo JText::_( "VIEWCUSTOMERZIP" ); ?><span class="error">*</span></td>
				<td><input name="zipcode" type="text" id="zipcode" size="30" value="<?php echo $cust->zipcode; ?>"><b>&nbsp;</b></td>
			</tr>
<?php if ($this->configs->askforship) { ?>
			<tr>
				<td colspan="2"><h3><?php echo JText::_( "VIEWCUSTOMERSHIPADDR" ); ?></h3>
					<input type="checkbox" name="same_as_bil" onclick="populateShipping();" /><?php echo JText::_( "VIEWCUSTOMERSAMEASBILLING" ); ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_( "VIEWCUSTOMERSADRESS" ); ?><span class="error">*</span></td>
				<td><input name="shipaddress" type="text" id="shipaddress" size="30" value="<?php echo $cust->shipaddress; ?>"><b>&nbsp;</b></td>
			</tr>
			<tr>
				<td><?php echo JText::_( "VIEWCUSTOMERSCOUNTRY" ); ?><span class="error">*</span></td>
				<td><?php echo $this->lists['shipcountry_options']; ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_( "VIEWCUSTOMERSSTATE" ); ?><span class="error">*</span></td>
				<td><?php
					echo $this->lists['customershippinglocation'];
				?></td>
			</tr>
			<tr>
				<td><?php echo JText::_( "VIEWCUSTOMERSCITY" ); ?><span class="error">*</span></td>
				<td>
					<div >
						<input id="shipcity" type="text" value="<?php echo $cust->shipcity; ?>" name="shipcity" size="40" />
					</div>
				</td>
			</tr>
			<tr>
				<td><?php echo JText::_( "VIEWCUSTOMERSZIP" ); ?><span class="error">*</span></td>
				<td><input name="shipzipcode" type="text" id="shipzipcode" size="30" value="<?php echo $cust->shipzipcode; ?>"><b>&nbsp;</b></td>
			</tr>
<?php } ?>
			<tr>
				<td></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="submit" value="Continue" class="btn btn-success" />
					<input type="hidden" name="images" value="" />
					<input type="hidden" name="option" value="com_digicom" />
					<input type="hidden" name="id" value="<?php echo $cust->id; ?>" />
					<input type="hidden" name="task" value="saveCustomer" />
					<input type="hidden" name="controller" value="Orders" />
				</td>
			</tr>
		</table>

	</fieldset>

</form>