<?php
/**
 * @package		DigiCom
 * @copyright	Copyright (c)2010-2015 ThemeXpert
 * @license 	GNU General Public License version 3, or later
 * @author 		ThemeXpert http://www.themexpert.com
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

JHtmlBehavior::core();
?>

<div id="j-toggle-sidebar-wrapper">
	<div id="j-toggle-button-wrapper" class="j-toggle-button-wrapper">
		<?php echo JLayoutHelper::render('joomla.sidebars.toggle'); ?>
	</div>
	<div id="j-toggle-sidebar-header" class="j-toggle-sidebar-header">
		<?php echo JText::_('JTOGGLE_SIDEBAR_LABEL');?>
	</div>
	<div id="sidebar" class="sidebar">
		<div class="sidebar-nav">
			<?php if ($displayData->displayMenu) : ?>
			<ul id="submenu" class="nav nav-list">
				<li>
					<a class="dglogo" href="index.php?option=com_digicom">DigiCom</a>
				</li>
				<?php
				$total = count($displayData->list);
				$i = 1;
				foreach ($displayData->list as $item) :
					if($i==$total){
						$dispatcher = JDispatcher::getInstance();
						$dispatcher->trigger( 'onDigicomAfterMainMenu', array());
					}

					if (isset ($item[2]) && $item[2] == 1) : ?>
						<li class="active hasTooltip" data-original-title="<?php echo $item[0]; ?>" data-placement="right">
					<?php else : ?>
						<li class="hasTooltip" title="<?php echo $item[0]; ?>" data-placement="right">
					<?php endif;
					if ($displayData->hide) : ?>
						<a class="nolink"><?php echo $item[0]; ?></a>
					<?php else :
						if (strlen($item[1])) : ?>
							<a href="<?php echo JFilterOutput::ampReplace($item[1]); ?>"><?php echo $item[0]; ?></a>
						<?php else : ?>
							<?php echo $item[0]; ?>
						<?php endif;
					endif; ?>
					</li>
					<?php
					$i++;
				endforeach;
				?>

			</ul>
			<?php endif; ?>
			<?php if ($displayData->displayMenu && $displayData->displayFilters) : ?>
			<hr />
			<?php endif; ?>
			<?php if ($displayData->displayFilters) : ?>
			<div class="filter-select hidden-phone">
				<h4 class="page-header"><?php echo JText::_('JSEARCH_FILTER_LABEL');?></h4>
				<?php foreach ($displayData->filters as $filter) : ?>
					<label for="<?php echo $filter['name']; ?>" class="element-invisible"><?php echo $filter['label']; ?></label>
					<select name="<?php echo $filter['name']; ?>" id="<?php echo $filter['name']; ?>" class="span12 small" onchange="this.form.submit()">
						<?php if (!$filter['noDefault']) : ?>
							<option value=""><?php echo $filter['label']; ?></option>
						<?php endif; ?>
						<?php echo $filter['options']; ?>
					</select>
					<hr class="hr-condensed" />
				<?php endforeach; ?>
			</div>
			<?php endif; ?>
		</div>
	</div>
	<div id="j-toggle-sidebar"></div>
</div>
