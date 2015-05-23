<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

$app=JFactory::getApplication();
$input = $app->input;
$Itemid = $input->get("Itemid", 0);
$returnpage = $input->get("returnpage", "");

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
				<input type="hidden" name="view" value="profile" />
				<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
				<input type="hidden" name="task" value="profile.logCustomerIn" />
				<input type="hidden" name="returnpage" value="<?php echo ($input->get("returnpage", ""));?>" />
				<input type="hidden" name="graybox" value="<?php echo ($input->get("graybox", ""));?>" />	 
			</form>
		</div>
		
		<div class="digicom_form span6 well well-small">
			<h2><?php echo JText::_("DIGI_CREATE_NEW_ACCOUNT"); ?></h2>
			
			<h3><?php echo JText::_("DIGI_REGISTER_NOW");?></h3>
			<form name="register" method="post" action="<?php echo JRoute::_('index.php?option=com_digicom&view=register&Itemid='.$Itemid); ?>">
				<input type="hidden" name="option" value="com_digicom" />
				<input type="hidden" name="view" value="register" />
				<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
				<input type="hidden" name="returnpage" value="<?php echo ($input->get("returnpage", "", 'request'));?>" />  
				
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
					<input type="hidden" name="controller" value="profile" />
					<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
					<input type="hidden" name="task" value="logCustomerIn" />
					<input type="hidden" name="pid" value="<?php echo ($input->get("pid", "", 'request'));?>" />
					<input type="hidden" name="cid" value="<?php echo ($input->get("cid", "", 'request'));?>" />
					<input type="hidden" name="licid" value="<?php echo ($input->get("licid", ""));?>" />
					<input type="hidden" name="returnpage" value="<?php echo ($input->get("returnpage", "", 'request'));?>" />
					<input type="hidden" name="graybox" value="<?php echo ($input->get("graybox", ""));?>" />	 
				</form>
			
			</div>
		</div>
	</div>
<?php
endif;
?>	