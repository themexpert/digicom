<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 420 $
 * @lastmodified	$LastChangedDate: 2013-11-16 11:08:21 +0100 (Sat, 16 Nov 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

defined ('_JEXEC') or die ("Go away.");

$k = 0;
$n = count ($this->custs);

$Itemid = JRequest::getVar("Itemid", "0");

$cart_itemid = DigiComHelper::getCartItemid();
$and_itemid = "";
if($cart_itemid != ""){
	$and_itemid = "&Itemid=".$cart_itemid;
}

?>

<div class="digicom">
	<div class="navbar">
		<div class="navbar-inner hidden-phone">
			<ul class="nav">
				<li>
					<a href="<?php echo JRoute::_("index.php?option=com_digicom&view=licenses&Itemid=".$Itemid); ?>"><i class="ico-download"></i> <?php echo JText::_("DIGI_MY_DOWNLOADS"); ?></a>
				</li>
				<li class="divider-vertical"></li>
				<li>
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
			<li>
				<a href="<?php echo JRoute::_("index.php?option=com_digicom&view=orders&Itemid=".$Itemid); ?>"><i class="ico-list-alt hidden-phone"></i> <?php echo JText::_("DIGI_MY_ORDERS"); ?></a>
			</li>
			<li class="divider-vertical"></li>
			<li>
				<a href="<?php echo JRoute::_("index.php?option=com_digicom&controller=cart&task=showCart".$and_itemid); ?>"><i class="ico-shopping-cart hidden-phone"></i> <?php echo JText::_("DIGI_MY_CART"); ?></a>
			</li>
		</ul>
	</div>

<h1><?php echo JText::_("DIGI_MY_STORE_ACCOUNT"); ?></h1>
<?php

	if ($n < 1):

		echo JText::_('DSNOCUSTOMER');

?>

	<form action="index.php?option=com_digicom" name="adminForm" method="post">
	  	<input type="hidden" name="option" value="com_digicom" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="controller" value="Customers" />
	</form>

<?php

	else:

?>
<form action="index.php?option=com_digicom" name="adminForm" method="post">
<div id="editcell" >
<table class="adminlist">
<thead>

	<tr>
		<th width="5">
			<input type="checkbox" onclick="checkAll(<?php echo $n; ?>)" name="toggle" value="" />
		</th>
			<th width="20">
			<?php echo JText::_('DSID');?>
		</th>
		<th>
			<?php echo JText::_('DSFULLNAME');?>
		</th>
		<th>
			<?php echo JText::_('DSUSERNAME');?>
		</th>


	</tr>
</thead>

<tbody>

<?php
	for ($i = 0; $i < $n; $i++):
	$cust = $this->custs[$i];
	$id = $cust->id;
	$checked = JHTML::_('grid.id', $i, $id);
	$link = JRoute::_("index.php?option=com_digicom&controller=Customers&task=edit&cid=".$id);
//	$published = JHTML::_('grid.published', $cat, $i );
?>
	<tr class="row<?php echo $k;?>">
		 	<td>
		 			<?php echo $checked;?>
		</td>

		 	<td>
		 			<?php echo $id;?>
		</td>
		 	<td>
		 			<a href="<?php echo $link;?>" ><?php echo $cust->firstname." ".$cust->lastname;?></a>
		</td>
		 	<td>
		 			<?php echo $cust->username;?>
		</td>

	</tr>


<?php
		$k = 1 - $k;
	endfor;
?>
</tbody>


</table>

</div>

<input type="hidden" name="option" value="com_digicom" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="Customers" />
</form>

<?php
	endif;

?>

<?php echo DigiComHelper::powered_by(); ?>

</div>