<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

// TODO : Translatable String
defined('_JEXEC') or die;

$document = JFactory::getDocument();
$document->addScriptDeclaration('var DIGICOM_ALERT_REMOVE_FILES = "'. JText::_("COM_DIGICOM_PRODUCTS_FILES_REMOVE_WARNING") . '";');
$document->addScript(JURI::root(true).'/media/com_digicom/js/repeatable-fields.js?v=1.0.0');
?>

<fieldset class="adminform">
	<legend><?php echo JText::_('COM_DIGICOM_PRODUCT_SINGLE_FILES');?></legend>
	<div id="digicom_item_files_items" class="repeat">
		<table class="table table-striped wrapper" id="itemList">
			<thead>
				<tr class="row">
					<th width="10%">
						<i class="icon-menu-2"></i>
					</th>
					<th width="10%">
						File Name
					</th>
					<th width="70%">
						File URL
					</th>
					<th style="width: 10%"></th>
				</tr>
			</thead>
			<tbody class="container">
				<tr class="template row">
					<td width="10%">
						<span class="move"><i class="icon-move"></i></span>
						<input type="hidden" name="jform[file][{{row-count-placeholder}}][id]" id="digicom_files_id" value="" />
					</td>

					<td width="10%">
						<input type="text" name="jform[file][{{row-count-placeholder}}][name]" id="files_row_count_placeholder_id_name" placeholder="File Name"/>
					</td>

					<td width="70%">
						<div class="input-prepend input-append" style="display: block;">
							<input type="text" name="jform[file][{{row-count-placeholder}}][url]" id="files_row_count_placeholder_id_url" placeholder="Upload or enter the file URL" class="span8"/>
							<a class="files_uploader_modal btn modal" title="Select"
							href="javascript:;" onclick="openModal(this);"
							>
							Select</a>
						</div>
					</td>

					<td width="10%">
						<span class="remove"><i class="icon-remove"></i></span>
						<input type="hidden" name="jform[file][{{row-count-placeholder}}][ordering]" value="" id="files_row_count_placeholder_id_ordering"/>
					</td>
				</tr>
				<?php
					$form = $displayData->getForm();
					$item = $displayData->get('item');
					$form_data = $form->getData();
					$files = $form_data->get('file');
					if((isset($item->file) && count($item->file) >=1 && is_array($item->file)) or is_array($files) ):

					foreach($files as $key => $value){
						if(is_array($value)) $value = (object) $value;
					?>
					<tr class="row">
						<td width="10%">
							<span class="move"><i class="icon-move"></i></span>
							<input type="hidden" name="jform[file][<?php echo $key; ?>][id]" id="digicom_files_id" value="<?php echo (isset($value->id) ? $value->id : ''); ?>" />
						</td>

						<td width="10%">
							<input type="text" id="files_<?php echo $key; ?>_name"
							name="jform[file][<?php echo $key; ?>][name]" placeholder="File Name" value="<?php echo (isset($value->name) ? $value->name : ''); ?>"/>
						</td>

						<td width="70%">
							<div class="input-prepend input-append" style="display: block;">
								<input type="text" name="jform[file][<?php echo $key; ?>][url]" id="files_<?php echo $key; ?>_url" placeholder="Upload or enter the file URL" class="span8"
								value="<?php echo (isset($value->url) ? $value->url : ''); ?>"
								/>
								<a class="files_uploader_modal btn modal" title="Select"
								href="javascript:;" onclick="openModal(this);"
								>
								Select</a>
							</div>
						</td>

						<td width="10%">
							<input type="hidden" name="jform[file][<?php echo $key; ?>][ordering]" id="files_ordering_<?php echo $key; ?>"
								value="<?php echo (isset($value->ordering) ? $value->ordering : ''); ?>"
								/>
							<span class="remove"><i class="icon-remove"></i></span>
						</td>
					</tr>
					<?php
					}
					endif;
				?>
			</tbody>
			<tfoot>
				<tr class="row">
					<td width="10%" colspan="4">
						<span class="add btn btn-mini">Add</span>
						<input type="hidden" name="jform[files_remove_id]" value="" id="jform_files_remove_id"/>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>



</fieldset>
