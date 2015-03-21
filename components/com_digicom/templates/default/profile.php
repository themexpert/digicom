<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 364 $
 * @lastmodified	$LastChangedDate: 2013-10-15 15:27:43 +0200 (Tue, 15 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

defined ('_JEXEC') or die ("Go away.");

JHTML::_('behavior.modal');

$cust = $this->customer->_customer;
$user = $this->customer->_user;
$eu = $this->eu;
$uid = $user->id ? $this->customer->_user->id : 0;
$configs = $this->configs;
$Itemid = JRequest::getVar("Itemid", "0");
?>
<div class="digicom">
	<?php DigiComSiteHelperDigicom::loadModules('digicom_toolber'); ?>

	<div class="digicom_form_account">
		<h2><?php echo JText::_("DIGI_MY_STORE_ACCOUNT"); ?></h2>

		<form action="index.php?option=com_digicom" method="post" name="adminForm" id="adminForm" onsubmit="return validateForm();" class="form-horizontal">
			<h3><?php echo JText::_('DSPROFILESETTINGS'); ?></h3>
			
			<div class="row-fluid">
				<div class="control-group">
					<label class="control-label" for="firstname"><?php echo JText::_("DSFIRSTNAME"); ?> <span class="error">*</span></label>
					<div class="controls" style="display:inherit;">
						<input name="firstname" type="text" id="firstname" size="30" value="<?php echo $cust->firstname ?>" />
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="control-group">
					<label class="control-label" for="lastname"><?php echo JText::_("DSLASTNAME"); ?> <span class="error">*</span></label>
					<div class="controls" style="display:inherit;">
						<input name="lastname" type="text" id="lastname" size="30" value="<?php echo $cust->lastname ?>" />
					</div>
				</div>
			</div>
	
			<?php if($configs->get('askforcompany',1) == 1) { ?>
			<div class="row-fluid">
				<div class="control-group">
					<label class="control-label" for="company"><?php echo JText::_("DSCOMPANY"); ?></label>
					<div class="controls" style="display:inherit;">
						<input name="company" type="text" id="company" size="30" value="<?php echo $cust->company ?>" />
					</div>
				</div>
			</div>
			<?php } ?>
			<div class="row-fluid">
				<div class="control-group">
					<label class="control-label" for="email"><?php echo JText::_("DSEMAIL"); ?> <span class="error">*</span></label>
					<div class="controls" style="display:inherit;">
						<input name="email" type="text" id="email" size="30" value="<?php echo $user->email ?>" />
					</div>
				</div>
			</div>

			<h3><?php echo JText::_('DSLOGININFO'); ?></h3>
			<div class="row-fluid">
				<div class="control-group">
					<label class="control-label" for="username"><?php echo JText::_("DSUSERNAME"); ?> <span class="error">*</span></label>
					<div class="controls" style="display:inherit;">
						<input name="username" <?php if ($cust->id) { ?> disabled <?php } ?> type="text" id="username" size="30" value="<?php echo $user->username ?>" />
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="control-group">
					<label class="control-label" for="password"><?php echo JText::_("DSPASS"); ?></label>
					<div class="controls" style="display:inherit;">
						<input name="password" type="password" id="password" size="30" />
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="control-group">
					<label class="control-label" for="password_confirm"><?php echo JText::_("DSCPASS"); ?></label>
					<div class="controls" style="display:inherit;">
						<input name="password_confirm" type="password" id="password_confirm" size="30" />
					</div>
				</div>
			</div>
		

			<?php if($configs->get('askforbilling','0') == 1){ ?>
			<h3><?php echo JText::_('DSBILLINGADR'); ?></h3>
			<div class="row-fluid">
				<div class="control-group">
					<label class="control-label" for="country_option"><?php echo JText::_("DSCOUNTRY"); ?> <span class="error">*</span></label>
					<div class="controls" style="display:inherit;">
						<?php echo $this->lists['country_option']; ?>
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="control-group">
					<label class="control-label" for="address"><?php echo JText::_("DSBILLING"); ?> <span class="error">*</span></label>
					<div class="controls" style="display:inherit;">
						<input name="address" type="text" id="address" value="<?php echo $cust->address; ?>" />
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="control-group">
					<label class="control-label" for="city"><?php echo JText::_("DSCITY"); ?> <span class="error">*</span></label>
					<div class="controls" style="display:inherit;">
						<input name="city" type="text" id="city" value="<?php echo $cust->city; ?>" />
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="control-group">
					<label class="control-label" for="customerlocation"><?php echo JText::_("DSSTATE"); ?> <span class="error">*</span></label>
					<div class="controls" style="display:inherit;">
						<?php echo $this->lists['customerlocation']; ?>
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="control-group">
					<label class="control-label" for="customerlocation"><?php echo JText::_("DSZIP"); ?> <span class="error">*</span></label>
					<div class="controls" style="display:inherit;">
						<input name="zipcode" type="text" id="zipcode" value="<?php echo $cust->zipcode; ?>" />
					</div>
				</div>
			</div>
			<?php } ?>

			<div id="vathead" class="row-fluid" style="display:<?php echo (isset($cust->country) && in_array($cust->country, $eu) ? "" : "none"); ?>">
				<div class="control-group">
					<label class="control-label" for="shipzipcode"><?php echo JText::_("DSSHIPPINGZIP"); ?> <span class="error">*</span></label>
					<div class="controls" style="display:inherit;">
						<input name="shipzipcode" type="text" id="shipzipcode" value="<?php echo $cust->shipzipcode; ?>" />
					</div>
				</div>
			</div>
			<div id="personcomp" class="row-fluid" style="display:<?php echo (isset($cust->country) && in_array($cust->country, $eu) ? "" : "none"); ?>">
				<div class="control-group">
					<label class="control-label" for="person"><?php echo JText::_("DSPERSONORCOMP"); ?> <span class="error">*</span></label>
					<div class="controls" style="display:inherit;">
						<select id="person" name="person" onchange="showTaxNum(this.value);">
							<option value="1" <?php echo (($cust->person != 0) ? "selected" : ""); ?> ><?php echo JText::_("DSPERS"); ?></option>
							<option value="0" <?php echo (($cust->person == 0) ? "selected" : ""); ?>><?php echo JText::_("DSCOMP"); ?></option>
						</select>
					</div>
				</div>
			</div>
			<div id="comptaxnum" class="row-fluid" style="display:<?php echo (isset($cust->country) && in_array($cust->country, $eu) ? "" : "none"); ?>">
				<div class="control-group">
					<label class="control-label" for="person"><?php echo JText::_("DSTAXNUM"); ?> <span class="error">*</span></label>
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
						$text = "DSSAVE";
					} ?>
					<button type="submit" class="btn btn-success btn-blue"><i class="ico-ok-sign ico-white"></i> <?php echo JText::_($text) ?></button>
				</div>
			</div>

			<input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>" />
			<input type="hidden" name="images" value="" />
			<input type="hidden" name="option" value="com_digicom" />
			<input type="hidden" name="id" value="<?php echo $cust->id; ?>" />
			<input type="hidden" name="task" value="saveCustomer" />
			<input type="hidden" name="pid" value="<?php echo (JRequest::getVar("pid", "", 'request'));?>" />
			<input type="hidden" name="cid" value="<?php echo (JRequest::getVar("cid", "", 'request'));?>" />
			<input type="hidden" name="returnpage" value="<?php echo (JRequest::getVar("returnpage", "", 'request')); ?>" />
			<input type="hidden" name="controller" value="Profile" />
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
