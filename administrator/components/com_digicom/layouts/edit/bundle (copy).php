<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
$app = JFactory::getApplication();
$form = $displayData->getForm();
$input = $app->input;
$component = $input->getCmd('option', 'com_digicom');

$document = JFactory::getDocument();
$document->addScript(JURI::root(true).'/media/digicom/assets/js/repeatable-fields.js?v=1.0.0');
$product = $displayData->get('item');
$link = 'index.php?option=com_digicom&amp;view=products&amp;layout=modal&amp;tmpl=component&amp;' . JSession::getFormToken() . '=1';
?>
<script>
	//bundle_source_option
	jQuery(function ($) {
		jQuery('#jform_bundle_source_option_select .btn').click(function(){
			var bundle_source = jQuery('input[type=radio]:checked').val();
			//alert(bundle_source);
			//var bundle_source = jQuery('input.jform_bundle_source:checked').val();
			jQuery('.bundle_source_option').hide('slide');
			jQuery('#bundle_source_'+bundle_source+'_option').show('slide');
		});
	});
</script>
<fieldset class="adminform">

	<legend><?php echo JText::_('VIEWPRODPACKAGE');?></legend>
	<div class="alert alert-info">
		<?php echo JText::_("HEADER_PRODUCTINCLUDE"); ?>
	</div>
	
	<?php echo $form->renderField('bundle_source'); ?>


	<hr>
	
	<div class="bundle_source_option <?php echo ($product->bundle_source == 'category' ? '' : ' hide');?>" id="bundle_source_category_option">
		<?php echo $form->renderField('bundle_category'); ?>
	</div>
	
	<div class="bundle_source_option <?php echo (($product->bundle_source == 'product' or $product->bundle_source =='') ? '' : ' hide');?>" id="bundle_source_product_option">
		
		<script type="text/javascript">

			function grayBoxiJoomla(link_element, width, height){
				SqueezeBox.open(link_element, {
					handler: 'iframe',
					size: {x: width, y: height}
				});
			}

			// Add new include item

			window.addEvent('domready', function(){

				$('buttonaddincludeproduct').addEvent('click', function(e) {
					e.stop()||new Event(e).stop();

					//var url = "index.php?option=com_digicom&controller=products&task=productincludeitem&no_html=1&tmpl=component&format=raw";
					var url = "<?php echo $link; ?>";

					 var req = new Request.HTML({
						method: 'get',
						url: url,
						data: { 'do' : '1' },
						//update: $('productincludes'),
						onComplete: function(transport){
							$('productincludes').adopt(transport);

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


			// Remove include item

			function remove_product_include( box_id ) {

				var product_include_id = document.getElementById('product_include_id' + box_id).value;
				//console.log(product_include_id);
				var bundle_remove_id = document.getElementById('jform_bundle_remove_id').value;
				//console.log(bundle_remove_id);
				if(product_include_id){
					if(bundle_remove_id){
						document.getElementById('jform_bundle_remove_id').value = bundle_remove_id + ',' + product_include_id; 
						//bundle_remove_id.val(bundle_remove_id + ',' + product_include_id); 
					}else{
						//bundle_remove_id.val(product_include_id); 
						document.getElementById('jform_bundle_remove_id').value = product_include_id; 
					}
				}
				
				var box = document.getElementById('product_include_box_' + box_id);
				//var box = box.parentNode;
				while (box.firstChild) {
					box.removeChild( box.firstChild );
				}

				// remove wrapper div to include item
				var parent_box = document.getElementById('productincludes');
				parent_box.removeChild(box);
				
				
				
			}
		</script>
		<table id="productincludes" class="table table-striped table-hover" id="productList">
			<thead>
				<tr>
					<td>Product Name</td>
					<td width="1%">Price</td>
					<td width="1%">Action</td>
				</tr>
			</thead>
			<tbody>
				
			</tbody>
		</table>
		<div>


			<?php 
			if(count($product->bundle_product) > 0) :
			foreach($product->bundle as $key => $include) :
				if($include->bundle_type == 'product'){
			?>
			
				<div id="product_include_box_<?php echo $key; ?>" style="border-bottom:1px solid #ccc;margin:15px;padding:10px;">
					<table width="100%">
						<tr>
							<td style="" width="30%"><?php echo JText::_( 'DSPROD' ); ?></td>
							<td style="">
								<div style="float:left">
									<span id="product_include_name_text_<?php echo $key; ?>" style="line-height: 17px;padding: 0.2em; border: 1px solid rgb(204, 204, 204); display: block; width: 250px;"><?php echo $include->name; ?></span>
									<input type="hidden" value="<?php echo $include->bundle_id; ?>" id="product_include_id<?php echo $key; ?>" name="jform[bundle][product][<?php echo $key; ?>]"/>
								</div>
								<div class="button2-left">
									<div class="blank input-append" style="padding:0">
										<a rel="{handler: 'iframe', size: {x: 800, y: 600}}" href="index.php?option=com_digicom&controller=products&task=selectProductInclude&id=<?php echo $key; ?>&tmpl=component" title="Select a Product Include" class="btn btn-small modal">Select</a>
									</div>
								</div>
							</td>
							<td style="">
								<a id="product_include_remove_1" class="btn btn-small btn-danger" href="javascript:void(0)" onclick="remove_product_include('<?php echo $key; ?>');">Remove</a>
							</td>
						</tr>
						
					</table>
				</div>

				<?php } ?>
			<?php endforeach; ?>
			<?php endif; ?>
		</div>

		<div style="margin:15px;padding:10px;">
			<a id="buttonaddincludeproduct" class="btn btn-small" href="#"><?php echo JText::_('VIEWPRODADDPRODUCT'); ?></a>
			<input type="hidden" name="bundle_remove_id" value="" id="jform_bundle_remove_id"/>
		</div>
		
	</div>

</fieldset>
