<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

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

	<h1 class="digi-page-title"><?php echo JText::_("COM_DIGICOM_LOGIN_REGISTER");?></h1>

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
			    <?php echo JText::_("COM_DIGICOM_REGISTER_LOGIN_BELOW"); ?>
			  </a>
			</div>
			<div id="collapseOne" class="accordion-body collapse in">
			  <div class="accordion-inner">
			    <div id="log_form">
			    	<form name="login" id="login" method="post" action="<?php echo JRoute::_('index.php?optioncom_digicom&view=profile&Itemid='.$Itemid); ?>">
						<table width="100%" style="border-collapse:separate !important;">
							<tr>
								<td class="field-login"><?php echo JText::_("COM_DIGICOM_USERNAME");?>:
									<input type="text" size="30" class="digi_textbox" id="user_name" name="username"  />
								</td>
							</tr>
							<tr>
								<td class="field-login"><?php echo JText::_("COM_DIGICOM_PASSWORD");?>:
									<?php $link = JRoute::_("index.php?option=com_users&view=reset"); ?>
									<input type="password" size="30" class="digi_textbox" id="passwd" name="passwd" /> (<a href="<?php echo $link;?>"><?php echo JText::_("COM_DIGICOM_REGISTER_LOGIN_FORGET_PASSWORD");?></a>)
								</td>
							</tr>
							<tr>
								<td>
									<input type="checkbox" value="1" name="rememeber"> <span class="general_text_larger"><?php echo JText::_("COM_DIGICOM_REMEMBER_ME");?></span>
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
			    <?php echo JText::_("COM_DIGICOM_REGISTER_REGISTER_BELOW"); ?>
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

