<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/html/');
JHtml::_('behavior.multiselect');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

$canDo = JHelperContent::getActions('com_digicom', 'component');
$configs = JComponentHelper::getComponent('com_digicom')->params;
?>
<script language="javascript" type="text/javascript">
jQuery(document).ready(function() {
  jQuery('button.js-stools-btn-clear').click(function(){
    jQuery('#filter_startdate').val('');
    jQuery('#filter_enddate').val('');

    this.form.submit();

  });
});


function OrderlistItemTask(id) {
    var f = document.adminForm, i, cbx,
    cb = f[id];
    if (cb) {
        for (i = 0; true; i++) {
            cbx = f['cb'+i];
            if (!cbx)
                break;
            cbx.checked = false;
        } // for
        cb.checked = true;
        f.boxchecked.value = 1;
        return true;
    }
    return false;
}
Joomla.submitbutton = function (pressbutton) {
	var form = document.adminForm;
  if (pressbutton == 'remove')
	{
		if (confirm("<?php echo JText::_("CONFIRM_ORDER_DELETE");?>"))
		{
			Joomla.submitform(pressbutton);
		}
		return;
	}
	Joomla.submitform(pressbutton);
}
</script>

<?php if (!empty( $this->sidebar)) : ?>
<div id="j-sidebar-container" class="">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="">
<?php else : ?>
<div id="j-main-container" class="">
<?php endif;?>

	<div class="dg-alert dg-alert-with-icon">
		<span class="icon-support"></span><?php echo JText::_("COM_DIGICOM_ORDERS_HEADER_NOTICE"); ?>
	</div>

	<form id="adminForm" action="<?php echo JRoute::_('index.php?option=com_digicom&view=orders'); ?>" method="post" name="adminForm" autocomplete="off" class="form-validate form-horizontal">

    <?php
		// Search tools bar
		echo JLayoutHelper::render('searchtools.orders', array('view' => $this));
		?>

		<table class="adminlist table table-striped">
			<thead>
				<tr>
					<th width="5">
						<?php echo JHtml::_('grid.checkall'); ?>
					</th>
					<th width="20">
						<?php echo JText::_( 'JGRID_HEADING_ID' ); ?>
					</th>

					<th>
						<?php echo JText::_( 'COM_DIGICOM_DATE' ); ?>
					</th>
					<th>
						<?php echo JText::_( 'COM_DIGICOM_NUM_PRODUCT_IN_ORDER' ); ?>
					</th>
					<th>
						<?php echo JText::_( 'COM_DIGICOM_PRICE' ); ?>
					</th>
					<th>
						<?php echo JText::_( 'COM_DIGICOM_AMOUNT_PAID' ); ?>
					</th>
					<!--
					<th>
						<?php echo JText::_( 'COM_DIGICOM_EMAIL' ); ?>
					</th>
					 -->
					<th>
						<?php echo JText::_( 'COM_DIGICOM_CUSTOMER' ); ?>
					</th>
					<th>
						<?php echo JText::_( 'JSTATUS' ); ?>
					</th>
					<th>
						<?php echo JText::_( 'COM_DIGICOM_CUSTOMER_PAYMENT_METHOD' ); ?>
					</th>
					<th style="width: 200px;">
						<?php echo JText::_( 'COM_DIGICOM_ACTION' ); ?>
					</th>
				</tr>
			</thead>

			<tbody>
  				<?php if(!empty($this->orders)):  ?>
				<?php
				foreach($this->orders as $i=>$order):
					$id = $order->id;
					$checked = JHTML::_( 'grid.id', $i, $id );
					$olink = JRoute::_( "index.php?option=com_digicom&view=order&task=order.edit&id=" . $id );
					$customerlink = JRoute::_( "index.php?option=com_digicom&view=customer&task=customer.edit&id=" . $order->userid );
					$order->published = 1;
					$published = JHTML::_( 'grid.published', $order, $i );
					$orderstatuslink = JRoute::_( "index.php?option=com_digicom&view=orders&task=orders.cycleStatus&id=" . $id );
					$userlink = "index.php?option=com_users&view=users&filter_search=".$order->email;

				?>
					<tr class="row<?php echo $i; ?>">
						<td align="center">
							<?php echo $checked; ?>
						</td>
						<td align="center">
							<?php if ($canDo->get('core.edit')) : ?>
                <a href="<?php echo $olink; ?>"><?php echo $id; ?></a>
              <?php else: ?>
                <span><?php echo $id; ?></span>
              <?php endif; ?>
						</td>
						<td align="center">
							<?php echo $order->order_date; ?>
						</td>
						<td>
							<?php
                //echo $order->number_of_products;
                $orderitems = DigiComHelperDigiCom::getProductsNamebyOrder($id);
            		foreach ($orderitems as $orderkey => $orderitem) {
              ?>
            			<a class="label" href="<?php echo JRoute::_('index.php?option=com_digicom&view=product&task=product.edit&id=' . $orderitem->id); ?>"><?php echo $orderitem->name; ?></a>
            	<?php } ?>
            </a>
						</td>
						<td align="center">
							<?php
								echo DigiComHelperDigiCom::format_price($order->amount, $configs->get('currency','USD'), true, $configs);
							?>
						</td>
						<td align="center">
							<?php
								$refunds = DigiComHelperDigiCom::getRefunds($order->id);
								$chargebacks = DigiComHelperDigiCom::getChargebacks($order->id);
								$order->amount_paid = $order->amount_paid - $refunds - $chargebacks;
								echo DigiComHelperDigiCom::format_price($order->amount_paid, $configs->get('currency','USD'), true, $configs);
							?>
						</td>
						 <td align="center">
							<?php if ($canDo->get('core.edit')) : ?>
              <a href="<?php echo $customerlink; ?>" >
                <?php echo $order->name; ?>
              </a>
            <?php else: ?>
              <?php echo $order->name; ?>
            <?php endif; ?>
						</td>
						<td align="center" width="1%">

 							<?php
                $class = 'badge badge-success';
								if($order->status != "Active"){
									$class = 'badge badge-warning';
								} ?>
                <span class="<?php echo $class; ?>">
                  <?php echo (trim( $order->status ) != "in_progres" ? $order->status : "Active"); ?>
                </span>
            </td>
						<td align="center">
							<?php echo $order->processor; ?>
						</td>
            <td align="center" class="orders-action">

 							<?php if ($canDo->get('core.edit.state')) : ?>
                 <?php echo DigiComHelperDigiCom::getOrderSratusList($order->status, $i, $order); ?>
						  <?php else: ?>
                <?php
                $class = 'badge badge-success';
								if($order->status == "Pending"){
									$class = 'badge badge-warning';
								}
                elseif($order->status == "Pending")
                {
                  $class = 'badge badge-important';
                }
                ?>
                <span class="<?php echo $class; ?>">
                  <?php echo (trim( $order->status ) != "in_progres" ? $order->status : "Active"); ?>
                </span>
              <?php endif; ?>
            </td>
					</tr>
          <?php endforeach; ?>
				<?php else: ?>
					<tr>
						<td colspan="9">
							<?php echo  JText::_('COM_DIGICOM_ORDERS_NOTICE_NO_ORDER_FOUND'); ?>
						</td>
					</tr>
				<?php endif; ?>
			</tbody>
      <?php
        $total_pag = $this->pagination->get("pages.total", "0");
        if($total_pag > 1):
      ?>
    <?php endif; ?>
		</table>
    <div class="pagination-centered">
      <?php echo $this->pagination->getListFooter(); ?>
    </div>

		<input type="hidden" name="option" value="com_digicom" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="view" value="orders" />
		<?php echo JHtml::_('form.token'); ?>

	</form>
</div>
<?php
	echo JHtml::_(
		'bootstrap.renderModal',
		'videoTutorialModal',
		array(
			'url' => 'https://www.youtube-nocookie.com/embed/zAEU6-Wv5c4?list=PL5eH3TQ0wUTZXKs632GyKMzGVkxdfPB4f&amp;showinfo=0',//&amp;autoplay=1
			'title' => JText::_('COM_DIGICOM_ORDERS_VIDEO_INTRO'),
			'height' => '400px',
			'width' => '1280'
		)
	);
?>
<div class="dg-footer">
	<?php echo JText::_('COM_DIGICOM_CREDITS'); ?>
</div>
