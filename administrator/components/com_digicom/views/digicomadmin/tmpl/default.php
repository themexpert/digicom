<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 440 $
 * @lastmodified	$LastChangedDate: 2013-11-20 04:53:55 +0100 (Wed, 20 Nov 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

defined ('_JEXEC') or die ("Go away.");
$document = JFactory::getDocument();
$document->addStyleSheet("components/com_digicom/assets/css/digicom.css");
?>
<form action="<?php echo JRoute::_('index.php?option=com_digicom'); ?>" class="clearfix" method="post" name="adminForm" id="adminForm">
<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container" class="span12">
<?php endif;?>
		
		<div class="row-fluid">
			<div class="span5">
				<img src="components/com_digicom/assets/images/logo.png" />
			</div>
			<div class="span7">
				<div class="alert alert-warning pull-right">
					<?php echo $this->versionNotify(); ?>
				</div>
			</div>
		</div>
		
		<!-- LATEST ORDERS -->
		<div class="well well-small">
			<div class="module-title nav-header"><a href="index.php?option=com_digicom&controller=orders"><?php echo JText::_('DIGICOM_LATESTORDERS'); ?></a></div>
			<div class="row-striped">
				<?php
				foreach($this->latest_orders AS $order) :
				?>
				<div class="row-fluid">
					<div class="span7">
						<span class="label label-ds hasTip" title="" data-original-title="Order ID"><a href="index.php?option=com_digicom&controller=orders&task=show&cid[]=<?php echo $order->id; ?>"><?php echo JText::_('VIEWLICLICORDERID').$order->id; ?></a></span>
						<strong class="row-title">
							<a href="index.php?option=com_digicom&controller=customers&task=edit&cid[]=<?php echo $order->userid;?>">
								<?php echo $order->firstname.' '.$order->lastname;?></a>
						</strong>
					</div>
					<div class="span2">
						<span class="small pull-right"><?php echo $order->amount. ' '.$order->currency; ?></span>
					</div>
					<div class="span3">
						<span class="small"><i class="icon-calendar"></i> <?php echo date("Y-m-d", $order->order_date); ?></span>
					</div>
				</div>
				<?php
				endforeach;
				?>
			</div>
		</div>
		
		<!-- LATEST PRODUCTS -->
		<div class="well well-small">
			<div class="module-title nav-header"><a href="index.php?option=com_digicom&controller=products"><?php echo JText::_('DIGICOM_RECENTPROD'); ?></a></div>
			<div class="row-striped">
				<?php
				foreach($this->latest_products AS $product) :
				?>
				<div class="row-fluid">
					<div class="span7">
						<span class="label label-ds hasTip" title="" data-original-title="Order ID"><?php echo JText::_('ID');?># <?php echo $product->id; ?></span>
						<strong class="row-title">
							<a href="index.php?option=com_digicom&controller=products&task=edit&cid[]=<?php echo $product->id;?>">
								<?php echo $product->name;?></a>
						</strong>
					</div>
					<div class="span2">
						<span class="small pull-right"><a href="index.php?option=com_digicom&controller=categories&task=edit&cid[]=<?php echo $product->catid; ?>"><?php echo $product->category; ?></a></span>
					</div>
					<div class="span3">
						<span class="small"><i class="icon-calendar"></i> <?php echo date("Y-m-d", $product->publish_up); ?></span>
					</div>
				</div>
				<?php
				endforeach;
				?>
			</div>
		</div>
		
		<!-- TOP CUSTOMERS -->
		<div class="well well-small">
			<div class="module-title nav-header"><a href="index.php?option=com_digicom&controller=customers"><?php echo JText::_('DIGICOM_TOPCUSTOMERS'); ?></a></div>
			<div class="row-striped">
				<?php
				foreach($this->top_customers AS $customer) :
				?>
				<div class="row-fluid">
					<div class="span7">
						<strong class="row-title">
							<a href="index.php?option=com_digicom&controller=customers&task=edit&cid[]=<?php echo $customer->userid;?>">
								<?php echo $customer->firstname.' '.$customer->lastname;?></a>
						</strong>
					</div>
					<div class="span2">
						<span class="small pull-right"><?php echo $customer->amount_paid. ' '.$customer->currency; ?></span>
					</div>
					<div class="span3">
						<span class="small"><i class="icon-calendar"></i> <?php echo date("Y-m-d", $customer->order_date); ?></span>
					</div>
				</div>
				<?php
				endforeach;
				?>
			</div>
		</div>
		
	</div>
</form>

<div class="alert alert-info text-center">
	<?php echo JText::_('DIGICOM_CREDITS'); ?>
</div>