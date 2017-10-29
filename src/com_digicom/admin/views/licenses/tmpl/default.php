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
$user 		= JFactory::getUser();
$canDo 		= JHelperContent::getActions('com_digicom', 'component');
$document 	= JFactory::getDocument();
$configs 	= $this->params->params;
?>

<form id="adminForm" action="<?php echo JRoute::_('index.php?option=com_digicom&view=licenses'); ?>" method="post" name="adminForm" autocomplete="off">
	<div id="digicom" class="dc digicom">
		<div class="tx-sidebar"><?php echo $this->sidebar; ?></div> <!-- .tx-sidebar -->
		<div class="tx-main">
			<div class="page-header">
				<h1>License Manager</h1>
				<p><?php echo JText::_("COM_DIGICOM_LICENSES_HEADER_NOTICE"); ?></p>
				<nav class="navbar navbar-default">
			  	<div class="container-fluid">
			  		<div class="collapse navbar-collapse">
			  			<div class="navbar-form navbar-left">
			  				<div class="input-group">
			  					<input id="filter_search" class="form-control" type="text" name="filter[search]" value="<?php echo trim($this->params->get('filter.search'));?>" placeholder="<?php echo JText::_("COM_DIGICOM_SEARCH");?>" />
									<span class="input-group-btn">
										<button type="submit" class="btn btn-primary hasTooltip" title="" data-original-title="Search">
											<i class="icon-search"></i>
										</button>
										<button type="button" class="btn btn-default hasTooltip js-stools-btn-clear" onclick="document.id('filter_search').value='';this.form.submit();">
											<i class="icon-remove"></i>
										</button>
										<span class="btn btn-default hasTooltip"
											data-title="<?php echo JText::_('COM_DIGICOM_LICENSE_SEARCH_HINTS');?>">
										<i class="icon-info"></i>
										</span>
									</span>
								</div>
			  			</div>
			  			<div class="navbar-form navbar-right">
									<?php echo $this->pagination->getLimitBox(); ?>
			  			</div>
			  		</div> <!-- navbar-collapse -->
			  	</div> <!-- container-fluid -->
			  </nav> <!-- nav end -->
			</div> <!-- .page-header -->
			<div class="page-content">
				<?php if( !$this->items ): ?>
					<div class="well well-lg text-center">
						<h3 class="muted"><?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?></h3>
					</div>
					<?php else: ?>
					<div id="editcell" >
						<table class="adminlist table table-striped table-hover">
							<thead>
								<tr>
									<th width="5"><?php echo JText::_('JGRID_HEADING_ID'); ?></th>
									<th><?php echo JText::_("COM_DIGICOM_LICENSE_ID");?></th>
									<th><?php echo JText::_('COM_DIGICOM_PRODUCT');?></th>
									<th><?php echo JText::_('COM_DIGICOM_CUSTOMER');?></th>
									<th><?php echo JText::_('COM_DIGICOM_ORDER_ID');?></th>
									<th><?php echo JText::_('COM_DIGICOM_LICENSE_ISSUE');?></th>
									<th><?php echo JText::_('COM_DIGICOM_LICENSE_EXPIRE');?></th>
									<th><?php echo JText::_("JSTATUS");?></th>
								</tr>
							</thead>
							<tbody>
							<?php
								// print_r($this->items);die;
								foreach ($this->items as $key => $item) {
									$id = $item->id;
									$link = JRoute::_("index.php?option=com_digicom&view=license&task=license.edit&id=".$id);
									$canChange  = $user->authorise('core.edit.state', 'com_digicom');
									$status = DigiComHelperDigiCom::licenseExpiryHelper($item, $configs);
							?>
								<tr class="row<?php echo $key;?>">
									<td><?php echo $id;?></td>

									<td>
										<?php if ($canDo->get('core.edit')): ?>
										<a href="<?php echo $link;?>">
											<?php echo $item->licenseid;?>
										</a>
										<?php else: ?>
											<?php echo $item->licenseid;?>
										<?php endif;?>
									</td>

									<td>
										<?php if ($canDo->get('core.edit')): ?>
										<a target="_blank" href="<?php echo JRoute::_('index.php?option=com_digicom&view=product&task=product.edit&id=' . $item->productid); ?>">
											<span class="label label-default"><?php echo $item->productname;?></span>
										</a>
										<?php else: ?>
											<span class="label label-default"><?php echo $item->productname;?></span>
										<?php endif;?>
									</td>

									<td>
										<?php if ($canDo->get('core.edit')): ?>
										<a target="_blank" href="<?php echo JRoute::_('index.php?option=com_digicom&view=customer&task=customer.edit&id=' . $item->userid);?>">
											<span class="label label-default"><?php echo $item->client; ?></span>
										</a>
										<?php else: ?>
											<span class="label label-default"><?php echo $item->client;?></span>
										<?php endif;?>
									</td>

									<td>
										<?php if ($canDo->get('core.edit')): ?>
										<a target="_blank" href="<?php echo JRoute::_('index.php?option=com_digicom&view=order&task=order.edit&id=' . $item->orderid);?>">
											<span class="label label-default">#<?php echo $item->orderid; ?></span>
										</a>
										<?php else: ?>
											<span class="label label-default">#<?php echo $item->orderid;?></span>
										<?php endif;?>
									</td>

									<td><strong><?php echo $item->purchase;?></strong></td>
									<td><strong><?php echo $item->expires;?></strong></td>

									<td align="center"><span class="label label-primary'>#%s"><?php echo $status; ?></span></td>
								</tr>
							<?php } ?>
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
	<input type="hidden" name="view" value="licenses" />
	<?php echo JHtml::_('form.token'); ?>
</form>