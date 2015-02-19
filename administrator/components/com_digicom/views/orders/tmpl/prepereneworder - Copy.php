<?php
	JHTML::_('behavior.tooltip');
	$document = JFactory::getDocument();

	if(isset($document->_scripts)){
		$temp_array = array();
		foreach($document->_scripts as $path=>$type){
			if(strpos($path, "plugins/system/mtupgrade/mootools.js") !== FALSE){
				$temp = str_replace("plugins/system/mtupgrade/mootools.js", "media/system/js/mootools.js", $path);
				$temp_array[$temp] = $type;
			}
			else{
				$temp_array[$path] = $type;
			}
		}
		if(isset($temp_array) && count($temp_array) > 0){
			$document->_scripts = $temp_array;
		}
	}
?>

<?php

$ajax = <<<EOD

	window.addEvent('domready', function(){

		$('buttonaddproduct').addEvent('click', function(e) {
			e.stop()||new Event(e).stop();

			var url = "index.php?option=com_digicom&controller=orders&task=productitem&userid={$this->cust->id}&no_html=1";

			 var req = new Request.HTML({
				method: 'get',
				url: url,
				data: { 'do' : '1' },
				onComplete: function(response){
					$('product_items').adopt(response);
					$$('a.modal').each(function(el) {
						el.addEvent('click', function(e) {
							new Event(e).stop();
							SqueezeBox.fromElement(el);
						});
					});
				}
			}).send();
		});
	});

	function grayBoxiJoomla(link_element, width, height){
		SqueezeBox.open(link_element, {
			handler: 'iframe',
			size: {x: width, y: height}
		});
	}

	function changePlain() {

		var product_ids = [];

		var inputs = Array.prototype.slice.call(document.getElementsByTagName('input'));
		for(i=0; i<inputs.length; i++){
			el = inputs[i];
			if(el.name.indexOf('product_id[') == 0){
				var tid = el.getAttribute('id').substr('product_id'.length, el.getAttribute('id').length);
				var tproduct = el.value;
				var ttype = "";
				if($('subscr_type_select'+tid)){
					ttype = $('subscr_type_select'+tid).value;
				}
				var tlic = $('licences_select'+tid).value;
				var tplan = $('subscr_plan_select'+tid).value;

				var tmp = [];
				tmp.push(tproduct);
				tmp.push(ttype);
				tmp.push(tlic);
				tmp.push(tplan);
				product_ids.push(tmp);
			}
		}
		var tprocessor = $('processor').value;
		var tpromocode = $('promocode').value;
		var tamount_paid = $('amount_paid').value;

		var jsonString = JSON.encode({pids: product_ids, customer_id: '{$this->cust->id}', processor: tprocessor, promocode: tpromocode, amount_paid: tamount_paid});
		var url = "index.php?option=com_digicom&controller=orders&task=calc&userid={$this->cust->id}&no_html=1&jsonString="+jsonString;

		var req = new Request.HTML({
			method: 'get',
			url: url,
			data: { 'do' : '1' },
			postBody: jsonString,
			onComplete: function(req){
				$("from_ajax_div").empty().adopt(req);
				var encoded_string = $("from_ajax_div").innerHTML;

				var resp = JSON.decode(encoded_string);

				var processor_select = $("processor");
				var CountPayments = processor_select.options.length;
				for(payindex = 0; payindex < CountPayments; payindex++) {
					if (processor_select.options[payindex].value == resp.processor) {
						processor_select.options[payindex].selected = true;
					}
				}

				var promocode_select = $("promocode");
				var CountPromocodes = promocode_select.options.length;
				for(payindex = 0; payindex < CountPromocodes; payindex++) {
					if (promocode_select.options[payindex].value == resp.promocode) {
						promocode_select.options[payindex].selected = true;
					}
				}

				$("amount").innerHTML = resp.amount;
				$("amount_value").value = resp.amount_value;
				$("tax").innerHTML = resp.tax;
				$("tax_value").value = resp.tax_value;
				$("discount").value = resp.discount;
				$("discount_sign").innerHTML = resp.discount_sign;
				$("total").innerHTML = resp.total;
				$("amount_paid").value = resp.total_value;
				$("currency_amount_paid").innerHTML = resp.currency;
				$("currency_value").value = resp.currency;
			}
		}).send();

	}

	function remove_product(id){
		var complete_id = 'product_item_'+id;
		var par = document.getElementById(complete_id);
		var parent_element = par.parentNode;
		parent_element.removeChild(par);
		changePlain();
	}

	function checkSubscriptionPlan(id){
		if(document.getElementById('subscr_plan_select'+id).style.display == 'none'){
			document.getElementById('subscr_plan_'+id).style.display = 'none';
		}
		else{
			document.getElementById('subscr_plan_'+id).style.display = '';
		}
	}

	function show_attribute_product(id) {
		document.getElementById('subscr_type_'+id).style.display = '';
		setTimeout(function(){checkSubscriptionPlan(id)}, 2000);
		//document.getElementById('subscr_plan_'+id).style.display = '';
		show_licences_renew(id);
	}

	function show_licences_renew(id) {

		var type = "";
		if(document.getElementById('subscr_type_select'+id)){
			type = document.getElementById('subscr_type_select'+id).value;
		}
		var pid = document.getElementById('product_id'+id).value;

		if (type == 'renewal') {

			var nochange = true;

			// License
			var url = "index.php?option=com_digicom&controller=licenses&task=licenseitem&hid="+id+"&pid="+pid+"&userid={$this->cust->id}&type="+type+"&no_html=1";

			var req = new Request.HTML({
				method: 'get',
				url: url,
				data: { 'do' : '1' },
				onComplete: function(response){
					if(response != 'none') {
						document.getElementById('licences_'+id).style.display = '';
						if(document.getElementById('licences_select'+id)){
							document.getElementById('licences_select'+id).parentNode.empty().adopt(response);
						}
						changePlain();
					}
					else{
						alert('User not have license to this product');
						nochange = false;
						document.getElementById('subscr_type_select'+id).selectedIndex = 0;
					}

					if(nochange){
						var url = "index.php?option=com_digicom&controller=plans&task=planitem&hid="+id+"&pid="+pid+"&userid={$this->cust->id}&type="+type+"&no_html=1";
						var req = new Request.HTML({
							method: 'get',
							url: url,
							data: { 'do' : '1' },
							onComplete: function(response){
								if(document.getElementById('subscr_plan_select'+id)){
									document.getElementById('subscr_plan_select'+id).parentNode.empty().adopt(response);
								}
								changePlain();
							}
						}).send();
					}
				}
			}).send();

			// may be need delete disabled code
			if (false) {
				alert('nochange Plain');
				// Plan
				var url = "index.php?option=com_digicom&controller=plans&task=planitem&hid="+id+"&pid="+pid+"&userid={$this->cust->id}&type="+type+"&no_html=1";
				new Ajax(url, {
					method: 'get',
					onComplete: function( response ) {
						if ( document.getElementById('subscr_plan_select'+id) ) {
							document.getElementById('subscr_plan_select'+id).parentNode.innerHTML=response;
						}
						changePlain();
					},
					onFailure: function(response) {
						alert('ajax: some error');
					}
				}).request();
			}

		} else {

		   document.getElementById('licences_'+id).style.display = 'none';

			// Plan
			var url = "index.php?option=com_digicom&controller=plans&task=planitem&hid="+id+"&pid="+pid+"&userid={$this->cust->id}&type="+type+"&no_html=1";
			var req = new Request.HTML({
				method: 'get',
				url: url,
				data: { 'do' : '1' },
				onComplete: function(response){
					if(document.getElementById('subscr_plan_select'+id)){
						document.getElementById('subscr_plan_select'+id).parentNode.empty().adopt(response);
					}
					changePlain();
				}
			}).send();
		}
	}

