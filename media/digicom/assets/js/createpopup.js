function closePopupCart(div)
{
	if(document.getElementById(div)){
		for_delete = document.getElementById(div);
		document.body.removeChild(for_delete);
	}
}

function centerPopup(div, option)
{
	var e = document.getElementById(div);
	height_det = e.offsetHeight;
	width_det = e.offsetWidth;

	page_width = window.screen.availWidth;
	page_height = window.screen.availHeight;

	if((height_det + 350) > page_height){
		if(!document.getElementById("contentpane")){
			digilistitems = document.getElementById("digilistitems").innerHTML;
			new_height = height_det-350;
			newdiv = '<tr><td><div align="center" style="overflow: scroll; height: '+new_height+'px;" id="contentpane"><table>';
			newdiv += digilistitems+"</table></div></td></tr>";
			document.getElementById("digilistitems").innerHTML = newdiv;
			centerPopup("digicart_popup");
		}
	}

	var e = document.getElementById(div);
	if(page_height <= height_det){
		e.style.top = parseInt(height_det - page_height - 10) + "px";
	}
	else{
		top = parseInt((page_height - height_det) / 2) - 120;
		e.style.top = top+"px";
	}
}

function updateCart()
{
	promocode = document.cart_form.promocode.value;

	var url = "index.php?option=com_digicom&view=cart&task=updateCart&promocode="+promocode+"&from=ajax";
	var req = new Request.HTML({
		method: 'get',
		url: url,
		data: { 'do' : '1' },
		onComplete: function(responseTree, responseElements, responseHTML, responseJavaScript){
			refresCartModule();
			if(typeof responseHTML === "undefined"){
				//document.getElementById("cart_body").innerHTML = responseTree;
			} else {
				document.getElementById("cart_body").innerHTML = responseHTML;
			}
		}
	}).send();
}

function deleteFromCart(cartid)
{
	var url = 'index.php?option=com_digicom&view=cart&task=deleteFromCart&from=ajax&cartid='+cartid;
	var req = new Request.HTML({
		method: 'get',
		url: url,
		data: { 'do' : '1' },
		onComplete: function(responseTree, responseElements, responseHTML, responseJavaScript){
			refresCartModule();
			document.getElementById("cart_body").innerHTML = responseHTML;
		}
	}).send();
}

function createPopUp(pid, cid, site, renew, renewlicid, itemid, to_cart)
{
	var width = parseInt(window.screen.availWidth/4) + 110;
	var height = parseInt(window.screen.availHeight/4);

	if(document.getElementById("digicart_popup"))
	{
		div = document.getElementById("digicart_popup");
		document.body.removeChild(div);
	}

	itemid_var = "";
	if(itemid != ""){
		itemid_var = "&Itemid="+itemid;
	}

	var divheader = document.createElement("div");
	divheader.id = "cart_header";
	divheader.className = "modal-header";
	header_content =  '<a class="close" onclick="jQuery(\'#digicart_popup\').remove();">&times;</a>';
	header_content+=  '<h3>Added to cart</h3>';
	divheader.innerHTML = header_content;

	var divbody = document.createElement("div");
	divbody.id = "cart_body";
	divbody.className = "modal-body";

	var url = 'index.php?option=com_digicom&view=cart&task=add'+
						  '&from=ajax'+
						  '&pid='+pid+
						  '&cid='+cid;
	console.log(req);
	var req = new Request.HTML({
		method: 'get',
		url: url,
		data: { 'do' : '1' },
		onComplete: function(responseTree, responseElements, responseHTML, responseJavaScript){
			divbody.innerHTML = responseHTML;
			refresCartModule()
		}
	}).send();

	renew_var = "";
	itemid_var = "";
	if(renew != "")
	{
		renew_var = "&renew=renew";
	}
	if(itemid != "")
	{
		itemid_var = "&Itemid="+itemid;
	}
	var divfutter = document.createElement("div");
	divfutter.id = "cart_futter";
	divfutter.className = "modal-footer";
	futter_content = '<a href="javascript:;" onclick="jQuery(\'#digicart_popup\').remove();" class="btn"><i class="ico-shopping-cart"></i> Continue Shopping</a>';
	futter_content+= '<a href="javascript:;" onclick="document.getElementById(\'returnpage\').value=\'checkout\'; javascript:updateCart(); window.location.href=\''+to_cart+'\'" class="btn btn-warning">Checkout <i class="ico-ok-sign"></i></a>';
	divfutter.innerHTML = futter_content;

	var modalparent = document.createElement("div");
	modalparent.className = "digicom";

	var popup = document.createElement("div");
	popup.id = "digicart_popup";
	popup.className = "modal";
	popup.style.left = "50%";
	//popup.style.top = "50%";
	popup.style.position = "fixed";
	popup.style.zIndex = "1000";

	popup.appendChild(divheader);
	popup.appendChild(divbody);
	popup.appendChild(divfutter);

	modalparent.appendChild(popup);

	document.body.appendChild(modalparent);

	return popup;
}


// from default.php -----------------------------------------------------------------------
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
		//document.getElementById(DivId).innerHTML = AJAX.responseText;

		var myObject = eval('(' + AJAX.responseText + ')');

		var cid = myObject.cid;
		var cart_item_price = eval('myObject.cart_item_price'+cid);
		var cart_item_total = eval('myObject.cart_item_total'+cid);

		document.getElementById('cart_item_price'+cid).innerHTML = cart_item_price;
		document.getElementById('cart_item_total'+cid).innerHTML = cart_item_total;
		document.getElementById('cart_total').innerHTML = myObject.cart_total;
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
	var url = "index.php?option=com_digicom&view=cart&task=getCartItem&cid="+item_id;

	var plan_id = document.getElementById('plan_id'+item_id);
	var plan_query = '';
	if ( plan_id.selectedIndex != -1)
	{
		var plan_value = plan_id.options[plan_id.selectedIndex].value;
		plan_query += '&plan_id='+plan_value;
	} else {
		return false;
	}

	url += plan_query;

	var qty = document.getElementById('quantity'+item_id);
	var qty_query = '';
	if ( qty.selectedIndex != -1)
	{
		var qty_value = qty.options[qty.selectedIndex].value;
		qty_query += '&quantity'+item_id+'='+qty_value;
	}

	url += qty_query;

	var attrs_query = '';
	for (var i = 1; i < 11; i++) {
		if ( document.getElementById('attributes'+item_id+''+i) ) {

			var attr = document.getElementById('attributes'+item_id+''+i);

			if ( attr.selectedIndex != -1)
			{
				var value = attr.options[attr.selectedIndex].value;
				attrs_query += '&attributes['+item_id+']['+i+']='+value;
			}

		} else break;
	}

	url += attrs_query;

	ajaxRequest(url, 'debugid');
}

function grayBoxiJoomla(link_element, width, height){
	SqueezeBox.initialize({
		size: {x: width, y: height}
	});
	SqueezeBox.open(link_element);
}

function changeImage(position, pid, width, height){
	var url = 'index.php?option=com_digicom&view=products&task=previwimage&tmpl=component&position='+position+'&pid='+pid;

	var req = new Request.HTML({
		method: 'get',
		url: url,
		data: { 'do' : '1' },
		onComplete: function(responseTree, responseElements, responseHTML, responseJavaScript){
			document.getElementById('all_layout').innerHTML = responseHTML;
			window.parent.changeGrayBoxSize(width, height);
		}
	}).send();
}

function refresCartModule(){
	var url = 'index.php?option=com_digicom&view=cart&task=get_cart_content';
	if( document.getElementById('mod_digicom_cart_wrap') ) {
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