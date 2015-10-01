<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

$data = $displayData;

// Load the form filters
$filters = $data['view']->filterForm->getGroup('filter');
?>
<?php if ($filters) : $i=0;?>
	<?php foreach ($filters as $fieldName => $field) : ?>
		<?php if($i == 3) break; ?>
		<?php if ($fieldName != 'filter_search') : ?>
			<div class="js-stools-field-filter">
				<?php echo $field->input; ?>
			</div>
		<?php endif; ?>
	<?php $i++; endforeach; ?>
<?php endif; ?>

<?php
// Load the form filters
$filters = $data['view']->filterForm->getGroup('filter');
?>
<?php if ($filters) : $i=0;?>
	<div class="js-stools-container-list hidden-phone hidden-tablet shown input-append input-prepend">
	<?php foreach ($filters as $fieldName => $field) : ?>
		<?php if ($fieldName == 'filter_startdate' or $fieldName == 'filter_enddate') : ?>
				<label class="add-on"><?php echo JText::_($field->getAttribute('label')); ?></label>
				<?php echo $field->input; ?>
		<?php endif; ?>
	<?php endforeach; ?>
	<input type="submit" name="go" value="<?php echo JText::_( "DSGO" ); ?>" class="btn" />
</div>
<?php endif; ?>
