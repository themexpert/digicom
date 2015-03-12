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

?>

<div id="digicom">

	<h1 class="digi-page-title">Buying: <?php echo $this->lists['product']->name; ?></h1>

	<table width="100%">
		<tr>
			<td><input type="radio" name="changetype" value="renew" id="changetyperenew"></td>
			<td><h1><label for="changetyperenew">Select and existing license to renew</label></h1><td>
		</tr>
		<tr>
			<td></td>
			<td>
				<?php //print_r($this->lists['licenses']); ?>
				<table width="100%">
					<tr style="background: #ccc">
						<th width="1%"></th>
						<th width="33%">License #</th>
						<th width="33%">Purchase Date</th>
						<th width="33%">Renewal Plan</th>
					</tr>
					<?php $k = 1;
	foreach($this->lists['licenses'] as $lic) { ?>
					<tr style="background: <?php if (($k % 2) == 0) {
			echo "#f7f7f7";
		} else {
			echo "#ffffff";
		} ?>">
						<td style="padding:0.5em"><input type="checkbox" name="licenses[]" value="<?php echo $lic->id; ?>" /></td>
						<td>#<?php echo $lic->licenseid; ?></td>
						<td><?php echo $lic->purchase_date; ?></td>
						<td><?php echo $lic->plains; ?></td>
					</tr>
		<?php
		$k++;
	}
	?>
				</table>
			</td>
		</tr>

		<tr>
			<td><input type="radio" name="changetype" value="plan" id="changetypeplan"></td>
			<td><h1><label for="changetypeplan">Purchase a new licenses for this product</label></h1></td>
		</tr>
		<tr>
			<td></td>
			<td>

				<form action="index.php" method="post">

					<h2>Subcription plan:</h2>
					<div>
						<?php echo $this->lists['plans']; ?>
					</div>

					<h2>Payment</h2>
					<div>
	<?php echo $this->lists['plugins']; ?>
					</div>

					<div>
						<br/>
						<br/>
						<input type="submit" value="Continue >>>"/>
					</div>

					<input type="hidden" name="option" value="com_digicom">
					<input type="hidden" name="controller" value="Cart"/>
					<input type="hidden" name="pid" value="<?php echo $this->lists['product']->id; ?>"/>
					<input type="hidden" name="cid" value="<?php echo $this->cid; ?>"/>
					<input type="hidden" name="task" value="add"/>
					<input type="hidden" name="status" value="change"/>
					<!-- input type="hidden" name="task" value="add_subscription_product"/ -->
					<input name="Itemid" type="hidden" value="<?php global $Itemid;
	echo $Itemid; ?>">
				</form>

			</td>
		</tr>

	</table>

	<?php echo DigiComHelper::powered_by(); ?>

</div> <!-- End of Digicom -->
