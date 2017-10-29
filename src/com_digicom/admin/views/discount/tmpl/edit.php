<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

jimport('joomla.html.pane');
JHtml::_('jquery.framework');
JHtml::_('jquery.ui');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.tooltip');
JHTML::_("behavior.calendar");
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen');
JHtml::_('behavior.modal');

$document = JFactory::getDocument();
$app = JFactory::getApplication();
$input = $app->input;
$configs = $this->configs;
$nullDate = 0;
$link = 'index.php?option=com_digicom&amp;view=products&amp;layout=modal&amp;tmpl=component&amp;' . JSession::getFormToken() . '=1';

$ajax = <<<EOD
jQuery(document).ready(function() {
	jQuery('#jform_discount_enable_range .btn').click(function(){
		var discount_enable_range = jQuery("input:radio[name='jform[discount_enable_range]']:checked").val();
		if(discount_enable_range === '1'){
			jQuery('#discount_enable_range_product').hide('slide');
		}else{
			jQuery('#discount_enable_range_product').show('slide');
		}
	});
});
function jSelectProduct(id, title, catid, object, link, lang,price)
{
	var hreflang = '';
	if (lang !== '')
	{
		var hreflang = ' hreflang = \"' + lang + '\"';
	}

	var tag = '<tr id=\"productincludes_item_' + id + '\"><td><input type=\"hidden\" id=\"product_include_id'+id+'\" name=\"jform[products][]\" value=\"'+id+'\" /><strong>'+ title +'</strong></td><td><a href=\#\" onclick=\"digiRemoveProduct('+ id +', event);\"><i class=\"icon-remove\"></i></a></td></tr>';
	//jInsertEditorText(tag, '" . 'productincludes_items' . "');
	jQuery('#productincludes_items').append(tag);
	jModalClose();
}
function digiRemoveProduct(id,e){
	e.preventDefault();
	jQuery('tr#productincludes_item_'+id).remove();
	//changePlain();
}

/* ]]> */
EOD;
$doc = JFactory::getDocument();
$doc->addScriptDeclaration( $ajax );
?>

<script language="javascript" type="text/javascript">
Joomla.submitbutton = function(task)
{
	if (task == 'discount.cancel' || document.formvalidator.isValid(document.id('adminForm')))
	{
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
}
</script>


<form action="<?php echo JRoute::_('index.php?option=com_digicom&view=discount&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
	
	<div id="digicom" class="dc digicom">
		<div class="tx-sidebar"><?php echo $this->sidebar; ?></div> <!-- .tx-sidebar -->
		<div class="tx-main">
			<div class="page-header">
				<h1>Manage Coupon</h1>
				<p><?php echo JText::_('COM_DIGICOM_DICOUNT_TITLE_DESC');?></p>
			</div> <!-- .page-header -->
			<div class="page-content">
				<div class="row">
					<div class="col-md-6">
						<div class="panel panel-default">
							<div class="panel-heading">Coupon Code</div>
							<div class="panel-body">
								<div class="form-horizontal">
									<?php 
										$fieldSets = $this->form->getFieldsets();
										foreach ($fieldSets as $name => $fieldSet) :
											foreach ($this->form->getFieldset($name) as $field):
												if($field->fieldname == 'discount_enable_range' or $field->fieldname == 'products') continue;
												echo $field->getControlGroup();
											endforeach;
										endforeach; 
									 ?>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="panel panel-default">
							<div class="panel-heading"><?php echo JText::_('COM_DIGICOM_DISCOUNT_CODE_ENABLE_FOR_ALL_PRODUCTS_LABEL');?></div>
							<div class="panel-body">
								<?php echo $this->form->getInput('discount_enable_range'); ?>
							</div>
							<div id="discount_enable_range_product"<?php echo (($this->item->discount_enable_range == '1' || $this->item->discount_enable_range === null) ? " class='hide'":"");?>>
								<div class="panel-footer">
									<a class="btn btn-primary modal" title="Products" href="<?php echo $link; ?>"
									rel="{handler: 'iframe', size: {x: 800, y: 500}}">
										<i class="icon-file-add"></i>
										<?php echo JText::_('COM_DIGICOM_ADD_PRODUCT'); ?>
									</a>
								</div>
								<table id="productincludes" class="table" id="productList">
									<tbody id="productincludes_items">
										<?php
										if(count($this->item->products) > 0):
										foreach ($this->item->products as $product): ?>
											<tr id="productincludes_item_<?php echo $product->id; ?>">
												<td>
													<input type="hidden" id="product_include_id<?php echo $product->id; ?>" name="jform[products][]" value="<?php echo $product->id; ?>">
													<strong><?php echo $product->name; ?></strong>
												</td>
												<td>
													<a href="#&quot;" onclick="digiRemoveProduct(<?php echo $product->id; ?>, event);">
														<i class="icon-remove"></i>
													</a>
												</td>
											</tr>
										<?php
										endforeach;
										endif;
										?>
									</tbody>
								</table>
							</div> <!-- #discount_enable_range_product -->
						</div>
					</div>
				</div>
			</div> <!-- .page-content -->
		</div> <!-- .tx-main -->
	</div>

	<?php
	$options = array(
		'onActive'		=> 'function(title, description){
					description.setStyle("display", "block");
					title.addClass("open").removeClass("closed");
				}',
		'onBackground'	=> 'function(title, description){
					description.setStyle("display", "none");
					title.addClass("closed").removeClass("open");
				}',
		'useCookie'		=> true, // this must not be a string. Don't use quotes.
		'active'		=> 'general-settings'
	);?>
	
  <input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="discount" />
	<input type="hidden" name="option" value="com_digicom" />
	<?php echo JHtml::_('form.token'); ?>
</form>
