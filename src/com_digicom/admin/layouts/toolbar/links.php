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
$href = $displayData['href'];
$role = $displayData['role'];
$class = $displayData['class'];
$data = $displayData['data'];
$icon = $displayData['icon'];
$text = $displayData['text'];
$title = $displayData['title'];

$attr = '';
$attr .= ($href ? ' href="'.$href.'"' : '');
$attr .= ($role ? ' role="'.$role.'"' : '');
$attr .= ($class ? ' class="'.$class.'"' : '');
$attr .= ($data ? ' data-toggle="'.$data.'"' : '');
$attr .= ($title ? ' title="'.$title.'"' : '');
?>
<a<?php echo $attr; ?>>
		<?php if($icon): ?> <span class="<?php echo $icon; ?>"></span><?php endif; ?>
		<?php echo $text; ?>
</a>
