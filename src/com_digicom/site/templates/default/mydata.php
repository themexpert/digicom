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
<div id="digicom" class="dc dc-thankyou text-center">
	
	<?php echo $this->item->event->beforeMydata; ?>
    <code style="display: block;text-align: justify;overflow: scroll;"><?php echo print_r($this->item); ?></code>
	<?php echo $this->item->event->afterMydata; ?>
	
</div>
