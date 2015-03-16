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

JHtml::_('behavior.multiselect');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHTML::_('behavior.tooltip');
$configs = $this->configs;
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
$ajax = <<<EOD

	jQuery(document).ready(function(){
		jQuery('#buttonaddproduct').click(function(e){
			e.preventDefault();
			var url = "index.php?option=com_digicom&controller=orders&task=productitem&no_html=1&tmpl=component&format=raw";

			var req = new Request.HTML({
				method: 'get',
				url: url,
				data: { 'do' : '1' },
				onComplete: function(response){
					jQuery('#product_items').append(response);
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
	/*
	window.addEvent('domready', function(){

		$('buttonaddproduct').addEvent('click', function(e) {
			e.stop()||new Event(e).stop();

			var url = "index.php?option=com_digicom&controller=orders&task=productitem&no_html=1&tmpl=component&format=raw";

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
		$('buttonaddproduct').click();
	});
	*/
	function grayBoxiJoomla(link_element, width, height){
		SqueezeBox.open(link_element, {
			handler: 'iframe',
			size: {x: width, y: height}
		});
	}

	function changePlain(castid) {

		var product_ids = [];

		var inputs = Array.prototype.slice.call(document.getElementsByTagName('input'));
		for(i=0; i<inputs.length; i++){
			el = inputs[i];
			if(el.name.indexOf('product_id[') == 0){
				var tid = el.getAttribute('id').substr('product_id'.length, el.getAttribute('id').length);
				var tproduct = el.value;

				var tmp = [];
				tmp.push(tproduct);
				product_ids.push(tmp);
			}
		}
		var tprocessor = $('processor').value;
		var tpromocode = $('promocode').value;
		var tamount_paid = $('amount_paid').value;

		//var jsonString = JSON.encode({pids: product_ids, customer_id: castid, processor: tprocessor, promocode: tpromocode, amount_paid: tamount_paid});
		var jsonString = JSON.encode({pids: product_ids, processor: tprocessor, promocode: tpromocode, amount_paid: tamount_paid});
		var url = "index.php?option=com_digicom&controller=orders&task=calc&no_html=1&jsonString="+jsonString;
		//console.log(url);
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
		//document.getElementById('subscr_type_'+id).style.display = '';
		//setTimeout(function(){checkSubscriptionPlan(id)}, 2000);
		//document.getElementById('subscr_plan_'+id).style.display = '';
		//show_licences_renew(id);
		changePlain(id);
	}

	function show_licences_renew(id) {

		var type = "";
		var pid = document.getElementById('product_id'+id).value;

		// Plan
		var url = "index.php?option=com_digicom&controller=plans&task=planitem&hid="+id+"&pid="+pid+"&type="+type+"&no_html=1";
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

/* ]]> */
EOD;
$doc = JFactory::getDocument();
$app = JFactory::getApplication();
$input = $app->input;
$input->set('layout', 'neworder');
$doc->addScriptDeclaration( $ajax );
?>
<script type="text/javascript">
	
	Joomla.submitbutton = function(task)
	{
		if(task == 'save'){
			var products = jQuery("input[id^='product_id']").val();
			if(products === null || products === undefined || products === '' ){
				message = Joomla.JText._('JLIB_FORM_FIELD_INVALID');
				error = {};
				error.error = [];
				label = '<?php echo JText::_('COM_DIGICOM_PRODUCT_REQUIRED_FOR_ORDER'); ?>';
				error.error[0] = message + label;
				
				Joomla.renderMessages(error);
				return false;
			}
		}
		if (task == 'cancel' || document.formvalidator.isValid(document.id('adminForm')))
		{
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
	}
</script>
<?php if (!empty( $this->sidebar)) : ?>
<div id="j-sidebar-container" class="">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="">
<?php else : ?>
<div id="j-main-container" class="">
<?php endif;?>

	<div id="returnJSON"></div>
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'New Order' ); ?></legend>

		<p class="alert alert-info"> <?php echo JText::_("HEADER_ORDERS_ADD"); ?> </p>

		<form id="adminForm" action="index.php" method="post" name="adminForm" class="form-horizontal">
			<div class="order-details">

				<h3>Order Details</h3>
				<div class="control-group">
				    <label class="control-label" for="userid">Customer</label>
				    <div class="controls">
				      <?php echo $this->form->getInput('userid'); ?>
				    </div>
			  	</div>

			  	<div class="control-group">
				    <label class="control-label" for="order_status"><?php echo JText::_( 'COM_DIGICOM_FIELD_ORDER_STATUS_LABEL' ); ?></label>
				    <div class="controls">
				      <?php echo $this->form->getInput('status'); ?>
				    </div>
			  	</div>

			  	<div class="control-group">
				    <label class="control-label" for="order_date"><?php echo JText::_( 'Order Date' ); ?></label>
				    <div class="controls">
				      	<?php echo $this->form->getInput('order_date'); ?>
				    </div>
			  	</div>

			  	<div class="control-group">
				    <label class="control-label" for="processor">
				    	<?php echo JText::_( 'COM_DIGICOM_ORDER_PAYMENT_METHOD' ); ?>
				    	<?php
							echo JHTML::tooltip(JText::_("COM_DIGICOM_ORDERPAYMETHOD_TIP"), '', '',  "<img src=".JURI::root()."administrator/components/com_digicom/assets/images/tooltip.png />", '', '', 'hasTip');
						?>
				    </label>
				    <div class="controls">
				      	<select id="processor" name="processor" class="inputbox" size="1">
							<?php
							$db = JFactory::getDBO();
							$condtion = array(0 => '\'digicom_pay\'');
							$condtionatype = join(',',$condtion);
							$query = "SELECT extension_id as id,name,element,enabled as published
									  FROM #__extensions
									  WHERE folder in ($condtionatype) AND enabled=1";
						
							$db->setQuery($query);
							$gatewayplugin = $db->loadobjectList();

							$lang = JFactory::getLanguage();
							$options = array();
							$options[] = JHTML::_('select.option', '', 'Select payment gateway');
							foreach($gatewayplugin as $gateway)
							{
								$gatewayname = strtoupper($gateway->element);
								$lang->load('plg_payment_' . strtolower($gatewayname), JPATH_ADMINISTRATOR);
								echo '<option value="' . $gateway->element . '" ' . ($configs->get('default_payment','paypal') == $gateway->element ? "selected" : "") . '>' . JText::_($gatewayname) . '</option>';
							} ?>
						</select>
						
				    </div>
			  	</div>

			</div>

			<div class="product-details">
				
				<h3>Product Details</h3>
				
				<!-- Div to show Product selection field -->
				<div id="product_items"></div>
			
				<div class="control-group">
				    <label class="control-label" for="buttonaddproduct">Add Products</label>
				    <div class="controls">
				      	<!-- a href="#" id="buttonaddproduct"><?php echo JText::_( 'Add Product' ); ?></a -->
						<input class="inputbox btn btn-small" type="button" id="buttonaddproduct" name="add_product_button" value="<?php echo JText::_( 'Add Product' ); ?>"/>
				    </div>
				</div>	
				
				<div class="promo-subtotal clearfix">
					
					<div class="promos">
					    <label class="control-label" for="promocode">
					    	<?php echo JText::_( 'Promocode' ); ?>
							<?php
								echo JHTML::tooltip(JText::_("COM_DIGICOM_ORDERPROMOCODE_TIP"), '', '',  "<img src=".JURI::root()."administrator/components/com_digicom/assets/images/tooltip.png />", '', '', 'hasTip');
							?>
					    </label>
					    <div class="controls">
					      	<?php echo $this->promocode; ?>
					    </div>
				  	</div>

				  	<div class="subtotal">
					    <label class="control-label" for="amount">
					    	<?php echo JText::_( 'COM_DIGICOM_ORDER_SUBTOTAL' ); ?>
					      	<?php
								echo JHTML::tooltip(JText::_("COM_DIGICOM_ORDERAMOUNT_TIP"), '', '',  "<img src=".JURI::root()."administrator/components/com_digicom/assets/images/tooltip.png />", '', '', 'hasTip');
							?>
					    </label>
					    <div class="controls">
					    	<span id="amount">00.00 <?php echo $configs->get('currency','USD'); ?></span>
					      	
					    </div>
				  	</div>

				</div>
				

			</div>
			
			<div class="grand-total clearfix">
			  	<div class="control-group hide">
				    <label class="control-label" for="total"><?php echo JText::_( 'Tax' ); ?></label>
				    <div class="controls">
				      	<span id="tax"></span>
				    </div>
			  	</div>

			  	<div class="control-group">
				    <label class="control-label" for="total"><?php echo JText::_( 'Discount' ); ?></label>
				    <div class="controls">
						<span id="discount_sign">00.00 <?php echo $configs->get('currency','USD'); ?></span>
				    </div>
			  	</div>

			  	<div class="control-group">
				    <label class="control-label" for="total">
				    	<?php echo JText::_( 'COM_DIGICOM_ORDER_TOTAL' ); ?> 
						<?php
							echo JHTML::tooltip(JText::_("COM_DIGICOM_ORDERPROMOCODE_TIP"), '', '',  "<img src=".JURI::root()."administrator/components/com_digicom/assets/images/tooltip.png />", '', '', 'hasTip');
						?>
					</label>
				    <div class="controls">
						<span id="total">00.00 <?php echo $configs->get('currency','USD'); ?></span>
						
				    </div>
			  	</div>

			  	<div class="control-group">
				    <label class="control-label" for="amount_paid">
				    	<?php echo JText::_( 'COM_DIGICOM_ORDER_AMOUNT_PAID' ); ?>
				      	<?php
							echo JHTML::tooltip(JText::_("COM_DIGICOM_ORDERAMOUNTPAID_TIP"), '', '',  "<img src=".JURI::root()."administrator/components/com_digicom/assets/images/tooltip.png />", '', '', 'hasTip');
						?>
				    </label>
				    <div class="controls">
				      	<span id="currency_amount_paid" class="hide"></span><input id="amount_paid" name="amount_paid" type="text" value=""/>
				    </div>
			  	</div>

			</div>
		  	

			

		  		<div id="from_ajax_div" style="display:none;"></div>

				<input type="hidden" name="option" value="com_digicom"/>
				<input type="hidden" name="controller" value="orders"/>
				<input type="hidden" id="tax_value" name="tax" value="0"/>
				<input type="hidden" name="shipping" value="0"/>
				<input type="hidden" id="amount_value" name="amount" value="0"/>
				<input type="hidden" id="discount" name="discount" value="0"/>
				<input type="hidden" id="currency_value" name="currency" value=""/>
				<input type="hidden" name="task" value=""/>
		</form>

	</fieldset>

<div>