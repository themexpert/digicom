<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

$Itemid = JRequest::getInt("Itemid", 0);

$customer = $this->customer->_customer;
$user = $this->customer->_user;
$configs = $this->configs;
?>


<div id="digicom">

	<?php DigiComSiteHelperDigicom::loadModules('digicom_toolber'); ?>

	<h2 class="digi-page-title"><?php echo JText::_("COM_DIGICOM_DASHBOARD_PAGE_TITLE"); ?></h2>
	<div class="row-fluid">
		<div class="span7">
			<p><?php echo JText::_('COM_DIGICOM_DASHBOARD_HEADER_INTRO'); ?></p>
		</div>
		<div class="span5">
			<div class="customer-info">
				<h4><?php echo $customer->firstname; ?> <?php echo $customer->lastname; ?> </h4>
				<p>
					<?php echo JText::_('COM_DIGICOM_CUSTOMER_ID'); ?>: <?php echo $customer->id; ?> <br>
					<?php echo JText::_('COM_DIGICOM_EMAIL'); ?>: <?php echo $user->email; ?> <br>
					<?php echo JText::_('COM_DIGICOM_CUSTOMER_SINCE') . $customer->registerDate; ?>
				</p>
			</div>			
		</div>
	</div>
	
	<h3><?php echo JText::_('COM_DIGICOM_DASHBOARD_MY_ACTIVE_LICENSES');?></h3>
	<form action="<?php echo JRoute::_('index.php?options=com_digicom&view=dashboard'); ?>" name="adminForm" method="post">	

		<div class="licenced-products tx-cards">
		<?php if(count($this->items) > 0): ?>
			<?php
			$i = 0;
				foreach($this->items as $key=>$licence){
					?>
					<div class="row-fluid">
						<div class="span6"><?php echo $licence->name; ?></div>
						<div class="span2">
							<?php 
							$date = new DateTime($licence->purchase);
							$result = $date->format('d M Y');
							echo $result;
							?>
						</div>						
						<div class="span2">
							<?php 
							$date = new DateTime($licence->expires);
							$result = $date->format('d M Y');
							?>
							<?php echo ($licence->expires == '0000-00-00 00:00:00' ? JText::_('COM_DIGICOM_PRODUCT_EXPIRATION_NEVER') : $result);?>
						</div>
						<div class="span2	">
							<span class="label label-info"><?php echo ($licence->expires == '0000-00-00 00:00:00' ? JText::_('COM_DIGICOM_PRODUCT_VALIDITY_UNLIMITED') : $licence->dayleft .' '. JText::_('COM_DIGICOM_DAYS') ) ; ?></span>
							<?php if($licence->expires != '0000-00-00 00:00:00'):?>
							<a target="_blank" class="btn btn-default btn-mini pull-right hasTooltip" 
							   title="<?php echo JText::_('COM_DIGICOM_ADD_TO_CALENDAR');?>" 
							   href="<?php echo DigiComSiteHelperDigicom::prepareGCalendarUrl($licence);?>">
								<i class="icon-calendar"></i>
							</a>
							<?php endif; ?>
						</div>
					</div>
					<?php
					$i++;
				}
			?>
		</div
		<?php else: ?>
			<div class="alert alert-warning">
				<?php echo JText::_('COM_DIGICOM_ORDERS_NO_ORDER_FOUND_NOTICE'); ?>
			</div>
		<?php endif;?>
	</form>

	<?php DigiComSiteHelperDigicom::loadModules('digicom_footer','xhtml'); ?>

</div>


<?php echo DigiComSiteHelperDigiCom::powered_by(); ?>