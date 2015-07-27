<?php
/**
 * @package		DigiCom
 * @copyright	Copyright (c)2010-2015 ThemeXpert
 * @license 	GNU General Public License version 3, or later
 * @author 		ThemeXpert http://www.themexpert.com
 * @since 		1.0.0
 */

defined('_JEXEC') or die;
$uri = JFactory::getURI();
$pageURL = $uri->toString();
?>

<div id="toggle_settings_wrap" class="">
	<h3><?php echo JText::_('COM_DIGICOM_SIDEBAR_RIGHT_GLOBAL_SETTINGS'); ?></h3>
	<ul>
		<?php if (JFactory::getUser()->authorise('core.admin', 'com_digicom')) { ?>
			<li><a href="<?php echo JRoute::_('index.php?option=com_config&view=component&component=com_digicom&return='.base64_encode($pageURL));?>">
				<?php echo JText::_('COM_DIGICOM_SIDEBAR_RIGHT_ACL_SETTINGS'); ?>
			</a></li>
			<li><a href="<?php echo JRoute::_('index.php?option=com_digicom&view=configs');?>">
				<?php echo JText::_('COM_DIGICOM_SIDEBAR_RIGHT_SETTINGS'); ?>
			</a></li>
		<?php } ?>
	</ul>

	<?php
	$dispatcher = JDispatcher::getInstance();
	$results = $dispatcher->trigger( 'onAfterSidebarMenu', array());
	?>

</div>
