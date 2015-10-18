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
JHtml::_('jquery.ui', array('sortable'));
JText::script('COM_DIGICOM_PRODUCTS_FILES_REMOVE_WARNING');
$document = JFactory::getDocument();
$fileSampleRow  = '';
$fileSampleRow .= '<tr>';
$fileSampleRow .= '<td style="vertical-align: middle;">';
$fileSampleRow .= '<div class="text-center">';
$fileSampleRow .= '<span class="move"><i class="icon-move"></i></span>';
$fileSampleRow .= '<input type="hidden" name="jform[file][{{row-count-placeholder}}][id]" id="digicom_files_id" value="" />';
$fileSampleRow .= '<input type="hidden" name="jform[file][{{row-count-placeholder}}][ordering]" value="" id="files_row_count_placeholder_id_ordering"/>';
$fileSampleRow .= '</div>';
$fileSampleRow .= '</td>';
$fileSampleRow .= '<td><input type="text" name="jform[file][{{row-count-placeholder}}][name]" id="files_row_count_placeholder_id_name" placeholder="File Name"/></td>';
$fileSampleRow .= '<td>';
$fileSampleRow .= '<div class="input-prepend input-append" style="display: block;">';
$fileSampleRow .= '<input type="text" name="jform[file][{{row-count-placeholder}}][url]" id="files_row_count_placeholder_id_url" placeholder="Upload or enter the file URL" class="span8"/>';
$fileSampleRow .= '<a class="files_uploader_modal btn modal" title="Select" href="javascript:;" onclick="openModal(this);">Select</a>';
$fileSampleRow .= '</div>';
$fileSampleRow .= '</td>';
$fileSampleRow .= '<td style="vertical-align: middle;">';
$fileSampleRow .= '<div class="text-center">';
$fileSampleRow .= '<a href="#" class="remove" onclick="removeFilesRow(this, event);"><i class="icon-remove"></i></a>';
$fileSampleRow .= '</div>';
$fileSampleRow .= '</td>';
$fileSampleRow .= '</tr>';
$document->addScriptDeclaration("var fileSampleRow = '" . $fileSampleRow . "';");
?>
<fieldset class="adminform">
	<legend><?php echo JText::_('COM_DIGICOM_PRODUCT_SINGLE_FILES');?></legend>
	<div id="digicom_item_files_items" class="repeat">

		<table class="table table-striped wrapper" id="filesitemList">
			<thead>
				<tr>
					<th>
						<div class="text-center">
							<i class="icon-menu-2"></i>
						</div>
					</th>
					<th><?php echo JText::_('COM_DIGICOM_PRODUCT_FILE_NAME_LABEL');?></th>
					<th><?php echo JText::_('COM_DIGICOM_PRODUCT_FILE_URL_LABEL');?></th>
					<th>
						<div class="text-center">
							<?php echo JText::_('COM_DIGICOM_ACTION');?>
						</div>
					</th>

				</tr>
			</thead>
			<tbody id="itemsfilesRows">
				<?php
					$form = $displayData->getForm();
					$item = $displayData->get('item');
					$form_data = $form->getData();
					$files = $form_data->get('file');
					if((isset($item->file) && count($item->file) >=1 && is_array($item->file)) or is_array($files) ):
					foreach($files as $key => $value){
						if(is_array($value)) $value = (object) $value;
					?>
					<tr data-index="<?php echo $key; ?>">
						<td style="vertical-align: middle;">
							<div class="text-center">
								<span class="move"><i class="icon-move"></i></span>
								<input type="hidden" name="jform[file][<?php echo $key; ?>][id]" id="digicom_files_id" value="<?php echo (isset($value->id) ? $value->id : ''); ?>" />
								<input type="hidden" name="jform[file][<?php echo $key; ?>][ordering]" id="files_ordering_<?php echo $key; ?>"
									value="<?php echo (isset($value->ordering) ? $value->ordering : ''); ?>" />
							</div>
						</td>
						<td>
							<input type="text" id="files_<?php echo $key; ?>_name"
							name="jform[file][<?php echo $key; ?>][name]" placeholder="File Name" value="<?php echo (isset($value->name) ? $value->name : ''); ?>" />
						</td>
						<td>
							<div class="input-prepend input-append" style="display: block;">
								<input type="text" name="jform[file][<?php echo $key; ?>][url]" id="files_<?php echo $key; ?>_url" placeholder="Upload or enter the file URL" class="span8"
								value="<?php echo (isset($value->url) ? $value->url : ''); ?>" />

								<a class="files_uploader_modal btn modal" title="Select"
									href="javascript:;" onclick="openModal(this);">

									Select
								</a>
							</div>
						</td>
						<td style="vertical-align: middle;">
							<div class="text-center">
								<a href="#" class="remove" onclick="removeFilesRow(this, event);"><i class="icon-remove"></i></a>
							</div>
						</td>
					</tr>
					<?php
					}
					endif;
				?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="4">
						<a href="#" onclick="addFilesRow(event);" class="add btn btn-success">Add File +</a>
						<input type="hidden" name="jform[files_remove_id]" value="" id="jform_files_remove_id"/>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
</fieldset>
