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

$configs = $this->configs;
JHTML::_('behavior.modal');

?>
<script language="javascript" type="text/javascript">
	function isEmail(string) {
		var str = string;
		return (str.search(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/) != -1);
	}

	function validateUSZip( strValue ) {
		var objRegExp  = /(^[A-Za-z0-9 ]{1,7}$)/;
		return objRegExp.test(strValue);
	}

	function validateForm(){
		if ((document.adminForm.firstname && document.adminForm.firstname.value=="")
			|| (document.adminForm.lastname && document.adminForm.lastname.value=="")
			|| (document.adminForm.email && document.adminForm.email.value=="")
			|| (document.adminForm.address && document.adminForm.address.value=="")
			|| (document.adminForm.city && document.adminForm.city.value=="")
			|| (document.adminForm.zipcode && document.adminForm.zipcode.value=="")
			|| (document.adminForm.country && document.adminForm.country.value=="")
			|| (document.adminForm.username && document.adminForm.username.value=="")
			|| (document.adminForm.password && document.adminForm.password.value=="")
		){
				var field_required = new Array("firstname", "lastname", "email", "address", "city", "zipcode", "country", "username2", "password", "password_confirm");
				for(i=0; i<field_required.length; i++){
					if(document.getElementById(field_required[i])){
						if(document.getElementById(field_required[i]).value == ""){
							document.getElementById(field_required[i]).style.borderColor = "red";
						}
						else{
							document.getElementById(field_required[i]).style.borderColor = "";
						}
					}
				}
				alert('<?php echo JText::_("DSALL_REQUIRED_FIELDS"); ?>');
				return false;
		}

		if (document.adminForm.password.value != document.adminForm.password_confirm.value) {
			var field_required = new Array("firstname", "lastname", "email", "address", "city", "zipcode", "country", "username");
			for(i=0; i<field_required.length; i++){
				if(document.getElementById(field_required[i])){
					document.getElementById(field_required[i]).style.borderColor = "";
				}
			}

			document.getElementById("password").style.borderColor = "red";
			document.getElementById("password_confirm").style.borderColor = "red";

			alert("<?php echo JText::_("DSCONFIRM_PASSWORD_MSG"); ?>");
			return false;
		}

		if (!isEmail(document.adminForm.email.value)){
			var field_required = new Array("firstname", "lastname", "address", "city", "zipcode", "country", "username", "password", "password_confirm");
			for(i=0; i<field_required.length; i++){
				if(document.getElementById(field_required[i])){
					document.getElementById(field_required[i]).style.borderColor = "";
				}
			}

		   document.getElementById("email").style.borderColor = "red";

		   alert('<?php echo JText::_("DSINVALID_EMAIL"); ?>');
		   return false;
		}

		if ((document.adminForm.zipcode) && !validateUSZip(document.adminForm.zipcode.value)){
		   	var field_required = new Array("firstname", "lastname", "email", "address", "city", "country", "username", "password", "password_confirm");
			for(i=0; i<field_required.length; i++){
				if(document.getElementById(field_required[i])){
					document.getElementById(field_required[i]).style.borderColor = "";
				}
			}

		   document.getElementById("zipcode").style.borderColor = "red";
		   alert("Invalid zipcode");
		   return false;
		}

		document.adminForm.name.value = document.adminForm.firstname.value+" "+document.adminForm.lastname.value;
		return true;
	}



var request_processed = 0;

function submitbutton(pressbutton) {
   submitform( pressbutton );
}

