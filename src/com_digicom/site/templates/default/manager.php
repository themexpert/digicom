<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;
JHtml::_('formbehavior.chosen', 'select');

$app		= JFactory::getApplication();
$dispatcher	= JDispatcher::getInstance();
$input 		= $app->input;
$configs 	= $this->configs;

$n = count ($this->orders);
$k = 0;

JFactory::getDocument()->addScriptDeclaration('
	Joomla.submitbutton = function(task)
	{
		if (task == "orders.cycleStatus")
		{
			var c = confirm("You are going to change Order status, Are you sure you want to proceed?");
			if(c) {Joomla.submitform(task);}
			else {jQuery("#manager-order-list").removeClass("hide");jQuery("#manager-order-list").addClass("hide");}
		}
	};
');

?>

<div id="digicom" class="dc dc-orders">

	<?php DigiComSiteHelperDigicom::loadModules('digicom_toolber'); ?>

	<h1 class="page-title center text-center"><?php echo JText::_("COM_DIGICOM_MANAGER_PAGE_TITLE"); ?></h1>

	<?php $dispatcher->trigger('onDigicomManagerAfterTitle',array('com_digicom.manager')); ?>

	<form class="form-inline form-group" action="<?php echo JRoute::_('index.php?option=com_digicom&view=manager'); ?>" name="adminForm" method="post" id="adminForm">

		<?php if ($this->params->get('filter_field', 1) || $this->params->get('show_pagination_limit', 1)) : ?>
		<fieldset class="filters well well-sm">
			<?php if ($this->params->get('filter_field', 1)) :?>
				<div class="btn-group">
					<label class="filter-search-lbl element-invisible" for="filter-search">
						<?php echo JText::_('COM_DIGICOM_SEARCH') . '&#160;'; ?>
					</label>
					<input type="text" name="search" id="search" value="<?php echo $this->state->get('filter.search'); ?>" 
						title="<?php echo JText::_('COM_DIGICOM_SEARCH'); ?>" 
						placeholder="<?php echo JText::_('COM_DIGICOM_SEARCH_ORDER_LABEL'); ?>" 
						class="input-group-input" 
						style="float: left;padding: 5px 10px;box-shadow: 0 4px 6px rgba(50, 50, 93, 0.11), 0 1px 3px rgba(0, 0, 0, 0.08);border: none;"
					/>
					<input type="button" onclick="document.adminForm.submit();" class="btn btn-large" value="submit">
					<input type="button" onclick="document.getElementById('search').value = '';document.adminForm.submit();" class="btn btn-large" value="Clear">
					<input type="button" onclick="jQuery('#hints').slideToggle();" class="btn btn-large" value="Hints">
				</div>
			<?php endif; ?>

			<?php if ($this->params->get('show_pagination_limit', 1)) : ?>
				<div class="btn-group pull-right">
					<label for="limit" class="element-invisible">
						<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>
					</label>

					<?php echo $this->pagination->getLimitBox(); ?>
				</div>
			<?php endif; ?>

			<input type="hidden" name="filter_order" value="" />
			<input type="hidden" name="filter_order_Dir" value="" />
			<input type="hidden" name="limitstart" value="" />
			<input type="hidden" name="task" value="" />
			<div class="clearfix"></div>
		</fieldset>
		<?php endif; ?>
		<!--
		<div class="input-group">
			<input type="text" id="dssearch" name="search" class="input-group-addon"  value="<?php echo trim($input->get('search', '')); ?>" size="30" />
			<div class="input-group-btn">
				<button type="submit" class="btn btn-default"><?php echo JText::_("COM_DIGICOM_SEARCH"); ?></button>
			</div>
		</div> -->

		<input type="hidden" name="boxchecked" value="1">
		<input type="hidden" name="option" value="com_digicom" />
		<input type="hidden" name="view" value="manager" />

	<p id="hints" class="alert alert-info" style="display: none;"><?php echo JText::_('COM_DIGICOM_ORDERS_SEARCH_NOTICE'); ?></p>
	<div id="manager-order-loader" class="digicom-loader hide"></div>
	<table id="manager-order-list" class="table table-bordered table-striped">
		<thead>
			<tr>
				<th><?php //echo JHtml::_('grid.checkall'); ?></th>
				<th><?php echo JText::_("JGRID_HEADING_ID"); ?></th>
				<th><?php echo JText::_("COM_DIGICOM_CUSTOMER"); ?></th>
				<th><?php echo JText::_("JDATE"); ?></th>
				<th><?php echo JText::_("JSTATUS"); ?></th>
				<th><?php echo JText::_("COM_DIGICOM_PRODUCTS"); ?></th>
				<th><?php echo JText::_("COM_DIGICOM_PRICE"); ?></th>
				<th><?php echo JText::_("COM_DIGICOM_TOTAL_PAID"); ?></th>
				<th><?php echo JText::_("COM_DIGICOM_ACTION"); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
		$i = 0;
		if(count($n) > 0){
			foreach($this->orders as $key=>$order){
				// print_r($order);die;
				$id = $order->id;
				$order_link = JRoute::_("index.php?option=com_digicom&view=order&id=".$id);
				$rec_link = JRoute::_("index.php?option=com_digicom&view=order&layout=invoice&id=".$id."&tmpl=component");

				// Price
				$order_price = DigiComSiteHelperPrice::format_price($order->amount_paid, $order->currency, true, $configs);
				?>
				<tr>
					<td>
						<?php echo JHTML::_( 'grid.id', $key, $id ); ?>
					</td>
					<td style="width: 164px;">
						<a class="btn btn-link" href="<?php echo $order_link; ?>">#<?php echo $order->id; ?></a>
						<span class="label label-info"><?php echo $order->processor; ?></span>	
					</td>
					<td>
						<?php echo $order->name ?>
					</td>
					<td>
						<?php echo $order->order_date;?>
					</td>
					<td>
						<?php
						$labelClass = 'label-danger';
						if ( strtolower($order->status) === 'active') $labelClass = 'label-success';
						elseif ( strtolower($order->status) === 'pending') $labelClass = 'label-warning';
						?>
						<span class="label <?php echo $labelClass; ?>"><?php echo $order->status; ?></span>
					</td>
					<td style="width: 200px;overflow: scroll;">
						<?php //echo $order->number_of_products; ?>
						<?php
		                $orderitems = DigiComHelperDigiCom::getProductsNamebyOrder($id);
	            		foreach ($orderitems as $orderkey => $orderitem) {
		              	?>
        				<span 
            				class="label label-info" 
            				href="<?php //echo JRoute::_('index.php?option=com_digicom&view=product&id=' . $orderitem->id); ?>"
            			>
            				<?php echo $orderitem->name; ?>
        				</span>
		            	<?php } ?>
		            </a>
					</td>
					<td>
						<?php echo DigiComSiteHelperPrice::format_price($order->amount, $order->currency, true, $configs); ?>
					</td>
					<td>
						<?php echo $order_price; ?>
					</td>
					<td>
						<?php echo DigiComHelperDigiCom::getOrderSratusList($order->status, $i, $order); ?>
					</td>
				</tr>
				<?php
				$i++;
			}
		}else{ ?>
			<tr>
				<td colspan="5">
					<?php echo JText::_('COM_DIGICOM_ORDERS_NO_ORDER_FOUND_NOTICE'); ?>
				</td>
			</tr>
		<?php } ?>

		</tbody>
	</table>
	</form>
	<div class="dc-pagination pagination" style="display: block;text-align: center;margin-top: 50px;">
		<?php echo $this->pagination->getPagesLinks(); ?>
	</div>

	<?php $dispatcher->trigger('onDigicomManagerAfterList',array('com_digicom.manager')); ?>

	<?php DigiComSiteHelperDigicom::loadModules('digicom_footer','xhtml'); ?>

	<?php echo DigiComSiteHelperDigiCom::powered_by(); ?>
	<script type="text/javascript">
function changeOrderStatus(id,task,index,val){
	jQuery('#manager-order-list').addClass('hide');
	jQuery('#manager-order-loader').removeClass('hide');
  var f = document.adminForm, i, cbx,
    cb = f[id],
    status = f['orderstatus'+index];
    if (cb) {
        for (i = 0; true; i++) {
            cbx = f['cb'+i];
            if (!cbx)
                break;
            cbx.checked = false;
        } // for
        cb.checked = true;
    }
    
    if(status){
      orderstatus = f['orderstatus'+index];
      for (i = 0; true; i++) {
          cbx = f['orderstatus'+i];
          if (!cbx)
              break;
          cbx.value = '';
      } // for
      status.value = val;

    }

    f.boxchecked.value = 1;

    submitbutton(task);
}
	</script>
	<style type="text/css">
#manager-order-list.disabled{
	position: relative;
}
#manager-order-list.disabled:before {
	content: '';
	position: absolute;
	z-index: 1;
	background: #00000014;
	width: 100%;
	height: 100%;
	box-shadow: 0px 0px 10px 10px #fdfdfd5f;
}
	</style>
</div>
