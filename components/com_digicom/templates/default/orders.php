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

$cart_itemid = DigiComSiteHelperDigiCom::getCartItemid();
$and_itemid = "";
if($cart_itemid != ""){
	$and_itemid = "&Itemid=".$cart_itemid;
}
$product_itemid = DigiComSiteHelperDigiCom::getProductItemid();
$andProdItem = "";
if($product_itemid != "0"){
	$andProdItem = "&Itemid=".$product_itemid;
}

$k = 0;
$n = count ($this->orders);
//customers returning from payment gateway message processing
$success = JRequest::getVar("success", 2, "request");
$configs = $this->configs;
$mosmsg =  JRequest::getVar("mosmsg", '', "request");
$c = new DigiComSiteHelperSession();
$details = $c->getTransactionData();
//echo '<pre>'.print_r($details, true).'</pre>';
//$gacc = DCConfig::get('google_account','');
//echo '<pre>'.print_r($gacc,true).'</pre>';
$database = JFactory::getDBO();


if(isset($in_trans) && $in_trans > 0)
{
	$_SESSION['in_trans'] = 0;
	
	if($success == 1){
		echo urldecode($mosmsg)."<br />".$configs->get('thankshtml','')."<br />";
		$non_taxed = $details['nontaxed'];
		$orderid = $details["cart"]["orderid"];

		DigiComSiteHelperDigiCom::affiliate($non_taxed, $orderid, $configs);
		//$mainframe->setPageTitle(JText::_("DSSUCCESSFULPAYMENT"));
		//show google tracking script
		
		if(DCConfig::get('conversion_id','') != '' && $this->ga)
		{
			echo GoogleHelper::trackingOrder($orderid);
		}
		if(DCConfig::get('google_account','') != '')
		{
			echo GoogleHelper::trackingOrder($orderid);
		}
	}
	elseif($success == 0){
		echo $configs->get('ftranshtml','')."<br />";
		$mainframe->setPageTitle(JText::_("DSFAILEDPAYMENT"));
	}
}
$invisible = 'style="display:none;"';

?>

<div id="digicom">

	<div class="navbar">
		<div class="navbar-inner hidden-phone">
			<ul class="nav hidden-phone">
				<li>
					<a href="<?php echo JRoute::_("index.php?option=com_digicom&view=downloads&Itemid=".$Itemid); ?>"><i class="ico-download"></i> <?php echo JText::_("DIGI_MY_DOWNLOADS"); ?></a>
				</li>
				<li class="divider-vertical"></li>
				<li class="active">
					<a href="<?php echo JRoute::_("index.php?option=com_digicom&view=orders&Itemid=".$Itemid); ?>"><i class="ico-list-alt"></i> <?php echo JText::_("DIGI_MY_ORDERS"); ?></a>
				</li>
				<li class="divider-vertical"></li>
				<li>
					<a href="<?php echo JRoute::_("index.php?option=com_digicom&view=cart&task=showCart".$and_itemid); ?>"><i class="ico-shopping-cart"></i> <?php echo JText::_("DIGI_MY_CART"); ?></a>
				</li>
			</ul>
		</div>
		<ul class="nav nav-pills hidden-desktop">
			<li>
				<a href="<?php echo JRoute::_("index.php?option=com_digicom&view=downloads&Itemid=".$Itemid); ?>"><i class="ico-download hidden-phone"></i> <?php echo JText::_("DIGI_MY_DOWNLOADS"); ?></a>
			</li>
			<li class="divider-vertical"></li>
			<li class="active">
				<a href="<?php echo JRoute::_("index.php?option=com_digicom&view=orders&Itemid=".$Itemid); ?>"><i class="ico-list-alt hidden-phone"></i> <?php echo JText::_("DIGI_MY_ORDERS"); ?></a>
			</li>
			<li class="divider-vertical"></li>
			<li>
				<a href="<?php echo JRoute::_("index.php?option=com_digicom&view=cart&task=showCart".$and_itemid); ?>"><i class="ico-shopping-cart hidden-phone"></i> <?php echo JText::_("DIGI_MY_CART"); ?></a>
			</li>
		</ul>
	</div>

	<h1 class="digi-page-title"><?php echo JText::_("DIGI_MY_ORDERS"); ?></h1>

	<?php

	if ($n < 1):

		/*  NO, orders is not foind  */

	$continue_url = DigiComSiteHelperDigiCom::DisplayContinueUrl($configs,$this->caturl);

	?>

	<form action="<?php echo $continue_url;?>" name="adminForm" method="post">

		<div id="digicom_body">
			<div class="digicom input-append">
				<input type="text" name="search"  value="<?php echo JRequest::getVar("search", ""); ?>">
				<button type="submit" class="btn"><i class="ico-search"></i> <?php echo JText::_("DIGI_SEARCH"); ?></button>
			</div>
			<div class="digicom_orders">
			<?php
				echo JText::_('DSNOORDER');
			?>
			</div>
		</div>

		<input type="submit" value="<?php echo JText::_("DSCONTINUESHOPING");?>" class="btn" />
		<input type="hidden" name="Itemid" value="<?php echo JRequest::getVar("Itemid", "0"); ?>" />

	</form>

	<?php

	else:

	/*  YES, orders is found  */
	$orders_link = JRoute::_("index.php?option=com_digicom&view=orders&Itemid=".$Itemid);

	?>

		<form action="<?php echo $orders_link ; ?>" name="adminForm" method="post">

		<div class="input-append">
			<input type="text" id="dssearch" name="search" class="digi-textbox"  value="<?php echo trim(JRequest::getVar('search', '')); ?>" size="30"/>
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
							};
						?>

					</table>
				</div>
			</div>

			<input type="hidden" name="option" value="com_digicom" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="view" value="orders" />
			<input type="hidden" name="Itemid" value="<?php echo JRequest::getVar("Itemid", "0"); ?>" />

	</form>

<?php
endif;
?>

<?php echo DigiComSiteHelperDigiCom::powered_by(); ?>

</div>
