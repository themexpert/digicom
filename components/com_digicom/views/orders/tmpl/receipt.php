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

$invisible = 'style="display:none;"';

$k = 0;
$n = count ($this->order->products);
$configs = $this->configs;
$order = $this->order;
$user = $this->customer->_customer;

global $Itemid;

if ($this->order->id < 1):

	echo JText::_('DSEMPTYORDER');
?>

	<form action="<?php echo JRoute::_("index.php?option=com_digicom&controller=orders&task=list"."&Itemid=".$Itemid); ?>" name="adminForm" method="post">
	  	<input type="hidden" name="option" value="com_digicom" />
		<input type="submit" value="<?php echo JText::_("DSVIEWORDERS");?>" />
	</form>

<?php
	else:
?>

<form action="index.php" name="adminForm" method="post" style="padding-left: 10px; padding-right:10px; padding-top:100px;">

<table  class="adminlist" width="100%"  border="0" cellpadding="3" cellspacing="0" bordercolor="#cccccc" style="border-collapse: collapse">
	<tr>
		<td align="left" valign="top">
			<?php
				$store_logo = $configs->get('store_logo','');
				if(trim($store_logo) != ""){
			?>
					<img src="<?php echo JURI::root()."images/stories/digicom/store_logo/".trim($store_logo); ?>" alt="store_logo" border="0">
			<?php
				}
			?>
		</td>
		<td align="right" valign="top">
			<input id="print_button" class="btn" type="button" value="<?php echo JText::_("DSPRINT");?>" onclick="document.getElementById('print_button').style.display='none'; javascript:window.print(); return false;" />
		</td>
	</tr>

	<tr>
	<th align="left">
		<b><?php echo trim($configs->get('store_name','DigiCom Store')) != "" ? $configs->get('store_name','DigiCom Store') : ""; ?></b>
	</th>

	<th style="font-weight:normal" align="right">
		<?php echo JText::_("DSMYORDER")." #".$order->id; ?>
		<br />
		<?php echo JText::_("DSDATE")." ".date( $configs->get('time_format','d-m-Y'), $order->order_date);?>
	</th>
<tr><tr>

	<td align="left">
		<?php echo trim($configs->get('store_url','') != "") ? "&nbsp;&nbsp;URL: ".$configs->get('store_url','') : ""; ?><br />
		<table>
			<?php
			if(trim($configs->address) != ""){
			?>
			<tr>
				<td>
					<?php echo JText::_('DSADDRESS');?>:</td><td> <?php echo $configs->address;?>
				</td>
			</tr>
			<?php
			}

			if(trim($configs->get('state','')) != ""){
			?>
			<tr>
				<td></td>
				<td>
					<?php if(!empty($configs->city)) : ?>
					<?php echo $configs->city;?>,&nbsp;
					<?php endif; ?>
					<?php echo $configs->get('state','');?>
				</td>
			</tr>
			<?php
			}

			if(trim($configs->get('country','')) != ""){
			?>
			<tr>
				<td></td>
				<td>
					<?php if(!empty($configs->zip)) : ?>
					<?php echo $configs->zip;?>,&nbsp;
					<?php endif; ?>
					<?php echo $configs->get('country','');?>
				</td>
			</tr>
			<?php
			}
			?>
		</table>
		<?php echo trim($configs->phone) != "" ? JText::_("PHONE").":".$configs->phone : ""; ?><br />
		<?php echo trim($configs->fax) != "" ? JText::_("FAX").":".$configs->fax : ""; ?><br />
	</td>

	<td></td>
</tr>
<tr /><td><br /></td>
<tr /><td><br /></td>

<tr>
	<th align="left">
		<?php echo JText::_("DSBILLEDTO");?>
	</th>

	<th align="right">
<?php if ($order->shipping > 0):?>
		<?php echo JText::_("DSSHIPPEDTO");?>
<?php endif; ?>
	</th>
<tr><tr>
	<td align="left">
		<?php if(!empty($user->firstname)) : ?>
		<?php echo $user->firstname?>&nbsp;<?php echo $user->lastname;?> <br />
		<?php endif; ?>
		<?php if(!empty($user->address)) : ?>
			<?php echo $user->address?><br />
		<?php endif; ?>
		<?php if(!empty($user->city)) : ?>
		<?php echo $user->city?>,&nbsp;<?php echo $user->state?> <br />
		<?php endif; ?>
		<?php if(!empty($user->zipcode)) : ?>
		<?php echo $user->zipcode;?>,&nbsp;<?php echo $user->country?> <br />
		<?php endif; ?>
	</td>

	<td>
<?php if ($order->shipping > 0):?>
		<?php echo $user->firstname?>&nbsp;<?php echo $user->lastname;?> <br />
		<?php echo $user->shipaddress?><br />
		<?php echo $user->shipcity?>,&nbsp;<?php echo $user->shipstate?> <br />
		<?php echo $user->shipzipcode;?>,&nbsp;<?php echo $user->shipcountry?> <br />

<?php endif;?>
	</td>
</tr>

