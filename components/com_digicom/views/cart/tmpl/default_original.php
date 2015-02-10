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

 global $isJ25; if($isJ25) JHTML::_('behavior.mootools');
JHTML::_('behavior.modal');

$configs = $this->configs;

if($configs->get('shopping_cart_style','') == "1"){
	JRequest::setVar("tmpl", "component");
}

global $Itemid;
$customer = $this->customer;
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
	require_once(JPATH_SITE.DS."components".DS."com_digicom".DS."views".DS."digicomcart".DS."tmpl".DS."popup.php"); 
}
else{
	foreach ($items as $itemnum => $item) {
		if ($itemnum < 0) continue;
		if ($item->domainrequired == 2) $shippingexists++;
		if (!empty($item->productfields))
			foreach ($item->productfields as $field) {
				$totalfields += count ($field);
			}
	}

	$invisible = 'style="display:none;"';
	if(count($items) == 0){

		$formlink = JRoute::_("index.php?option=com_digicom"."&Itemid=".$Itemid);
		$redirect_url = DigiComHelper::DisplayContinueUrl($configs, $cat_url);
	?>
		<?php echo JText::_("DIGI_CART_IS_EMPTY"); ?>. <a href="<?php echo $redirect_url; ?>"><?php echo JText::_("DIGI_CLICK_HERE"); ?>.</a>
	<?php
		return;
	}
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
					$user = JFactory::getUser();
					if($user->id == "0"){
				?>
						type_button_value = document.cart_form.type_button.value;
						if(type_button_value == "checkout"){
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
								alert('<?php echo JText::_("DSALL_REQUIRED_FIELDS"); ?>');
								return false;
							}

							if(document.cart_form.password.value != document.cart_form.password_confirm.value) {
								alert("<?php echo JText::_("DSCONFIRM_PASSWORD_MSG"); ?>");
								return false;
							}
							if (!isEmail(document.cart_form.email.value)){
								alert('<?php echo JText::_("DSINVALID_EMAIL"); ?>');
								return false;
							}
							if (!validateUSZip(document.cart_form.zipcode.value)){
								//alert("Invalid zipcode");
								//return false;
							}

							<?php
								if($configs->get('askterms',0) == '1'){
							?>
							   if(document.cart_form.agreeterms.checked != true){
								   alert('<?php echo JText::_("ACCEPT_TERMS_CONDITIONS"); ?>');
								   return false;
							   }
							<?php
								}
							?>

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
						var el = document.getElementById("attributes[" + i + "][" +mandatory[i][j]['fld'] +"]");
						if (el.selectedIndex < 1) {
							alert ("<?php echo JText::_("DSSELECTALLREQ"); ?>");
							return false;
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
				alert('Please select plan for <?php echo $item->name; ?>');
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

			document.getElementById('cart_item_price'+cid).innerHTML = cart_item_price;
			document.getElementById('cart_item_total'+cid).innerHTML = cart_item_total;
			document.getElementById('cart_total').innerHTML = myObject.cart_total;

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
			var url = "index.php?option=com_digicom&controller=cart&task=getCartItem&cid="+item_id;

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

	<form <?php echo $form_style; ?> name="cart_form" method="post" action="<?php echo $formlink?>" onSubmit="return cartformsubmit();">

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

	<table <?php echo $table_style; ?> >
	<?php
		if(trim($configs->get('store_logo','')) != "" && $configs->get('shopping_cart_style','') == "1"){
	?>
			<tr>
				<td>
					<a href="<?php echo JURI::root(); ?>">
						<img src="<?php echo JURI::root()."images/stories/digicom/store_logo/".trim($configs->get('store_logo','')); ?>" alt="store_logo" border="0">
					</a>
				</td>
				<?php
					$user = JFactory::getUser();
					if($user->id != "0"){
				?>
						<td width="50%" align="right" style="color:#000000;" valign="bottom">
							<?php
								echo JText::_("DIGI_LOGGED_IN_AS")." ".$user->name;
							?>
						</td>
				<?php
					}
				?>
			</tr>
	<?php
		}
		elseif($configs->get('shopping_cart_style','') == "0"){
	?>
			<tr>
				<td width="50%" align="left">
					<span style="font-size:24px; font-weight:bold;"><?php echo JText::_("DIGI_MY_CART"); ?></span>
				</td>
				<?php
					$user = JFactory::getUser();
					if($user->id != "0"){
				?>
						<td width="50%" align="right" style="color:#000000;" valign="bottom">
							<?php
								echo JText::_("DIGI_LOGGED_IN_AS")." ".$user->name;
							?>
						</td>
				<?php
					}
				?>
			</tr>
	<?php
		}
	?>

	<tr>
		<td width="100%" colspan="2">
	<table id="digi_table" cellspacing="0" <?php echo $table_style; ?>>
		<tr>
			<th width="20%">
			</th>
			<th>
				<?php echo JText::_("DSPRICEPLAN");?>
			</th>
			<th <?php if ($configs->get('showcam',1) == 0){echo $invisible; $nr_columns --;}?> >
				<?php echo JText::_("DSQUANTITY"); ?>
			</th>
			<?php
			if($shippingexists > 0){
			?>
			<th>
				<?php 
					$nr_columns ++;
					echo JText::_("DSSHIPING");
				?>
			</th>
			<?php
			}
			?>
			<th <?php if($configs->get('showcremove',1) == 0){ echo $invisible; $nr_columns --;}?> >
			</th>
			<th><?php echo JText::_("DSSUBTOTAL");?></th>
		</tr>
	<?php

	$k++;

	foreach($items as $itemnum => $item ){
		$renew = "";
		if(isset($item) && isset($item->renew) && $item->renew == "1"){
			$renew = "&nbsp;&nbsp;&nbsp;(".JText::_("DIGI_RENEWAL").")";
		}
		if($itemnum < 0){
			continue;
		}
	?>
		<tr class="item_row">
			<!-- Product name -->
			<td class="item_column">
				<ul>
					<li class="digicom_product_name"><?php echo $item->name.$renew; ?></li>
				</ul>
			</td>
			<!-- /End Product name -->

			<td nowrap="nowrap" class="item_column">
				<ul>
					<li class="digicom_details">
						<?php echo $item->plans_select; ?>
					</li>
				</ul>
			</td>

			<!-- Quantity -->
			<td align="center" <?php if ($configs->get('showcam',1) == 0) echo $invisible;?> nowrap="nowrap" class="item_column">
				<ul>
					<li class="digicom_product_name">
						<span class="digicom_details">
							<strong>
								<?php  if ( !isset( $item->noupdate) ) {
									echo $lists[$item->cid]['quantity'];
								} else {
									echo $item->quantity;
								}?>
							</strong>
						</span>
					</li>
				</ul>
			</td>
			<!-- /End Quantity -->

			<!-- Price -->
			<td style="display:none;" nowrap="nowrap" class="item_column">
				<span class="digi_cart_amount" id="cart_item_price<?php echo $item->cid; ?>"><?php echo DigiComHelper::format_price($item->price, $item->currency, true, $configs); ?></span>
			</td>
			<!-- /End Price -->

			<!-- Discount -->
			<td style=" <?php if($discount!=1) echo 'display:none;'?>" nowrap="nowrap" class="item_column">
				<span class="digi_cart_amount">
					<?php echo (isset($item->percent_discount)) ? $item->percent_discount : "N/A" ;?>
				</span>
			</td>
			<!-- /End Discount -->

			<!-- Shipping -->
			<td class="item_column" nowrap="nowrap" <?php echo ($shippingexists >0 ) ? "style='text-align:center;'" : 'style="text-align:center; display:none;"';?>><span class="digi_cart_amount"><?php
					$lvs = DigiComHelper::getLiveSite();
					if($configs->shipping_price == 1){
						$item->shipping += $item->itemtax;
					}
					if($item->domainrequired == 2){
						$shipping_value = (isset($item->shipping) && $item->domainrequired==2 ? DigiComHelper::format_price($item->shipping, $item->currency, true, $configs) : "N/A");
						if($shipping_value != "N/A"){
							echo $shipping_value;
						}
					}
			?></span></td>
			<!-- /End Shipping -->

			<!-- Remove -->
			<td class="item_column" style="text-align:center; <?php if ($configs->get('showcremove',1) == 0) echo "display:none;";?>" nowrap="nowrap">
				<?php
					$remove_link = "index.php?option=com_digicom&controller=cart&task=deleteFromCart&cartid=".$item->cid . (isset($item->discount1)?('&discount=1&noupdate='.(isset($item->noupdate)?$item->noupdate:'').'&qty='.$item->quantity ):"" )."&Itemid=".$Itemid;
				?>
				<!-- <input type="button" class="digi_remove_button" value="" onClick="window.location='<?php echo JRoute::_($remove_link); ?>';"> -->
				<img height="25" src="<?php echo JURI::root()."components/com_digicom/assets/images/icon_trash.png"; ?>" onClick="window.location='<?php echo JRoute::_($remove_link); ?>';">
			</td>
			<!-- /End Remove -->

			<!-- Attribute -->
			<?php if ($totalfields > 0  ) { ?>
			<td nowrap="nowrap" <?php echo ($k%2) ? 'class="digi_alt"' : ''; ?> style="text-align:right;" nowrap="nowrap"><?php echo $lists[$item->cid]['attribs']; ?></td>
			<?php } ?>
			<!-- /End Attribute -->

			<!-- Total -->
			<td class="item_column" nowrap style="text-align:center;" id="cart_item_total<?php echo $item->cid; ?>"><span class="digi_cart_amount"><?php
				echo DigiComHelper::format_price($item->subtotal, $item->currency, true, $configs);
			?></span></td>
			<!-- /End Total -->

		</tr>
	<?php
		$total += $item->subtotal;
		$k++;
	}
	?>

		<tr>
			<td colspan="<?php echo $nr_columns - 1; ?>" valign="bottom">
				<?php
					echo JText::_("DIGI_IF_PROMOCODE");
				?>
			</td>

			<?php
				$border_bottom = "";
				if($customer->_user->id > 0){
					$border_bottom = 'border-bottom:1px solid #CCCCCC !important;';
				}
			?>

			<td nowrap="nowrap" style="text-align: center; <?php echo $border_bottom; ?> padding-top:15px;">
				<?php $tax = $this->tax; ?>
				<ul style="margin: 0; padding: 0;">
					<?php if ($configs->tax_summary == 1) { ?>

					<?php if ($tax['promo'] > 0 && $tax['promoaftertax'] == '0'): ?>
					<li class="digi_cart_total"><?php echo JText::_("DSPROMODISCOUNT"); ?></li>
					<?php endif; ?>

					<?php  if (($tax['value'] > 0) || ($configs->get('tax_zero',1) == 1) && ($customer->_user->id > 0)) : ?>
					<li class="digi_cart_total"><?php echo $tax['type']; ?></li>
					<?php endif; ?>

					<?php  if ($tax['shipping'] > 0 && $customer->_user->id > 0): ?>
					<li class="digi_cart_total"><?php echo JText::_("DSSHIPING"); ?></li>
					<?php endif; ?>

					<?php if ($tax['promo'] > 0 && $tax['promoaftertax'] == '1'): ?>
					<li class="digi_cart_total"><?php echo JText::_("DSPROMOCODEDISCOUNT"); ?></li>
					<?php endif; ?>

					<?php }	?>
				</ul>
			</td>
			<td nowrap="nowrap" style="text-align: center; <?php echo $border_bottom; ?> padding-top:15px;">
				<ul  style="margin: 0; padding: 0;" >
					<?php if ($configs->tax_summary == 1) { ?>

					<?php if ($tax['promo'] > 0 && $tax['promoaftertax'] == '0') : ?>
					<li class="digi_cart_amount"><?php echo DigiComHelper::format_price($tax['promo'], $tax['currency'], true, $configs) ?></li>
					<?php endif;?>

					<?php if (($tax['value'] > 0 || $configs->get('tax_zero',1) == 1) && $customer->_user->id > 0) : ?>
					<li class="digi_cart_amount"><?php echo DigiComHelper::format_price($tax['value'], $tax['currency'], true, $configs); ?></li>
					<?php endif; ?>

					<?php if ($tax['shipping'] > 0 && $customer->_user->id > 0) : ?>
					<li class="digi_cart_amount"><?php echo DigiComHelper::format_price($tax['shipping'], $tax['currency'], true, $configs); ?></li>
					<?php endif; ?>

					<?php if ($tax['promo'] > 0 && $tax['promoaftertax'] == '1') : ?>
						<li class="digi_cart_amount"><?php echo DigiComHelper::format_price($tax['promo'], $tax['currency'], true, $configs); ?></li>
					<?php endif; ?>

					<?php } ?>
				</ul>
			</td>
		</tr>

		<tr>
			<td colspan="<?php echo $nr_columns - 1; ?>">
				<table>
					<tr>
						<td>
							<span class="digicom_details"><?php echo JText::_("DSPROMO"); ?>:</span>
						</td>
						<td>
							<input type="text" name="promocode"  value="<?php echo $this->promocode; ?>" />
							<input type="submit" name="Submit" value="Re-Calculate" class="digi_button" onclick="document.getElementById('returnpage').value=''; document.getElementById('type_button').value='recalculate';"/>
							<br/>
							<span style='color:red'><?php echo $this->promoerror; ?></span>
						</td>
					</tr>
				 </table>
			</td>
			<td nowrap="nowrap" style="text-align: center;">
				<ul style="margin: 0; padding: 0;">
					<li class="digi_cart_total"><?php echo JText::_("DSTOTAL");?></li>
				</ul>
			</td>
			<td nowrap="nowrap" style="text-align: center;">
				<ul style="margin: 0; padding: 0;">
					<li class="digi_cart_amount" id="cart_total" style="color:green;"><?php echo DigiComHelper::format_price($tax['taxed'], $tax['currency'], true, $configs); ?></li>
				</ul>
			</td>
		</tr>
		<?php $k++; ?>

		<?php 
			if($configs->showccont == 0){
		?>
				<tr >
					<td colspan="5"  nowrap="nowrap"><div class="make_payment">
						<?php
							echo JText::_("DIGI_PAYMENT_METHOD").": ".$this->lists['plugins'];
							$onclick = "document.getElementById('returnpage').value='checkout'; document.getElementById('type_button').value='checkout';";

							if($user->id == 0 || $customer->_customer->country == ""){
								$onclick = "document.getElementById('returnpage').value='login_register'; document.getElementById('type_button').value='checkout';";
							}

						?>
						<input type="submit" name="Submit" class="btn" value="<?php echo JText::_("DSCHECKOUTE");?>" onClick="<?php echo $onclick; ?>">
					</div></td>

					<td <?php echo ($k%2) ? 'class="digi_alt"' : ''; ?> <?php if ($discount!=1) echo 'style="display:none"'?>>&nbsp;</td>
					<?php if ($totalfields > 0  ) { ?>
					<td <?php echo ($k%2) ? 'class="digi_alt"' : ''; ?>>&nbsp;</td>
					<td <?php echo ($k%2) ? 'class="digi_alt"' : ''; ?>>&nbsp;</td>
					<?php } ?>
				</tr>
		<?php
			}
			else{
		?>
				<tr>
					<td colspan="6" style="padding-top:10px;">
						<table width="100%" cellspacing="0">
							<td width="50%" align="left" style="padding-left:10px;">
								<input type="button" class="digicom_cancel" name="continue" value="<?php echo JText::_("DSCONTINUESHOPING")?>" 
									   onClick="window.location='<?php echo DigiComHelper::DisplayContinueUrl($configs, $cat_url); ?>';" />
							</td>
							<td width="50%" align="right" style="padding-right:10px;" nowrap="nowrap">
								<?php echo JText::_("DIGI_PAYMENT_METHOD").": ".$this->lists['plugins']; ?>
								<?php
									$button_value = "DSCHECKOUTE";
									$onclick = "document.getElementById('returnpage').value='checkout'; document.getElementById('type_button').value='checkout';";

									if($user->id == 0 || $customer->_customer->country == ""){
										$button_value = "DSSAVEPROFILE";
										$onclick = "document.getElementById('returnpage').value='login_register'; document.getElementById('type_button').value='checkout';";
									}
								?>
								<input type="submit" name="Submit" class="btn" value="<?php echo JText::_($button_value);?>" onClick="<?php echo $onclick; ?>">
							</td>
						</table>
					</td>
				</tr>
		<?php
			}
		?>
	</table>
			</td>
		</tr>
	</table>
	<input name="controller" type="hidden" id="controller" value="Cart">
	<input name="task" type="hidden" id="task" value="updateCart">
	<input name="returnpage" type="hidden" id="returnpage" value="">
	<input name="type_button" type="hidden" id="type_button" value="">
	<input name="Itemid" type="hidden" value="<?php global $Itemid; echo $Itemid; ?>">
	</form>

<?php 
		if($configs->get('shopping_cart_style','') == "0"){
			echo DigiComHelper::powered_by();
		}
	}
?>