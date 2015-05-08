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
	<form action="<?php echo JRoute::_('index.php?options=com_digicom&view=dashboard'); ?>" name="adminForm" method="post">

		<div class="input-append">
			<input type="text" id="filter-search" name="filter-search" class="digi-textbox"  value="<?php echo trim(JRequest::getVar('filter-search', '')); ?>" size="30" />
			<button type="submit" class="btn"><?php echo JText::_("COM_DIGICOM_SEARCH"); ?></button>
			<button class="btn" onclick="document.getElementById('filter-search').value='';this.form.submit();"><i class="icon-remove"></i></button>
		</div>			

		<table class="table table-bordered table-striped">
			<thead>
				<tr>
					<th><?php echo JText::_("COM_DIGICOM_PRODUCT"); ?></th>
					<th><?php echo JText::_("COM_DIGICOM_LICENSE_ISSUE_DATE"); ?></th>
					<th><?php echo JText::_("COM_DIGICOM_LICENSE_EXPIRE_DATE"); ?></th>
					<th><?php echo JText::_("COM_DIGICOM_LICENSE_DAY_LEFT"); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php
			$i = 0;
			if(count($this->items) > 0){
				foreach($this->items as $key=>$licence){
					?>
					<tr>
						<td>
							<?php echo $licence->name; ?>
						</td>
						<td>
							<?php 
							$date = new DateTime($licence->purchase);
							$result = $date->format('d M Y');
							echo $result;
							?>
						</td>						
						<td>
							<?php 
							$date = new DateTime($licence->expires);
							$result = $date->format('d M Y');
							?>
							<?php echo ($licence->expires == '0000-00-00 00:00:00' ? JText::_('COM_DIGICOM_PRODUCT_EXPIRATION_NEVER') : $result);?>
						</td>
						<td>
							<span class="label label-info"><?php echo ($licence->expires == '0000-00-00 00:00:00' ? JText::_('COM_DIGICOM_PRODUCT_VALIDITY_UNLIMITED') : $licence->dayleft .' '. JText::_('COM_DIGICOM_DAYS') ) ; ?></span>
							<?php if($licence->expires != '0000-00-00 00:00:00'):?>
							<a target="_blank" class="btn btn-default btn-mini pull-right hasTooltip" 
							   title="<?php echo JText::_('COM_DIGICOM_ADD_TO_CALENDAR');?>" 
							   href="<?php echo DigiComSiteHelperDigicom::prepareGCalendarUrl($licence);?>">
								<i class="icon-calendar"></i>
							</a>
							<?php endif; ?>
						</td>

					</tr>
					<?php
					$i++;
				}
			}else{ ?>
				<tr>
					<td colspan="5">
						<?php echo JText::_('COM_DIGICOM_ORDERS_NO_ORDER_FOUND_NOTICE'); ?>
					</td>
				</tr>
			<?php } ?>

			</tbody>
		</table>

		


	</form>

	<?php DigiComSiteHelperDigicom::loadModules('digicom_footer','xhtml'); ?>

</div>


<?php echo DigiComSiteHelperDigiCom::powered_by(); ?>