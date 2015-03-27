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
JHtml::_('jquery.framework');
JHtml::_('behavior.framework');
JHtml::_('behavior.formvalidation');
$Itemid = JRequest::getInt("Itemid", 0);
?>


<div id="digicom">

	<?php if($this->configs->get('show_steps',0) == 0){ ?>
		<div class="pagination pagination-centered">
			<ul>
				<li><span><?php echo JText::_("DIGI_STEP_ONE"); ?></span></li>
				<li class="active"><span><?php echo JText::_("DIGI_STEP_TWO"); ?></span></li>
				<li><span><?php echo JText::_("DIGI_STEP_THREE"); ?></span></li>
			</ul>
		</div>
	<?php } ?>

	<h1 class="digi-page-title"><?php echo JText::_("DSREGORLOG");?></h1>

	<?php
		$checked = "";
		$display = "none";
		$display1 = "block";
		$login_register_invalid = isset($_SESSION["login_register_invalid"])?$_SESSION["login_register_invalid"]:'';
		if(trim($login_register_invalid) == "notok"){
			$checked = ' checked="checked" ';
			$display = "block";
			$display1 = "none";
		}
	?>

	<?php
	if(count(JFactory::getApplication()->getMessageQueue())):
		$message = JFactory::getApplication()->getMessageQueue();
	?>
	<div class="alert">
		<?php echo $message[0]['message']; ?>
	</div>
	<?php endif; ?>

	<div class="accordion" id="accordion2">
		<div class="accordion-group">
			<div class="accordion-heading">
			  <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne">
			    <?php echo JText::_("DIGI_LOGIN_BELOW"); ?>
			  </a>
			</div>
			<div id="collapseOne" class="accordion-body collapse in">
			  <div class="accordion-inner">
			    <div id="log_form">
			    	<form name="login" id="login" method="post" action="<?php echo JRoute::_('index.php?optioncom_digicom&view=profile&Itemid='.$Itemid); ?>">
						<table width="100%" style="border-collapse:separate !important;">
							<tr>
								<td class="field-login"><?php echo JText::_("DSUSERNAME");?>:
									<input type="text" size="30" class="digi_textbox" id="user_name" name="username"  />
								</td>
							</tr>
							<tr>
								<td class="field-login"><?php echo JText::_("DSPASS");?>:
									<?php $link = JRoute::_("index.php?option=com_users&view=reset"); ?>
									<input type="password" size="30" class="digi_textbox" id="passwd" name="passwd" /> (<a href="<?php echo $link;?>"><?php echo JText::_("DIGI_PROFILE_FRG_PSW");?></a>)
								</td>
							</tr>
							<tr>
								<td>
									<input type="checkbox" value="1" name="rememeber"> <span class="general_text_larger"><?php echo JText::_("DIGI_PROFILE_REMEMBER_ME");?></span>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<button type="submit" name="submit" class="btn btn-primary" style="margin-top: 10px;">Login <i class="ico-chevron-right ico-white"></i></button>
								</td>
							</tr>
						</table>

						<input type="hidden" name="Itemid" value="<?php echo $Itemid;?>" />
						<input type="hidden" name="option" value="com_digicom" />
						<input type="hidden" name="task" value="profile.logCustomerIn" />
						<input type="hidden" name="processor" value="<?php echo JRequest::getVar("processor", ""); ?>" />
						<input type="hidden" name="returnpage" value="<?php echo JRequest::getVar("returnpage", ""); ?>" />
					</form>
			    </div>
			  </div>
			</div>
		</div>

		<div class="accordion-group">
			<div class="accordion-heading">
			  <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseTwo">
			    <?php echo JText::_("DIGI_REGISTER_BELOW"); ?>
			  </a>
			</div>

			<div id="collapseTwo" class="accordion-body collapse">
			  <div class="accordion-inner">
			    <div id="reg_form">
			    	<?php include('register.php');	?>
			    </div>
			  </div>
			</div>
		</div>
	</div>

</div>

