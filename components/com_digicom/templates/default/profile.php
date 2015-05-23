<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

JHTML::_('behavior.modal');
$app=JFactory::getApplication();
$input = $app->input;

$cust = $this->customer->_customer;
$user = $this->customer->_user;
$eu = $this->eu;
$uid = $user->id ? $this->customer->_user->id : 0;
$configs = $this->configs;
?>
<div class="digicom">
	<?php DigiComSiteHelperDigicom::loadModules('digicom_toolber'); ?>

	<div class="digicom_form_account">
		<h2 class="digi-page-title"><?php echo JText::_("COM_DIGICOM_PROFILE_PAGE_TITLE"); ?></h2>

		<form action="<?php echo JRoute::_('index.php?option=com_digicom&view=profile');?>" method="post" name="adminForm" id="adminForm" onsubmit="return validateForm();" class="form-horizontal">
			<h3 class="digi-section-title"><?php echo JText::_('COM_DIGICOM_PROFILE_SECTION_TITLE_PROFILE_SETTINGS'); ?></h3>
			
			<div class="row-fluid">
				<div class="control-group">
					<label class="control-label" for="firstname"><?php echo JText::_("COM_DIGICOM_FIRST_NAME"); ?> <span class="error">*</span></label>
					<div class="controls" style="display:inherit;">
						<input name="firstname" type="text" id="firstname" size="30" value="<?php echo $cust->firstname ?>" />
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="control-group">
					<label class="control-label" for="lastname"><?php echo JText::_("COM_DIGICOM_LAST_NAME"); ?> <span class="error">*</span></label>
					<div class="controls" style="display:inherit;">
						<input name="lastname" type="text" id="lastname" size="30" value="<?php echo $cust->lastname ?>" />
					</div>
				</div>
			</div>
	
			<?php if($configs->get('askforcompany',1) == 1) { ?>
			<div class="row-fluid">
				<div class="control-group">
					<label class="control-label" for="company"><?php echo JText::_("COM_DIGICOM_COMPANY"); ?></label>
					<div class="controls" style="display:inherit;">
						<input name="company" type="text" id="company" size="30" value="<?php echo $cust->company ?>" />
					</div>
				</div>
			</div>
			<?php } ?>
			<div class="row-fluid">
				<div class="control-group">
					<label class="control-label" for="email"><?php echo JText::_("COM_DIGICOM_EMAIL"); ?> <span class="error">*</span></label>
					<div class="controls" style="display:inherit;">
						<input name="email" type="text" id="email" size="30" value="<?php echo $user->email ?>" />
					</div>
				</div>
			</div>

			<h3 class="digi-section-title"><?php echo JText::_('COM_DIGICOM_PROFILE_SECTION_TITLE_LOGIN_INFO'); ?></h3>
			<div class="row-fluid">
				<div class="control-group">
					<label class="control-label" for="username"><?php echo JText::_("COM_DIGICOM_USERNAME"); ?> <span class="error">*</span></label>
					<div class="controls" style="display:inherit;">
						<input name="username" <?php if ($cust->id) { ?> disabled <?php } ?> type="text" id="username" size="30" value="<?php echo $user->username ?>" />
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="control-group">
					<label class="control-label" for="password"><?php echo JText::_("COM_DIGICOM_PASSWORD"); ?></label>
					<div class="controls" style="display:inherit;">
						<input name="password" type="password" id="password" size="30" />
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="control-group">
					<label class="control-label" for="password_confirm"><?php echo JText::_("COM_DIGICOM_CONFIRM_PASSWORD"); ?></label>
					<div class="controls" style="display:inherit;">
						<input name="password_confirm" type="password" id="password_confirm" size="30" />
					</div>
				</div>
			</div>
		

			<?php if($configs->get('askforbilling','0') == 1){ ?>
			<h3 class="digi-section-title"><?php echo JText::_('COM_DIGICOM_PROFILE_SECTION_TITLE_BILLING_ADDRESS'); ?></h3>
			<div class="row-fluid">
				<div class="control-group">
					<label class="control-label" for="country_option"><?php echo JText::_("COM_DIGICOM_COUNTRY"); ?> <span class="error">*</span></label>
					<div class="controls" style="display:inherit;">
						<?php echo $this->lists['country_option']; ?>
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="control-group">
					<label class="control-label" for="customerlocation"><?php echo JText::_("COM_DIGICOM_STATE"); ?> <span class="error">*</span></label>
					<div class="controls" style="display:inherit;">
						<?php echo $this->lists['customerlocation']; ?>
					</div>
				</div>
			</div>
			
			<div class="row-fluid">
				<div class="control-group">
					<label class="control-label" for="address"><?php echo JText::_("COM_DIGICOM_ADDRESS"); ?> <span class="error">*</span></label>
					<div class="controls" style="display:inherit;">
						<input name="address" type="text" id="address" value="<?php echo $cust->address; ?>" />
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="control-group">
					<label class="control-label" for="city"><?php echo JText::_("COM_DIGICOM_CITY"); ?> <span class="error">*</span></label>
					<div class="controls" style="display:inherit;">
						<input name="city" type="text" id="city" value="<?php echo $cust->city; ?>" />
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="control-group">
					<label class="control-label" for="customerlocation"><?php echo JText::_("COM_DIGICOM_ZIP"); ?> <span class="error">*</span></label>
					<div class="controls" style="display:inherit;">
						<input name="zipcode" type="text" id="zipcode" value="<?php echo $cust->zipcode; ?>" />
					</div>
				</div>
			</div>
			<?php } ?>

			<div id="vathead" class="row-fluid" style="display:<?php echo (isset($cust->country) && in_array($cust->country, $eu) ? "" : "none"); ?>">
				<div class="control-group">
					<label class="control-label" for="shipzipcode"><?php echo JText::_("COM_DIGICOM_SHIPPING_ZIP"); ?> <span class="error">*</span></label>
					<div class="controls" style="display:inherit;">
						<input name="shipzipcode" type="text" id="shipzipcode" value="<?php echo $cust->shipzipcode; ?>" />
					</div>
				</div>
			</div>
			<div id="personcomp" class="row-fluid" style="display:<?php echo (isset($cust->country) && in_array($cust->country, $eu) ? "" : "none"); ?>">
				<div class="control-group">
					<label class="control-label" for="person"><?php echo JText::_("COM_DIGICOM_PERSON_OR_COMPANY"); ?> <span class="error">*</span></label>
					<div class="controls" style="display:inherit;">
						<select id="person" name="person" onchange="showTaxNum(this.value);">
							<option value="1" <?php echo (($cust->person != 0) ? "selected" : ""); ?> ><?php echo JText::_("COM_DIGICOM_PERSON"); ?></option>
							<option value="0" <?php echo (($cust->person == 0) ? "selected" : ""); ?>><?php echo JText::_("COM_DIGICOM_COMPANY"); ?></option>
						</select>
					</div>
				</div>
			</div>
			<div id="comptaxnum" class="row-fluid" style="display:<?php echo (isset($cust->country) && in_array($cust->country, $eu) ? "" : "none"); ?>">
				<div class="control-group">
					<label class="control-label" for="person"><?php echo JText::_("COM_DIGICOM_TAX_NUMBER"); ?> <span class="error">*</span></label>
					<div class="controls" style="display:inherit;">
						<input name="taxnum" type="text" id="taxnum" value="<?php echo ( $cust->taxnum > 0 ) ? $cust->taxnum : ""; ?>" />
					</div>
				</div>
			</div>
			<div class="control-group">
				<div class="controls"><?php
					$text = "";
					if($cust->id < 1)
					{
						$text = "DSSAVEPROFILE";
					}
					else
					{
						$text = "JSAVE";
					} ?>
					<button type="submit" class="btn btn-success btn-blue"><i class="ico-ok-sign ico-white"></i> <?php echo JText::_($text) ?></button>
				</div>
			</div>

			<input type="hidden" name="images" value="" />
			<input type="hidden" name="option" value="com_digicom" />
			<input type="hidden" name="id" value="<?php echo $cust->id; ?>" />
			<input type="hidden" name="task" value="profile.save" />
			<input type="hidden" name="return" value="<?php echo base64_encode($input->get("return", JURI::getInstance()->toString(), 'request')); ?>" />
			<input type="hidden" name="view" value="profile" />
		</form>
	</div>

	<?php DigiComSiteHelperDigicom::loadModules('digicom_footer','xhtml'); ?>

</div>

<script>
<?php 
include (JPATH_COMPONENT.'/helpers/sajax.php'); 
sajax_show_javascript(); 
?>
</script>

<?php echo DigiComSiteHelperDigiCom::powered_by(); ?>
