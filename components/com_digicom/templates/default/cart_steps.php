<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;
$input = JFactory::getApplication()->input;
$view = $input->get('view','cart');
?>
<?php if($this->configs->get('show_steps',1) == 1){ ?>

<div class="pagination pagination-centered">
	<ul>
		<li<?php echo ($view == 'cart' ? ' class="active"' : ''); ?>><span><?php echo JText::_("COM_DIGICOM_BUYING_PROCESS_STEP_ONE"); ?></span></li>
		<li<?php echo ($view == 'register' ? ' class="active"' : ''); ?>><span><?php echo JText::_("COM_DIGICOM_BUYING_PROCESS_STEP_TWO"); ?></span></li>
		<li<?php echo ($view == 'checkout' ? ' class="active"' : ''); ?>><span><?php echo JText::_("COM_DIGICOM_BUYING_PROCESS_STEP_THREE"); ?></span></li>
	</ul>
</div>
<?php } ?>
