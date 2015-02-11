<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 432 $
 * @lastmodified	$LastChangedDate: 2013-11-18 04:29:45 +0100 (Mon, 18 Nov 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

defined ('_JEXEC') or die ("Go away.");

global $isJ25; 
if($isJ25) {
	JHTML::_('behavior.mootools');
} else {
	JHtml::_('jquery.framework');
};

JHTML::_('behavior.modal');

$user = JFactory::getUser();
$configs = $this->configs;

echo '<div class="digicom" style="' . ($configs->get('shopping_cart_style','') ? 'width: 70%;margin-left: auto;margin-right: auto;padding: 20px;background: #fff;' : '') . '">';

$button_value = "DSCHECKOUTE";
$onclick = "document.getElementById('returnpage').value='checkout'; document.getElementById('type_button').value='checkout';";

if($user->id == 0 || $this->customer->_customer->country == "")
{
	$button_value = "DSSAVEPROFILE";
	$onclick = "document.getElementById(\'returnpage\').value=\'login_register\'; document.getElementById(\'type_button\').value=\'checkout\';";
}

if($configs->get('askterms',0) == '1')
{
	$onclick= "if(document.cart_form.agreeterms.checked != true){ alert(\'".JText::_("ACCEPT_TERMS_CONDITIONS")."\'); return false; }".$onclick;
}

$agreeterms = JRequest::getVar("agreeterms", "");
$processor = JRequest::getVar("processor", "");

$document=JFactory::getDocument();
$url="index.php?option=com_digicom&controller=cart&task=gethtml&tmpl=component&format=raw&processor=";
/*$ajax="jQuery(document).ready(function()
{
	jQuery('#processor').change(function()
	{
		var url1='".$url."'+jQuery('#processor').val();
		jQuery('#html-container').empty();
	 	jQuery.ajax(
	 	{
			url:url1,
			type:'GET',
			dataType:'html',
			success:function(response){
				//button = '<input type=\"submit\" name=\"Submit\" class=\"btn\" value=\"".JText::_($button_value)."\" onClick=\"".$onclick."\">';
				//jQuery('#html-container').html(button+response);
				jQuery('#html-container').html(response);
			}
		});
	});
});";
$document->addScriptDeclaration($ajax);*/

if($configs->get('shopping_cart_style','') == "1")
{
	JRequest::setVar("tmpl", "component");
}

$Itemid = JRequest::getInt("Itemid", 0);
$this->customer = $this->customer;
$items = $this->items;

$total = 0;//$this->total;//0;
$totalfields = $this->totalfields;//0;
$optlen = $this->optlen;//array();

$discount = $this->discount;//0;

$lists = $this->lists;
$cat_url = $this->cat_url;
$totalfields = 0;
$shippingexists = 0;
$from = JRequest::getVar("from", "");
$nr_columns = 4;

if($from == "ajax"){
	require_once(JPATH_SITE.DS."components".DS."com_digicom".DS."views".DS."cart".DS."tmpl".DS."popup.php"); 
} else {
	foreach ($items as $itemnum => $item) {
		if ($itemnum < 0) continue;
		if ($item->domainrequired == 2) $shippingexists++;
		if (!empty($item->productfields)) {
			foreach ($item->productfields as $field) {
				$totalfields += count ($field);
			}
		}
	}
	
	$invisible = 'style="display:none;"';
	
	?>
	
	<script language="javascript" type="text/javascript">
		function checkIfIframe(){
			if(top !== self){
				var fileref=document.createElement("link");
				fileref.setAttribute("rel", "stylesheet");
				fileref.setAttribute("type", "text/css");
				fileref.setAttribute("href", "/media/digicom/assets/css/changegrayposition.css");
				if (typeof fileref!="undefined"){
					document.getElementsByTagName("head")[0].appendChild(fileref);
				}
			}
		}
		checkIfIframe();
	</script>
	
	<script language="javascript" type="text/javascript">
			function cartformsubmit(){
				
				<?php
					if($user->id == "0"){
				?>
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
									jQuery("#myModalLabel").html("<?php echo JText::_("DIGI_ATENTION");?>");
									jQuery("#myModalBody").html("<p><?php echo JText::_("DSALL_REQUIRED_FIELDS");?></p>");
									jQuery('#myModal').modal('show');
									return false;
								}
							
								if(document.cart_form.password.value != document.cart_form.password_confirm.value) {
									//alert("<?php echo JText::_("DSCONFIRM_PASSWORD_MSG"); ?>");
									jQuery("#myModalLabel").html("<?php echo JText::_("DIGI_ATENTION");?>");
									jQuery("#myModalBody").html("<p><?php echo JText::_("DSCONFIRM_PASSWORD_MSG");?></p>");
									jQuery('#myModal').modal('show');
									return false;
								}
								if (!isEmail(document.cart_form.email.value)){
									//alert('<?php echo JText::_("DSINVALID_EMAIL"); ?>');
									jQuery("#myModalLabel").html("<?php echo JText::_("DIGI_ATENTION");?>");
									jQuery("#myModalBody").html("<p><?php echo JText::_("DSINVALID_EMAIL");?></p>");
									jQuery('#myModal').modal('show');
									return false;
								}
								if (!validateUSZip(document.cart_form.zipcode.value)){
									//alert("Invalid zipcode");
									//return false;
								}
							}
							
							<?php
							if($configs->get('askterms',0) == '1')
							{ ?>
							   if(document.cart_form.agreeterms.checked != true){
								   //alert('<?php echo JText::_("ACCEPT_TERMS_CONDITIONS"); ?>');
								   jQuery('#myModalLabel').html('<?php echo JText::_("DIGI_ATENTION");?>');
								   jQuery('#myModalBody').html("<p><?php echo JText::_("ACCEPT_TERMS_CONDITIONS");?></p>");
								   jQuery('#myModal').modal('show');
								   return false;
							   }<?php
							} ?>
							
						}	
				<?php
					}
				?>
				if (!checkSelectedPlain()) return false;
	
				var mandatory = new Object();
				var i,j;
	<?php
	
	
		foreach ($items as $j => $v) {
			if ($j < 0 ) continue;
			echo "mandatory[".$v->cid."] = new Object();";
			if (!empty($v->productfields))
				foreach ($v->productfields as $ii => $field) {
					echo "mandatory[".$v->cid."][".$ii."] = new Object();";
					echo "mandatory[".$v->cid."][".$ii."]['fld'] = '".$field->id."';\n";
					echo ($field->mandatory == 1)?"mandatory[".$v->cid."][".$ii."]['req']=1;\n":"mandatory[".$v->cid."][".$ii."]['req']=0;\n";
				}
		}
	?>
			for (i in mandatory) {
				for (j in mandatory[i]){
					if (mandatory[i][j]['req'] == 1) {
						var el = document.getElementById("attributes" + i +mandatory[i][j]['fld']);
						if(el){
							if (el.selectedIndex < 1) {
								alert ("<?php echo JText::_("DSSELECTALLREQ"); ?>");
								return false;
							}
						}
					}
				}
			}
			return true;
		}
	
		function checkSelectedPlain() {
	
	<?php
			foreach ($items as $key => $item) :
				if ($key < 0 ) continue;
	?>
			plan_id<?php echo $item->cid;?> = document.getElementById('plan_id<?php echo $item->cid;?>');
			if (plan_id<?php echo $item->cid;?>.value == -1) {
				alert('Please select plan for <?php echo addcslashes($item->name,"'"); ?>');
				plan_id<?php echo $item->cid;?>.focus();
				return false;
			}
	<?php
			endforeach;
	?>
			return true;
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
			var url = "<?php JUri::root();?>index.php?option=com_digicom&controller=cart&task=getCartItem&cid="+item_id;
	
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

			var promocode = document.getElementById('promocode');
			var promocode_query = '&promocode='+promocode.value;
			url += promocode_query;
	
			var qty = document.getElementById('quantity'+item_id);
			var qty_value = qty.value;
			var qty_query = '';
			qty_query += '&quantity'+item_id+'='+qty_value;
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
	
		function refresCartModule(){
			if(document.getElementById('mod_digicom_cart_wrap')){
				var url = 'index.php?option=com_digicom&controller=cart&task=get_cart_content';
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
	</script>
	
	<div id="debugid"></div>
	
	
	<?php
		$formlink = JRoute::_("index.php?option=com_digicom&controller=cart");
		$form_style = 'style="';
		if($configs->get('cart_alignment',0) == "0"){
			$form_style .= "float:left; ";
		}
		elseif($configs->get('cart_alignment',0) == "2"){
			$form_style .= "float:right; ";
		}
		$form_style .= '"';
	?>
	
	<?php
		if($configs->get('shopping_cart_style','') == "1"){
	?>
			<style type="text/css">
				h2{
					color: #646566;
					font-size: 20px;
					line-height: 20px;
					margin-top: 25px !important;
					font-weight: normal;
					margin-bottom: 10px !important;
				}
			</style>
	<?php	
		}
	?>
	
	<form <?php echo $form_style; ?> id="cart_form" name="cart_form" method="post" action="<?php echo $formlink?>" onSubmit="return cartformsubmit();">
	
	<!-- New Cart -->
	
	<?php
		$k = 1;
		$table_style = 'style="';
		if(trim($configs->get('cart_width','')) != "" || trim($configs->get('cart_width','')) != "0"){
			$cart_width_type = $configs->get('cart_width_type','');
			$format = "px";
			if($cart_width_type == 0){
				$format = "px";
			}
			else{
				$format = "%";
			}
			$table_style .= 'width:'.intval($configs->get('cart_width','')).$format."; ";
		}
		if($configs->get('cart_alignment',0) == "1"){
			$table_style .= "margin:auto; ";
		}
		$table_style .= '"';
	?>

<?php
	if($configs->get('show_steps',1) == 1){
?>
		<div class="bar">
			<span class="active-step">
			<?php
				echo JText::_("DIGI_STEP_ONE");
			?>
			</span>
			
			<span class="inactive-step">
			<?php
				echo JText::_("DIGI_STEP_TWO");
			?>
			</span>
			
			<span class="inactive-step">
			<?php
				echo JText::_("DIGI_STEP_THREE");
			?>
			</span>
		</div>
<?php
	}
?>

	
	<div class="row-fluid">
	<?php
		if(trim($configs->get('store_logo','')) != "" && $configs->get('shopping_cart_style','') == "1"){
	?>
			<div class="span6">
				<a href="<?php echo JURI::root(); ?>">
					<img src="<?php echo JURI::root()."images/stories/digicom/store_logo/".trim($configs->get('store_logo','')); ?>" alt="store_logo" border="0">
				</a>
			</div><?php
			$user = JFactory::getUser();
			if($user->id != "0"){ ?>
				<div class="span6" style="text-align:right;vertical-align:bottom;">
					<?php echo JText::_("DIGI_LOGGED_IN_AS")." ".$user->name; ?>
				</div>
			<?php }
		} elseif($configs->get('shopping_cart_style','') == "0"){ ?>
			<div class="span6">
				<h1><?php echo JText::_("DIGI_MY_CART"); ?></h1>
			</div><?php
			$user = JFactory::getUser();
			if($user->id != "0"){ ?>
				<div class="span6" style="text-align:right;vertical-align:bottom;">
					<?php echo JText::_("DIGI_LOGGED_IN_AS")." ".$user->name; ?>
				</div>
			<?php }
		}

		$tax = $this->tax; ?>
	</div>

		<table id="digicomcarttable" class="table table-striped table-bordered" width="100%">
		<thead>
		<tr valign="top">
			<th width="30%">
				<?php echo JText::_("DSPROD");?>
			</th>
			<th>
				<?php echo JText::_("DSPRICEPLAN");?>
			</th>
			<th <?php if ($configs->get('showcam',1) == 0){echo $invisible; $nr_columns --;}?> >
				<?php echo JText::_("DSQUANTITY"); ?>
			</th><?php
			if ($tax['discount_calculated']){?>
				<th>
					<?php echo JText::_("DSPROMODISCOUNT"); ?>
				</th><?php
			}
			if($shippingexists > 0){?>
				<th>
					<?php
						$nr_columns ++;
						echo JText::_("DSSHIPING");
					?>
				</th><?php
			}
			?>
			<th><?php echo JText::_("DSSUBTOTAL");?></th>
			<th <?php if($configs->get('showcremove',1) == 0){ echo $invisible; $nr_columns --;}?> ></th>
		</tr>
		</thead>
			<tbody>
	<?php
	
	$k++;
	
	if(count($items) == 0){
	
		$formlink = JRoute::_("index.php?option=com_digicom"."&Itemid=".$Itemid);
		$redirect_url = DigiComHelper::DisplayContinueUrl($configs, $cat_url);
	?>
				<tr><td colspan="5"><?php echo JText::_("DIGI_CART_IS_EMPTY"); ?>. <a href="<?php echo $redirect_url; ?>"><?php echo JText::_("DIGI_CLICK_HERE"); ?></a></td></tr>
	<?php
	} else {
		foreach($items as $itemnum => $item ){
			$renew = "";
			if(isset($item) && isset($item->renew) && $item->renew == "1"){
				$renew = "&nbsp;&nbsp;&nbsp;(".JText::_("DIGI_RENEWAL").")";
			}
			if($itemnum < 0){
				continue;
			}
		?>
			<tr>
				<!-- Product name -->
				<td>
					<?php 
					$item_link = JRoute::_('index.php?option=com_digicom&view=products&cid='.$item->catid.'&pid='.$item->id);
					echo '<a href="'.$item_link.'" target="blank">'.$item->name.$renew.'</a>';
					?>
				</td>
				<!-- /End Product name -->

				<td nowrap="nowrap">
							<?php echo $item->plans_select; ?>
				</td>

				<!-- Quantity -->
				<td align="center" <?php if ($configs->get('showcam',1) == 0) echo $invisible;?> nowrap="nowrap">
							<span class="digicom_details">
								<strong>
									<?php  if ( !isset( $item->noupdate) ) { ?>
										<?php // echo $lists[$item->cid]['quantity']; ?>
										
										<input id="quantity<?php echo $item->cid; ?>" type="number" onchange="update_cart(<?php echo $item->cid; ?>);" name="quantity[<?php echo $item->cid; ?>]" min="1" class="input-small" value="1" size="2" placeholder="<?php echo JText::_('DSQUANTITY'); ?>">
									<?php } else {
										echo $item->quantity;
									}?>
								</strong>
							</span>
				</td>
				<!-- /End Quantity -->

				<!-- Price -->
				<td style="display:none;" nowrap="nowrap">
					<span class="digi_cart_amount" id="cart_item_price<?php echo $item->cid; ?>"><?php echo DigiComHelper::format_price2($item->price, $item->currency, true, $configs); ?></span>
				</td>
				<!-- /End Price -->

				<!-- Discount -->
				<td style="text-align:center;<?php if(!$tax['discount_calculated']) echo 'display:none;'?>" nowrap="nowrap">
					<span id="cart_item_discount<?php echo $item->cid; ?>" class="digi_cart_amount">
						<?php
						$value_discount = 0;
						if ( $item->discount > 0)
						{
							$value_discount = $item->discount;
						}
						elseif ( isset($item->percent_discount) && $item->percent_discount > 0)
						{
							$value_discount = ($item->price * $item->percent_discount) / 100;
						}
						echo (isset($item->percent_discount) && $item->percent_discount > 0) ? $item->percent_discount : DigiComHelper::format_price2($item->discount, $item->currency, true, $configs);;?>
					</span>
				</td>
				<!-- /End Discount -->

				<!-- Shipping -->
				<td nowrap="nowrap" <?php echo ($shippingexists >0 ) ? "style='text-align:center;'" : 'style="text-align:center; display:none;"';?>><span class="digi_cart_amount"><?php
				$lvs = DigiComHelper::getLiveSite();
				if($configs->shipping_price == 1){
					$item->shipping += $item->itemtax;
				}
				if($item->domainrequired == 2){
					$shipping_value = (isset($item->shipping) && $item->domainrequired==2 ? DigiComHelper::format_price2($item->shipping, $item->currency, true, $configs) : "N/A");
					if($shipping_value != "N/A"){
						echo $shipping_value;
					}
				}
				?></span></td>
				<!-- /End Shipping -->

				<!-- Attribute -->
				<?php if ($totalfields > 0  ) { ?>
				<td nowrap="nowrap" <?php echo ($k%2) ? 'class="digi_alt"' : ''; ?> style="text-align:right;" nowrap="nowrap"><?php echo $lists[$item->cid]['attribs']; ?></td>
				<?php } ?>
				<!-- /End Attribute -->

				<!-- Total -->
				<td nowrap style="text-align:center;">
					<span id="cart_item_total<?php echo $item->cid; ?>" class="digi_cart_amount"><?php
						echo DigiComHelper::format_price2($item->subtotal-$value_discount, $item->currency, true, $configs); ?>
					</span>
				</td>
				<!-- /End Total -->

				<!-- Remove -->
				<td style="text-align:center; <?php if ($configs->get('showcremove',1) == 0) echo "display:none;";?>" nowrap="nowrap">
					<a href="javascript:;" class="btn btn-small btn-danger" onclick="RemoveFromCart(<?php echo $item->cid;?>);"><i class="icon-trash icon-white"></i></a>
				</td>
				<!-- /End Remove -->
			</tr>
		<?php
			$total += $item->subtotal;
			$k++;
		}
	}
	?>
			</tbody>
	</table>
	<table id="digicomcartpromo" width="100%">
		<tr valign="top">
			<td class="general_text" colspan="<?php echo $nr_columns - 1; ?>" valign="bottom">
				<?php
					echo JText::_("DIGI_IF_PROMOCODE");
				?>
			</td>
			
			<?php
				$border_bottom = "";
				if($this->customer->_user->id > 0){
					//$border_bottom = 'border-bottom:1px solid #CCCCCC !important;';
				}
			?>
			
			<td nowrap="nowrap" style="text-align: center; <?php echo $border_bottom; ?> padding-top:15px;">
				<ul style="margin: 0; padding: 0;list-style-type: none;">
					<?php if ($configs->get('tax_summary',0) == 1) { ?>
	
					<?php if ($tax['promo'] > 0 && $tax['promoaftertax'] == '0'): ?>
					<li class="digi_cart_total"><?php echo JText::_("DSPROMODISCOUNT"); ?></li>
					<?php endif; ?>
	
					<?php  if (($tax['value'] > 0) || ($configs->get('tax_zero',1) == 1) && ($this->customer->_user->id > 0)) : ?>
					<li class="digi_cart_total"><?php echo $tax['type']; ?></li>
					<?php endif; ?>
	
					<?php  if ($tax['shipping'] > 0 && $this->customer->_user->id > 0): ?>
					<li class="digi_cart_total"><?php echo JText::_("DSSHIPING"); ?></li>
					<?php endif; ?>
	
					<?php if ($tax['promo'] > 0 && $tax['promoaftertax'] == '1'): ?>
					<li class="digi_cart_total"><?php echo JText::_("DSPROMOCODEDISCOUNT"); ?></li>
					<?php endif; ?>
	
					<?php }	?>
				</ul>
			</td>
			<?php if ($configs->get('tax_summary',0) == 1) { ?>
			<td nowrap="nowrap" style="text-align: center; <?php echo $border_bottom; ?> padding-top:15px;">
				<ul style="margin: 0; padding: 0;list-style-type: none;" >
					<?php if ($tax['promo'] > 0 && $tax['promoaftertax'] == '0') : ?>
					<li class="digi_cart_amount" style="text-align:right;" id="digicom_cart_discount"><?php echo DigiComHelper::format_price2($tax['promo'], $tax['currency'], true, $configs) ?></li>
					<?php endif;?>

					<?php if (($tax['value'] > 0 || $configs->get('tax_zero',1) == 1) && $this->customer->_user->id > 0) : ?>
					<li class="digi_cart_amount" style="text-align:right;" id="digicom_cart_tax"><?php echo DigiComHelper::format_price2($tax['value'], $tax['currency'], true, $configs); ?></li>
					<?php endif; ?>

					<?php if ($tax['shipping'] > 0 && $this->customer->_user->id > 0) : ?>
					<li class="digi_cart_amount" style="text-align:right;"><?php echo DigiComHelper::format_price2($tax['shipping'], $tax['currency'], true, $configs); ?></li>
					<?php endif; ?>

					<?php if ($tax['promo'] > 0 && $tax['promoaftertax'] == '1') : ?>
						<li class="digi_cart_amount" style="text-align:right;"><?php echo DigiComHelper::format_price2($tax['promo'], $tax['currency'], true, $configs); ?></li>
					<?php endif; ?>
				</ul>
			</td>
			<?php } else { ?>
			<td>&nbsp;</td>
			<?php } ?>
		</tr>
		
		<tr valign="top">
			<td colspan="<?php echo $nr_columns - 1; ?>" >
				<div class="input-append">
					<input type="text" id="promocode" name="promocode" size="15" value="<?php echo $this->promocode; ?>" />
					<button type="submit" class="btn" onclick="document.getElementById('returnpage').value=''; document.getElementById('type_button').value='recalculate';"><i class="ico-gift"></i> <?php echo JText::_("DIGI_RECALCULATE"); ?></button>
				</div>
				<span class="digi_error">
					<?php echo $this->promoerror; ?>
					<?php if($tax['promo'] <= 0 && $this->promocode != ''):?><br /><?php echo JText::_('DIGI_PROMO_NO_ACCESS');?><?php endif;?>
				</span>
			</td>
			<td nowrap="nowrap" style="text-align: center;">
				<ul style="margin: 0; padding: 0;list-style-type: none;">
					<li class="digi_cart_total" style="font-weight: bold;font-size: 18px;text-align:right;"><?php echo JText::_("DSTOTAL");?></li>
				</ul>
			</td>
			<td nowrap="nowrap" style="text-align: center;">
				<ul style="margin: 0; padding: 0;list-style-type: none;">
					<li class="digi_cart_amount" id="cart_total" style="color:green;font-weight: bold;font-size: 18px;text-align:right;"><?php echo DigiComHelper::format_price2($tax['taxed'], $tax['currency'], true, $configs); ?></li>
				</ul>
			</td>
		</tr>
		<?php $k++; ?>
	</table>

	<?php if($configs->get('askterms',0) == '1'):?>
		<div class="row-fluid">
			<input type="checkbox" name="agreeterms" id="agreeterms" /><?php
			$db = JFactory::getDBO();
			$sql = "select `alias`, `catid`, `introtext`
							from #__content
							where id=".intval($configs->termsid);
			$db->setQuery($sql);
			$db->query();
			$result = $db->loadAssocList();
			$terms_content = $result["0"]["introtext"];
			$alias = $result["0"]["alias"];
			$catid = $result["0"]["catid"]; ?>
			<a href="javascript:;" onclick="jQuery('#myModalTerms').modal('show');"><?php echo JText::_("AGREE_TERMS"); ?></a>
		</div>
	<?php endif;?>

	<?php if($configs->get('showccont',0) == 0){ ?>
		<div id="digicomcartcontinue" class="row-fluid">
			<div class="span6">
				<?php
				echo JText::_("DIGI_PAYMENT_METHOD").": ".$this->lists['plugins'];
				$onclick = "document.getElementById('returnpage').value='checkout'; document.getElementById('type_button').value='checkout';";
				if($user->id == 0 || $this->customer->_customer->country == ""){
					$onclick = "document.getElementById('returnpage').value='login_register'; document.getElementById('type_button').value='checkout';";
				} ?>
				<input type="submit" name="Submit" class="btn btn-warning" value="<?php echo JText::_("DSCHECKOUTE");?>" onClick="<?php echo $onclick; ?>">
			</div>
			<div class="span6" <?php if ($discount!=1) echo 'style="display:none"'?>>&nbsp;</div>
		</div>
	<?php } else { ?>
		<div id="digicomcartcontinue" class="row-fluid">
			<div class="span6" style="margin-bottom:10px;">
				<button type="button" class="btn" onclick="window.location='<?php echo DigiComHelper::DisplayContinueUrl($configs, $cat_url); ?>';" ><i class="ico-shopping-cart"></i> <?php echo JText::_("DSCONTINUESHOPING")?></button>
			</div>
			<div class="span6" style="margin-bottom:10px;">
				<?php
				$button_value = "DSCHECKOUTE";
				$onclick = "if(jQuery('#processor').val() == ''){ ShowPaymentAlert(); return false; }";
				$onclick.= "jQuery('#returnpage').val('checkout'); jQuery('#type_button').val('checkout');";

				if($user->id == 0 || $this->customer->_customer->country == "")
				{
					$button_value = "DSSAVEPROFILE";
					$onclick.= "jQuery('#returnpage').val('login_register'); jQuery('#type_button').val('checkout');";
				}

				if($configs->get('askterms',0) == '1')
				{
					$onclick.= "if(ShowTermsAlert()) {" . $onclick . " jQuery('#cart_form').submit(); }else{ return false; }";
				}
				else
				{
					$onclick.= "jQuery('#cart_form').submit();";
				} ?>

				<?php echo MostraFormPagamento($configs); ?>
				<div id="html-container" style="float:right;width:100%;"></div>
				<button type="button" class="btn btn-warning" style="float:right;margin-left:10px;" onclick="<?php echo $onclick; ?> "><?php echo JText::_($button_value);?> <i class="ico-ok-sign"></i></button>
			</div>
		</div>
	<?php } ?>


	<input name="controller" type="hidden" id="controller" value="Cart">
	<input name="task" type="hidden" id="task" value="updateCart">
	<input name="returnpage" type="hidden" id="returnpage" value="">
	<input name="type_button" type="hidden" id="type_button" value="">
	<input name="Itemid" type="hidden" value="<?php echo $Itemid; ?>">
	</form><?php

	/*if($configs->get('shopping_cart_style','') == "0"){
		echo DigiComHelper::powered_by();
	}*/
}

function MostraFormPagamento($configs){

	$db = JFactory::getDBO();

	$condtion = array(0 => '\'payment\'');
	$condtionatype = join(',',$condtion);
	if(JVERSION >= '1.6.0')
	{
		$query = "SELECT extension_id as id,name,element,enabled as published
				  FROM #__extensions
			  	  WHERE folder in ($condtionatype) AND enabled=1";
	}
	else
	{
		$query = "SELECT id,name,element,published
				  FROM #__plugins
				  WHERE folder in ($condtionatype) AND published=1";
	}
	$db->setQuery($query);
	$gatewayplugin = $db->loadobjectList();

	$lang = JFactory::getLanguage();
	$options = array();
	$options[] = JHTML::_('select.option', '', 'Select payment gateway');
	foreach($gatewayplugin as $gateway)
	{
		$gatewayname = strtoupper(str_replace('plugpayment', '',$gateway->element));
		$lang->load('plg_payment_' . strtolower($gatewayname), JPATH_ADMINISTRATOR);
		$options[] = JHTML::_('select.option',$gateway->element, JText::_($gatewayname));
	}

	return JHTML::_('select.genericlist', $options, 'processor', 'class="inputbox required" style="float:right;"', 'value', 'text', $configs->default_payment, 'processor' );

}
?>

	<?php if(isset($tax) && $tax['promo_error'] != ''):?>
		<div id="digicart_login" style="width:350px;left:50%;top:30%;position:fixed;z-index:1000;background:#eee;margin-left:-175px;">
			<div id="cart_header" style="background-color: rgb(204, 204, 204);">
				<table width="100%" style="font-size:12px;">
					<tbody>
					<tr>
						<td width="80%" align="left">
							&nbsp;<?php echo JText::_("Login");?>
						</td>
						<td align="right">
							<a onclick="javascript:closePopupLogin('digicart_login'); return false;" class="close_btn" href="#">&nbsp;</a>
						</td>
					</tr>
					</tbody>
				</table>
			</div>
			<div id="cart_body">
				<div style="padding:10px;"><span class="digi_error"><?php echo $tax['promo_error']; ?></span></div>
				<form id="dslogin" name="dslogin" method="post" action="index.php">
					<table width="100%" id="digilistitems" style="font-size:12px;">
						<tbody>
						<tr style="padding-bottom:3px;">
							<td style="padding-left:5px;">
								<?php echo JText::_("Username");?>
							</td>
							<td style="padding-left:5px; width:150px; text-align:left;" class="digicom_product_name">
								<input type="text" id="dsusername" name="username" style="width:150px;" />
							</td>
						</tr>
						 <tr style="padding-bottom:3px;">
							<td style="padding-left:5px;">
								<?php echo JText::_("Password");?>
							</td>
							<td style="padding-left:5px; width:150px; text-align:left;" class="digicom_product_name">
								<input type="password" id="dspassword" name="password" style="width:150px;" />
							</td>
						</tr>
						<tr>
							<td></td>
							<td>
								<a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>">
									<?php echo JText::_('LOGIN_FORGOT_YOUR_PASSWORD'); ?></a>
								<br />
								<a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>">
									<?php echo JText::_('LOGIN_FORGOT_YOUR_USERNAME'); ?></a>
							</td>
						</tr>
						</tbody>
					</table>

					<input type="hidden" name="option" value="com_users"/>
					<input type="hidden" name="Itemid" value="<?php echo JRequest::getInt('Itemid', 0);?>"/>
					<input type="hidden" name="task" value="user.login"/>
					<input type="hidden" name="return"
						   value="<?php echo base64_encode('index.php?option=com_digicom&controller=cart&task=showCart&Itemid=' . JRequest::getInt('Itemid', 0)); ?>"/>
					<?php echo JHTML::_('form.token'); ?>
				</form>
			</div>
			<div id="cart_futter" style="background-color: rgb(204, 204, 204);">
				<table width="100%">
					<tbody>
					<tr>
						<td width="100%">
							<table width="100%">
								<tbody>
								<tr>
									<td width="60%" align="left"><input type="button" class="btn" onclick="javascript:closePopupLogin('digicart_login'); return false;" value=" Cancel " name="Submit1" style="padding:0px !important;"></td>
									<td width="40%" align="right"><input type="button" class="btn btn-warning" onclick="document.dslogin.submit();" value="Login" name="Submit"></td>
								</tr>
								</tbody>
							</table>
						</td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>

		<script>
		function closePopupLogin(div) {
			if(document.getElementById(div)){
				for_delete = document.getElementById(div);
				for_delete.parentNode.removeChild(for_delete);
			}
		}
		</script>
	<?php endif;?>

	<div id="myModal" class="modal" style="display:none;">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3 id="myModalLabel">...</h3>
		</div>
		<div id="myModalBody" class="modal-body">

		</div>
		<div id="myModalFooter" class="modal-footer">
			<button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_("DIGI_CLOSE");?></button>
		</div>
	</div>

	<?php if($configs->get('askterms',0) == '1'):?>
	<div id="myModalTerms" class="modal" style="display:none;">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<h3><?php echo JText::_("DIGI_TERMS");?></h3>
		</div>
		<div class="modal-body">
			<?php echo $terms_content;?>
		</div>
		<div class="modal-footer">
			<button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_("DIGI_CLOSE");?></button>
		</div>
	</div>
	<?php endif;?>

</div>

<script>
<?php
if ($agreeterms != '')
{
	echo 'jQuery("#agreeterms").attr("checked","checked");';
}
if ($processor != '')
{
	echo 'jQuery("#processor").val("' . $processor . '");';
}
?>
function ShowTermsAlert()
{
	if (document.cart_form.agreeterms.checked != true)
	{
		jQuery('#myModalLabel').html("<?php echo JText::_("DIGI_ATENTION");?>");
		jQuery('#myModalBody').html("<p><?php echo JText::_("ACCEPT_TERMS_CONDITIONS");?></p>");
		jQuery('#myModal').modal('show');
		return false;
	}
	else
	{
		return true;
	}
}
function ShowPaymentAlert()
{
	jQuery('#myModalLabel').html("<?php echo JText::_("DIGI_ATENTION");?>");
	jQuery('#myModalBody').html("<p><?php echo JText::_("DIGI_PAYMENT_REQUIRED");?></p>");
	jQuery('#myModal').modal('show');
}

function RemoveFromCart(CARTID)
{
	window.location = "<?php echo JURI::root();?>index.php?option=com_digicom&controller=cart&task=deleteFromCart&cartid="+CARTID+"<?php echo (isset($item->discount1)?('&discount=1&noupdate='.(isset($item->noupdate)?$item->noupdate:'').'&qty='.$item->quantity ):"" )."&Itemid=".$Itemid;?>&processor="+jQuery("#processor").val()+"&agreeterms="+jQuery("#agreeterms").val();
}

if(jQuery(window).width() > jQuery("#digicomcarttable").width() && jQuery(window).width() < 550)
{
	jQuery(".digicom table select").css("width", (jQuery("#digicomcarttable").width()-30)+"px");
}
</script>
