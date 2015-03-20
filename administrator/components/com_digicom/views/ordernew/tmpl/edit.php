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
$document = JFactory::getDocument();
$doc = JFactory::getDocument();
$app = JFactory::getApplication();
$input = $app->input;
$input->set('layout', 'neworder');

$link = 'index.php?option=com_digicom&amp;view=products&amp;layout=modal&amp;tmpl=component&amp;' . JSession::getFormToken() . '=1';
$js = "
function changePlain() {

	var product_ids = [];

	var inputs = Array.prototype.slice.call(document.getElementsByTagName('input'));
	for(i=0; i<inputs.length; i++){
		el = inputs[i];
		if(el.name.indexOf('jform[product_id][') == 0){
			var tid = el.getAttribute('id').substr('product_id'.length, el.getAttribute('id').length);
			var tproduct = el.value;

			var tmp = [];
			tmp.push(tproduct);
			product_ids.push(tmp);
		}
	}
	var tprocessor = $('jform_processor').value;
	var tpromocode = $('jformdiscount').value;
	var tamount_paid = $('amount_paid').value;

	var jsonString = JSON.encode({pids: product_ids, processor: tprocessor, promocode: tpromocode, amount_paid: tamount_paid});
	//var url = \"index.php?option=com_digicom&controller=orders&task=calc&no_html=1&jsonString=\"+jsonString;
	var url = \"index.php?option=com_digicom&task=orders.calc&tmpl=component&jsonString=\"+jsonString;
	console.log(url);
	//index.php?option=com_digicom&controller=orders&task=calc&no_html=1&
	//jsonString={\"pids\":[],\"processor\":\"offline\",\"promocode\":\"\",\"amount_paid\":\"\"}
	var req = new Request.HTML({
		method: 'get',
		url: url,
		data: { 'do' : '1' },
		postBody: jsonString,
		onComplete: function(req){
			$('from_ajax_div').empty().adopt(req);
			var encoded_string = $('from_ajax_div').innerHTML;

			var resp = JSON.decode(encoded_string);

			var processor_select = $('jform_processor');
			var CountPayments = processor_select.options.length;
			for(payindex = 0; payindex < CountPayments; payindex++) {
				if (processor_select.options[payindex].value == resp.processor) {
					processor_select.options[payindex].selected = true;
				}
			}
			
			var promocode_select = $('jformdiscount');
			if(promocode_select){
				var CountPromocodes = promocode_select.options.length;
				for(payindex = 0; payindex < CountPromocodes; payindex++) {
					if (promocode_select.options[payindex].value == resp.promocode) {
						promocode_select.options[payindex].selected = true;
					}
				}
			}

			$('amount').innerHTML = resp.amount;
			$('amount_value').value = resp.amount_value;
			$('tax').innerHTML = resp.tax;
			$('tax_value').value = resp.tax_value;
			$('discount').value = resp.discount;
			$('discount_sign').innerHTML = resp.discount_sign;
			$('total').innerHTML = resp.total;
			$('amount_paid').value = resp.total_value;
			$('currency_amount_paid').innerHTML = resp.currency;
			$('currency_value').value = resp.currency;
		}
	}).send();

}
function jSelectProduct(id, title, catid, object, link, lang,price)
{
	var hreflang = '';
	if (lang !== '')
	{
		var hreflang = ' hreflang = \"' + lang + '\"';
	}

	var tag = '<tr id=\"productincludes_item_' + id + '\"><td><input type=\"hidden\" id=\"product_include_id'+id+'\" name=\"jform[product_id][]\" value=\"'+id+'\" /> <a' + hreflang + ' href=\"' + link + '\">' + title + '</a></td><td>'+ price +'</td><td><a href=\#\" onclick=\"digiRemoveProduct('+ id +');\"><i class=\"icon-remove\"></i></a></td></tr>';
	//jInsertEditorText(tag, '" . 'productincludes_items' . "');
	jQuery('#productincludes_items').append(tag);
	changePlain();
	jModalClose();
}
function digiRemoveProduct(id){
	event.preventDefault();
	jQuery('tr#productincludes_item_'+id).remove();
	changePlain();
}

