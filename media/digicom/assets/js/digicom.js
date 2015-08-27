/**
 * @version 	1.0.0
 * @package 	Com DigiCOm
 * @author 		ThemeXpert
 * @copyright 	Copyright (c) 2006 - 2014 ThemeXpert Ltd. All rights reserved.
 * @license 	GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */
var request_processed = 0;

function update_cart(item_id)
{
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
function addtoCart(pid, to_cart)
{
	if(document.getElementById("quantity_"+pid)){
		var qty = document.getElementById("quantity_"+pid).value;
	}else{
		var qty = 1;
	}
	var url = 'index.php?option=com_digicom&view=cart&task=cart.add&from=ajax&pid='+pid+'&qty='+qty;
	jQuery('#cartPopup').modal({
		remote: url,
		show: true
	});
	refresCartModule();
}
function refresCartModule()
{
	if(document.getElementById('mod_digicom_cart_wrap')){
		var url = digicom_site + 'index.php?option=com_digicom&view=cart&task=cart.get_cart_content';
		jQuery.ajax({
	      url: url,
	      data: { 'do' : '1' },
				method: 'get',
	      success: function (data, textStatus, xhr) {
					jQuery('#mod_digicom_cart_wrap').html(data);
	      }
	  });
	}
}
//
// function validateForm(register)
// {
// 		console.log(register);
// 		if ((document.adminForm.name && document.adminForm.name.value=="")
// 			|| (document.adminForm.email && document.adminForm.email.value=="")
// 			|| (document.adminForm.address && document.adminForm.address.value=="")
// 			|| (document.adminForm.city && document.adminForm.city.value=="")
// 			|| (document.adminForm.zipcode && document.adminForm.zipcode.value=="")
// 			|| (document.adminForm.country && document.adminForm.country.value=="")
// 			|| (document.adminForm.username && document.adminForm.username.value=="")
//
// 		){
// 				if(register){
// 					var field_required = new Array("name", "email", "address", "city", "zipcode", "country", "username", "password", "password_confirm");
// 				}else{
// 					var field_required = new Array("name", "email", "address", "city", "zipcode", "country", "username2");
// 				}
//
// 				var found = false;
// 				for(i=0; i<field_required.length; i++){
// 					if(document.getElementById(field_required[i])){
// 						if(document.getElementById(field_required[i]).value == ""){
// 							found = true;
// 							document.getElementById(field_required[i]).style.borderColor = "red";
// 						}else{
// 							document.getElementById(field_required[i]).style.borderColor = "";
// 						}
// 					}
// 				}
//
// 				if(found){
// 					alert(DSALL_REQUIRED_FIELDS);
// 				}else if(register){
// 					alert(DSALL_REQUIRED_FIELDS);
// 				}
//
// 				return false;
// 		}
//
// 		if (document.adminForm.password.value != document.adminForm.password_confirm.value) {
// 			var field_required = new Array("name", "email", "address", "city", "zipcode", "country", "username");
// 			for(i=0; i<field_required.length; i++){
// 				if(document.getElementById(field_required[i])){
// 					document.getElementById(field_required[i]).style.borderColor = "";
// 				}
// 			}
//
// 			document.getElementById("password").style.borderColor = "red";
// 			document.getElementById("password_confirm").style.borderColor = "red";
//
// 			alert(DSCONFIRM_PASSWORD_MSG);
// 			return false;
// 		}
//
// 		if (!isEmail(document.adminForm.email.value)){
// 			var field_required = new Array("name", "address", "city", "zipcode", "country", "username", "password", "password_confirm");
// 			for(i=0; i<field_required.length; i++){
// 				if(document.getElementById(field_required[i])){
// 					document.getElementById(field_required[i]).style.borderColor = "";
// 				}
// 			}
//
// 		   document.getElementById("email").style.borderColor = "red";
//
// 		   alert(DSINVALID_EMAIL);
// 		   return false;
// 		}
//
// 		if ((document.adminForm.zipcode) && !validateUSZip(document.adminForm.zipcode.value,document.adminForm.country.value)){
// 		   	var field_required = new Array("name", "email", "address", "city", "country", "username", "password", "password_confirm");
// 			for(i=0; i<field_required.length; i++){
// 				if(document.getElementById(field_required[i])){
// 					document.getElementById(field_required[i]).style.borderColor = "";
// 				}
// 			}
//
// 		   document.getElementById("zipcode").style.borderColor = "red";
// 		   alert("Invalid zipcode");
// 		   return false;
// 		}
//
// 		document.adminForm.name.value = document.adminForm.name.value;
// 		return true;
// }
// function isEmail(string)
// {
// 	var str = string;
// 	return (str.search(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/) != -1);
// }
// function validateUSZip( strValue , country)
// {
//
// 	if(country == 'United-States'){
// 		var objRegExp  = /(^[A-Za-z0-9 ]{1,7}$)/;
// 		return objRegExp.test(strValue);
// 	}
//
// 	return true;
// }

function validateInput(input)
{
	var formname = 'jform_'+input;
	value = document.getElementById(formname).value;
	if(value != ""){

		var myAjax = new Request(
	   	{
		    url:   'index.php?option=com_digicom&task=cart.validate_input&input='+input+'&value='+value,
	        method: 'get',
		    	onSuccess: function(response)
	        {
	            response = parseInt(response);
							if(response == "1"){
								if(input == "email"){
									var msg = 'COM_DIGICOM_REGISTRATION_EMAIL_ALREADY_USED';
								}
								else{
									var msg = 'COM_DIGICOM_REGISTER_USERNAME_TAKEN';
								}
								var myAjax = new Request(
									{
										url:   'index.php?option=com_digicom&task=getLanguage&txt='+msg,
											method: 'get',
											onSuccess: function(response)
											{
												var warning = '<span id="'+formname+'-warning" class="label label-warning">'+response+'</span>';
												jQuery('#'+formname).parent().append(warning);
											}
									});
									myAjax.send();
							}else{
								jQuery('#'+formname+'-warning').remove();
							}
            }
		});
		myAjax.send();
	}
}
function deleteFromCart(cartid)
{
	jQuery.ajax({
      url: 'index.php?option=com_digicom&view=cart&task=cart.deleteFromCart&from=ajax&cartid='+cartid,
			method: 'get',
      success: function (data, textStatus, xhr) {
				location.reload();
      }
  });
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
		if (document.getElementById('cart_item_discount'+cid)) {
			 document.getElementById('cart_item_discount'+cid).innerHTML = cart_item_discount;
		}
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
