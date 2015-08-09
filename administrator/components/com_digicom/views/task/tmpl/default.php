<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;
$app = JFactory::getApplication();
$input = $app->input;
$input->set('layout', 'dgtabs');
?>

<form action="<?php echo JRoute::_('index.php?option=com_digicom&view=task'); ?>" method="post" name="adminForm" autocomplete="off" class="form-validate form-horizontal">
	<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="">
<?php else : ?>
	<div id="j-main-container" class="">
<?php endif;?>

		<?php 
			JPluginHelper::importPlugin('digicom',$this->source);
			$dispatcher = JDispatcher::getInstance();
			$dispatcher->trigger('onDisplayView', array());
		?>

	</div>
</form>
