<?php
/**
 * @package		DigiCom
 * @copyright	Copyright (c)2010-2015 ThemeXpert
 * @license 	GNU General Public License version 3, or later
 * @author 		ThemeXpert http://www.themexpert.com
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

// Instantiate a new JLayoutFile instance and render the layout
$title = $displayData['title'];
$class = $displayData['class'];
?>
<h3 class="<?php echo $class; ?>"><?php echo $title; ?></h3>