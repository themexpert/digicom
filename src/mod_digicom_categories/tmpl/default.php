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
<ul class="digicom-categories-module nav nav-menu <?php echo $moduleclass_sfx; ?>">
<?php require JModuleHelper::getLayoutPath('mod_digicom_categories', $params->get('layout', 'default') . '_items'); ?>
</ul>