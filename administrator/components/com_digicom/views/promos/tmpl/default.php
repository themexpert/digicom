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

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

$k = 0;
$n = count ($this->promos);
$configs = $this->configs;
$document = JFactory::getDocument();
//$document->addStyleSheet("components/com_digicom/assets/css/digicom.css");
?>
<form id="adminForm" action="<?php echo JRoute::_('index.php?option=com_digicom&controller=promos'); ?>" method="post" name="adminForm" autocomplete="off" class="form-horizontal">
	<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="">
	<?php else : ?>
	<div id="j-main-container" class="">
	<?php endif;?>
		<div class="js-stools">
			<div class="clearfix">
				<div class="btn-wrapper input-append">
					<?php $promosearch = JRequest::getVar("promosearch", ""); ?>
					<input id="filter_search" type="text" name="promosearch" value="<?php echo trim($promosearch);?>" placeholder="<?php echo JText::_("DIGI_FIND");?>" />
					<button type="submit" class="btn hasTooltip" title="" data-original-title="Search">
						<i class="icon-search"></i>
					</button>
					<button type="button" class="btn hasTooltip js-stools-btn-clear" onclick="document.id('filter_search').value='';this.form.submit();">
							<i class="icon-remove"></i>	
						</button>
				</div>
				<div class="btn-wrapper pull-right">
					
					<?php echo JText::_("VIEWPROMOPUBLISHED");?>:
					<select name="status" onchange="document.adminForm.task.value=''; document.adminForm.submit();">
						<option value="" <?php if($this->status == ""){ echo 'selected="selected"';} ?> ><?php echo JText::_("DIGI_SELECT"); ?></option>
						<option value="0" <?php if($this->status == "0"){ echo 'selected="selected"';} ?> ><?php echo JText::_("HELPERUNPUBLISHED"); ?></option>
						<option value="1" <?php if($this->status == "1"){ echo 'selected="selected"';} ?> ><?php echo JText::_("PLAINPUBLISHED"); ?></option>
					</select>
					<?php echo JText::_("VIEWORDERSSTATUS");?>:
					<select name="condition" onchange="document.adminForm.task.value=''; document.adminForm.submit();">
						<option value="-1" <?php if($this->condition == "-1"){ echo 'selected="selected"';} ?> ><?php echo JText::_("DIGI_SELECT"); ?></option>
						<option value="0" <?php if($this->condition == "0"){ echo 'selected="selected"';} ?> ><?php echo JText::_("HELPEREXPIRE"); ?></option>
						<option value="1" <?php if($this->condition == "1"){ echo 'selected="selected"';} ?> ><?php echo JText::_("HELPERACTIVE"); ?></option>
					</select>

				</div>
				
			</div>
		</div>
		<br>
			
		<div class="alert alert-info">
			<?php echo JText::_("HEADER_PROMOS"); ?>
		</div>

		<table width="100%">
			<tr>
				<td width="100%" align="right" style="padding-bottom: 5px;">
					
				</td>
			</tr>
		</table>

		<div id="editcell" >
			<table class="adminlist table">
				<thead>

					<tr>
						<th width="5">
							<input type="checkbox" onclick="checkAll(<?php echo $n; ?>)" name="toggle" value="" />
						</th>
						
						<th>
							<?php echo JText::_("VIEWPROMOPUBLISHED");?>
						</th>

						<th>
							<?php echo JText::_('VIEWPROMOTITLE');?>
						</th>

						<th>
							<?php echo JText::_('VIEWPROMOCODE');?>
						</th>

						<th>
							<?php echo JText::_('VIEWPROMODISCAMOUNT');?>
						</th>

						<th>
							<?php echo JText::_("VIEWORDERSSTATUS");?>
						</th>

						<th>
							<?php echo JText::_('VIEWPROMOTIMEUSED');?>
						</th>

						<th>
							<?php echo JText::_('VIEWPROMOUSAGESLIST');?>
						</th>

						<th width="20">
							<?php echo JText::_('VIEWPROMOID');?>
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
						$link = JRoute::_("index.php?option=com_digicom&controller=promos&task=edit&cid[]=".$id);

						$published = JHTML::_('grid.published', $promo->published, $i);
						DigiComAdminHelper::publishAndExpiryHelper($img, $alt, $times, $status, $promo->codestart, $promo->codeend, $promo->published, $configs, $promo->codelimit, $promo->used);

				?>
					<tr class="row<?php echo $k;?>"> 
						<td>
							<?php echo $checked;?>
						</td>

						<td align="center">
							<?php echo $published; ?>
						</td>

						<td>
							<a href="<?php echo $link;?>" ><?php echo $promo->title;?></a>
						</td>

						<td>
							<a href="<?php echo $link;?>" ><?php echo $promo->code;?></a>
						</td>

						<td align="center">
							<?php echo ($promo->promotype == '0' ? DigiComAdminHelper::format_price($promo->amount, $configs->get('currency','USD'), true, $configs) : $promo->amount . ' %');?>
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
									<?php echo $promo->codelimit>0?($promo->codelimit - $promo->used):JText::_("DS_UNLIMITED");?>
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

				<tfoot>
					<tr>
						<td colspan="9">
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

		</div>

		<input type="hidden" name="option" value="com_digicom" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="controller" value="promos" />
		
	</div>
</form>