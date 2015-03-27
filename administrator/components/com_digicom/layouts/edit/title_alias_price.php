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

$title = $form->getField('title') ? 'title' : ($form->getField('name') ? 'name' : '');

?>
<div class="form-title-alias">
	<div class="row-fluid">
		<div class="span12">
			<?php echo $title ? $form->renderField($title) : '';?>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span7">
			<?php echo $form->renderField('alias'); ?>
		</div>
		<div class="span5">
			<?php echo $form->renderField('price'); ?>
		</div>
	</div>
</div>
