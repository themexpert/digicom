<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

$form = $displayData->getForm();
$item = $displayData->get('item');
$title = $form->getField('title') ? 'title' : ($form->getField('name') ? 'name' : '');
?>
<script>
jQuery(function($) {
	changeValidity();
});
</script>
<div class="form-title-alias">
	<div class="row-fluid digicom-product-title">
		<div class="span12">
			<?php echo $title ? $form->renderField($title) : '';?>

			<?php if(!empty($item->alias)):?>
				<div class="form-inline">
					<?php echo $form->getLabel('alias'); ?> : 
					<span id="digicom-product-alias" class="muted">
						<?php echo $item->alias; ?>
					</span>
					<span id="digicom-product-alias-edit" class="hide">
						<?php echo $form->getInput('alias'); ?>
					</span>

					<a href="#" id="digicom-edit-alias"><i class="icon-edit"></i></a>
				</div>
			<?php endif; ?>

		</div>
	</div>
	
	<div class="row-fluid form-inline digicom-product-price">
		
		<div class="span4">
			<div class="control-group">
				<div class="control-label"><?php echo $form->getLabel('price'); ?></div>
				<div class="controls">
					<div class="row-fluid input-prepend">
						<?php echo $form->getInput('price'); ?>
						
					</div>
				</div>
			</div>
		</div>
		<div class="span8">
			<div class="control-group">
				<div class="control-label"><?php echo $form->getLabel('expiration_length'); ?></div>
				<div class="controls">
					<div class="row-fluid input-prepend">
						<?php echo $form->getInput('price_type'); ?>
						<?php echo $form->getInput('expiration_length'); ?>
						<?php echo $form->getInput('expiration_type'); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
