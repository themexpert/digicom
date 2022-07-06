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
$n = count ($this->promos);
$configs = $this->configs;
$document = JFactory::getDocument();
//$document->addStyleSheet("components/com_digicom/assets/css/digicom.css");
?>
<div id="digicom" class="dc digicom">
<form id="adminForm" action="<?php echo JRoute::_('index.php?option=com_digicom&view=discounts'); ?>" method="post" name="adminForm" autocomplete="off" class="form-horizontal">
	<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="">
	<?php else : ?>
	<div id="j-main-container" class="">
	<?php endif;?>

		<div class="dg-alert dg-alert-with-icon">
			<span class="icon-support"></span>
			<?php echo JText::_("COM_DIGICOM_DISCOUNTS_HEADER_NOTICE"); ?>
		</div>
		<br>
		<div class="js-stools">
			<div class="clearfix">
				<div class="btn-group input-append">
					<?php $promosearch = JRequest::getVar("promosearch", ""); ?>
					<input id="filter_search" type="text" name="promosearch" value="<?php echo trim($promosearch);?>" placeholder="<?php echo JText::_("COM_DIGICOM_SEARCH");?>" />
					<button type="submit" class="btn hasTooltip" title="" data-original-title="Search">
						<i class="icon-search"></i>
					</button>
					<button type="button" class="btn hasTooltip js-stools-btn-clear" onclick="document.id('filter_search').value='';this.form.submit();">
							<i class="icon-remove"></i>
						</button>
				</div>
				<div class="btn-group pull-right hidden-phone">
					<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
					<?php echo $this->pagination->getLimitBox(); ?>
				</div>

				<div class="btn-group pull-right">
					<?php echo JText::_("COM_DIGICOM_PUBLISH");?>:
					<select class="input-small" name="status" onchange="document.adminForm.task.value=''; document.adminForm.submit();">
						<option value="" <?php if($this->status == ""){ echo 'selected="selected"';} ?> ><?php echo JText::_("COM_DIGICOM_SELECT"); ?></option>
						<option value="0" <?php if($this->status == "0"){ echo 'selected="selected"';} ?> ><?php echo JText::_("COM_DIGICOM_UNPUBLISHED"); ?></option>
						<option value="1" <?php if($this->status == "1"){ echo 'selected="selected"';} ?> ><?php echo JText::_("COM_DIGICOM_PUBLISHED"); ?></option>
					</select>
				</div>
				<div class="btn-group pull-right">
					<?php echo JText::_("JSTATUS");?>:
					<select class="input-small" name="condition" onchange="document.adminForm.task.value=''; document.adminForm.submit();">
						<option value="-1" <?php if($this->condition == "-1"){ echo 'selected="selected"';} ?> ><?php echo JText::_("COM_DIGICOM_SELECT"); ?></option>
						<option value="0" <?php if($this->condition == "0"){ echo 'selected="selected"';} ?> ><?php echo JText::_("COM_DIGICOM_EXPIRED"); ?></option>
						<option value="1" <?php if($this->condition == "1"){ echo 'selected="selected"';} ?> ><?php echo JText::_("COM_DIGICOM_ACTIVE"); ?></option>
					</select>

				</div>


			</div>
		</div>
		<?php if(!$n): ?>
		<div class="alert alert-no-items">
			<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
		</div>
		<?php else: ?>
		<div id="editcell" class="panel">
			<table class="adminlist table">
				<thead>

					<tr>
						<th width="5">
							<?php echo JHtml::_('grid.checkall'); ?>
						</th>

						<th>
							<?php echo JText::_("COM_DIGICOM_PUBLISHED");?>
						</th>

						<th>
							<?php echo JText::_('JGLOBAL_TITLE');?>
						</th>

						<th>
							<?php echo JText::_('COM_DIGICOM_DISCOUNT_CODE');?>
						</th>

						<th>
							<?php echo JText::_('COM_DIGICOM_DISCOUNTS_DISCOUNT_AMOUNT');?>
						</th>

						<th>
							<?php echo JText::_("JSTATUS");?>
						</th>

						<th>
							<?php echo JText::_('COM_DIGICOM_DISCOUNTS_DISCOUNT_USED_TIMES');?>
						</th>

						<th>
							<?php echo JText::_('COM_DIGICOM_DISCOUNTS_DISCOUNT_USAGE_LEFT');?>
						</th>

						<th width="20">
							<?php echo JText::_('JGRID_HEADING_ID');?>
						</th>

					</tr>
				</thead>

				<tbody>

				<?php
					JHTML::_("behavior.tooltip");
					for ($i = 0; $i < $n; $i++):
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
						<td>
							<?php echo $checked;?>
						</td>

						<td class="center">
							<div class="btn-group">
								<?php echo JHtml::_('jgrid.published', $promo->published, $i, 'discounts.', $canChange, 'cb'); ?>
							</div>
						</td>

						<td>
							<?php	if ($canDo->get('core.edit')): ?>
							<a href="<?php echo $link;?>">
								<?php echo $promo->title;?>
							</a>
						<?php else: ?>
							<?php echo $promo->title;?>
						<?php endif;?>
						</td>

						<td>
							<span class="label"><?php echo $promo->code;?></label>
						</td>

						<td align="center">
							<?php echo ($promo->promotype == '0' ? DigiComHelperDigiCom::format_price($promo->amount, $configs->get('currency','USD'), true, $configs) : $promo->amount . ' %');?>
						</td>

						<td align="center">
							<?php
							echo $status;
							?>
						</td>

						<td align="center">
							<?php echo ($promo->used);?>
						</td>

						<td align="center">
							<?php echo $promo->codelimit>0?($promo->codelimit - $promo->used):JText::_("COM_DIGICOM_UNLIMITED");?>
						</td>

						<td>
							<?php echo $id;?>
						</td>

					</tr>


				<?php
						$k = 1 - $k;
					endfor;
				?>

				</tbody>

			</table>

			
		</div>
		<div class="pagination-centered">
				<?php echo $this->pagination->getListFooter(); ?>
			</div>
	<?php endif; ?>
		<input type="hidden" name="option" value="com_digicom" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="view" value="discounts" />
		<?php echo JHtml::_('form.token'); ?>

	</div>
</form>

<?php
	echo JHtml::_(
		'bootstrap.renderModal',
		'videoTutorialModal',
		array(
			'url' => 'https://www.youtube-nocookie.com/embed/UtDgs00sbhw?list=PL5eH3TQ0wUTZXKs632GyKMzGVkxdfPB4f&amp;showinfo=0',//&amp;autoplay=1
			'title' => JText::_('COM_DIGICOM_ABOUT_DISCOUNT_USE_VIDEO'),
			'height' => '400px',
			'width' => '1280'
		)
	);
?>
<div class="dg-footer">
	<?php echo JText::_('COM_DIGICOM_CREDITS'); ?>
</div>
</div>