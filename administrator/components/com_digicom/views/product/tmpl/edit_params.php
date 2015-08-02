<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;
?>
<?php $fields = $this->form->getFieldset('attribs'); ?>
<?php if (count($fields)) : ?>
    <?php echo JHtml::_('bootstrap.addTab', 'digicomTab', 'params', JText::_('COM_DIGICOM_PRODUCT_BUNDLE_FILES_SELECTION', true)); ?>

    <?php echo $this->form->getControlGroup('attribs'); ?>
    <?php foreach ($this->form->getGroup('attribs') as $field) : ?>
        <?php echo $field->getControlGroup(); ?>
    <?php endforeach; ?>

    <?php echo JHtml::_('bootstrap.endTab'); ?>
<?php endif;?>