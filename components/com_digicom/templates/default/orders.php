<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 434 $
 * @lastmodified	$LastChangedDate: 2013-11-18 11:52:38 +0100 (Mon, 18 Nov 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

defined ('_JEXEC') or die ("Go away.");

$Itemid = JRequest::getVar("Itemid", "0");
$configs = $this->configs;
$k = 0;
$n = count ($this->orders);
?>

<div id="digicom">

	<?php DigiComSiteHelperDigicom::loadModules('digicom_toolber'); ?>

	<h1 class="digi-page-title"><?php echo JText::_("DIGI_MY_ORDERS"); ?></h1>

	<form action="<?php echo JRoute::_('index.php?options=com_digicom&view=orders'); ?>" name="adminForm" method="post">

		<div class="input-append">
			<input type="text" id="dssearch" name="search" class="digi-textbox"  value="<?php echo trim(JRequest::getVar('search', '')); ?>" size="30" />
			<button type="submit" class="btn"><?php echo JText::_("DIGI_SEARCH"); ?></button>
		</div>			

		<table class="table table-bordered table-striped">
			<thead>
				<tr>
					<th><?php echo JText::_("COM_DIGICOM_ORDER_ID"); ?></th>
					<th><?php echo JText::_("COM_DIGICOM_DATE"); ?></th>
					<th><?php echo JText::_("COM_DIGICOM_STATUS"); ?></th>
					<th><?php echo JText::_("COM_DIGICOM_AMOUNT_PAID"); ?></th>
					<th><?php echo JText::_("COM_DIGICOM_ORDER_ACTION"); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php
			$i = 0;
			if(count($n) > 0){
				foreach($this->orders as $key=>$order){
					//print_r($order);die;
					$id = $order->id;

					$order_link = JRoute::_("index.php?option=com_digicom&view=order&id=".$id."&Itemid=".$Itemid);
					$order_link = '<a class="btn btn-success" href="'.$order_link.'">'.JText::_('COM_DIGICOM_ORDER_DETAILS').'</a>';

					$rec_link = JRoute::_("index.php?option=com_digicom&view=order&layout=invoice&id=".$id."&tmpl=component&Itemid=".$Itemid);
					$rec_link = '<a class="btn btn-info" href="'.$rec_link.'" target="_blank">'.JText::_('DSVIEWANDPRINT').'</a>';

					// Price
					$order_price = DigiComSiteHelperDigiCom::format_price($order->amount_paid, $order->currency, true, $configs);
					?>
					<tr>
						<td>
							#<?php echo $order->id; ?>
						</td>
						<td>
							<?php echo date($configs->get('time_format','d-M-Y'), $order->order_date);?>
						</td>
						<td>
							<?php
							$labelClass = '';
							if ( strtolower($order->status) === 'active') $labelClass = 'label-success';
							elseif ( strtolower($order->status) === 'pending') $labelClass = 'label-warning';
							?>
							<span class="label <?php echo $labelClass; ?>"><?php echo $order->status; ?></span>
						</td>
						<td>
							<?php echo $order_price; ?>
						</td>
						<td>
							<?php echo $order_link . ' ' .$rec_link; ?>
						</td>
					</tr>
					<?php
					$i++;
				}
			}else{ ?>
				<tr>
					<td colspan="5">
						<?php echo JText::_('COM_DIGICOM_ORDERS_EMPTY'); ?>
					</td>
				</tr>
			<?php } ?>

			</tbody>
		</table>

	</form>

	<?php DigiComSiteHelperDigicom::loadModules('digicom_footer','xhtml'); ?>

	<input type="hidden" name="option" value="com_digicom" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="view" value="orders" />
	<input type="hidden" name="Itemid" value="<?php echo JRequest::getVar("Itemid", "0"); ?>" />
</div>

<?php echo DigiComSiteHelperDigiCom::powered_by(); ?>
