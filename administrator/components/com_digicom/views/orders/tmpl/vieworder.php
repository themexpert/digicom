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

$document = JFactory::getDocument();
$document->addStyleSheet("components/com_digicom/assets/css/digicom.css");

$k = 0;
$n = count ($this->order->products);
$configs = $this->configs;
$order = $this->order;
$refunds = DigiComAdminModelOrder::getRefunds($order->id);
$chargebacks = DigiComAdminModelOrder::getChargebacks($order->id);

if ($this->order->id < 1):
	echo JText::_('DSEMPTYORDER');
	global $Itemid; ?>

	<form action="<?php echo JRoute::_("index.php?option=com_digicom&controller=orders&task=list"."&Itemid=".$Itemid); ?>" name="adminForm" method="post">
	  	<input type="hidden" name="option" value="com_digicom" />
		<input type="submit" value="<?php echo JText::_("DSVIEWORDERS");?>" />
	</form>

<?php


	else:
?>
  <form id="adminForm" action="index.php" name="adminForm" method="post">
<div id="contentpane" >
<table class="adminlist" width="100%"  border="1" cellpadding="3" cellspacing="0" bordercolor="#cccccc" style="border-collapse: collapse">
<caption class="componentheading"><?php echo JText::_("DSMYORDER")." #".$order->id; ?></caption>
</table>
<span align="left"><b><?php echo JText::_("DSDATE")." ".date( $configs->get('time_format','DD-MM-YYYY'), $order->order_date);?></b></span>
<br /><br />
<table class="adminlist" width="100%"  border="0" cellpadding="3" cellspacing="0" bordercolor="#cccccc" style="border-collapse: collapse">
<thead>

	<tr>
		<th class="sectiontableheader"></th>
		<th class="sectiontableheader"  >
			<?php echo JText::_('DSPROD');?>
		</th>

		<th class="sectiontableheader"  >
			<?php echo JText::_('DSLICENSEID');?>
		</th>

		<th class="sectiontableheader"  >
			<?php echo JText::_('DSAMOUNT');?>
		</th>


		<th class="sectiontableheader"  >
			<?php echo JText::_('DSPRICE');?>
		</th>


		<th class="sectiontableheader"  >
			<?php echo JText::_('DSDISCOUNT');?>
		</th>

		<th class="sectiontableheader" >
			<?php echo JText::_('DSTOTAL');?>
		</th>

	</tr>
</thead>

<tbody>

<?php 
	for ($i = 0; $i < $n; $i++):
		$prod = $order->products[$i];
		$id = $order->id;
//		$checked = JHTML::_('grid.id', $i, $id);
		if (count ($prod->orig_fields) > 0)
		foreach ($prod->orig_fields as $j => $z) {
			$val = explode(",", $z->optioname);
			if (isset($val[1]) && strlen (trim($val[1])) > 0) { 
				$prod->price += floatval(trim($val[1]));
				$prod->amount_paid += floatval(trim($val[1]));
			}



		}
		if (!isset($prod->currency)) $prod->currency = $configs->get('currency','USD');
;
?>
	<tr class="row<?php echo $k;?> sectiontableentry<?php echo ($i%2 + 1);?>"> 
		<td><?php echo $i+1; ?></td>
		 	<td >
		 			<?php echo $prod->name;?>
		</td>

		 	<td  >
		 			<?php echo $prod->licenseid;?>
		</td>

		 	<td  >
		 			<?php echo 1;//$prod->count;?>
		</td>

		<td>
			<?php echo DigiComAdminHelper::format_price($prod->price, $prod->currency, true, $configs);?>
		</td>

		<td>
			<?php echo DigiComAdminHelper::format_price($prod->price - $prod->amount_paid, $prod->currency, true, $configs);?>
		</td>

		<td>
			<?php echo DigiComAdminHelper::format_price($prod->amount_paid, $prod->currency, true, $configs);?>
		</td>



<?php 
		$k = 1 - $k;
	endfor;
?>
<tr style="border-style:none;"><td style="border-style:none;" colspan="7"><hr /></td></tr>
<tr><td colspan="5" ></td>
	<td style="font-weight:bold"><?php echo JText::_("DSSUBTOTAL");?></td>
	<td><?php echo DigiComAdminHelper::format_price($order->amount - $order->tax - $order->shipping, $prod->currency, true, $configs);?></td></tr>
<?php if ($order->shipping > 0):?>
<tr><td colspan="5"></td>
	<td style="font-weight:bold"><?php echo JText::_("DSSHIPPING");?></td>
	<td><?php echo DigiComAdminHelper::format_price($order->shipping, $prod->currency, true, $configs);?></td></tr>
<?php endif; ?>
<tr><td colspan="5"></td>
	<td style="font-weight:bold"><?php echo JText::_("DSTAX");?></td>
	<td><?php echo DigiComAdminHelper::format_price($order->tax, $prod->currency, true, $configs);?></td></tr>
<?php if ($refunds > 0):?>
<tr>
	<td colspan="4"></td>
	<td style="font-weight:bold"><?php echo JText::_("LICENSE_REFUNDS");?></td>
	<td><?php echo DigiComAdminHelper::format_price($refunds, $prod->currency, true, $configs); ?></td>
</tr>
<?php endif;?>
<?php if ($chargebacks > 0):?>
<tr>
	<td colspan="4"></td>
	<td style="font-weight:bold"><?php echo JText::_("LICENSE_CHARGEBACKS");?></td>
	<td><?php echo DigiComAdminHelper::format_price($chargebacks, $prod->currency, true, $configs); ?></td>
</tr>
<?php endif;?>
<tr><td colspan="5"></td>
	   	<td style="font-weight:bold"><?php echo JText::_("DSTOTAL");?></td>
	<td><?php
		$order->amount = $order->amount - $refunds - $chargebacks;
		echo DigiComAdminHelper::format_price($order->amount, $prod->currency, true, $configs);?></td></tr>
</tbody>


</table>

</div>

<input type="hidden" name="option" value="com_digicom" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="Orders" />
</form>
<?php

endif;