<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 439 $
 * @lastmodified	$LastChangedDate: 2013-11-20 04:30:39 +0100 (Wed, 20 Nov 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

defined ('_JEXEC') or die ("Go away.");

$Itemid = JRequest::getInt("Itemid", 0);

$customer = $this->customer->_customer;
$user = $this->customer->_user;
?>


<div id="digicom">


	<div class="navbar">
		<div class="navbar-inner hidden-phone">
			<ul class="nav">
				<li class="active">
					<a href="<?php echo JRoute::_("index.php?option=com_digicom&view=dashboard&Itemid=".$Itemid); ?>"><i class="icon-home"></i> <?php echo JText::_("DIGI_MY_DASHBOARD"); ?></a>
				</li>
				<li class="divider-vertical"></li>
				<li>
					<a href="<?php echo JRoute::_("index.php?option=com_digicom&view=downloads&Itemid=".$Itemid); ?>"><i class="icon-download"></i> <?php echo JText::_("DIGI_MY_DOWNLOADS"); ?></a>
				</li>
				<li class="divider-vertical"></li>
				<li>
					<a href="<?php echo JRoute::_("index.php?option=com_digicom&view=orders&Itemid=".$Itemid); ?>"><i class="icon-list-alt"></i> <?php echo JText::_("DIGI_MY_ORDERS"); ?></a>
				</li>
				<li class="divider-vertical"></li>
				<li>
					<a href="<?php echo JRoute::_("index.php?option=com_digicom&view=cart&Itemid=".$Itemid); ?>"><i class="icon-shopping-cart"></i> <?php echo JText::_("DIGI_MY_CART"); ?></a>
				</li>
			</ul>
		</div>
		<ul class="nav nav-pills">
			<li class="active">
				<a href="<?php echo JRoute::_("index.php?option=com_digicom&view=dashboard&Itemid=".$Itemid); ?>"><i class="icon-home"></i> <?php echo JText::_("DIGI_MY_DASHBOARD"); ?></a>
			</li>
			<li class="divider-vertical"></li>
			<li>
				<a href="<?php echo JRoute::_("index.php?option=com_digicom&view=downloads&Itemid=".$Itemid); ?>"><i class="icon-download"></i> <?php echo JText::_("DIGI_MY_DOWNLOADS"); ?></a>
			</li>
			<li class="divider-vertical"></li>
			<li>
				<a href="<?php echo JRoute::_("index.php?option=com_digicom&view=orders&Itemid=".$Itemid); ?>"><i class="icon-list-alt"></i> <?php echo JText::_("DIGI_MY_ORDERS"); ?></a>
			</li>
			<li class="divider-vertical"></li>
			<li>
				<a href="<?php echo JRoute::_("index.php?option=com_digicom&view=cart&Itemid=".$Itemid); ?>"><i class="icon-shopping-cart"></i> <?php echo JText::_("DIGI_MY_CART"); ?></a>
			</li>
		</ul>
	</div>

	<h3><?php echo JText::_("COM_DIGICOM_MY_ACCOUNT_DASHBOARD_TITLE"); ?></h3>
	<div class="row-fluid">
		<div class="span7">
			<p>Enjoy all the features that are available on your personal space to view, track and manage all your data.</p>
		</div>
		<div class="span5">
			<div class="customer-info">
				<h4><?php echo $customer->firstname; ?> <?php echo $customer->lastname; ?> </h4>
				<p>Customer ID: <?php echo $customer->id; ?> <br>
				Email: <?php echo $user->email; ?> <br>
				Customer since <?php echo $customer->registerDate; ?>
			</div>			
		</div>
	</div>

</div>


<?php echo DigiComSiteHelperDigiCom::powered_by(); ?>