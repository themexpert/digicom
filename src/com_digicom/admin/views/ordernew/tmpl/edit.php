<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$configs = $this->configs;
$document = JFactory::getDocument();
$doc = JFactory::getDocument();
$app = JFactory::getApplication();
$input = $app->input;

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
	var tuserid = $('jform_userid_id').value;
	var tprocessor = $('jform_processor').value;
	var tpromocode = $('jformpromocode').value;
	var tamount_paid = $('amount_paid').value;

	var jsonString = JSON.encode({pids: product_ids, processor: tprocessor, promocode: tpromocode, amount_paid: tamount_paid, userid: tuserid});
	var url = \"index.php?option=com_digicom&task=ordernew.calc&tmpl=component&jsonString=\"+jsonString;
	var req = new Request.HTML({
		method: 'get',
		url: url,
		data: { 'do' : '1' },
		postBody: jsonString,
		onComplete: function(req){
			$('from_ajax_div').empty().adopt(req);
			var encoded_string = $('from_ajax_div').innerHTML;

			var resp = JSON.decode(encoded_string);
			console.log(resp);
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
			$('price_value').value = resp.price_value;
			$('tax').innerHTML = resp.tax;
			$('tax_value').value = resp.tax_value;
			$('discount').value = resp.discount;
			$('discount_sign').innerHTML = resp.discount_sign;
			$('total').innerHTML = resp.total;
			/* $('amount_paid').value = resp.total_value; */
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
		if(task == 'ordernew.save'){
			var products = jQuery("input[id^='product_include_id']").val();
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
		if (task == 'order.cancel' || document.formvalidator.isValid(document.id('adminForm')))
		{
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
	}
</script>

<form id="adminForm" action="index.php" method="post" name="adminForm" class="">
	<div id="digicom" class="dc digicom">
		<div class="tx-sidebar"><?php echo $this->sidebar; ?></div> <!-- .tx-sidebar -->
		<div class="tx-main">
			<div class="page-header">
				<h1><?php echo JText::_( 'New Order' ); ?></h1>
				<p><?php echo JText::_("COM_DIGICOM_ORDER_CREATE_NEW_ORDER_HEADER_NOTICE"); ?></p>
			</div>
			<div class="page-content">
				<div class="row">
					<div class="col-md-5">
						<div class="panel panel-default">
							<div class="panel-heading">
								<?php echo JText::_( 'COM_DIGICOM_ORDER_OPTIONS' ); ?>
							</div> <!-- .panel-heading -->
							<div class="panel-body">
								<?php echo $this->form->renderField('userid'); ?>
						  	<?php echo $this->form->renderField('status'); ?>
						  	<?php echo $this->form->renderField('order_date'); ?>
						  	<?php echo $this->form->renderField('processor'); ?>
							</div> <!-- .panel-body -->
						</div> <!-- .panel -->
					</div> <!-- .col-md-4 -->
					<div class="col-md-7">
						<div class="panel panel-default">
							<div class="panel-heading">
								<?php echo JText::_( 'COM_DIGICOM_ORDER_CREATE_NEW_PRODUCT_DETAILS_TITLE' ); ?>
							</div> <!-- .panel-heading -->
							<table id="productincludes" class="table table-striped table-hover" id="productList">
								<thead>
									<tr>
										<th><?php echo JText::_('COM_DIGICOM_PRODUCT'); ?></th>
										<th width="100px"><?php echo JText::_('COM_DIGICOM_PRICE'); ?></th>
										<th width="1%"><?php echo JText::_('COM_DIGICOM_ACTION'); ?></th>
									</tr>
								</thead>
								<tbody id="productincludes_items">
								</tbody>
							</table>

							<div class="panel-body">
								<a class="btn btn-success modal" title="Products" href="<?php echo $link; ?>"
								rel="{handler: 'iframe', size: {x: 800, y: 500}}">
									<i class="icon-file-add"></i>
									<?php echo JText::_('COM_DIGICOM_ADD_PRODUCT'); ?>
								</a>
								<div class="pull-right">
									<?php echo $this->form->renderField('promocode'); ?>
								</div>
							</div> <!-- .panel-body -->

							<table class="table table-hover table-condensed">
								<tr>
									<td style="width: 70%; text-align: right;"><strong><?php echo JText::_( 'COM_DIGICOM_SUBTOTAL' ); ?></strong></td>
									<td><span id="amount">00.00</span></td>
								</tr>
								<tr>
									<td style="text-align: right;"><strong><?php echo JText::_( 'COM_DIGICOM_DISCOUNT' ); ?></strong></td>
									<td><span id="discount_sign">00.00 <?php echo $configs->get('currency','USD'); ?></span></td>
								</tr>
								<tr>
									<td style="text-align: right;"><strong><?php echo JText::_( 'COM_DIGICOM_TAX' ); ?></strong></td>
									<td><span id="tax">00.00 <?php echo $configs->get('currency','USD'); ?></span></td>
								</tr>
								<tr>
									<td style="text-align: right;"><strong><?php echo JText::_( 'COM_DIGICOM_TOTAL' ); ?></strong></td>
									<td>
										<span id="total" class="hide">00.00 <?php echo $configs->get('currency','USD'); ?></span>
										<input id="amount_value" name="jform[amount]" type="text" value=""/>
									</td>
								</tr>
								<tr>
									<td style="text-align: right;"><strong><?php echo JText::_( 'COM_DIGICOM_AMOUNT_PAID' ); ?></strong></td>
									<td>
										<span id="currency_amount_paid" class="hide"></span>
					      		<input id="amount_paid" name="jform[amount_paid]" type="text" value="0"/>
									</td>
								</tr>
							</table>
						</div> <!-- .panel -->
					</div> <!-- .col-md-8 -->
				</div> <!-- .row -->
			</div> <!-- .page-content -->
		</div> <!-- .tx-main -->
	</div>

	<div id="from_ajax_div" style="display:none;"></div>
	<input type="hidden" name="option" value="com_digicom"/>
	<input type="hidden" name="view" value="ordernew"/>
	<input type="hidden" name="jform[price]" id="price_value" value="0"/>
	<input type="hidden" name="jform[tax]" id="tax_value" value="0"/>
	<input type="hidden" name="jform[shipping]" value="0"/>
	<input type="hidden" name="jform[discount]" id="discount" value="0"/>
	<input type="hidden" name="jform[currency]" id="currency_value" value=""/>
	<input type="hidden" name="task" value=""/>
	<?php echo JHtml::_('form.token'); ?>
</form>
