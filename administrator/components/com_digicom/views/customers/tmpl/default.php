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
$document = JFactory::getDocument();
//$document->addStyleSheet("components/com_digicom/assets/css/digicom.css");
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

		<div class="js-stools">
			<div class="clearfix">
				<div class="btn-wrapper input-append">
					<input type="text" id="filter_search" class="input-large" name="keyword" placeholder="<?php echo JText::_('DSSEARCH'); ?>" value="<?php echo (strlen(trim($this->keyword)) > 0 ?$this->keyword:"");?>" class="span6" />		
					<button type="submit" class="btn hasTooltip" title="" data-original-title="Search">
						<i class="icon-search"></i>
					</button>
					<button type="button" class="btn hasTooltip js-stools-btn-clear" onclick="document.id('filter_search').value='';this.form.submit();">
						<i class="icon-remove"></i>	
					</button>
				</div>

			</div>
		</div>
		<br>
		<div class="alert alert-info">
			<?php echo JText::_("HEADER_CUSTOMERS"); ?>
		</div>
		<div id="editcell" >
			
			<table class="adminlist table">
				<thead>
					<tr>
						<th width="20">
							<?php echo JText::_('VIEWCUSTOMERID');?>
						</th>
						<th>
							<?php echo JText::_('VIEWCUSTOMERNAME');?>
						</th>
						<th>
							<?php echo JText::_('VIEWCUSTOMERUSER');?>
						</th>
						<th>
							<?php echo JText::_('COM_DIGICOM_TOTAL_ORDER');?>
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
						$link = JRoute::_("index.php?option=com_digicom&controller=customers&task=edit&cid[]=".$id.(strlen(trim($this->keyword))>0?"&keyword=".$this->keyword:""));
						$ulink = JRoute::_("index.php?option=com_users&view=user&layout=edit&id=".$id);
					?>
					<tr class="row<?php echo $k;?>"> 
						<td>
									<?php echo $id;?>
						</td>
						<td>
								<a href="<?php echo $link;?>" ><?php echo $cust->firstname." ".$cust->lastname;?></a>
						</td>
						<td>
							<?php echo $cust->username;?>
						</td>
						<td>
							<?php echo $cust->total_order;?>
						</td>

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
							<p class="alert alert-warning"><?php 	echo JText::_('VIEWCUSTOMERNOCUST'); ?></p>
						</td>
					</tr>
				<?php endif; ?>
				</tbody>
			</table>

		</div>

		<input type="hidden" name="option" value="com_digicom" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="controller" value="Customers" />
	</form>
</div>