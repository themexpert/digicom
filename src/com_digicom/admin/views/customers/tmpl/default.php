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
$n = count ($this->Items);

?>

<form id="adminForm" action="<?php echo JRoute::_('index.php?option=com_digicom&view=customers');?>" name="adminForm" method="post">

	<div id="digicom" class="dc digicom">
		<div class="tx-sidebar"><?php echo $this->sidebar; ?></div>
		<div class="tx-main">
			<div class="page-header">
				<h1>Customers</h1>
				<p><?php echo JText::_("COM_DIGICOM_CUSTOMERS_HEADER_NOTICE"); ?></p>
				<nav class="navbar navbar-default">
			  	<div class="container-fluid">
			  		<div class="collapse navbar-collapse">
			  			<div class="navbar-form navbar-left">
			  				<div class="input-group">
									<input type="text" id="filter_search" class="form-control" name="keyword" placeholder="<?php echo JText::_('COM_DIGICOM_SEARCH'); ?>" value="<?php echo $this->state->get('filter.search');?>" class="span6" />
									<span class="input-group-btn">
										<button type="submit" class="btn btn-default hasTooltip" title="" data-original-title="Search">
											<i class="icon-search"></i>
										</button>
										<button type="button" class="btn btn-default hasTooltip js-stools-btn-clear" onclick="document.id('filter_search').value='';this.form.submit();">
											<i class="icon-remove"></i>
										</button>
									</span>
								</div>
			  			</div>
			  			<div class="navbar-form navbar-right">
									<?php echo $this->pagination->getLimitBox(); ?>
			  			</div>
			  		</div> <!-- navbar-collapse -->
			  	</div> <!-- container-fluid -->
			  </nav> <!-- nav end -->
			</div>
			<div class="page-content">
				<?php if($n > 0): ?>
				<div id="editcell" >
					<table class="adminlist table">
						<thead>
							<tr>
								<th width="20"> <?php echo JText::_('JGRID_HEADING_ID');?></th>
								<th><?php echo JText::_('COM_DIGICOM_FULL_NAME');?></th>
								<th><?php echo JText::_('COM_DIGICOM_CUSTOMMER_USER_NAME');?></th>
								<th><?php echo JText::_('COM_DIGICOM_CUSTOMERS_TOTAL_ORDER');?></th>
							</tr>
						</thead>
						<tbody>
							<?php
							for ($i = 0; $i < $n; $i++):
								$cust = $this->Items[$i];
								$id = $cust->id;
								$link = JRoute::_("index.php?option=com_digicom&view=customer&task=customer.edit&id=".$id.(strlen(trim($this->keyword))>0?"&keyword=".$this->keyword:""));
								$ulink = JRoute::_("index.php?option=com_users&view=user&layout=edit&id=".$id);
							?>
							<tr class="row<?php echo $k;?>">
								<td><?php echo $id;?></td>
								<td>
									<?php if ($canDo->get('core.edit')) : ?>
										<a href="<?php echo $link;?>" ><?php echo $cust->name;?></a>
									<?php else: ?>
										<span><?php echo $cust->name;?></span>
									<?php endif; ?>
								</td>
								<td><?php echo ($cust->username ? $cust->username : $cust->email) ;?></td>
								<td><?php echo $this->getCustomerOrdersTotal($cust->id);?></td>
							</tr>

							<?php
							$k = 1 - $k;
							endfor;
							?>
						</tbody>
					</table>
				</div> <!-- #editcell -->
				<?php else :?>
				<div class="well well-lg text-center">
					<h3 class="muted"><?php echo JText::_('COM_DIGICOM_CUSTOMERS_NO_CUSTOMER_NOTICE'); ?></h3>
				</div>
				<?php endif; ?>
				<div class="pagination-centered">
					<?php echo $this->pagination->getListFooter(); ?>
				</div> <!-- .pagination -->
			</div> <!-- .page-content -->
		</div> <!-- .tx-main -->
	</div> <!-- #digicom -->

	<input type="hidden" name="option" value="com_digicom" />
	<input type="hidden" name="view" value="customers" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
</form>