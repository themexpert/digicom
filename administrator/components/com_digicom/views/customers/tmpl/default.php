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
$canDo = JHelperContent::getActions('com_digicom', 'component');
$document = JFactory::getDocument();
$k = 0;
$n = count ($this->custs);

?>
<?php if (!empty( $this->sidebar)) : ?>
<div id="j-sidebar-container" class="">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="">
<?php else : ?>
<div id="j-main-container" class="">
<?php endif;?>
	<form id="adminForm" action="index.php" name="adminForm" method="post">

		<div class="dg-alert dg-alert-with-icon">
			<span class="icon-support"></span>
			<?php echo JText::_("COM_DIGICOM_CUSTOMERS_HEADER_NOTICE"); ?>
		</div>

		<div class="js-stools">
			<div class="clearfix">
				<div class="btn-wrapper input-append">
					<input type="text" id="filter_search" class="input-large" name="keyword" placeholder="<?php echo JText::_('COM_DIGICOM_SEARCH'); ?>" value="<?php echo (strlen(trim($this->keyword)) > 0 ?$this->keyword:"");?>" class="span6" />
					<button type="submit" class="btn hasTooltip" title="" data-original-title="Search">
						<i class="icon-search"></i>
					</button>
					<button type="button" class="btn hasTooltip js-stools-btn-clear" onclick="document.id('filter_search').value='';this.form.submit();">
						<i class="icon-remove"></i>
					</button>
				</div>

			</div>
		</div>

		<div id="editcell" >

			<table class="adminlist table">
				<thead>
					<tr>
						<th width="20">
							<?php echo JText::_('JGRID_HEADING_ID');?>
						</th>
						<th>
							<?php echo JText::_('COM_DIGICOM_FULL_NAME');?>
						</th>
						<th>
							<?php echo JText::_('COM_DIGICOM_CUSTOMMER_USER_NAME');?>
						</th>
						<th>
							<?php echo JText::_('COM_DIGICOM_CUSTOMERS_TOTAL_ORDER');?>
						</th>
					</tr>
				</thead>

				<tbody>
				<?php
					//var_dump($this->custs);
					if ($n > 0): ?>
					<?php
					for ($i = 0; $i < $n; $i++):
						$cust = $this->custs[$i];
						//print_r( $cust);die;
						$id = $cust->id;
						$link = JRoute::_("index.php?option=com_digicom&view=customer&task=customer.edit&id=".$id.(strlen(trim($this->keyword))>0?"&keyword=".$this->keyword:""));
						$ulink = JRoute::_("index.php?option=com_users&view=user&layout=edit&id=".$id);
					?>
					<tr class="row<?php echo $k;?>">
						<td><?php echo $id;?></td>
						<td>
							<?php if ($canDo->get('core.edit')) : ?>
								<a href="<?php echo $link;?>" ><?php echo $cust->firstname." ".$cust->lastname;?></a>
							<?php else: ?>
								<span><?php echo $cust->firstname." ".$cust->lastname;?></span>
							<?php endif; ?>
						</td>
						<td><?php echo ($cust->username ? $cust->username : $cust->email) ;?></td>
						<td><?php echo $cust->total_order;?></td>
					</tr>


					<?php
					$k = 1 - $k;
					endfor;
					?>

					<tr>
						<td colspan="4">
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
				<?php else: ?>
					<tr>
						<td colspan="4">
							<p class="alert alert-warning"><?php 	echo JText::_('COM_DIGICOM_CUSTOMERS_NO_CUSTOMER_NOTICE'); ?></p>
						</td>
					</tr>
				<?php endif; ?>
				</tbody>
			</table>

		</div>

		<input type="hidden" name="option" value="com_digicom" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="view" value="customers" />
	</form>
</div>

<div class="dg-footer">
	<?php echo JText::_('COM_DIGICOM_CREDITS'); ?>
</div>
