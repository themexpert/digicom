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

$cart_itemid = DigiComHelper::getCartItemid();
$and_itemid = "";
if($cart_itemid != ""){
	$and_itemid = "&Itemid=".$cart_itemid;
}
$product_itemid = DigiComHelper::getProductItemid();
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
$c = new DigiComSessionHelper();
$details = $c->getTransactionData();
//echo '<pre>'.print_r($details, true).'</pre>';
$gacc = DCConfig::get('google_account','');
//echo '<pre>'.print_r($gacc,true).'</pre>';
$database = JFactory::getDBO();

$sql = "select `in_trans` from #__digicom_settings";
$database->setQuery($sql);
$database->query();
$in_trans = $database->loadResult();
//echo '<hr/>';
//var_dump(isset($in_trans) && $in_trans > 0);
//echo '<hr/>';

if(isset($in_trans) && $in_trans > 0)
{
	$sql = "update #__digicom_settings set `in_trans`=0";
	$database->setQuery($sql);
	$database->query();
	$_SESSION['in_trans'] = 0;
	$db = JFactory::getDBO();
	$sql = "select transaction_details from #__digicom_session";
	if($success == 1){
		echo urldecode($mosmsg)."<br />".$configs->thankshtml."<br />";
		$non_taxed = $details['nontaxed'];
		$orderid = $details["cart"]["orderid"];

		DigiComHelper::affiliate($non_taxed, $orderid, $configs);
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
		echo $configs->ftranshtml."<br />";
		$mainframe->setPageTitle(JText::_("DSFAILEDPAYMENT"));
	}
}
$invisible = 'style="display:none;"';

?>

<div class="digicom">
	<div class="navbar">
		<div class="navbar-inner hidden-phone">
			<ul class="nav hidden-phone">
				<li>
					<a href="<?php echo JRoute::_("index.php?option=com_digicom&view=licenses&Itemid=".$Itemid); ?>"><i class="ico-download"></i> <?php echo JText::_("DIGI_MY_DOWNLOADS"); ?></a>
				</li>
				<li class="divider-vertical"></li>
				<li class="active">
					<a href="<?php echo JRoute::_("index.php?option=com_digicom&view=orders&Itemid=".$Itemid); ?>"><i class="ico-list-alt"></i> <?php echo JText::_("DIGI_MY_ORDERS"); ?></a>
				</li>
				<li class="divider-vertical"></li>
				<li>
					<a href="<?php echo JRoute::_("index.php?option=com_digicom&controller=cart&task=showCart".$and_itemid); ?>"><i class="ico-shopping-cart"></i> <?php echo JText::_("DIGI_MY_CART"); ?></a>
				</li>
			</ul>
		</div>
		<ul class="nav nav-pills hidden-desktop">
			<li>
				<a href="<?php echo JRoute::_("index.php?option=com_digicom&view=licenses&Itemid=".$Itemid); ?>"><i class="ico-download hidden-phone"></i> <?php echo JText::_("DIGI_MY_DOWNLOADS"); ?></a>
			</li>
			<li class="divider-vertical"></li>
			<li class="active">
				<a href="<?php echo JRoute::_("index.php?option=com_digicom&view=orders&Itemid=".$Itemid); ?>"><i class="ico-list-alt hidden-phone"></i> <?php echo JText::_("DIGI_MY_ORDERS"); ?></a>
			</li>
			<li class="divider-vertical"></li>
			<li>
				<a href="<?php echo JRoute::_("index.php?option=com_digicom&controller=cart&task=showCart".$and_itemid); ?>"><i class="ico-shopping-cart hidden-phone"></i> <?php echo JText::_("DIGI_MY_CART"); ?></a>
			</li>
		</ul>
	</div>

<h1><?php echo JText::_("DIGI_MY_ORDERS"); ?></h1>

<?php

if ($n < 1):

	/*  NO, orders is not foind  */

	$continue_url = DigiComHelper::DisplayContinueUrl($configs,$this->caturl);

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
	$orders_link = JRoute::_("index.php?option=com_digicom&controller=orders&Itemid=".$Itemid);

