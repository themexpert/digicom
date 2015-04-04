<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

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
?>
<div id="digicom">
	 
	<?php DigiComSiteHelperDigicom::loadModules('digicom_toolber'); ?>
	
	<h2 class="digi-page-title"><?php echo JText::_("COM_DIGICOM_DOWNLOADS_PAGE_TITLE"); ?></h2>
	<div class="accordion" id="">
		<?php foreach($this->products as $key=>$item): ?>
		<div class="accordion-group">
			<div class="accordion-heading">
				<a class="accordion-toggle" data-toggle="collapse" data-parent="" href="#product<?php echo $item->productid; ?>">
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
								<th><?php echo JText::_('COM_DIGICOM_DOWNLOADS_FILE_NAME'); ?></th>
								<th><?php echo JText::_('COM_DIGICOM_SIZE'); ?></th>
								<th><?php echo JText::_('COM_DIGICOM_DOWNLOADS_FILE_UPDATED'); ?></th>
								<th><?php echo JText::_('JGLOBAL_HITS'); ?></th>
								<th><?php echo JText::_('COM_DIGICOM_ACTION'); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($item->files as $key2=>$file):?>
							<tr>
								<td><?php echo $file->name; ?></td>
								<td><?php echo $file->filesize; ?></td>
								<td><?php echo $file->filemtime; ?></td>
								<td><?php echo $file->hits; ?></td>
								<td>
									<a href="<?php echo JRoute::_('index.php?option=com_digicom&view=downloads&task=downloads.makeDownload&downloadid='.$file->downloadid.'&Itemid='.$Itemid);?>" class="btn btn-warning btn-mini"><?php echo JText::_('COM_DIGICOM_DOWNLOAD'); ?></a>
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

	<?php DigiComSiteHelperDigicom::loadModules('digicom_footer','xhtml'); ?>
	
</div>
	<?php echo DigiComSiteHelperDigiCom::powered_by(); ?>
