<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
$user		= JFactory::getUser();
$canDo = JHelperContent::getActions('com_digicom', 'component');
$k = 0;
$coupons = count ($this->promos);
$configs = $this->configs;
$document = JFactory::getDocument();
?>
<form id="adminForm" action="<?php echo JRoute::_('index.php?option=com_digicom&view=discounts'); ?>" method="post" name="adminForm" autocomplete="off">
	<div id="digicom" class="dc digicom">
		<div class="tx-sidebar"><?php echo $this->sidebar; ?></div> <!-- .tx-sidebar -->
		<div class="tx-main">
			<div class="page-header">
				<h1>Coupon Manager</h1>
				<p><?php echo JText::_("COM_DIGICOM_DISCOUNTS_HEADER_NOTICE"); ?></p>
				<!-- navbar -->
				<nav class="navbar navbar-default">
			  	<div class="container-fluid">
			  		<div class="collapse navbar-collapse">
			  			<div class="navbar-form navbar-left">
			  				<div class="input-group">
			  					<?php $promosearch = JRequest::getVar("promosearch", ""); ?>
									<input id="filter_search" class="form-control" type="text" name="promosearch" value="<?php echo trim($promosearch);?>" placeholder="<?php echo JText::_("COM_DIGICOM_SEARCH");?>" />
									<span class="input-group-btn">
										<button type="submit" class="btn btn-primary hasTooltip" title="" data-original-title="Search">
											<i class="icon-search"></i>
										</button>
										<button type="button" class="btn btn-default hasTooltip js-stools-btn-clear" onclick="document.id('filter_search').value='';this.form.submit();">
											<i class="icon-remove"></i>
										</button>
									</span>
								</div>
			  			</div>
			  			<div class="navbar-form navbar-right">
			  				<select class="input-small" name="status" onchange="document.adminForm.task.value=''; document.adminForm.submit();">
									<option value="" <?php if($this->status == ""){ echo 'selected="selected"';} ?> ><?php echo JText::_("COM_DIGICOM_SELECT"); ?></option>
									<option value="0" <?php if($this->status == "0"){ echo 'selected="selected"';} ?> ><?php echo JText::_("COM_DIGICOM_UNPUBLISHED"); ?></option>
									<option value="1" <?php if($this->status == "1"){ echo 'selected="selected"';} ?> ><?php echo JText::_("COM_DIGICOM_PUBLISHED"); ?></option>
								</select>
								<select class="input-small" name="condition" onchange="document.adminForm.task.value=''; document.adminForm.submit();">
									<option value="-1" <?php if($this->condition == "-1"){ echo 'selected="selected"';} ?> ><?php echo JText::_("COM_DIGICOM_SELECT"); ?></option>
									<option value="0" <?php if($this->condition == "0"){ echo 'selected="selected"';} ?> ><?php echo JText::_("COM_DIGICOM_EXPIRED"); ?></option>
									<option value="1" <?php if($this->condition == "1"){ echo 'selected="selected"';} ?> ><?php echo JText::_("COM_DIGICOM_ACTIVE"); ?></option>
								</select>
								<?php echo $this->pagination->getLimitBox(); ?>
			  			</div>
			  		</div> <!-- navbar-collapse -->
			  	</div> <!-- container-fluid -->
			  </nav> <!-- nav end -->
			</div> <!-- .page-header -->
			<div class="page-content">
				<?php if(!$coupons): ?>
					<div class="well well-lg text-center">
						<h3 class="muted"><?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?></h3>
					</div>
				<?php else: ?>
					<div id="editcell" >
						<table class="adminlist table table-striped table-hover">
							<thead>
								<tr>
									<th width="5"><?php echo JHtml::_('grid.checkall'); ?></th>
									<th><?php echo JText::_("COM_DIGICOM_PUBLISHED");?></th>
									<th><?php echo JText::_('JGLOBAL_TITLE');?></th>
									<th><?php echo JText::_('COM_DIGICOM_DISCOUNT_CODE');?></th>
									<th><?php echo JText::_('COM_DIGICOM_DISCOUNTS_DISCOUNT_AMOUNT');?></th>
									<th><?php echo JText::_("JSTATUS");?></th>
									<th><?php echo JText::_('COM_DIGICOM_DISCOUNTS_DISCOUNT_USED_TIMES');?></th>
									<th><?php echo JText::_('COM_DIGICOM_DISCOUNTS_DISCOUNT_USAGE_LEFT');?></th>
									<th width="20"><?php echo JText::_('JGRID_HEADING_ID');?></th>
								</tr>
							</thead>
							<tbody>
								<?php
									JHTML::_("behavior.tooltip");
									for ($i = 0; $i < $coupons; $i++):
										$promo = $this->promos[$i];
										$id = $promo->id;

										$checked = JHTML::_('grid.id', $i, $id);
										$link = JRoute::_("index.php?option=com_digicom&view=discount&task=discount.edit&id=".$id);

										$canCheckin = $user->authorise('core.manage',     'com_checkin') || $promo->checked_out == $user->id || $promo->checked_out == 0;
										$canChange  = $user->authorise('core.edit.state', 'com_digicom') && $canCheckin;
										$status = '';
										$status = DigiComHelperDigiCom::publishAndExpiryHelper($promo, $configs);

								?>
								<tr class="row<?php echo $k;?>">
									<td><?php echo $checked;?></td>

									<td>
										<?php echo JHtml::_('jgrid.published', $promo->published, $i, 'discounts.', $canChange, 'cb'); ?>
									</td>

									<td>
										<?php	if ($canDo->get('core.edit')): ?>
										<a href="<?php echo $link;?>"><?php echo $promo->title;?></a>
									<?php else: ?>
										<?php echo $promo->title;?>
									<?php endif;?>
									</td>

									<td><span class="label label-info"><?php echo $promo->code;?></label></td>

									<td>
										<?php echo ($promo->promotype == '0' ? DigiComHelperDigiCom::format_price($promo->amount, $configs->get('currency','USD'), true, $configs) : $promo->amount . ' %');?>
									</td>

									<td align="center"><strong><?php echo $status; ?></strong></td>

									<td class="success"><strong><?php echo ($promo->used);?></strong></td>

									<td align="center">
										<?php echo $promo->codelimit>0?($promo->codelimit - $promo->used):JText::_("COM_DIGICOM_UNLIMITED");?>
									</td>
									<td><?php echo $id;?></td>
								</tr>
							<?php
									$k = 1 - $k;
								endfor;
							?>
							</tbody>
						</table> <!-- .table -->

						<div class="pagination-centered">
							<?php echo $this->pagination->getListFooter(); ?>
						</div> <!-- .pagination -->
					</div>
				<?php endif; ?>				
			</div> <!-- .page-content -->
		</div> <!-- .tx-main -->
	</div>

	<input type="hidden" name="option" value="com_digicom" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="view" value="discounts" />
	<?php echo JHtml::_('form.token'); ?>
</form>
