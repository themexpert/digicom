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

$emails = $this->emails;
$task = JRequest::getVar("task", "");
$configs = $this->configs;
$search = JRequest::getVar("search", "");

$document = JFactory::getDocument();
//$document->addStyleSheet("components/com_digicom/assets/css/digicom.css");

?>
<fieldset class="adminform">
	<legend><?php echo JText::_('VIEWPRODEMAILS'); ?></legend>
		<form id="adminForm" name="adminForm" action="index.php" method="post">
			<table width="100%">
				<tr>
					<td width="100%" align="right">
						<input type="text" name="search" value="<?php echo $search; ?>" placeholder="<?php echo JText::_("DSSEARCH"); ?>" />
						<input type="submit" name="go" value="<?php echo JText::_( "DSSEARCH" ); ?>" class="btn" />
					</td>
				</tr>
			</table>

			<table>
				<tr>
					<td class="header_zone" colspan="4">
						<?php
							echo JText::_("HEADER_SYSTEMEMAILS");
						?>
					</td>
				</tr>
				<tr>
					<td align="right">
						<a class="modal digi_video" rel="{handler: 'iframe', size: {x: 750, y: 435}}" href="index.php?option=com_digicom&controller=about&task=vimeo&id=38437540">
							<img src="<?php echo JURI::base(); ?>components/com_digicom/assets/images/icon_video.gif" class="video_img" />
							<?php echo JText::_("COM_DIGICOM_VIDEO_SYSTEM_EMAILS"); ?>				  
						</a>
					</td>
				</tr>
			</table>

			<?php
			if(isset($emails) && count($emails) > 0){
			?>
				<table class="adminlist table">
					<th width="15%">
						<?php echo JText::_('DIGI_EMAIL_TYPE_NAME'); ?>
					</th>
					<th width="30%">
						<?php echo JText::_('DSTO'); ?>
					</th>
					<th width="20%">
						<?php echo JText::_('VIEWPRODSUBJ'); ?>
					</th>
					<th width="25%">
						<?php echo JText::_('DIGI_DATE_TIME'); ?>
					</th>
					<th width="10%">
						<?php echo JText::_('DIGI_VIEW'); ?>
					</th>

					<?php
						$k = 0;
						foreach($emails as $key=>$email){
							$email = (Array)$email;
					?>
							<tr class="row<?php echo $k; ?>">
								<td>
									<?php
										if(intval($email["emailid"]) == 0){
											if($email["emailname"] == "New Customer Email" || $email["emailname"] == "New purchase emails"){
												echo '<a href="index.php?option=com_digicom&controller=configs&task2=email">'.$email["emailname"].'</a>';
											}
											elseif($email["emailname"] == "Product Email"){
												echo '<a href="index.php?option=com_digicom&controller=products&task=edit&cid[]='.$email["productid"].'&tab=10">'.$email["emailname"].'</a>';
											}
											elseif($email["emailname"] == "Order Email"){
												echo '<a href="index.php?option=com_digicom&controller=configs&task2=email">'.$email["emailname"].'</a>';
											}
										}
										else{
											$email_name = $this->getEmailName($email["emailid"]);
											echo '<a href="index.php?option=com_digicom&controller=emailreminders&task=edit&cid[]='.$email["emailid"].'&tab=8">'.$email_name.'</a>';
										}
									?>
								</td>
								<td>
									<?php
										$user_details = $this->getUserDetails($email["userid"]);
										echo '<a href="index.php?option=com_digicom&controller=customers&task=edit&cid[]='.$email["userid"].'">'.$user_details["0"]["firstname"].' '.$user_details["0"]["lastname"].'</a>'.' ('.$user_details["0"]["username"].')';
									?>
								</td>
								<td>
									<?php
										$subject = $email["subject"];
										if(strlen($subject) > 30){
											$subject = substr($subject, 0, 30)."...";
										}
										echo $subject;
									?>
								</td>
								<td>
									<?php 
										$email_date_int = strtotime($email["send_date"]);
										$email_day = date("d", $email_date_int);
										$email_month = date("m", $email_date_int);
										$email_year = date("Y", $email_date_int);
										$email_hour = date("H", $email_date_int);
										$email_min = date("i", $email_date_int);

										$today = date("Y-m-d H:i:s");
										$today_int = strtotime($today);
										$today_day = date("d", $today_int);
										$today_month = date("m", $today_int);
										$today_year = date("Y", $today_int);
										$today_hour = date("H", $today_int);
										$today_min = date("i", $today_int);
										if(($today_day == $email_day) && ($today_month == $email_month) && ($today_year == $email_year)){
											echo JText::_("VIEWSTATTODAY")." (".date($configs->get('time_format','DD-MM-YYYY'), $email_date_int).") ".JText::_("DIGI_AT")." ".date("H:i:s A", $email_date_int)." (PST)";
										}
										elseif((($today_day-1) == $email_day) && ($today_month == $email_month) && ($today_year == $email_year)){
											echo JText::_("VIEWSTATYESTODAY")." (".date($configs->get('time_format','DD-MM-YYYY'), $email_date_int).") ".JText::_("DIGI_AT")." ".date("H:i:s A", $email_date_int)." (PST)";
										}
										else{
											echo strftime("%A", $email_date_int)." (".date($configs->get('time_format','DD-MM-YYYY'), $email_date_int).") ".JText::_("DIGI_AT")." ".date("H:i:s A", $email_date_int)." (PST)";
										}
									?>
								</td>
								<td>
									<?php
										echo "<a rel=\"{handler: 'iframe', size: {x: 700, y: 500}}\"  class=\"modal\"  href=\"index.php?option=com_digicom&controller=logs&task=editEmail&id=".$email["id"]."&tmpl=component\">".JText::_('DIGI_VIEW_EMAIL')."</a>";
									?>
								</td>
							</tr>
					<?php
							$k = 1 - $k;
						}
					?>
					<tfoot>
						<tr>
							<td colspan="5">
								<?php
									$total_pag = $this->pagination->get("pages.total", "0");
									$pag_start = $this->pagination->get("pages.start", "1");
									if($total_pag > ($pag_start + 9)){
										$this->pagination->set("pages.stop", ($pag_start + 9));
									}
									else{
										$this->pagination->set("pages.stop", $total_pag);
									}
									echo $this->pagination->getListFooter();
								?>
							</td>
						</tr>
					</tfoot>
				</table>
			<?php
			}
			?>
			<input type="hidden" name="option" value="com_digicom" />
			<input type="hidden" name="task" value="<?php echo $task; ?>" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="controller" value="Logs" />
		</form>
</fieldset>
