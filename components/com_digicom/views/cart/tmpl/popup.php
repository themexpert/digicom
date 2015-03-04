<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 376 $
 * @lastmodified	$LastChangedDate: 2013-10-21 11:54:05 +0200 (Mon, 21 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

defined ('_JEXEC') or die ("Go away.");

$configs = $this->configs;
JRequest::setVar("tmpl", "component");
global $Itemid;
$customer = $this->customer;
$items = $this->items;

$total = 0;
$optlen = $this->optlen;//array();
$discount = $this->discount;//0;
$lists = $this->lists;
$cat_url = $this->cat_url;
$totalfields = 0;
$shippingexists = 0;
foreach($items as $itemnum => $item){
	if($itemnum < 0){
		continue;
	}
}

$invisible = 'style="display:none;"';
if(count($items) == 0){
	$formlink = JRoute::_("index.php?option=com_digicom"."&Itemid=".$Itemid);
	$redirect_url = DigiComHelper::DisplayContinueUrl($configs, $cat_url);
	echo JText::_("DIGI_CART_IS_EMPTY").'. <a href="'.$redirect_url.'">'.JText::_("DIGI_CLICK_HERE").'.</a>';
	return;
}

$login_link = JRoute::_("index.php?option=com_digicom&view=profile&task=login&returnpage=cart"."&Itemid=".$Itemid);
?>

<style type="text/css">
	#cart_body .rt-container {
		width: auto !important;
	}
</style>

<script language="javascript" type="text/javascript">
<?php
foreach ($items as $j => $v) {
	if ($j < 0 ) continue;
}
?>
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
		
		var qty = document.getElementById('quantity'+item_id);
		var qty_query = '';
		if ( qty.selectedIndex != -1)
		{
			var qty_value = qty.options[qty.selectedIndex].value;
			qty_query += '&quantity'+item_id+'='+qty_value;
		}

		url += qty_query;

		ajaxRequest(url, 'debugid');
	}

</script>

<?php 
	$formlink = JRoute::_("index.php?option=com_digicom&view=cart");
	$currency = $configs->get('currency','USD');
?>

<form name="cart_form" method="post" action="<?php echo $formlink?>" onSubmit="return cartformsubmit();">
	<table class="table table-hover table-striped">
	<tbody><?php
	$k = 0;
	foreach($items as $itemnum => $item){
		if($itemnum < 0){
			continue;
		}
	?>
		<tr>
			<!-- Product image -->
			<td width="70">
				<img height="100" width="200" title="<?php echo $item->name; ?>" src="<?php echo $item->images; ?>" alt="<?php echo $item->name; ?>"/>
			</td>
			<!-- /End Product image -->

			<!-- Product name -->
			<td style="text-align:left;" class="digicom_product_name">
				<?php 
					echo $item->name; 
				?>
			</td>
			<!-- /End Product name -->

			<!-- Price -->
			<td align="right" style="vertical-align:top;text-align:right;">
				<?php 
					echo DigiComHelper::format_price2($item->price, $item->currency, true, $configs);
					$currency = $item->currency;
				?>
			</td>
			<!-- /End Price -->

			<!-- Remove -->
			<td align="center" style="vertical-align:top;width:80px;text-align:right;">
				<a href="javascript:void();" onclick="javascript:deleteFromCart(<?php echo $item->cid; ?>);"><i class="icon-remove"></i></a>
			</td>
			<!-- /End Remove -->
		</tr>
	<?php
		$total += $item->subtotal;
		$k++;
	}
	?>
		</tbody>
		<tfoot>
		<tr class="info">
			<td></td>
			<td style="color:#fff;font-weight:bold;">
				<b><?php
					$text = "DIGI_ITEM_IN_CART";
					if($k > 1){
						$text = "DIGI_ITEMS_IN_CART";
					}
					echo $k." ".JText::_($text); 
				?></b>
			</td>
			<td style="color:#fff;text-align:right;"><b><?php echo JText::_("DSSUBTOTAL");?></b></td>
			<td style="color:#fff;text-align:right;">
				<b><?php echo DigiComHelper::format_price2($total, $currency, true, $configs); ?></b>
			</td>
		</tr>
		</tfoot>
	</table>

	<input name="controller" type="hidden" id="controller" value="Cart">
	<input name="task" type="hidden" id="task" value="updateCart">
	<input name="returnpage" type="hidden" id="returnpage" value="">
	<input name="Itemid" type="hidden" value="<?php global $Itemid; echo $Itemid; ?>">
	<input name="promocode" type="hidden" value="" />
	<input type="hidden" name="processor" id="processor" value="paypaypal">
</form>
<?php exit; ?>