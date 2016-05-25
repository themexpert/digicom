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

<?php
JPluginHelper::importPlugin('digicom',$this->source);
$dispatcher = JDispatcher::getInstance();
$dispatcher->trigger('onDigicomTaskDisplayView', array());
?>
