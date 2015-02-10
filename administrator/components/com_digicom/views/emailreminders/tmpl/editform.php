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

JHtml::_('behavior.tooltip');
JHtml::_('jquery.framework');
JHtml::_('formbehavior.chosen', 'select');
$doc = JFactory::getDocument();
$doc->addScript(JURI::root() . 'administrator/components/com_digicom/assets/js/redactor.min.js');
$doc->addStyleSheet(JURI::root() . 'administrator/components/com_digicom/assets/css/redactor.css');
$upload_script = '
window.addEvent( "domready", function(){
		jQuery(".useredactor").redactor();
		jQuery(".redactor_useredactor").css("height","400px");
	});';
$doc->addScriptDeclaration( $upload_script );
$doc->addStyleSheet("components/com_digicom/assets/css/digicom.css"); 
?>


<form id="adminForm" action="index.php" name="adminForm" method="post">
	<fieldset>

		<legend><?php echo $this->action. " " . JText::_('EMAILSUBACTIONNAME') ?></legend>
		<div class="alert alert-info">
			<?php echo JText::_('HEADER_EMAILSEDIT'); ?>
		</div>
		<div class="row-fluid">
			<div class="span8">
				<div class="control-group">
					<div class="control-label">
						<label class="editlinktip hasTip" title="<?php echo JText::_('COM_DIGICOM_EMAILSNAME_TIP'); ?>" ><?php echo JText::_('PLAINNAME');?>:</label>
					</div>
					<div class="controls">
						<input type="text" name="name" value="<?php echo $this->email->name; ?>" style="width:250px;" />
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<label class="editlinktip hasTip" title="<?php echo JText::_('COM_DIGICOM_EMAILSSUBJECT_TIP'); ?>" ><?php echo JText::_('EMAILREMINDERSUBJECT');?>:</label>
					</div>
					<div class="controls">
						<input type="text" id="subject" name="subject" value="<?php echo $this->email->subject; ?>"/>
					</div>
				</div>
				
				<div class="control-group">
					<div class="control-label">
						<label class="editlinktip hasTip" title="<?php echo JText::_('COM_DIGICOM_EMAILSPUBLISHED_TIP'); ?>" ><?php echo JText::_('PLAINPUBLISHED');?>:</label>
					</div>
					<div class="controls">
						<fieldset class="radio btn-group btn-group-yesno">
							<input type="radio" name="published" id="published1" value="1" <?php echo (($this->email->published == 1 || $this->email->published === null)?"checked='checked'":"");?> />
							<label class="btn" for="published1"><?php echo JText::_('DSYES'); ?></label>
							<input type="radio" name="published" id="published0" value="0" <?php echo (($this->email->published == '0')?"checked='checked'":"");?> />
							<label class="btn" for="published0"><?php echo JText::_('DSNO'); ?></label>
						</fieldset>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<label class="editlinktip hasTip" title="<?php echo JText::_('COM_DIGICOM_EMAILSTERMS_TIP'); ?>" ><?php echo JText::_('TRIGGER');?>:</label>
					</div>
					<div class="controls">
						<?php
						/* Email Trigger Number */
						for ($i=1; $i<=100; $i++)
						{
							$type[] = JHTML::_('select.option', $i, $i);
						}

						$trigger_type = JHTML::_('select.genericlist', $type, 'type', 'class="span2" size="1"', 'value', 'text', $this->email->type);
						echo $trigger_type;

						/* Email Trigger Period */
						$period = array();
						$period[] = JHTML::_('select.option', 'day', JText::_("SUBCRUB_DURATION_DAY"));
						$period[] = JHTML::_('select.option', 'week', JText::_("SUBCRUB_DURATION_WEEK"));
						$period[] = JHTML::_('select.option', 'month', JText::_("SUBCRUB_DURATION_MONTH"));
						$period[] = JHTML::_('select.option', 'year', JText::_("SUBCRUB_DURATION_YEAR"));
						$trigger_period = JHTML::_('select.genericlist', $period, 'period', 'class="span2" ', 'value', 'text', $this->email->period);
						echo $trigger_period ;

						/* Email Trigger Calc */
						$calc = array();
						$calc[] = JHTML::_('select.option', 'before', JText::_("TRIGGER_BEFORE"));
						$calc[] = JHTML::_('select.option', 'after', JText::_("TRIGGER_AFTER"));
						$trigger_calc = JHTML::_('select.genericlist', $calc, 'calc', 'class="span2" size="1"', 'value', 'text', $this->email->calc);
						echo $trigger_calc;

						/* Email Trigger Date Calc */
						$date_calc = array();
						$date_calc[] = JHTML::_('select.option', 'expiration', JText::_("DATE_CALC_EXPIRATION"));
						$date_calc[] = JHTML::_('select.option', 'purchase', JText::_("DATE_CALC_PURCHASE"));
						$trigger_date_calc = JHTML::_('select.genericlist', $date_calc, 'date_calc', 'class="span2" size="1"', 'value', 'text', $this->email->date_calc);
						echo $trigger_date_calc;

						?>
					</div>
				</div>
				
				<div class="control-group">
					<div class="control-label">
						<label class="editlinktip hasTip" title="<?php echo JText::_('COM_DIGICOM_EMAILSBODY_TIP'); ?>" ><?php echo JText::_('VIEWDSADMINCONTENT');?>:</label>
					</div>
					<div class="controls">
						<textarea id="body" name="body" class="useredactor" style="width:100%;height:450px;"><?php echo $this->email->body;?></textarea>
					</div>
				</div>
				
			</div>
			<div class="span4">
				<div class="hasAffix" data-spy="affix" data-offset-top="100" data-offset-bottom="100" style="top: 80px;">
					<h3><?php echo( JText::_( 'VIEWCONFIGTEMPLATEVARS' ) ); ?></h3>

					<table class="table table-condensed">
						<tr>
							<td width=200><?php echo JText::_( 'VIEWCONFIGTEMPLATEVARSSITENAME' ); ?></td>
							<td width=200><span class="label label-info">[SITENAME]</span></td>
						</tr>
						<tr>
							<td width=200><?php echo JText::_( 'VIEWCONFIGTEMPLATEVARSSITEURL' ); ?></td>
							<td width=200><span class="label label-info">[SITEURL]</span></td>
						</tr>
						<tr>
							<td width=200><?php echo JText::_( 'VIEWCONFIGTEMPLATEVARSUSERNAME' ); ?></td>
							<td width=200><span class="label label-info">[CUSTOMER_USER_NAME]</span></td>
						</tr>
						<tr>
							<td width=200><?php echo JText::_( 'VIEWCONFIGTEMPLATEVARSCUSTOMERNAME' ); ?></td>
							<td width=200><span class="label label-info">[CUSTOMER_FIRST_NAME]</span></td>
						</tr>
						<tr>
							<td width=200><?php echo JText::_( 'VIEWCONFIGTEMPLATEVARSLANSTNAME' ); ?></td>
							<td width=200><span class="label label-info">[CUSTOMER_LAST_NAME]</span></td>
						</tr>
						<tr>
							<td width=200><?php echo JText::_( 'VIEWCONFIGTEMPLATEVARSEMAIL' ); ?></td>
							<td width=200><span class="label label-info">[CUSTOMER_EMAIL]</span></td>
						</tr>
						<tr>
							<td width=200><?php echo JText::_( 'VIEWCONFIGTEMPLATEVARSTODAY' ); ?></td>
							<td width=200><span class="label label-info">[TODAY_DATE]</span></td>
						</tr>
						<tr>
							<td width=200><?php echo JText::_( 'VIEWCONFIGTEMPLATEPWD' ); ?></td>
							<td width=200><span class="label label-info">[CUSTOMER_PASSWORD]</span></td>
						</tr>
						<tr>
							<td width=200><?php echo JText::_( 'VIEWCONFIGTEMPLATECUSTCOMPANY' ); ?></td>
							<td width=200><span class="label label-info">[CUSTOMER_COMPANY_NAME]</span></td>
						</tr>
						<tr>
							<td width=200><?php echo JText::_( 'RENEW_URL' ); ?></td>
							<td width=200><span class="label label-info">[RENEW_URL]</span></td>
						</tr>
						<tr>
							<td width=200><?php echo JText::_( 'PRODUCT_URL' ); ?></td>
							<td width=200><span class="label label-info">[PRODUCT_URL]</span></td>
						</tr>
						<tr>
							<td width=200><?php echo JText::_( 'SUBSCRIPTION_TERM' ); ?></td>
							<td width=200><span class="label label-info">[SUBSCRIPTION_TERM]</span></td>
						</tr>
						<tr>
							<td width=200><?php echo JText::_( 'PRODUCT_NAME' ); ?></td>
							<td width=200><span class="label label-info">[PRODUCT_NAME]</span></td>
						</tr>
						<tr>
							<td width=200><?php echo JText::_( 'EXPIRE_DATE' ); ?></td>
							<td width=200><span class="label label-info">[EXPIRE_DATE]</span></td>
						</tr>
						<tr>
							<td width=200><?php echo JText::_( 'MY_ORDERS' ); ?></td>
							<td width=200><span class="label label-info">[MY_ORDERS]</span></td>
						</tr>
						<tr>
							<td width=200><?php echo JText::_( 'RENEW_TERM' ); ?></td>
							<td width=200><span class="label label-info">[RENEW_TERM]</span></td>
						</tr>
					</table>
				</div>
			</div>
		</div>
		

	</fieldset>

	<input type="hidden" name="id" value="<?php echo $this->email->id; ?>"/>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_digicom" />
	<input type="hidden" name="controller" value="Emailreminders" />

</form>