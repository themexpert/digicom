/**
 * @version 	1.0.0
 * @package 	Com DigiCOm
 * @author 		ThemeXpert
 * @copyright 	Copyright (c) 2006 - 2014 ThemeXpert Ltd. All rights reserved.
 * @license 	GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */


function checkIfIframe(){
	if(top !== self){
		var fileref=document.createElement("link");
		fileref.setAttribute("rel", "stylesheet");
		fileref.setAttribute("type", "text/css");
		fileref.setAttribute("href", digicom_site + "media/digicom/assets/css/changegrayposition.css");
		if (typeof fileref!="undefined"){
			document.getElementsByTagName("head")[0].appendChild(fileref);
		}
	}
}

function ajaxRequest(Url,DivId)
{
	 var AJAX;
	 try
	 {
	  AJAX = new XMLHttpRequest();
	 }
	 catch(e)
	 {
	  try
	  {
	   AJAX = new ActiveXObject("Msxml2.XMLHTTP");
	  }
	  catch(e)
	  {
	   try
	   {
		AJAX = new ActiveXObject("Microsoft.XMLHTTP");
	   }
	   catch(e)
	   {
		alert("Your browser does not support AJAX.");
		return false;
	   }
	  }
	 }
	 AJAX.onreadystatechange = function()
	 {
	  if(AJAX.readyState == 4)
	  {
	   if(AJAX.status == 200)
	   {
		// debug info
		//console.log(AJAX.responseText);
		//document.getElementById(DivId).innerHTML = AJAX.responseText;
		var myObject = eval("(" + AJAX.responseText + ")");
		var cid = myObject.cid;
		var cart_item_price = eval('myObject.cart_item_price'+cid);
		var cart_item_total = eval('myObject.cart_item_total'+cid);
		var cart_item_discount = eval('myObject.cart_item_discount'+cid);

		document.getElementById('cart_item_price'+cid).innerHTML = cart_item_price;
		document.getElementById('cart_item_total'+cid).innerHTML = cart_item_total;
		document.getElementById('cart_item_discount'+cid).innerHTML = cart_item_discount;
		document.getElementById('cart_total').innerHTML = myObject.cart_total;
		var cd = document.getElementById('digicom_cart_discount');
		if(cd) cd.innerHTML = myObject.cart_discount;
		// document.getElementById('digicom_cart_discount').innerHTML = myObject.cart_discount;
		var ct = document.getElementById('digicom_cart_tax');
		if(ct)ct.innerHTML = myObject.cart_tax;
		refresCartModule();
	   }
	   else
	   {
		alert("Error: "+ AJAX.statusText +" "+ AJAX.status);
	   }
	  }
	 }
	 AJAX.open("get", Url, true);
	 AJAX.send(null);
}

function update_cart(item_id) {
	var url = digicom_site + "index.php?option=com_digicom&view=cart&task=cart.getCartItem&cid="+item_id;
	var promocode = document.getElementById('promocode');
	var promocode_query = '&promocode='+promocode.value;
	url += promocode_query;

	var qty = document.getElementById('quantity'+item_id);
	var qty_value = qty.value;
	var qty_query = '';
	qty_query += '&quantity'+item_id+'='+qty_value;
	url += qty_query;
	//console.log(url);
	ajaxRequest(url, 'debugid');
}

function refresCartModule(){
	if(document.getElementById('mod_digicom_cart_wrap')){
		var url = digicom_site + 'index.php?option=com_digicom&view=cart&task=cart.get_cart_content';
		var req = new Request.HTML({
			method: 'get',
			url: url,
			data: { 'do' : '1' },
			onComplete: function(responseTree, responseElements, responseHTML, responseJavaScript){
				document.getElementById('mod_digicom_cart_wrap').innerHTML = responseHTML;
			}
		}).send();
	}
}

