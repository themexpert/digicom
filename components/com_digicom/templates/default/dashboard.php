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

	<?php DigiComSiteHelperDigicom::loadModules('digicom_toolber'); ?>

	<h2><?php echo JText::_("COM_DIGICOM_MY_ACCOUNT_DASHBOARD_TITLE"); ?></h2>
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

	<?php DigiComSiteHelperDigicom::loadModules('digicom_footer','xhtml'); ?>

</div>


<?php echo DigiComSiteHelperDigiCom::powered_by(); ?>