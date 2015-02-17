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
//$document->addStyleSheet("components/com_digicom/assets/css/digicom.css");

?>
<div id="returnJSON"></div>
<fieldset class="adminform">

	<legend><?php echo JText::_( 'Edit Order' ); ?></legend>

<?php if (!empty( $this->sidebar)) : ?>
<div id="j-sidebar-container" class="">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="">
<?php else : ?>
<div id="j-main-container" class="">
<?php endif;?>
<form id="adminForm" action="index.php" method="post" name="adminForm">
	<table width="100%">
		<tr>
			<td width="30%">Username</td>
			<td><?php echo $this->cust->username.""; ?></td>
			<td>
				<!-- a href="index.php?option=com_digicom&controller=orders&task=newCreateCustomer&username=<?php echo $this->cust->username; ?>">Change</a -->
			</td>
		</tr>
		<tr>
			<td colspan="3" style="background:#ccc;">
				<h3><?php echo JText::_( 'Product(s) to this order' ); ?></h3>
			</td>
		</tr>
		<tr>
			<td colspan="3" id="product_items">

<!-- Products -->

<?php foreach( $this->products as $product ) { ?>

				<div id="product_item_<?php echo $product->name; ?>">
					<table width="100%">
						<tr>
							<td style="border-top: 1px solid rgb(204, 204, 204); padding-top: 5px;" width="30%"><?php echo JText::_( 'Product' ); ?></td>
							<td style="border-top: 1px solid rgb(204, 204, 204); padding-top: 5px;">
								<div style="float:left">
									<span id="product_name_text_<?php echo $product->id; ?>" style="line-height: 17px;padding: 0.2em; border: 1px solid rgb(204, 204, 204); display: block; width: 250px; overflow: visible;"><?php echo $product->name; ?></span>
									<input type="hidden" value="" name="product_id[<?php echo $product->id; ?>]" id="product_id<?php echo $product->id; ?>"/>
								</div>
							</td>
						</tr>
						<tr>
							<td><?php echo JText::_( 'Subcription type' ); ?></td>
							<td><?php echo ($product->license->renew)?'Renewal':'New'; ?></td>
						</tr>
						<?php if($product->license->renew) {  
//							dsdebug($product->renewlicense); ?>

						<tr>
							<td><?php echo JText::_( 'License to renew' ); ?></td>
							<?php
								if (empty($product->renewlicense->domain)) {
									$domain = '( domain is not set )';
								} else {
									$domain = '('.$product->renewlicense->domain.')';
								}
							?>
							<td><?php echo "#".$product->renewlicense->licenseid." - ".$product->renewlicense->purchase_date.$domain; ?></td>
						</tr>
						<?php } ?>
						<tr>
							<td><?php echo JText::_( 'Subcription plain' ); ?></td>
							<td><?php echo $product->plans->name; ?> - <?php echo $product->plans->price; ?></td>
						</tr>
					</table>
				</div>

<?php  } ?>

<!-- /Products -->

			</td>
		</tr>
		<!-- Add Products -->
		<!-- tr>
			<td style="border-top:1px solid #ccc;padding-top:5px;"></td>
			<td style="border-top:1px solid #ccc;padding-top:5px;"><a href="#" id="buttonaddproduct"><?php echo JText::_( 'Add Product' ); ?></a>
			</td>
			<td style="border-top:1px solid #ccc;padding-top:5px;"></td>
		</tr -->
		<!-- Common info  -->
		<tr>
			<td style="border-top:1px solid #ccc;padding-top:5px;"><?php echo JText::_( 'Payment method' ); ?></td>
			<td style="border-top:1px solid #ccc;padding-top:5px;" id="payment_method"><?php echo $this->plugins; ?></td>
			<td style="border-top:1px solid #ccc;padding-top:5px;"></td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'Promocode' ); ?></td>
			<td><?php echo $this->promocode; ?></td>
			<td></td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'Amount' ); ?></td>
			<td id="amount"><?php echo $this->order->amount; ?></td>
			<td></td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'Tax' ); ?></td>
			<td id="tax">0</td>
			<td></td>
		</tr>
		<!-- tr>
			<td><?php echo JText::_( 'Discount' ); ?></td>
			<td id="discount">0</td>
			<td></td>
		</tr -->
		<tr>
			<td><?php echo JText::_( 'Total' ); ?></td>
			<td id="total"><?php echo $this->total; ?></td>
			<td></td>
		</tr>
		<tr>
			<td><?php echo JText::_( 'Amount paid' ); ?></td>
			<td><span id="currency_amount_paid"></span><input id="amount_paid" name="amount_paid" type="text" value="<?php echo $this->amount_paid; ?>"/></td>
			<td></td>
		</tr>
		<!-- /Common info  -->
	</table>


		<input type="hidden" name="option" value="com_digicom"/>
		<input type="hidden" name="controller" value="Orders"/>
		<input type="hidden" name="userid" value="<?php echo $this->cust->id; ?>"/>
		<input type="hidden" name="username" value="<?php echo $this->cust->username; ?>"/>
		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="id" value="<?php echo $this->order->id; ?>"/>
</form>


<div style="border-top:1px solid #ccc;padding-top:5px;">
	<input onclick="javascript: submitbutton('save')" type="button" name="task" value="Save"/>
</div>

</div>
</fieldset>