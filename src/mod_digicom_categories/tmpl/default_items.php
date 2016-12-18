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
<?php foreach($list as $item) :?>
	<li <?php if ($_SERVER['REQUEST_URI'] == JRoute::_(DigiComSiteHelperRoute::getCategoryRoute($item->id))) echo ' class="active"';?>>
		<a href="<?php echo JRoute::_(DigiComSiteHelperRoute::getCategoryRoute($item->id)); ?>">
			<?php echo $item->title; ?>
				<?php if ($params->get('numitems')) : ?>
					(<?php echo $item->numitems; ?>)
				<?php endif; ?>
		</a>

		<?php if ($params->get('show_children', 0) && (($params->get('maxlevel', 0) == 0)
				|| ($params->get('maxlevel') >= ($item->level - $startLevel)))
			&& count($item->getChildren())) : ?>
			<?php echo '<ul>'; ?>
			<?php $temp = $list; ?>
			<?php $list = $item->getChildren(); ?>
			<?php require JModuleHelper::getLayoutPath('mod_digicom_categories', $params->get('layout', 'default') . '_items'); ?>
			<?php $list = $temp; ?>
			<?php echo '</ul>'; ?>
		<?php endif; ?>
	</li>
<?php endforeach; ?>