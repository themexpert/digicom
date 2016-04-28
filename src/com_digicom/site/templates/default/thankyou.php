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
	
	<?php echo $this->item->event->beforeThankyou; ?>

	<div class="thankyou-sign">
		<i class="icon-checkmark-2" style="font-size: 100px;color: #00C853;height: 65px;width: 100px;margin: 0px;padding: 50px 0 0;"></i>
	</div>
	<h2 class="thankyou-title">
		<?php echo JText::_('COM_DIGICOM_THANKYOU_TITLE'); ?>
	</h2>
	<p><?php echo JText::_('COM_DIGICOM_THANKYOU_DESC'); ?></p>
	
	<p class="thankyou-action">
		<a 
			class="btn btn-default" 
			href="<?php echo JRoute::_('index.php?option=com_digicom&view=order&id='.$this->item->id); ?>"
		>
			<?php echo JText::_('COM_DIGICOM_ORDER_VIEW_INVOICE'); ?>
		</a>
		<a 
			class="btn btn-default" 
			href="<?php echo JRoute::_('index.php?option=com_digicom&view=downloads'); ?>"
		>
			<?php echo JText::_('COM_DIGICOM_DOWNLOAD'); ?>
		</a>
	</p>
	
	<?php echo $this->item->event->afterThankyou; ?>
	
</div>