?>

		<form action="index.php" name="adminForm" method="post">

			<div id="digicom_body">
				<div class="digicom input-append">
					<input type="text" id="dssearch" name="search" class="digi_textbox"  value="<?php echo trim(JRequest::getVar('search', '')); ?>" size="30"/>
					<button type="submit" class="btn"><i class="ico-search"></i> <?php echo JText::_("DIGI_SEARCH"); ?></button>
				</div>
				<div>

					<table class="table table-bordered table-striped">
						<thead>
						<tr>
							<th><?php echo JText::_("DIGI_ORDER_DETAILS"); ?></th>
						</tr>
						</thead>
						<tbody>
					<?php
							$i = 0;
							foreach($this->orders as $key=>$order){
								$id = $order->id;

								$order_link = JRoute::_("index.php?option=com_digicom&controller=orders&task=view&orderid=".$id."&Itemid=".$Itemid);
								$order_link = '<a href="'.$order_link.'">'.(JText::_("DSVIEWORDER")).'</a>';

								$l_link = JRoute::_("index.php?option=com_digicom&controller=licenses&task=list&licid=".$id."&Itemid=".$Itemid);
								$l_link = '<a href="'.$l_link.'">'.$order->lcount.'</a>';

								$order_link = JRoute::_("index.php?option=com_digicom&controller=orders&task=view&orderid=".$id."&Itemid=".$Itemid);
								$order_link = '<a href="'.$order_link.'">'.$id.'</a>';

								$rec_link = JRoute::_("index.php?option=com_digicom&controller=orders&task=showrec&orderid=".$id."&tmpl=component&Itemid=".$Itemid);
								$rec_link = '<a class="btn" href="'.$rec_link.'" target="_blank">'.JText::_('DSVIEWANDPRINT').'</a>';

								// Price
								$order_price = DigiComHelper::format_price($order->amount_paid, $configs->get('currency','USD'), true, $configs);
						?>
						<!-- Order -->
						<tr>
							<td>
								<ul>
									<?php
									if(is_array($order->products)){
										foreach($order->products as $prod)
										{
											$p_link = '';
											$db = JFactory::getDbo();
											$sql = "SELECT `articlelink`, `articlelinkid`, `articlelinkuse`, `id`
														FROM `#__digicom_products`
 													WHERE `id`=".$prod->id;
											$db->setQuery($sql);
											$pro_item = $db->loadObject();
											if( $pro_item->articlelinkuse==1 && $pro_item->articlelinkid ) {
												require_once JPATH_SITE . '/components/com_content/helpers/route.php';
												$sql = "SELECT
															co.id,
															concat(co.id, ':', co.alias) AS `slug`,
															concat(ca.id, ':', ca.alias) AS `catslug`
														FROM
															#__content AS co
																INNER JOIN
															#__categories AS ca ON co.catid = ca.id
														WHERE
															co.id =".$pro_item->articlelinkid;
												$db->setQuery($sql);
												$res = $db->loadObject();
												if( $res ){
													$p_link = JRoute::_(ContentHelperRoute::getArticleRoute($res->slug, $res->catslug));
												}
											} elseif ($pro_item->articlelink ){
												$p_link = JURI::root().$pro_item->articlelink;
											}
											
											if(!$p_link )
												$p_link = JRoute::_("index.php?option=com_digicom&controller=products&task=view&cid=".$prod->catid."&pid=".$prod->id.$andProdItem);
											if ($prod->hide_public)
											{
												echo '<li class="general_text_larger">'.$prod->name.' (#'.$order->id.')</li>';
											}
											else
											{
												echo '<li class="general_text_larger"><a href="'.$p_link.'">'.$prod->name.'</a> (#'.$order->id.')</li>';
											}
										}
									}
									?>
									<li class="general_text_larger"><strong><?php echo JText::_("DIGI_PURCHASED_ON"); ?>:</strong> <?php echo date($configs->get('time_format','d-m-Y'), $order->order_date);?> </li>
								</ul>
								<span class="digicom_invoice">
									<?php
									if($order->status == "Pending"){
										echo JText::_("DIGI_PENDING_PAYMENT");
									}
									else{
										echo $rec_link;
									}
									?>
								</span>
							</td>
						</tr>
						<!-- /End Order -->
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
			<input type="hidden" name="controller" value="Orders" />
			<input type="hidden" name="Itemid" value="<?php echo JRequest::getVar("Itemid", "0"); ?>" />

		</form>

<?php
endif;

if( $this->ga && isset($in_trans) && $in_trans > 0 ){
	
	if( !isset($orderid) || !$orderid ){
		$orderid = $details["cart"]["orderid"];
	}
 	if(DCConfig::get('conversion_id','') != '' && DCConfig::get('conversion_label','') != ''){
		echo GoogleHelper::trackingOrder($orderid);
	}

}
?>

<?php echo DigiComHelper::powered_by(); ?>

</div>