function cartformsubmit(user_id,askterms){
				
	if(user_id == '0'){

		type_button_value = document.cart_form.type_button.value;
		if(type_button_value == "checkout"){
			if(jQuery("#firstname").length > 0)
			{
				if(document.cart_form.firstname.value==""
				|| document.cart_form.lastname.value==""
				|| document.cart_form.email.value==""
				|| document.cart_form.address.value==""
				|| document.cart_form.city.value==""
				|| document.cart_form.zipcode.value==""
				|| document.cart_form.country.value==""
				|| document.cart_form.username.value==""
				|| document.cart_form.password.value==""
				){
					//alert('<?php echo JText::_("DSALL_REQUIRED_FIELDS"); ?>');
					jQuery("#myModalLabel").html(DIGI_ATENTION);
					jQuery("#myModalBody").html("<p>" + DSALL_REQUIRED_FIELDS + "</p>");
					jQuery('#myModal').modal('show');
					return false;
				}
		
				if(document.cart_form.password.value != document.cart_form.password_confirm.value) {
					//alert("<?php echo JText::_("DSCONFIRM_PASSWORD_MSG"); ?>");
					jQuery("#myModalLabel").html(DIGI_ATENTION);
					jQuery("#myModalBody").html("<p>" + DSCONFIRM_PASSWORD_MSG + "</p>");
					jQuery('#myModal').modal('show');
					return false;
				}
				if (!isEmail(document.cart_form.email.value)){
					//alert('<?php echo JText::_("DSINVALID_EMAIL"); ?>');
					jQuery("#myModalLabel").html(DIGI_ATENTION);
					jQuery("#myModalBody").html("<p>" + DSINVALID_EMAIL + "</p>");
					jQuery('#myModal').modal('show');
					return false;
				}
				if (!validateUSZip(document.cart_form.zipcode.value,document.adminForm.country.value)){
					//alert("Invalid zipcode");
					//return false;
				}
			}
			
			if(askterms){
			   if(document.cart_form.agreeterms.checked != true){
				   jQuery("#myModalLabel").html(DIGI_ATENTION);
				   jQuery('#myModalBody').html("<p>" + ACCEPT_TERMS_CONDITIONS + "</p>");
				   jQuery('#myModal').modal('show');
				   return false;
			   }
			}
			
		}	
	}	

	
	return true;
}

function isEmail(string) {
	var str = string;
	return (str.search(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/) != -1);
}

function validateUSZip( strValue , country) {

	if(country == 'United-States'){
		var objRegExp  = /(^[A-Za-z0-9 ]{1,7}$)/;
		return objRegExp.test(strValue);
	}

	return true;
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
				alert(DSALL_REQUIRED_FIELDS);
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

			alert(DSCONFIRM_PASSWORD_MSG);
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

		   alert(DSINVALID_EMAIL);
		   return false;
		}

		if ((document.adminForm.zipcode) && !validateUSZip(document.adminForm.zipcode.value,document.adminForm.country.value)){
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
		
		var myAjax = new Request(
	   	{
		    url:   'index.php?option=com_digicom&task=cart.validate_input&input='+input+'&value='+value,
	        method: 'get',
		    onSuccess: function(response)
	        {
	            response = parseInt(response);
	            console.log(response);
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
		myAjax.send();
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

function changeProvince_cb(province_option) {
	 document.getElementById("province").innerHTML = province_option;
 }

 function changeProvince() {
	 // get the folder name
	 var country;
	 country = document.getElementById('country').value;
	 
	var euc = Array();
	
	 var flag = 0;
	 for (i = 0; i< euc.length; i++)
		 if (country == euc[i]) flag = 1;

	 x_phpchangeProvince(country, 'main', changeProvince_cb);
 }

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
 function ChangeLogOption(value){
	if(value == 0){
		document.getElementById("log_form").style.display = "block";
		document.getElementById("reg_form").style.display = "none";
		document.getElementById("continue_button").style.display = "none";
	}
	else if(value == 1){
		document.getElementById("log_form").style.display = "none";
		document.getElementById("reg_form").style.display = "block";
		document.getElementById("continue_button").style.display = "block";
	}
}
function closePopupLogin(div) {
	if(document.getElementById(div)){
		for_delete = document.getElementById(div);
		for_delete.parentNode.removeChild(for_delete);
	}
}