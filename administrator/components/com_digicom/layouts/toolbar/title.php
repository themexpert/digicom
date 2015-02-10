<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_messages
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
// Instantiate a new JLayoutFile instance and render the layout
$title = $displayData['title'];
$class = $displayData['class'];
?>
<h3 class="<?php echo $class; ?>"><?php echo $title; ?></h3>