<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
$document = JFactory::getDocument();
$document->addScript(JURI::root(true).'/media/digicom/assets/js/repeatable-fields.js?v=1.0.0');
?>
<script type="text/javascript">
jQuery(function(){
	jQuery('.repeat').each(function() {
		jQuery(this).repeatable_fields();
	});
});
</script>
<fieldset class="adminform">
	<legend><?php echo JText::_('COM_DIGICOM_PRODUCT_SINGLE_FILES');?></legend>
	<div id="digicom_item_files_items" class="repeat">
		<table class="table table-striped wrapper" id="itemList">
			<thead>
				<tr class="row">
					<th width="1%">
						<i class="icon-menu-2"></i>
					</th>
					<th style="width: 20%">
						File Name
					</th>
					<th>
						File URL
					</th>
					<th style="width: 2%"></th>
				</tr>
			</thead>
			<tbody class="container">
				<tr class="template row">
					<td width="1%"><span class="move"><i class="icon-move"></i></span></td>
					
					<td width="10%">
						<input type="text" name="jform[file][{{row-count-placeholder}}][name]" placeholder="File Name"/>
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
					
					<td width="10%"><span class="remove"><i class="icon-remove"></i></span></td>
				</tr>
				<?php
					$item = $displayData;
					
					if(isset($item->file) && count($item->file) >=1 && is_array($item->file)) :
					$files = $item->file;
					foreach($files as $key => $value){?>
					<tr class="row">
						<td width="1%">
							<span class="move"><i class="icon-move"></i></span>
							<input type="hidden" name="digicom_files_id" value="<?php echo $value->id; ?>" />
						</td>
						
						<td width="10%">
							<input type="text" 
							name="jform[file][<?php echo $key; ?>][name]" placeholder="File Name" value="<?php echo $value->name; ?>"/>
						</td>
						
						<td width="70%">
							<div class="input-prepend input-append" style="display: block;">
								<input type="text" name="jform[file][<?php echo $key; ?>][url]" id="files_<?php echo $key; ?>_url" placeholder="Upload or enter the file URL" class="span8"
								value="<?php echo $value->url; ?>"
								/>
								<a class="files_uploader_modal btn modal" title="Select" 
								href="javascript:;" onclick="openModal(this);"
								>
								Select</a>
							</div>
						</td>
						
						<td width="10%">
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