function validateInput(input){
	value = document.getElementById(input).value;
	if(value != ""){
		var myAjax = new Ajax('index.php?option=com_digicom&controller=cart&format=raw&task=validate_input&input='+input+'&value='+value,
				   {
					onSuccess: function(response){
						response = parseInt(response);
						if(response == "1"){
							if(input == "email"){
								document.getElementById("email_span").className = "invalid";
								document.getElementById("email_span_msg").style.display = "block";
							}
							else{
								document.getElementById("username_span").className = "invalid";
								document.getElementById("username_span_msg").style.display = "block";
							}
						}
						else{
							if(input == "email"){
								document.getElementById("email_span").className = "valid";
								document.getElementById("email_span_msg").style.display = "none";
							}
							else{
								document.getElementById("username_span").className = "valid";
								document.getElementById("username_span_msg").style.display = "none";
							}
						}
					}
				});
		myAjax.request();
	}
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
</script>

<?php
	$old_values = array();
	if(isset($_SESSION["new_customer"])){
		$old_values = $_SESSION["new_customer"];

	}
	$firstname = "";
	$lastname = "";
	$company = "";
	$email = "";
	$username = "";
	$password = "";
	$password_confirm = "";
	$address = "";
	$city = "";
	$zipcode = "";
	$country = "";
	$state = "";
	if(isset($old_values) && count($old_values) > 0){
		$firstname = $old_values["firstname"];
		$lastname = $old_values["lastname"];
		$company = $old_values["company"];
		$email = $old_values["email"];
		$username = $old_values["username"];
		$password = $old_values["password"];
		$password_confirm = $old_values["password_confirm"];
		$address = $old_values["address"];
		$city = $old_values["city"];
		$zipcode = $old_values["zipcode"];
		$country = $old_values["country"];
		$state = $old_values["state"];
		unset($_SESSION["new_customer"]);
	}
	$login_link = JRoute::_("index.php?option=com_digicom&controller=profile&task=login&returnpage=cart&tmpl=component&returnpage=cart&graybox=true");
?>

<tr>
	<td>
		<?php echo JText::_("DSFIRSTNAME"); ?>&nbsp;<span class="error">*</span>
	</td>

	<td>
		<input name="firstname" type="text" id="firstname"   size="30" class="digi_textbox" value="<?php echo $firstname; ?>"><b>&nbsp;</b>
	</td>
</tr>

<tr>
	<td>
		<?php echo JText::_("DSLASTNAME"); ?>&nbsp;<span class="error">*</span>
	</td>

	<td>
		<input name="lastname" type="text" id="lastname"   size="30" class="digi_textbox" value="<?php echo $lastname; ?>"><b>&nbsp;</b>
	</td>
</tr>

<?php
	if($this->askforcompany == 1){
?>

<tr>
	<td>
		<?php echo JText::_("DSCOMPANY"); ?><b></b>
	</td>

	<td>
		<input name="company" type="text" id="company"   size="30" class="digi_textbox" value="<?php echo $company; ?>">
	</td>
</tr>

<?php
	}
?>

<tr>
	<td>
		<?php echo JText::_('DSEMAIL'); ?>&nbsp;<span class="error">*</span>
	</td>

	<td>
		<input name="email" type="text" id="email"  size="30" class="digi_textbox" value="<?php echo $email; ?>" onchange="javascript:validateInput('email');" />
		&nbsp;&nbsp;
		<span class="" id="email_span">&nbsp;&nbsp;&nbsp;&nbsp;</span>
		<br/>
		<span style="display:none; color:#FF0000; font-size: 12px;" id="email_span_msg"><?php echo JText::_("DIGI_EMAIL_TAKEN")." "."<a rel=\"{handler: 'iframe', size: {x: 300, y: 300}}\"  class=\"modal\"  href=\"".$login_link."\">".JText::_('DIGI_HERE')."</a> ".JText::_("DIGI_TO_LOGIN"); ?></span>
	</td>
</tr>

<tr>
	<td>
		<h2><?php echo JText::_("DSLOGININFO"); ?></h2>
	</td>
	<td>&nbsp;</td>
</tr>

<tr>
	<td>
		<?php echo JText::_('DSUSERNAME'); ?>&nbsp;<span class="error">*</span>
	</td>

	<td>
		<input name="username" type="text" id="username2" size="30" class="digi_textbox" value="<?php echo $username; ?>" onchange="javascript:validateInput('username');" />
		&nbsp;&nbsp;
		<span class="" id="username_span">&nbsp;&nbsp;&nbsp;&nbsp;</span>
		<br/>
		<span style="display:none; color:#FF0000; font-size: 12px;" id="username_span_msg"><?php echo JText::_("DIGI_USERNAME_TAKEN")." "."<a rel=\"{handler: 'iframe', size: {x: 300, y: 300}}\"  class=\"modal\"  href=\"".$login_link."\">".JText::_('DIGI_HERE')."</a> ".JText::_("DIGI_TO_CONTINUE"); ?></span>
	</td>
</tr>

<tr>
	<td>
		<?php echo JText::_("DSPASS"); ?>&nbsp;<span class="error">*</span>
	</td>

	<td>
		<input name="password" type="password" id="password" size="30" class="digi_textbox"   value="<?php echo $password; ?>" ><b>&nbsp;</b>
	</td>
</tr>

<tr>
	<td>
		<?php echo JText::_("DSCPASS"); ?>&nbsp;<span class="error">*</span>
	</td>

	<td>
		<input name="password_confirm" type="password" id="password_confirm"   size="30" class="digi_textbox" value="<?php echo $password_confirm; ?>"><b>&nbsp;</b>
	</td>
</tr>

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

		 // get the folder name
		 <?php
		 	if(isset($eu) && count($eu) > 0){
		 ?>
		 		var euc = Array(<?php echo "'" . implode("','", $eu) . "'"; ?>);
		<?php
			}
			else{
		?>
				var euc = Array();
		<?php
			}
		?>
		 var flag = 0;
		 for (i = 0; i< euc.length; i++)
			 if (country == euc[i]) flag = 1;

		 x_phpchangeProvince(country, 'main', changeProvince_cb);
	 }
	 var request_processed = 0;
	 function changeProvince_cb_ship(province_option) {
		 //alert(province_option+'MYYYYYYYYYYYY');

		 var idx = 0;
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

	 function showTaxNum(x) {
		 var taxnum = document.getElementById("comptaxnum");
		 if (x == 0) taxnum.style.display = "";
		 else taxnum.style.display = "none";

	 }

</script>

<?php
	/*if($this->askforbilling == 0){
		require_once(JPATH_SITE.DS."components".DS."com_digicom".DS."models".DS."cart.php");
		$cart = new DigiComModelCart();
		require_once(JPATH_SITE.DS."components".DS."com_digicom".DS."models".DS."config.php");
		$configs = new DigiComModelConfig();
		$configs = $configs->getConfigs();
		$items = $cart->getCartItems($this->cust, $configs);

	}*/

	if($this->askforbilling == 1){
?>

		<tr>
			<td>
				<h2><?php echo JText::_("DSBILLINGADR"); ?></h2>
			</td>
			<td>&nbsp;</td>
		</tr>

		<tr>
			<td>
				<?php echo JText::_("DSCOUNTRY"); ?>&nbsp;<span class="error">*</span>
			</td>

			<td>
				<?php
					$customer = $this->customer;
					$customer->country = $country;
					$country_option = DigiComHelper::get_country_options($customer, false, $configs);
					echo $country_option;
				?>
			</td>
		</tr>

		<tr>
			<td>
				<?php echo Jtext::_("DSBILLING"); ?>&nbsp;<span class="error">*</span>
			</td>

			<td>
				<input name="address" type="text" id="address"   size="30" class="digi_textbox" value="<?php echo $address; ?>"><b>&nbsp;</b>
			</td>
		</tr>

		<tr>
			<td>
				<?php echo JText::_("DSCITY"); ?>&nbsp;<span class="error">*</span>
			</td>

			<td>
				<input id="city" type="text" value="<?php echo $city; ?>" name="city"   size="30" class="digi_textbox" />
			</td>
		</tr>

		<tr>
			<td>
				<?php echo JText::_("DSSTATE"); ?>&nbsp;<span class="error">*</span>
			</td>

			<td>
				<?php
					$customer = $this->customer;
					$customer->state = $state;
					echo DigiComHelper::get_store_province($customer, false);
				?>
			</td>
		</tr>

		<tr>
			<td>
				<?php echo JText::_("DSZIP"); ?>&nbsp;<span class="error">*</span>
			</td>

			<td>
				<input name="zipcode" type="text" id="zipcode"   size="30" class="digi_textbox" value="<?php echo $zipcode; ?>"><b>&nbsp;</b>
			</td>
		</tr>
<?php
	}
?>