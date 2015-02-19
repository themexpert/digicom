<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 364 $
 * @lastmodified	$LastChangedDate: 2013-10-15 15:27:43 +0200 (Tue, 15 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

defined ('_JEXEC') or die ("Go away.");

JHTML::_('behavior.modal');

$cust = $this->cust;
// echo "<pre>";
// print_r($cust);
// echo "</pre>";
$eu = $this->eu;
$uid = $this->uid ? $this->uid : 0;
$configs = $this->configs;

$Itemid = JRequest::getVar("Itemid", "0");

$cart_itemid = DigiComHelper::getCartItemid();
$and_itemid = "";
if($cart_itemid != ""){
	$and_itemid = "&Itemid=".$cart_itemid;
}

if($uid != "0"){
?>
<div class="digicom">
	<div class="navbar">
		<div class="navbar-inner hidden-phone">
			<ul class="nav">
				<li class="active">
					<a href="<?php echo JRoute::_("index.php?option=com_digicom&view=profile&Itemid=".$Itemid); ?>"><i class="ico-user"></i> <?php echo JText::_("DIGI_MY_ACCOUNT"); ?></a>
				</li>
				<li class="divider-vertical"></li>
				<li>
					<a href="<?php echo JRoute::_("index.php?option=com_digicom&view=downloads&Itemid=".$Itemid); ?>"><i class="ico-download"></i> <?php echo JText::_("DIGI_MY_DOWNLOADS"); ?></a>
				</li>
				<li class="divider-vertical"></li>
				<li>
					<a href="<?php echo JRoute::_("index.php?option=com_digicom&view=orders&Itemid=".$Itemid); ?>"><i class="ico-list-alt"></i> <?php echo JText::_("DIGI_MY_ORDERS"); ?></a>
				</li>
				<li class="divider-vertical"></li>
				<li>
					<a href="<?php echo JRoute::_("index.php?option=com_digicom&view=cart&task=showCart".$and_itemid); ?>"><i class="ico-shopping-cart"></i> <?php echo JText::_("DIGI_MY_CART"); ?></a>
				</li>
				<li class="divider-vertical"></li>
				<li>
					<a href="index.php?option=com_users&task=user.logout&<?php echo JSession::getFormToken(); ?>=1&return=<?php echo base64_encode(JURI::root()); ?>"><?php echo 'Logout'; ?></a>
				</li>
			</ul>
		</div>
		<ul class="nav nav-pills hidden-desktop">
			<li class="active">
				<a href="<?php echo JRoute::_("index.php?option=com_digicom&view=profile&Itemid=".$Itemid); ?>"><i class="ico-user hidden-phone"></i> <?php echo JText::_("DIGI_MY_ACCOUNT"); ?></a>
			</li>
			<li class="divider-vertical"></li>
			<li>
				<a href="<?php echo JRoute::_("index.php?option=com_digicom&view=licenses&Itemid=".$Itemid); ?>"><i class="ico-download hidden-phone"></i> <?php echo JText::_("DIGI_MY_DOWNLOADS"); ?></a>
			</li>
			<li class="divider-vertical"></li>
			<li>
				<a href="<?php echo JRoute::_("index.php?option=com_digicom&view=orders&Itemid=".$Itemid); ?>"><i class="ico-list-alt hidden-phone"></i> <?php echo JText::_("DIGI_MY_ORDERS"); ?></a>
			</li>
			<li class="divider-vertical"></li>
			<li>
				<a href="<?php echo JRoute::_("index.php?option=com_digicom&controller=cart&task=showCart".$and_itemid); ?>"><i class="ico-shopping-cart hidden-phone"></i> <?php echo JText::_("DIGI_MY_CART"); ?></a>
			</li>
		</ul>
	</div>

<?php
}
?>

<div class="digicom_form_account">
<h1><?php echo JText::_("DIGI_MY_STORE_ACCOUNT"); ?></h1>

<script language="javascript" type="text/javascript">
	function isEmail(string) {
		var str = string;
		return (str.search(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/) != -1);
	}

	function validateUSZip( strValue ) {
		/************************************************
	DESCRIPTION: Validates that a string a United
	States zip code in 5 digit format or zip+4
	format. 99999 or 99999-9999

	PARAMETERS:
	strValue - String to be tested for validity

	RETURNS:
	True if valid, otherwise false.

		 *************************************************/
		var objRegExp  = /(^[A-Za-z0-9 ]{1,7}$)/;

		//check for valid US Zipcode
		return objRegExp.test(strValue);
	}
	function validateForm(){
		if (document.adminForm.firstname.value==""
			|| document.adminForm.lastname.value==""
			|| document.adminForm.email.value==""
			|| (eval(document.adminForm.address) && document.adminForm.address.value=="")
			|| (eval(document.adminForm.city) && document.adminForm.city.value=="")
			|| (eval(document.adminForm.zipcode) && document.adminForm.zipcode.value=="")
			|| (eval(document.adminForm.country) && document.adminForm.country.value=="")
<?php
	 if ($uid == 0) { ?>
			|| document.adminForm.username.value==""
			|| document.adminForm.password.value==""
<?php } ?>
			){
				alert('<?php echo JText::_("DSALL_REQUIRED_FIELDS"); ?>');
				return false;
		}

	  if(document.adminForm.password.value != "" && (document.adminForm.password.value != document.adminForm.password_confirm.value)){
		  alert("<?php echo JText::_("DSCONFIRM_PASSWORD_MSG"); ?>");
		  return false;
	  }

   if (!isEmail(document.adminForm.email.value)){
	   alert('<?php echo JText::_("DSINVALID_EMAIL"); ?>');
	   return false;
   }
   if (eval(document.adminForm.zipcode) && !validateUSZip(document.adminForm.zipcode.value)){
	   //alert("Invalid zipcode");
	   //return false;
   }

   document.adminForm.name.value = document.adminForm.firstname.value+" "+document.adminForm.lastname.value;
   return true;
}



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
</script>


<form action="index.php?option=com_digicom" method="post" name="adminForm" id="adminForm" onsubmit="return validateForm();" class="form-horizontal">
	<h2><?php echo JText::_('DSPROFILESETTINGS'); ?></h2>
	<div class="row-fluid">
		<div class="control-group">
			<label class="control-label" for="firstname"><?php echo JText::_("DSFIRSTNAME"); ?> <span class="error">*</span></label>
			<div class="controls" style="display:inherit;">
				<input name="firstname" type="text" id="firstname" size="30" value="<?php echo $cust->firstname ?>" />
			</div>
		</div>
	</div>
	<div class="row-fluid">
		<div class="control-group">
			<label class="control-label" for="lastname"><?php echo JText::_("DSLASTNAME"); ?> <span class="error">*</span></label>
			<div class="controls" style="display:inherit;">
				<input name="lastname" type="text" id="lastname" size="30" value="<?php echo $cust->lastname ?>" />
			</div>
		</div>
	</div>
	<?php if($configs->get('askforcompany',1) == 1) { ?>
	<div class="row-fluid">
		<div class="control-group">
			<label class="control-label" for="company"><?php echo JText::_("DSCOMPANY"); ?></label>
			<div class="controls" style="display:inherit;">
				<input name="company" type="text" id="company" size="30" value="<?php echo $cust->company ?>" />
			</div>
		</div>
	</div>
	<?php } ?>
	<div class="row-fluid">
		<div class="control-group">
			<label class="control-label" for="email"><?php echo JText::_("DSEMAIL"); ?> <span class="error">*</span></label>
			<div class="controls" style="display:inherit;">
				<input name="email" type="text" id="email" size="30" value="<?php echo $cust->email ?>" />
			</div>
		</div>
	</div>

	<h2><?php echo JText::_('DSLOGININFO'); ?></h2>
	<div class="row-fluid">
		<div class="control-group">
			<label class="control-label" for="username"><?php echo JText::_("DSUSERNAME"); ?> <span class="error">*</span></label>
			<div class="controls" style="display:inherit;">
				<input name="username" <?php if ($cust->id) { ?> disabled <?php } ?> type="text" id="username" size="30" value="<?php echo $cust->username ?>" />
			</div>
		</div>
	</div>
	<div class="row-fluid">
		<div class="control-group">
			<label class="control-label" for="password"><?php echo JText::_("DSPASS"); ?></label>
			<div class="controls" style="display:inherit;">
				<input name="password" type="password" id="password" size="30" />
			</div>
		</div>
	</div>
	<div class="row-fluid">
		<div class="control-group">
			<label class="control-label" for="password_confirm"><?php echo JText::_("DSCPASS"); ?></label>
			<div class="controls" style="display:inherit;">
				<input name="password_confirm" type="password" id="password_confirm" size="30" />
			</div>
		</div>
	</div>
		

	<?php if($configs->get('askforbilling','0') == 1){ ?>
		<h2><?php echo JText::_('DSBILLINGADR'); ?></h2>
		<div class="row-fluid">
			<div class="control-group">
				<label class="control-label" for="country_option"><?php echo JText::_("DSCOUNTRY"); ?> <span class="error">*</span></label>
				<div class="controls" style="display:inherit;">
					<?php echo $this->lists['country_option']; ?>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="control-group">
				<label class="control-label" for="address"><?php echo JText::_("DSBILLING"); ?> <span class="error">*</span></label>
				<div class="controls" style="display:inherit;">
					<input name="address" type="text" id="address" value="<?php echo $cust->address; ?>" />
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="control-group">
				<label class="control-label" for="city"><?php echo JText::_("DSCITY"); ?> <span class="error">*</span></label>
				<div class="controls" style="display:inherit;">
					<input name="city" type="text" id="city" value="<?php echo $cust->city; ?>" />
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="control-group">
				<label class="control-label" for="customerlocation"><?php echo JText::_("DSSTATE"); ?> <span class="error">*</span></label>
				<div class="controls" style="display:inherit;">
					<?php echo $this->lists['customerlocation']; ?>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="control-group">
				<label class="control-label" for="customerlocation"><?php echo JText::_("DSZIP"); ?> <span class="error">*</span></label>
				<div class="controls" style="display:inherit;">
					<input name="zipcode" type="text" id="zipcode" value="<?php echo $cust->zipcode; ?>" />
				</div>
			</div>
		</div>
	<?php
	}
	?>

<?php if ($configs->get('askforship','0') == 1): ?>
	<h2><?php echo JText::_('DSSHIPADDR'); ?></h2>
	<input type="checkbox" name="same_as_bil" onclick="populateShipping();" /><?php echo JText::_("DSSAMEASBILLING"); ?></td>
	<div class="row-fluid">
		<div class="control-group">
			<label class="control-label" for="shipcountry_options"><?php echo JText::_("DSSHIPINGCOUNTRY"); ?> <span class="error">*</span></label>
			<div class="controls" style="display:inherit;">
				<?php echo $this->lists['shipcountry_options']; ?>
			</div>
		</div>
	</div>
	<div class="row-fluid">
		<div class="control-group">
			<label class="control-label" for="customershippinglocation"><?php echo JText::_("DSSHIPINGPROVINCE"); ?> <span class="error">*</span></label>
			<div class="controls" style="display:inherit;">
				<?php echo $this->lists['customershippinglocation']; ?>
			</div>
		</div>
	</div>
	<div class="row-fluid">
		<div class="control-group">
			<label class="control-label" for="shipcity"><?php echo JText::_("DSSHIPINGCITY"); ?> <span class="error">*</span></label>
			<div class="controls" style="display:inherit;">
				<input name="shipcity" type="text" id="shipcity" value="<?php echo $cust->shipcity; ?>" />
			</div>
		</div>
	</div>
	<div class="row-fluid">
		<div class="control-group">
			<label class="control-label" for="shipaddress"><?php echo JText::_("DSSHIPPINGADDRESS"); ?> <span class="error">*</span></label>
			<div class="controls" style="display:inherit;">
				<input name="shipaddress" type="text" id="shipaddress" value="<?php echo $cust->shipaddress; ?>" />
			</div>
		</div>
	</div>
	<div class="row-fluid">
		<div class="control-group">
			<label class="control-label" for="shipzipcode"><?php echo JText::_("DSSHIPPINGZIP"); ?> <span class="error">*</span></label>
			<div class="controls" style="display:inherit;">
				<input name="shipzipcode" type="text" id="shipzipcode" value="<?php echo $cust->shipzipcode; ?>" />
			</div>
		</div>
	</div>
	<?php endif; ?>

	<div id="vathead" class="row-fluid" style="display:<?php echo (isset($cust->country) && in_array($cust->country, $eu) ? "" : "none"); ?>">
		<div class="control-group">
			<label class="control-label" for="shipzipcode"><?php echo JText::_("DSSHIPPINGZIP"); ?> <span class="error">*</span></label>
			<div class="controls" style="display:inherit;">
				<input name="shipzipcode" type="text" id="shipzipcode" value="<?php echo $cust->shipzipcode; ?>" />
			</div>
		</div>
	</div>
	<div id="personcomp" class="row-fluid" style="display:<?php echo (isset($cust->country) && in_array($cust->country, $eu) ? "" : "none"); ?>">
		<div class="control-group">
			<label class="control-label" for="person"><?php echo JText::_("DSPERSONORCOMP"); ?> <span class="error">*</span></label>
			<div class="controls" style="display:inherit;">
				<select id="person" name="person" onchange="showTaxNum(this.value);">
					<option value="1" <?php echo (($cust->person != 0) ? "selected" : ""); ?> ><?php echo JText::_("DSPERS"); ?></option>
					<option value="0" <?php echo (($cust->person == 0) ? "selected" : ""); ?>><?php echo JText::_("DSCOMP"); ?></option>
				</select>
			</div>
		</div>
	</div>
	<div id="comptaxnum" class="row-fluid" style="display:<?php echo (isset($cust->country) && in_array($cust->country, $eu) ? "" : "none"); ?>">
		<div class="control-group">
			<label class="control-label" for="person"><?php echo JText::_("DSTAXNUM"); ?> <span class="error">*</span></label>
			<div class="controls" style="display:inherit;">
				<input name="taxnum" type="text" id="taxnum" value="<?php echo ( $cust->taxnum > 0 ) ? $cust->taxnum : ""; ?>" />
			</div>
		</div>
	</div>
	<div class="control-group">
		<div class="controls"><?php
			$text = "";
			if($cust->id < 1)
			{
				$text = "DSSAVEPROFILE";
			}
			else
			{
				$text = "DSSAVE";
			} ?>
			<button type="submit" class="btn btn-success btn-blue"><i class="ico-ok-sign ico-white"></i> <?php echo JText::_($text) ?></button>
		</div>
	</div>

	<input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>" />
	<input type="hidden" name="images" value="" />
	<input type="hidden" name="option" value="com_digicom" />
	<input type="hidden" name="id" value="<?php echo $cust->id; ?>" />
	<input type="hidden" name="task" value="saveCustomer" />
	<input type="hidden" name="pid" value="<?php echo (JRequest::getVar("pid", "", 'request'));?>" />
	<input type="hidden" name="cid" value="<?php echo (JRequest::getVar("cid", "", 'request'));?>" />
	<input type="hidden" name="returnpage" value="<?php echo (JRequest::getVar("returnpage", "", 'request')); ?>" />
	<input type="hidden" name="controller" value="Profile" />
</form>

</div>
</div>

<script>
<?php sajax_show_javascript(); ?>

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
	var euc = Array(<?php echo "'" . implode("','", $eu) . "'"; ?>);
	var flag = 0;
	for (i = 0; i< euc.length; i++)
		if (country == euc[i]) flag = 1;
	if (flag == 1) {
		document.getElementById('vathead').style.display = '';
		document.getElementById('personcomp').style.display = '';
		document.getElementById('comptaxnum').style.display = '';
	} else {
		document.getElementById('vathead').style.display = 'none';
		document.getElementById('personcomp').style.display = 'none';
		document.getElementById('comptaxnum').style.display = 'none';
	}

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