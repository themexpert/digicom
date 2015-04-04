<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

// No direct access.
defined('_JEXEC') or die;
?>

<?php if ($menuItems) : ?>
<ul id="menu" class="nav<?php echo ($hideMainmenu ? ' disabled' : ''); ?>" >
	<li class="dropdown<?php echo ($hideMainmenu ? ' disabled' : ''); ?>" >
		<a class="dropdown-toggle" data-toggle="dropdown" href="#">
			<span class="icon-cart" style="color: #1f82e0;"></span>
			<?php echo $menuItems->text;?>
			<span class="caret"></span>
		</a>

		<?php if (!$hideMainmenu) : ?>
			<?php if (count($menuItems->submenu) > 0) : ?>
				<ul class="dropdown-menu">
					<?php foreach ($menuItems->submenu as $sub) { 
						$child = ($sub->child ? true : false);
					?>
						<li<?php echo ($child ? ' class="dropdown-submenu"' : ''); ?>>
							<a href="<?php echo $sub->link; ?>" <?php echo ($child ? ' class="dropdown-toggle" data-toggle="dropdown"' : ''); ?>><?php echo $sub->text; ?></a>
							<?php if($child) : ?>

								<ul id="product-submenu-com-digicom" class="dropdown-menu mod-menu-digicom">
									<?php foreach ($sub->childitems as $key => $item) { ?>
										<li><a href="<?php echo $item['link']; ?>"><?php echo $item['title']; ?></a></li>
									<?php }?>
								</ul>

							<?php endif; ?>
						</li>
					<?php } ?>
				</ul>
			<?php endif; ?>
		<?php endif; ?>
	</li>
</ul>
<?php endif; ?>