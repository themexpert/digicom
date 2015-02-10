<?php
/**
  @version		$Id: default.php 341 2013-10-10 12:28:28Z thongta $
 * @package		obRSS Feed Creator for Joomla.
 * @copyright	(C) 2007-2012 themexpert.com. All rights reserved.
 * @author		themexpert.com
 * @license		GNU/GPLv3, see LICENSE
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

echo '<ul class="nav nav-menu">';
foreach ($categories AS $category) {
	echo '
		<li><a href="index.php?option=com_digicom&controller=products&task=list&cid='.$category->id.'&Itemid='.$itemid.'">'.$category->title.'</a></li>
	';
}
echo '</ul>';