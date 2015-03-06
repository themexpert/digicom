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

$mosConfig_absolute_path = JPATH_ROOT;

$document = JFactory::getDocument();
$app = JFactory::getApplication();
$input = $app->input;
$input->set('layout', 'dgform');
//$document->addStyleSheet("components/com_digicom/assets/css/digicom.css");
?>
<form action="<?php echo JRoute::_('index.php?option=com_digicom&controller=email'); ?>" id="adminForm" method="post" name="adminForm" autocomplete="off" class="form-validate form-horizontal">
	<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
		<div class="row-fluid">
			<div class="span12">
				<div class="alert alert-info">
					<?php echo JText::_( "HEADER_EMAIL_SETTINGS" ); ?>
				</div>
				<ul class="nav nav-tabs">
				  <li role="presentation"><a href="<?php echo JRoute::_('index.php?option=com_digicom&controller=email'); ?>">Register</a></li>
				  <li role="presentation"><a href="<?php echo JRoute::_('index.php?option=com_digicom&controller=email&type=order'); ?>">Order</a></li>
				  <li role="presentation" class="active"><a href="<?php echo JRoute::_('index.php?option=com_digicom&controller=email&type=approved'); ?>">Approved</a></li>
				</ul>
				
				<div class="tab-content">
					<div class="tab-pane active">
				<div class="alert alert-info">
					<?php echo JText::_('COM_DIGICOM_OFFLINE_ORDEREMAIL_TIP'); ?>
				</div>

		<div class="row-fluid">
			<div class="span8">
				<fieldset class="adminform">
					
					<legend><?php echo JText::_('VIEWCONFIGAPPROVEDEMAIL');?></legend>
					<div class="control-group ">
						<div class="control-label">
							<label id="jform_title-lbl" for="jform_title" class="required" title="">
								<?php echo JText::_('VIEWCONFIGSUBJ');?>
							</label>
						</div>
						<div class="controls">
							<input type="text" value="<?php echo $this->template->subject; ?>" name="subject" size="60"/>
						</div>
					</div>
					<div class="control-group ">
										<textarea id="approved_editor" name="body" class="useredactor" style="width:90%;height:550px;"><?php echo $this->template->body; ?></textarea>
					</div>
					
				</fieldset>
			</div>

			<div class="span4">
				<div>
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
						</tr>
						<tr>
							<td width=200><?php echo JText::_( 'VIEWCONFIGTEMPLATEVARSOID' ); ?></td>
							<td width=200><span class="label label-info">[ORDER_ID]</span></td>
						</tr>
						</tr>
						<tr>
							<td width=200><?php echo JText::_( 'VIEWCONFIGTEMPLATEVARSAMOUNT' ); ?></td>
							<td width=200><span class="label label-info">[ORDER_AMOUNT]</span></td>
						</tr>
						</tr>
						<tr>
							<td width=200><?php echo JText::_( 'VIEWCONFIGTEMPLATEVARSLICNUM' ); ?></td>
							<td width=200><span class="label label-info">[NUMBER_OF_LICENSES]</span></td>
						</tr>
						<tr>
							<td width=200><?php echo JText::_( 'VIEWCONFIGTEMPLATEVARSPRODB' ); ?></td>
							<td width=200><span class="label label-info">[PRODUCTS]</span></td>
						</tr>

						<tr>
							<td width=200><?php echo JText::_( 'VIEWCONFIGTEMPLATEVARSPROMO' ); ?></td>
							<td width=200><span class="label label-info">[PROMO]</span></td>
						</tr>
						<tr>
							<td width=200><?php echo JText::_( 'VIEWCONFIGTEMPLATECUSTCOMPANY' ); ?></td>
							<td width=200><span class="label label-info">[CUSTOMER_COMPANY_NAME]</span></td>
						</tr>
					</table>

								</div>
				</div>
			</div>
		</div>
				</div>
				
			</div>
		</div>
		
		
		
		
	</div>
	<div>
		<input type="hidden" name="type" value="approved" />
		<input type="hidden" name="controller" value="email" />
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
