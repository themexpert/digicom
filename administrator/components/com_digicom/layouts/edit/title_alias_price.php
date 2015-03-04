<?php
/**
 * @package     Joomla.Cms
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

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