</table>
<span align="left"><b></b></span>
<br /><br />
<table class="adminlist" width="100%"  border="0" cellpadding="3" cellspacing="0" bordercolor="#cccccc" style="border-collapse: collapse">
<thead>

	<tr>
		<th></th>
		<th class="sectiontableheader"  <?php //if ($configs->showoipurch == 0) echo $invisible;?> >
			<?php echo JText::_('DSPROD');?>
		</th>

		<th class="sectiontableheader" <?php if ($configs->showolics == 0) echo $invisible;?>>
			<?php echo JText::_('DSLICENSEID');?>
		</th>

		<th class="sectiontableheader"  <?php if ($configs->showopaid == 0) echo $invisible;?> >
			<?php echo JText::_('DSPAID');?>
		</th>


		<th class="sectiontableheader digi_header">
			<?php echo JText::_('DSPRICE');?>
		</th>


		<th class="sectiontableheader digi_header">
			<?php echo JText::_('DSDISCOUNT');?>
		</th>

		<th class="sectiontableheader digi_header">
			<?php echo JText::_('DSTOTAL');?>
		</th>

	</tr>
</thead>

<tbody>

<?php
	$total = "0";

	for ($i = 0; $i < $n; $i++):
		$prod = $order->products[$i];
		$id = $order->id;

		if (count ($prod->orig_fields) > 0)
		foreach ($prod->orig_fields as $j => $z) {
			$val = explode(",", $z->optioname);
			if (isset($val[1]) && strlen (trim($val[1])) > 0) {
				$prod->price += floatval(trim($val[1]));
				$prod->amount_paid += floatval(trim($val[1]));
			}
		}
		if (!isset($prod->currency)) $prod->currency = $configs->get('currency','USD');
?>
	<tr class="row<?php echo $k;?> sectiontableentry<?php echo ($i%2 + 1);?>">
		<td>
			<?php 
				echo $i+1; 
			?>
		</td>

		<td <?php //if($configs->showoipurch == 0) echo $invisible;?>>
		 	<?php echo $prod->name;?>
		</td>

		 	<td <?php if ($configs->showolics == 0) echo $invisible;?> >
		 			<?php echo $prod->licenseid;?>
		</td>

		 	<td  <?php if ($configs->showopaid == 0) echo $invisible;?>>
		 			<?php echo 1;//$prod->count;?>
		</td>

		<td>
			<span style="white-space:nowrap;"><?php echo DigiComHelper::format_price($prod->price, $prod->currency, true, $configs);?></span>
		</td>

		<td>
			<span style="white-space:nowrap;"><?php echo DigiComHelper::format_price($prod->price - $prod->amount_paid, $prod->currency, true, $configs);?></span>
		</td>

		<td>
			<?php
				$total += $prod->amount_paid;
			?>
			<span style="white-space:nowrap;"><?php echo DigiComHelper::format_price($prod->amount_paid, $prod->currency, true, $configs);?></span>
		</td>



<?php
		$k = 1 - $k;
	endfor;

	$colspan=5;
	if ($configs->showolics == 0) $colspan--;
?>
	<tr style="border-style:none;">
		<td style="border-style:none;" colspan="7"><hr /></td>
	</tr>

	<tr>
		<td colspan="<?php echo $colspan; ?>" ></td>
		<td style="font-weight:bold"><?php echo JText::_("DSSUBTOTAL");?></td>
		<td><span style="white-space:nowrap;"><?php echo DigiComHelper::format_price($order->amount, $prod->currency, true, $configs);?></span></td>
	</tr>

<?php 
	if($order->shipping > 0){
?>
	<tr>
		<td colspan="<?php echo $colspan; ?>"></td>
		<td style="font-weight:bold"><?php echo JText::_("DSSHIPPING");?></td>
		<td><span style="white-space:nowrap;"><?php echo DigiComHelper::format_price($order->shipping, $prod->currency, true, $configs);?></span></td>
	</tr>
<?php
	}
?>

<?php
	if($order->promocodediscount > 0){
		$total = $total - $order->promocodediscount;
?>
	<tr>
		<td colspan="<?php echo $colspan; ?>"></td>
		<td style="font-weight:bold"><?php echo JText::_("DSPROMO");?></td>
		<td><span style="white-space:nowrap;"><?php echo DigiComHelper::format_price($order->promocodediscount, $prod->currency, true, $configs);?></span></td>
	</tr>
<?php
	}
?>

	<tr>
		<td colspan="<?php echo $colspan; ?>"></td>
		<td style="font-weight:bold"><?php echo JText::_("DSTAX");?></td>
		<td><span style="white-space:nowrap;"><?php echo DigiComHelper::format_price($order->tax, $prod->currency, true, $configs);?></span></td>
	</tr>

	<tr>
		<td colspan="<?php echo $colspan; ?>"></td>
   		<td style="font-weight:bold"><?php echo JText::_("DSTOTAL");?></td>
		<?php
			if($order->amount_paid != "" && $order->amount_paid != "-1" && $order->amount_paid != $total){
				$total = $order->amount_paid;
			}
		?>
		<td><span style="white-space:nowrap;"><?php echo DigiComHelper::format_price($total, $prod->currency, true, $configs);?></span></td>
	</tr>
</tbody>


</table>


<input type="hidden" name="option" value="com_digicom" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="Orders" />
</form>
<?php

endif;

echo DigiComHelper::powered_by(); ?>