";
$document->addScriptDeclaration($js);
JHtml::_('behavior.modal');
JHtml::_('behavior.multiselect');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHTML::_('behavior.tooltip');
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
				label = '<?php echo JText::_('COM_DIGICOM_ORDER_PRODUCT_REQUIRED'); ?>';
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
		<!-- <legend><?php echo JText::_( 'New Order' ); ?></legend> -->

		<p class="alert alert-info"> <?php echo JText::_("COM_DIGICOM_ORDER_CREATE_NEW_ORDER_HEADER_NOTICE"); ?> </p>

		<form id="adminForm" action="index.php" method="post" name="adminForm" class="form-horizontal">
			<div class="order-details">

				<h3><?php echo JText::_( 'COM_DIGICOM_ORDER_DETAILS_HEADER_TITLE' ); ?></h3>
				<div class="control-group">
				    <label class="control-label" for="userid"><?php echo JText::_('COM_DIGICOM_ORDER_CREATE_NEW_FIELD_CUSTOMER_LABEL'); ?></label>
				    <div class="controls">
				      <?php echo $this->form->getInput('userid'); ?>
				    </div>
			  	</div>

			  	<div class="control-group">
				    <label class="control-label" for="order_status"><?php echo JText::_( 'COM_DIGICOM_ORDER_CREATE_NEW_FIELD_ORDER_STATUS_LABEL' ); ?></label>
				    <div class="controls">
				      <?php echo $this->form->getInput('status'); ?>
				    </div>
			  	</div>

			  	<div class="control-group">
				    <label class="control-label" for="order_date"><?php echo JText::_( 'COM_DIGICOM_ORDER_CREATE_NEW_FIELD_ORDER_DATE_LABEL' ); ?></label>
				    <div class="controls">
				      	<?php echo $this->form->getInput('order_date'); ?>
				    </div>
			  	</div>

			  	<div class="control-group">
				    <label class="control-label" for="processor">
				    	<?php echo JText::_( 'COM_DIGICOM_ORDER_CREATE_NEW_FIELD_PAYMENT_METHOD_LABEL' ); ?>
				    	<?php
							echo JHTML::tooltip(JText::_("COM_DIGICOM_ORDER_CREATE_NEW_FIELD_PAYMENT_METHOD_TIP"), '', '',  "<img src=".JURI::root()."administrator/components/com_digicom/assets/images/tooltip.png />", '', '', 'hasTip');
						?>
				    </label>
				    <div class="controls">
				   		<?php echo $this->form->getInput('processor'); ?>
				    </div>
			  	</div>

			</div>

			<div class="product-details">
				
				<h3><?php echo JText::_( 'COM_DIGICOM_ORDER_CREATE_NEW_PRODUCT_DETAILS_TITLE' ); ?></h3>

				<table id="productincludes" class="table table-striped table-hover" id="productList">
					<thead>
						<tr>
							<td>Product Name</td>
							<td width="100px">Price</td>
							<td width="1%">Action</td>
						</tr>
					</thead>
					<tbody id="productincludes_items">
						
					</tbody>
				</table>
				

				<div style="margin:15px;padding:10px;">
					<a class="btn btn-small btn-primary modal" title="Products" href="<?php echo $link; ?>" 
					rel="{handler: 'iframe', size: {x: 800, y: 500}}">
						<i class="icon-file-add"></i> 
						<?php echo JText::_('COM_DIGICOM_ADD_PRODUCT'); ?>
					</a>

				</div>
				
				<div class="promo-subtotal clearfix">
					
					<div class="promos">
					    <label class="control-label" for="promocode">
					    	<?php echo JText::_( 'COM_DIGICOM_PROMO_CODE' ); ?>
							<?php
								echo JHTML::tooltip(JText::_("COM_DIGICOM_ORDER_PROMO_CODE_TIP"), '', '',  "<img src=".JURI::root()."administrator/components/com_digicom/assets/images/tooltip.png />", '', '', 'hasTip');
							?>
					    </label>
					    <div class="controls">
					      	<?php 
					      		echo $this->form->getInput('discount');
					      		/*if(!empty($this->promocode)){
						      		echo $this->promocode;
					      		}else{
					      			echo '<input type="hidden" value="" id="promocode"/>';
					      		}*/
				      		?>
					    </div>
				  	</div>

				  	<div class="subtotal">
					    <label class="control-label" for="amount">
					    	<?php echo JText::_( 'COM_DIGICOM_SUBTOTAL' ); ?>
					      	<?php
								echo JHTML::tooltip(JText::_("COM_DIGICOM_ORDER_SUBTOTAL_TIP"), '', '',  "<img src=".JURI::root()."administrator/components/com_digicom/assets/images/tooltip.png />", '', '', 'hasTip');
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
				    <label class="control-label" for="total"><?php echo JText::_( 'COM_DIGICOM_TAX' ); ?></label>
				    <div class="controls">
				      	<span id="tax"></span>
				    </div>
			  	</div>

			  	<div class="control-group">
				    <label class="control-label" for="total"><?php echo JText::_( 'COM_DIGICOM_DISCOUNT' ); ?></label>
				    <div class="controls">
						<span id="discount_sign">00.00 <?php echo $configs->get('currency','USD'); ?></span>
				    </div>
			  	</div>

			  	<div class="control-group">
				    <label class="control-label" for="total">
				    	<?php echo JText::_( 'COM_DIGICOM_TOTAL' ); ?> 
						<?php
							echo JHTML::tooltip(JText::_("COM_DIGICOM_ORDER_TOTAL_TIP"), '', '',  "<img src=".JURI::root()."administrator/components/com_digicom/assets/images/tooltip.png />", '', '', 'hasTip');
						?>
					</label>
				    <div class="controls">
						<span id="total">00.00 <?php echo $configs->get('currency','USD'); ?></span>
						
				    </div>
			  	</div>

			  	<div class="control-group">
				    <label class="control-label" for="amount_paid">
				    	<?php echo JText::_( 'COM_DIGICOM_AMOUNT_PAID' ); ?>
				      	<?php
							echo JHTML::tooltip(JText::_("COM_DIGICOM_ORDER_AMOUNT_PAID_TIP"), '', '',  "<img src=".JURI::root()."administrator/components/com_digicom/assets/images/tooltip.png />", '', '', 'hasTip');
						?>
				    </label>
				    <div class="controls">
				      	<span id="currency_amount_paid" class="hide"></span><input id="amount_paid" name="jform[amount_paid]" type="text" value=""/>
				    </div>
			  	</div>

			</div>
		  	

			

		  		<div id="from_ajax_div" style="display:none;"></div>

				<input type="hidden" name="option" value="com_digicom"/>
				<input type="hidden" name="view" value="ordernew"/>
				<input type="hidden" name="jform[tax]" id="tax_value" value="0"/>
				<input type="hidden" name="jform[shipping]" value="0"/>
				<input type="hidden" name="jform[amount]" id="amount_value" value="0"/>
				<input type="hidden" name="jform[discount]" id="discount" value="0"/>
				<input type="hidden" name="jform[currency]" id="currency_value" value=""/>
				<input type="hidden" name="task" value=""/>
				<?php echo JHtml::_('form.token'); ?>
		</form>

	</fieldset>

<div>