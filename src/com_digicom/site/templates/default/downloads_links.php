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
<div class="list-group">
<?php foreach($this->itemList['items'] as $pkey=>$item):?>
	<a href="index.php?option=com_digicom&view=download&id=<?php echo $item->productid; ?>" class="list-group-item list-group-item-action">
		<?php echo $item->name; ?>
	</a>
<?php endforeach; ?>
</div>
