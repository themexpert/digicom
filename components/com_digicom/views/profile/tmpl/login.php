<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 341 $
 * @lastmodified	$LastChangedDate: 2013-10-10 14:28:28 +0200 (Thu, 10 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

defined ('_JEXEC') or die ("Go away.");

global $Itemid;
$returnpage = JRequest::getVar("returnpage", "", 'request');
if($returnpage != "cart") :
?>
<div class="digicom">
	<div class="digicom_title">
		<h2><?php echo JText::_("DSREGORLOG");?></h2>
	</div>
	
	<div class="register_table row-fluid">
		
		<div class="digicom_form span6 well well-small">
			<h2><?php echo JText::_("DIGI_HAVE_ACCOUNT"); ?></h2>
				
			<h3><?php echo JText::_("DIGI_EXIST_CUSTOMER");?></h3>
				
			<form name="login" method="post" action="index.php">
				<div class="control-group">
					<label class="control-label" for=""><?php echo JText::_("DSUSERNAME");?> <span class="error">*</span></label>
					<div class="controls">
						<input name="username" type="text" id="username" size="15" class="digi_textbox">
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label" for=""><?php echo JText::_("DSPASS");?> <span class="error">*</span></label>
					<div class="controls">
						<input name="passwd" type="password" id="passwd" size="15" class="digi_textbox">
					</div>
				</div>
				
				<div class="control-group">
					<div class="controls">
						<label class="checkbox">
							<input type="checkbox" name="rememeber" value="1" /> <?php echo JText::_("DIGI_PROFILE_REMEMBER_ME");?>
						</label>
					</div>
				</div>
				
				<div class="control-group">
					<div class="controls">
						<?php $link = JRoute::_("index.php?option=com_users&view=remind");?>
						<a href="<?php echo $link;?>"><?php echo JText::_("DIGI_PROFILE_FRG_USER");?></a> <br />
						<?php $link = JRoute::_("index.php?option=com_users&view=reset");?>
						<a href="<?php echo $link;?>"><?php echo JText::_("DIGI_PROFILE_FRG_PSW");?></a>
					</div>
				</div>
				
				<div class="control-group">
					<div class="controls">
						<input type="submit" class="btn" name="submit" value="<?php echo JText::_("DIGI_LOGIN_AND_CONTINUE"); ?>" />
					</div>
				</div>
			
				<input type="hidden" name="option" value="com_digicom" />
				<input type="hidden" name="controller" value="Profile" />
				<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
				<input type="hidden" name="task" value="logCustomerIn" />
				<input type="hidden" name="pid" value="<?php echo (JRequest::getVar("pid", "", 'request'));?>" />
				<input type="hidden" name="cid" value="<?php echo (JRequest::getVar("cid", "", 'request'));?>" />
				<input type="hidden" name="licid" value="<?php echo (JRequest::getVar("licid", ""));?>" />
				<input type="hidden" name="returnpage" value="<?php echo (JRequest::getVar("returnpage", "", 'request'));?>" />
				<input type="hidden" name="graybox" value="<?php echo (JRequest::getVar("graybox", ""));?>" />	 
			</form>
		</div>
		
		<div class="digicom_form span6 well well-small">
			<h2><?php echo JText::_("DIGI_CREATE_NEW_ACCOUNT"); ?></h2>
			
			<h3><?php echo JText::_("DIGI_REGISTER_NOW");?></h3>
			<form name="register" method="post" action="index.php">
				<input type="hidden" name="option" value="com_digicom" />
				<input type="hidden" name="controller" value="Profile" />
				<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
				<input type="hidden" name="task" value="register" />
				<input type="hidden" name="pid" value="<?php echo (JRequest::getVar("pid", "", 'request'));?>" />
				<input type="hidden" name="cid" value="<?php echo (JRequest::getVar("cid", "", 'request'));?>" />
				<input type="hidden" name="licid" value="<?php echo (JRequest::getVar("licid", ""));?>" />
				<input type="hidden" name="returnpage" value="<?php echo (JRequest::getVar("returnpage", "", 'request'));?>" />  
				
				<span class="text"><?php echo JText::_("DIGI_REGISTRATION_EASY"); ?></span>		
				<input type="submit" class="btn" style="display:block" value="<?php echo JText::_("DIGI_MYPROGRAMS_ACTION_CONTINUE");?>" />
			</form> 
		</div>
		
		<div class="clearfix"></div>
	</div>
</div>
<?php
	else :
?>
<div class="digicom">
		
		<div class="login_table">
			
			<div class="digicom_form">
				<h2><?php echo JText::_("DIGI_LOGIN"); ?></h2>

				<form name="login" method="post" action="index.php">
					<div class="control-group">
						<label class="control-label" for=""><?php echo JText::_("DSUSERNAME");?> <span class="error">*</span></label>
						<div class="controls">
							<input name="username" type="text" id="username" size="30">
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label" for=""><?php echo JText::_("DSPASS");?> <span class="error">*</span></label>
						<div class="controls">
							<input name="passwd" type="password" id="passwd" size="30">
						</div>
					</div>
					
					<div class="control-group">
						<div class="controls">
							<label class="checkbox">
								<input type="checkbox" name="rememeber" value="1" /> <?php echo JText::_("DIGI_PROFILE_REMEMBER_ME");?>
							</label>
						</div>
					</div>
					
					<div class="control-group">
						<div class="controls">
							<?php $link = JRoute::_("index.php?option=com_users&view=remind&tmpl=component");?>
							<a href="<?php echo $link;?>"><?php echo JText::_("DIGI_PROFILE_FRG_USER");?></a> <br />
							<?php $link = JRoute::_("index.php?option=com_users&view=reset&tmpl=component");?>
							<a href="<?php echo $link;?>"><?php echo JText::_("DIGI_PROFILE_FRG_PSW");?></a>
						</div>
					</div>

					<div class="control-group">
						<div class="controls">
							<input type="submit" class="btn" name="submit" value="<?php echo JText::_("DIGI_LOGIN")." >>"; ?>" />
						</div>
					</div>

					<input type="hidden" name="option" value="com_digicom" />
					<input type="hidden" name="controller" value="Profile" />
					<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
					<input type="hidden" name="task" value="logCustomerIn" />
					<input type="hidden" name="pid" value="<?php echo (JRequest::getVar("pid", "", 'request'));?>" />
					<input type="hidden" name="cid" value="<?php echo (JRequest::getVar("cid", "", 'request'));?>" />
					<input type="hidden" name="licid" value="<?php echo (JRequest::getVar("licid", ""));?>" />
					<input type="hidden" name="returnpage" value="<?php echo (JRequest::getVar("returnpage", "", 'request'));?>" />
					<input type="hidden" name="graybox" value="<?php echo (JRequest::getVar("graybox", ""));?>" />	 
				</form>
			
			</div>
		</div>
	</div>
<?php
endif;
?>	