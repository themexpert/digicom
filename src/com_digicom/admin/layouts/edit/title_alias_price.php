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
<div class="row">
	<div class="col-md-12">
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
<div class="row">
	<div class="col-md-4">
		<div class="form-group">
	    <label><?php echo $form->getLabel('price'); ?></label>
	    <?php echo $form->getInput('price'); ?>
	  </div>
	</div>
	<div class="col-md-8">

			<label><?php echo $form->getLabel('expiration_length'); ?></label>
			<div class="input-group">
				<?php echo $form->getInput('price_type'); ?>
				<?php echo $form->getInput('expiration_length'); ?>
				<?php echo $form->getInput('expiration_type'); ?>
			</div>

	</div>
</div>
