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
?>
<div class="digicom">
	<div class="navbar">
		<div class="navbar-inner hidden-phone">
			<ul class="nav hidden-phone">
				<li class="active">
					<a href="<?php echo JRoute::_("index.php?option=com_digicom&view=downloads&Itemid=".$Itemid); ?>"><i class="ico-download"></i> <?php echo JText::_("DIGI_MY_DOWNLOADS"); ?></a>
				</li>
				<li class="divider-vertical"></li>
				<li>
					<a href="<?php echo JRoute::_("index.php?option=com_digicom&view=orders&Itemid=".$Itemid); ?>"><i class="ico-list-alt"></i> <?php echo JText::_("DIGI_MY_ORDERS"); ?></a>
				</li>
				<li class="divider-vertical"></li>
				<li>
					<a href="<?php echo JRoute::_("index.php?option=com_digicom&view=cart&task=showCart".$and_itemid); ?>"><i class="ico-shopping-cart"></i> <?php echo JText::_("DIGI_MY_CART"); ?></a>
				</li>
			</ul>
		</div>
		<ul class="nav nav-pills hidden-desktop">
			<li class="active">
				<a href="<?php echo JRoute::_("index.php?option=com_digicom&view=downloads&Itemid=".$Itemid); ?>"><i class="ico-download hidden-phone"></i> <?php echo JText::_("DIGI_MY_DOWNLOADS"); ?></a>
			</li>
			<li class="divider-vertical"></li>
			<li>
				<a href="<?php echo JRoute::_("index.php?option=com_digicom&view=orders&Itemid=".$Itemid); ?>"><i class="ico-list-alt hidden-phone"></i> <?php echo JText::_("DIGI_MY_ORDERS"); ?></a>
			</li>
			<li class="divider-vertical"></li>
			<li>
				<a href="<?php echo JRoute::_("index.php?option=com_digicom&view=cart&task=showCart".$and_itemid); ?>"><i class="ico-shopping-cart hidden-phone"></i> <?php echo JText::_("DIGI_MY_CART"); ?></a>
			</li>
		</ul>
	</div>
	
</div>
<div class="accordion" id="digicom_products_download">
	<?php foreach($this->products as $key=>$item): ?>
	<div class="accordion-group">
		<div class="accordion-heading">
			<a class="accordion-toggle" data-toggle="collapse" data-parent="#digicom_products_download" href="#product<?php echo $item->productid; ?>">
				<?php echo $item->name; ?>
				<span class="pull-right"><i class="icon-download"></i></span>
			</a>
		</div>
		<div id="product<?php echo $item->productid; ?>" class="accordion-body<?php echo ($key==0 ? ' in' : ''); ?> collapse">
			<div class="accordion-inner">
				<?php //dsdebug($item->files); ?>
				<table class="table table-bordered">
					<thead>
						<tr>
							<th>#</th>
							<th><?php echo JText::_('COM_DIGICOM_FILE_NAME'); ?></th>
							<th><?php echo JText::_('COM_DIGICOM_FILE_SIZE'); ?></th>
							<th><?php echo JText::_('COM_DIGICOM_FILE_UPDATES'); ?></th>
							<th><?php echo JText::_('COM_DIGICOM_FILE_HITS'); ?></th>
							<th><?php echo JText::_('COM_DIGICOM_FILE_DOWNLOAD_ACTION'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($item->files as $key2=>$file):?>
							<tr>
								<td><?php echo $key2; ?></td>
								<td><?php echo $file->name; ?></td>
								<td><?php echo $file->filesize; ?></td>
								<td><?php echo $file->filemtime; ?></td>
								<td><?php echo $file->hits; ?></td>
								<td>
									<a href="<?php echo JRoute::_('index.php?option=com_digicom&view=downloads&task=makeDownload&downloadid='.$file->downloadid.'&Itemid='.$Itemid);?>" class="btn btn-download btn-mini"><?php echo JText::_('COM_DIGICOM_FILE_DOWNLOAD'); ?></a>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<?php endforeach; ?>
</div>
<?php echo DigiComHelper::powered_by(); ?>
