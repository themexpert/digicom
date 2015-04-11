<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

JHtml::_('behavior.formvalidation');

require_once( JPATH_COMPONENT . '/helpers/sajax.php' );
$configs = $this->configs;
JHTML::_('behavior.modal');
	$login_link = JRoute::_("index.php?option=com_digicom&view=register&task=register.login&returnpage=cart&tmpl=component&returnpage=cart&graybox=true");
?>
<script type="text/javascript"><?php sajax_show_javascript(); ?></script>
<form name="adminForm" id="adminForm" method="post" action="<?php echo JRoute::_('index.php?option=com_digicom&view=profile'); ?>" onsubmit="return validateForm();" >
	<h2 class="digi-section-title"><?php echo JText::_('COM_DIGICOM_REGISTER');?></h2>
	<table style="border-collapse:separate !important;">

	<tr>
		<td>
			<?php echo JText::_("COM_DIGICOM_FIRST_NAME"); ?>&nbsp;<span class="error">*</span>
		</td>

		<td>
			<input name="firstname" type="text" id="firstname"   size="30" class="digi_textbox" value="<?php echo $this->userinfo->firstname; ?>"><b>&nbsp;</b>
		</td>
	</tr>

	<tr>
		<td>
			<?php echo JText::_("COM_DIGICOM_LAST_NAME"); ?>&nbsp;<span class="error">*</span>
		</td>

		<td>
			<input name="lastname" type="text" id="lastname"   size="30" class="digi_textbox" value="<?php echo $this->userinfo->lastname; ?>"><b>&nbsp;</b>
		</td>
	</tr>

	<?php if($this->askforcompany == 1){ ?>

	<tr>
		<td>
			<?php echo JText::_("COM_DIGICOM_COMPANY"); ?><b></b>
		</td>

		<td>
			<input name="company" type="text" id="company"   size="30" class="digi_textbox" value="<?php echo $this->userinfo->company; ?>">
		</td>
	</tr>

	<?php } ?>

	<tr>
		<td>
			<?php echo JText::_('COM_DIGICOM_EMAIL'); ?>&nbsp;<span class="error">*</span>
		</td>

		<td>
			<input name="email" type="text" id="email"  size="30" class="digi_textbox" value="<?php echo $this->userinfo->email; ?>" onchange="javascript:validateInput('email');" />
			&nbsp;&nbsp;
			<span class="" id="email_span">&nbsp;&nbsp;&nbsp;&nbsp;</span>
			<br/>
			<span style="display:none; color:#FF0000; font-size: 12px;" id="email_span_msg"><?php echo JText::_("DIGI_EMAIL_TAKEN")." "."<a rel=\"{handler: 'iframe', size: {x: 300, y: 300}}\"  class=\"modal\"  href=\"".$login_link."\">".JText::_('DIGI_HERE')."</a> ".JText::_("DIGI_TO_LOGIN"); ?></span>
		</td>
	</tr>

	<tr>
		<td>
			<h2 class="digi-section-title"><?php echo JText::_("COM_DIGICOM_REGISTER_LOGIN_INFORMATION"); ?></h2>
		</td>
		<td>&nbsp;</td>
	</tr>

	<tr>
		<td>
			<?php echo JText::_('COM_DIGICOM_USERNAME'); ?>&nbsp;<span class="error">*</span>
		</td>

		<td>
			<input name="username" type="text" id="username" size="30" class="digi_textbox" value="<?php echo $this->userinfo->username; ?>" onchange="javascript:validateInput('username');" />
			&nbsp;&nbsp;
			<span class="" id="username_span">&nbsp;&nbsp;&nbsp;&nbsp;</span>
			<br/>
			<span style="display:none; color:#FF0000; font-size: 12px;" id="username_span_msg"><?php echo JText::_("COM_DIGICOM_REGISTER_USERNAME_TAKEN")." "."<a rel=\"{handler: 'iframe', size: {x: 300, y: 300}}\"  class=\"modal\"  href=\"".$login_link."\">".JText::_('COM_DIGICOM_REGISTER_CLICK_HERE')."</a> ".JText::_("COM_DIGICOM_REGISTER_TO_CONTINUE"); ?></span>
		</td>
	</tr>

	<tr>
		<td>
			<?php echo JText::_("COM_DIGICOM_PASSWORD"); ?>&nbsp;<span class="error">*</span>
		</td>

		<td>
			<input name="password" type="password" id="password" size="30" class="digi_textbox"   value="<?php echo $this->userinfo->password; ?>" ><b>&nbsp;</b>
		</td>
	</tr>

	<tr>
		<td>
			<?php echo JText::_("COM_DIGICOM_CONFIRM_PASSWORD"); ?>&nbsp;<span class="error">*</span>
		</td>

		<td>
			<input name="password_confirm" type="password" id="password_confirm"   size="30" class="digi_textbox" value="<?php echo $this->userinfo->password_confirm; ?>"><b>&nbsp;</b>
		</td>
	</tr>

	<?php if($this->askforbilling == 1){ ?>

		<tr>
			<td>
				<h2><?php echo JText::_("COM_DIGICOM_BILLING_ADDRESS"); ?></h2>
			</td>
			<td>&nbsp;</td>
		</tr>

		<tr>
			<td>
				<?php echo JText::_("COM_DIGICOM_COUNTRY"); ?>&nbsp;<span class="error">*</span>
			</td>

			<td>
				<?php
					$customer = $this->customer;
					$customer->country = $this->userinfo->country;
					$country_option = DigiComSiteHelperDigiCom::get_country_options($customer, false, $configs);
					echo $country_option;
				?>
			</td>
		</tr>

		<tr>
			<td>
				<?php echo Jtext::_("COM_DIGICOM_ADDRESS"); ?>&nbsp;<span class="error">*</span>
			</td>

			<td>
				<input name="address" type="text" id="address"   size="30" class="digi_textbox" value="<?php echo $this->userinfo->address; ?>"><b>&nbsp;</b>
			</td>
		</tr>

		<tr>
			<td>
				<?php echo JText::_("COM_DIGICOM_CITY"); ?>&nbsp;<span class="error">*</span>
			</td>

			<td>
				<input id="city" type="text" value="<?php echo $this->userinfo->city; ?>" name="city"   size="30" class="digi_textbox" />
			</td>
		</tr>

		<tr>
			<td>
				<?php echo JText::_("COM_DIGICOM_STATE"); ?>&nbsp;<span class="error">*</span>
			</td>

			<td>
				<?php
					$customer = $this->customer;
					$customer->state = $this->userinfo->state;
					echo DigiComSiteHelperDigiCom::get_store_province($customer, false);
				?>
			</td>
		</tr>

		<tr>
			<td>
				<?php echo JText::_("COM_DIGICOM_ZIP"); ?>&nbsp;<span class="error">*</span>
			</td>

			<td>
				<input name="zipcode" type="text" id="zipcode"   size="30" class="digi_textbox" value="<?php echo $this->userinfo->zipcode; ?>"><b>&nbsp;</b>
			</td>
		</tr>
	<?php } ?>

	</table>

	<input type="hidden" name="option" value="com_digicom" >
	<input type="hidden" name="task" value="profile.saveCustomer" >
	<input type="hidden" name="processor" value="<?php echo JRequest::getVar("processor", ""); ?>" />
	<input type="hidden" name="return" value="<?php echo JRequest::getVar("return", ""); ?>" />
	<input type="hidden" name="view" value="profile" >
	
	<table width="100%">
		<tr>
			<td align="left">
				<button id="continue_button" type="submit" name="submit" class="btn btn-primary"><?php echo JText::_("COM_DIGICOM_REGISTER"); ?> <i class="ico-chevron-right ico-white"></i></button>
			</td>
		</tr>
	</table>
</form>