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
<div class="form-title-alias">
	<div class="row-fluid digicom-product-title">
		<div class="span12">
			<?php echo $title ? $form->renderField($title) : '';?>

			<?php if(!empty($item->alias)):?>
				<div class="form-inline">
					<?php echo $form->getLabel('alias'); ?> : <span id="digicom-product-alias" class="muted"><?php echo $form->getInput('alias'); ?></span><i class="icon-edit"></i>
				</div>
			<?php endif; ?>

		</div>
	</div>
	
	<div class="row-fluid">
		<div class="span12">
			<?php echo $form->renderField('price'); ?>
		</div>
	</div>
</div>
