<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

// TODO : Translatable string

defined('_JEXEC') or die;

$app 			= JFactory::getApplication();
$form 			= $displayData->getForm();
$input 			= $app->input;
$component 		= $input->getCmd('option', 'com_digicom');
$document 		= JFactory::getDocument();
$product 		= $displayData->get('item');
$configs 		= $displayData->get('configs');
$document->addScriptDeclaration('var DIGICOM_ALERT_REMOVE_PRODUCTS = "'. JText::_("COM_DIGICOM_PRODUCTS_ITEMS_REMOVE_WARNING") . '";');

$link = 'index.php?option=com_digicom&amp;view=products&amp;layout=modal&amp;tmpl=component&amp;' . JSession::getFormToken() . '=1';

$js = "
function jSelectProduct(id, title, catid, object, link, lang,price)
{
	var hreflang = '';
	if (lang !== '')
	{
		var hreflang = ' hreflang = \"' + lang + '\"';
	}

	var tag = '<tr id=\"productincludes_item_' + id + '\"><td><input type=\"hidden\" id=\"product_include_id'+id+'\" name=\"jform[bundle_product][]\" value=\"'+id+'\" /> <a target=\"_blank\"' + hreflang + ' href=\"' + link + '\">' + title + '</a></td><td>'+ price +'</td><td><a href=\#\" onclick=\"jRemveProduct('+ id +');\"><i class=\"icon-remove\"></i></a></td></tr>';
	//jInsertEditorText(tag, '" . 'productincludes_items' . "');
	jQuery('#productincludes_items').append(tag);
	// jModalClose();
	jQuery('#productselectorModal').modal('toggle');
}
function jRemveProduct(id){
	event.preventDefault();
	if (confirm(DIGICOM_ALERT_REMOVE_PRODUCTS))
	{
		jQuery('tr#productincludes_item_'+id).remove();
	}
}
";
$document->addScriptDeclaration($js);
$js = "
jQuery(function ($) {
	jQuery('input[name=\"jform[bundle_source]\"]').on('change', function(){
		var bundle_source = jQuery('#jform_bundle_source_option_select input:checked').val();
		jQuery(`.bundle_source_option:not(#bundle_source_` + bundle_source + `_option)`).hide();
		jQuery(`#bundle_source_` + bundle_source + `_option`).show();
	});
});
";

$document->addScriptDeclaration($js);
JHtml::_('behavior.modal');
$link = 'index.php?option=com_digicom&amp;view=products&amp;filter[product_type]=reguler&amp;layout=modal&amp;tmpl=component&amp;' . JSession::getFormToken() . '=1';
$form_data = $form->getData();
$bundle_source = $form_data->get('bundle_source');
?>

<fieldset class="adminform">

	<legend><?php echo JText::_('COM_DIGICOM_PRODUCT_BUNDLE_FILES');?></legend>
	<div class="alert alert-info">
		<?php echo JText::_("COM_DIGICOM_PRODUCT_BUNDLE_HEADER_NOTICE"); ?>
	</div>
	
	<?php echo $form->renderField('bundle_source'); ?>


	<hr>
	
	<div class="bundle_source_option <?php echo ($bundle_source == 'category' ? '' : ' hide');?>" id="bundle_source_category_option">
		<?php echo $form->renderField('bundle_category'); ?>
	</div>
	
	<div class="bundle_source_option <?php echo (($bundle_source == 'product' or $bundle_source =='') ? '' : ' hide');?>" id="bundle_source_product_option">
		
		<table id="productincludes" class="table table-striped table-hover" id="productList">
			<thead>
				<tr>
					<td>Product Name</td>
					<td width="100px">Price</td>
					<td width="1%">Action</td>
				</tr>
			</thead>
			<tbody id="productincludes_items">
				<?php
				
				$bundle_product = $form_data->get('bundle_product');
				
				if(isset($bundle_product) && count($bundle_product) > 0) :
					foreach($bundle_product as $key => $include) :
						if(is_array($include)) $include = (object) $include;
						$iprice = (isset($include->price) ? $include->price : '');
						$price = DigiComHelperDigiCom::format_price($iprice, $configs->get('currency','USD'), true, $configs);
					?>
						<tr id="productincludes_item_<?php echo $include->id;?>">
							<td>
								<input type="hidden" id="product_include_id<?php echo $include->id;?>" name="jform[bundle_product][]" value="<?php echo $include->id;?>">
								<a target="_blank" href="<?php echo JRoute::_('index.php?option=com_digicom&view=product&task=product.edit&id='.$include->id);?>"><?php echo $include->name;?></a>
							</td>
							<td width="100px"><?php echo $price;?></td>
							<td width="1%"><a href="#" onclick="jRemveProduct('<?php echo $include->id;?>');"><i class="icon-remove"></i></a></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
		

		<div style="margin:15px;padding:10px;">
			<a href="#productselectorModal" role="button" class="btn btn-link" data-toggle="modal" 
				title="<?php echo JText::_('COM_DIGICOM_PRODUCT_BUNDLE_ADD_PRODUCT'); ?>" 
				id="productselectorModalLink"
			>
				<i class="icon-file-add"></i> 
				<?php echo JText::_('COM_DIGICOM_PRODUCT_BUNDLE_ADD_PRODUCT'); ?>
			</a>
		</div>
		
	</div>

</fieldset>
<?php echo JHtml::_(
	'bootstrap.renderModal',
	'productselectorModal',
	array(
		'title'       => JText::_('COM_DIGICOM_PRODUCT_BUNDLE_ADD_PRODUCT'),
		'backdrop'    => 'static',
		'keyboard'    => false,
		'closeButton' => true,
		'url'         => $link,
		'height'      => '400px',
		'width'       => '800px',
		'bodyHeight'  => '70',
		'modalWidth'  => '80',
		'footer'      => '<a type="button" class="btn" data-dismiss="modal" aria-hidden="true"'
				. ' onclick="jQuery(\'#productselectorModal iframe\').contents().find(\'#closeBtn\').click();">'
				. JText::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</a>',
	)
); ?>
