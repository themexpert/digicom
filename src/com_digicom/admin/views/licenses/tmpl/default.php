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
					<input id="filter_search" type="text" name="filter[search]" value="<?php echo trim($this->params->get('filter.search'));?>" placeholder="<?php echo JText::_("COM_DIGICOM_SEARCH");?>" />
					<button type="submit" class="btn hasTooltip" title="" data-original-title="Search">
						<i class="icon-search"></i>
					</button>
					<button type="button" class="btn hasTooltip js-stools-btn-clear" onclick="document.id('filter_search').value='';this.form.submit();">
							<i class="icon-remove"></i>
					</button>
					<span class="btn hasTooltip"
						data-title="<?php echo JText::_('COM_DIGICOM_LICENSE_SEARCH_HINTS');?>">
					<i class="icon-info"></i>
					</span>
				</div>
				<div class="btn-group pull-right hidden-phone">
					<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
					<?php echo $this->pagination->getLimitBox(); ?>
				</div>


			</div>
		</div>
		<?php if( !$this->items ): ?>
		<div class="alert alert-no-items">
			<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
		</div>
		<?php else: ?>
		<div id="editcell" >
			<table class="adminlist table">
				<thead>

					<tr>
						<th width="5">
							<?php echo JText::_('JGRID_HEADING_ID'); ?>
						</th>

						<th>
							<?php echo JText::_("COM_DIGICOM_LICENSE_ID");?>
						</th>

						<th>
							<?php echo JText::_('COM_DIGICOM_PRODUCT');?>
						</th>

						<th>
							<?php echo JText::_('COM_DIGICOM_CUSTOMER');?>
						</th>

						<th>
							<?php echo JText::_('COM_DIGICOM_ORDER_ID');?>
						</th>

						<th>
							<?php echo JText::_('COM_DIGICOM_LICENSE_ISSUE');?>
						</th>

						<th>
							<?php echo JText::_('COM_DIGICOM_LICENSE_EXPIRE');?>
						</th>

						<th>
							<?php echo JText::_("JSTATUS");?>
						</th>

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
					<tr class="row<?php echo $k;?>">
						<td>
							<?php echo $id;?>
						</td>

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
								<span class="label">
									<?php echo $item->productname;?>							
								</span>
							</a>
							<?php else: ?>
								<span class="label">
									<?php echo $item->productname;?>
								</span>
							<?php endif;?>
						</td>

						<td>
							<?php if ($canDo->get('core.edit')): ?>
							<a target="_blank" href="<?php echo JRoute::_('index.php?option=com_digicom&view=customer&task=customer.edit&id=' . $item->userid);?>">
								<span class="label">
									<?php echo $item->client; ?>
								</span>
							</a>
							<?php else: ?>
								<span class="label">
									<?php echo $item->client;?>
								</span>
							<?php endif;?>
						</td>

						<td>
							<?php if ($canDo->get('core.edit')): ?>
							<a target="_blank" href="<?php echo JRoute::_('index.php?option=com_digicom&view=order&task=order.edit&id=' . $item->orderid);?>">
								<span class="label">
									#<?php echo $item->orderid; ?>
								</span>
							</a>
							<?php else: ?>
								<span class="label">
									#<?php echo $item->orderid;?>
								</span>
							<?php endif;?>
						</td>

						<td>
							<?php echo $item->purchase;?>
						</td>

						<td>
							<?php echo $item->expires;?>
						</td>

						<td align="center">
							<?php echo $status; ?>
						</td>


					</tr>


				<?php } ?>

				</tbody>

			</table>

			<div class="pagination-centered">
				<?php echo $this->pagination->getListFooter(); ?>
			</div>
		</div>
	<?php endif; ?>
		<input type="hidden" name="option" value="com_digicom" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="view" value="licenses" />
		<?php echo JHtml::_('form.token'); ?>

	</div>
</form>

<?php
	echo JHtml::_(
		'bootstrap.renderModal',
		'videoTutorialModal',
		array(
			'url' => 'https://www.youtube-nocookie.com/embed/UtDgs00sbhw?list=PL5eH3TQ0wUTZXKs632GyKMzGVkxdfPB4f&amp;showinfo=0',//&amp;autoplay=1
			'title' => JText::_('COM_DIGICOM_ABOUT_LICENSES_USE_VIDEO'),
			'height' => '400px',
			'width' => '1280'
		)
	);
?>
<div class="dg-footer">
	<?php echo JText::_('COM_DIGICOM_CREDITS'); ?>
</div>