/* ]]> */
EOD;

$doc = JFactory::getDocument();
$doc->addScriptDeclaration( $ajax );
$doc->addStyleSheet("components/com_digicom/assets/css/digicom.css");

?>
<div id="returnJSON"></div>

<fieldset class="adminform">
	<legend><?php echo JText::_( 'New Order' ); ?></legend>

	<table>
		<tr>
			<td class="header_zone" colspan="4">
				<?php
					echo JText::_("HEADER_ORDERS_ADD");
				?>
			</td>
		</tr>
	</table>

<form id="adminForm" action="index.php" method="post" name="adminForm">
	<table width="100%">
		<tr>
			<td width="30%">Username</td>
			<td><?php echo $this->cust->username.""; ?></td>
			<td>
				<a href="index.php?option=com_digicom&controller=orders&task=checkcreateuser&usertype=3">Change</a>
			</td>
		</tr>
		<tr>
			<td colspan="3" style="background:#ccc;">
				<h3><?php echo JText::_( 'Add Product(s) to this order' ); ?></h3>
			</td>
		</tr>
		<tr>
			<td colspan="3" id="product_items">
<!-- Products -->
<div id="product_item_1">
</div>
<!-- /Products -->
			</td>
		</tr>
		<!-- Add Products -->
		<tr>
			<td style="border-top:1px solid #ccc;padding-top:5px;"></td>
			<td style="border-top:1px solid #ccc;padding-top:5px;">
				<!-- a href="#" id="buttonaddproduct"><?php echo JText::_( 'Add Product' ); ?></a -->
				<input class="inputbox btn btn-small" type="button" id="buttonaddproduct" name="add_product_button" value="<?php echo JText::_( 'Add Product' ); ?>"/>
			</td>
			<td style="border-top:1px solid #ccc;padding-top:5px;"></td>
		</tr>
		<!-- Common info  -->
		<tr>
			<td style="border-top:1px solid #ccc;padding-top:5px;"><?php echo JText::_( 'Payment method' ); ?></td>
			<td style="border-top:1px solid #ccc;padding-top:5px;" id="payment_method">
				<?php // echo $this->plugins; ?>
				<select id="processor" name="processor" class="inputbox" size="1">
					<?php
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
						echo '<option value="' . $gateway->element . '" ' . ($configs->get('default_payment','paypal') == $gateway->element ? "selected" : "") . '>' . JText::_($gatewayname) . '</option>';
					} ?>
				</select>
				<?php
					echo JHTML::tooltip(JText::_("COM_DIGICOM_ORDERPAYMETHOD_TIP"), '', '',  "<img src=".JURI::root()."administrator/components/com_digicom/assets/images/tooltip.png />", '', '', 'hasTip');
				?>
			</td>
			<td style="border-top:1px solid #ccc;padding-top:5px;"></td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'Promocode' ); ?></td>
			<td>
				<?php echo $this->promocode; ?>
				<?php
					echo JHTML::tooltip(JText::_("COM_DIGICOM_ORDERPROMOCODE_TIP"), '', '',  "<img src=".JURI::root()."administrator/components/com_digicom/assets/images/tooltip.png />", '', '', 'hasTip');
				?>
			</td>
			<td></td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'Amount' ); ?></td>
			<td id="amount" width="10%"></td>
			<td>
			<?php
				echo JHTML::tooltip(JText::_("COM_DIGICOM_ORDERAMOUNT_TIP"), '', '',  "<img src=".JURI::root()."administrator/components/com_digicom/assets/images/tooltip.png />", '', '', 'hasTip');
			?>
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'Tax' ); ?></td>
			<td id="tax"></td>
			<td>
			<?php
				echo JHTML::tooltip(JText::_("COM_DIGICOM_ORDERTAX_TIP"), '', '',  "<img src=".JURI::root()."administrator/components/com_digicom/assets/images/tooltip.png />", '', '', 'hasTip');
			?>
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'Discount' ); ?></td>
			<td id="discount_sign"></td>
			<td></td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'Total' ); ?></td>
			<td id="total"></td>
			<td></td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'Amount paid' ); ?></td>
			<td><span id="currency_amount_paid"></span><input id="amount_paid" name="amount_paid" type="text" value=""/></td>
			<td>
			<?php
				echo JHTML::tooltip(JText::_("COM_DIGICOM_ORDERAMOUNTPAID_TIP"), '', '',  "<img src=".JURI::root()."administrator/components/com_digicom/assets/images/tooltip.png />", '', '', 'hasTip');
			?>
			</td>
		</tr>
		
		<tr>
			<td><?php echo JText::_( 'Order Date' ); ?></td>
			<td><span id="order_date"></span><input id="purchase_date" name="purchase_date" type="text" value=""/></td>
			<td>
			</td>
		</tr>
		<!-- /Common info  -->
	</table>


		<input type="hidden" name="option" value="com_digicom"/>
		<input type="hidden" name="controller" value="Orders"/>
		<input type="hidden" name="userid" value="<?php echo $this->cust->id; ?>"/>
		<input type="hidden" name="username" value="<?php echo $this->cust->username; ?>"/>
		<input type="hidden" id="tax_value" name="tax" value="0"/>
		<input type="hidden" name="shipping" value="0"/>
		<input type="hidden" id="amount_value" name="amount" value="0"/>
		<input type="hidden" id="discount" name="discount" value="0"/>
		<input type="hidden" id="currency_value" name="currency" value=""/>
		<input type="hidden" name="status" value="Active"/>
		<input type="hidden" name="task" value=""/>
</form>


<div style="border-top:1px solid #ccc;padding-top:5px;">
	<input onclick="javascript: submitbutton('saveorder')" type="button" name="task" value="Save" class="btn btn-success" />
	<div id="from_ajax_div" style="display:none;"></div>
</div>

</fieldset>
