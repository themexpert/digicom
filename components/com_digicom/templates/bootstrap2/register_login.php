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
<form
	method="post"
	class="form-horizontal"
	action="<?php echo JRoute::_('index.php?option=com_digicom&view=register'); ?>"
	enctype="multipart/form-data"
	>

	<div class="control-group">
		<div class="control-label">
			<label id="username-lbl" for="username" class="required">
					<?php echo JText::_("COM_DIGICOM_USERNAME");?> <span class="star">&nbsp;*</span>
			</label>
		</div>
		<div class="controls">
			<input type="text" size="30" class="digi_textbox validate-username required" id="username" name="username" required="required" />
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<label id="passwd-lbl" for="passwd" class="required">
					<?php echo JText::_("COM_DIGICOM_PASSWORD");?> <span class="star">&nbsp;*</span>
			</label>
		</div>
		<div class="controls">
			<input type="password" size="30" class="digi_textbox validate-passwd required" id="passwd" name="passwd" required="required" />

			<?php $link = JRoute::_("index.php?option=com_users&view=reset"); ?>
			 (<a href="<?php echo $link;?>"><?php echo JText::_("COM_DIGICOM_REGISTER_LOGIN_FORGET_PASSWORD");?></a>)
		</div>
	</div>

	<div class="control-group">
		<div class="control-label">
			<label id="rememeber-lbl" for="rememeber">
					<?php echo JText::_("COM_DIGICOM_REMEMBER_ME");?>
		</div>
		<div class="controls">
			<input type="checkbox" value="1" id="rememeber" name="rememeber" />
		</div>
	</div>

	<div class="control-group">
		<button type="submit" class="btn btn-primary" style="margin-top: 10px;">
			Login
			<i class="icon-chevron-right icon-white"></i>
		</button>
	</div>

	<input type="hidden" name="option" value="com_digicom" />
	<input type="hidden" name="task" value="profile.logCustomerIn" />
	<input type="hidden" name="return" value="<?php echo JFactory::getApplication()->input->get("return", "","base64"); ?>" />
	<?php echo JHtml::_('form.token');?>
</form>
