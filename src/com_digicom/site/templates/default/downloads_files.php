<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;
$dispatcher	= JEventDispatcher::getInstance();
?>
<?php foreach($this->itemList['items'] as $pkey=>$item): ?>

<div class="panel-group" id="accordion<?php echo $this->itemList['catid'];?>" role="tablist" aria-multiselectable="true">
	<div class="panel panel-default">
		<div class="panel-heading" role="tab" id="#productheading<?php echo $item->productid; ?>">
			<h4 class="panel-title">

				<?php echo $item->name; ?>

				<?php $dispatcher->trigger('onDigicomProductDownloadAction', array ('com_digicom.downloads', &$item)); ?>

				<?php $dispatcher->trigger('onDigicomProductDownloadInfo', array ('com_digicom.downloads', &$item)); ?>

				<a 
					role="button" 
					data-toggle="collapse" data-parent="#accordion<?php echo $this->itemList['catid'];?>" 
					href="#product<?php echo $item->productid; ?>" aria-expanded="true" aria-controls="product<?php echo $item->productid; ?>">
						<span class="pull-right"><i class="glyphicon glyphicon-download icon-download"></i></span>
				</a>

			</h4>
			</div>
		<div id="product<?php echo $item->productid; ?>" 
			class="panel-collapse collapse<?php echo ($pkey==0 ? ' in' : ''); ?>" role="tabpanel" 
			aria-labelledby="#productheading<?php echo $item->productid; ?>">
	  		
	  		<div class="panel-body">
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
								<a href="<?php echo JRoute::_('index.php?option=com_digicom&view=downloads&task=downloads.go&token='.$file->downloadid);?>" class="btn btn-warning btn-mini"><?php echo JText::_('COM_DIGICOM_DOWNLOAD'); ?></a>
							</td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
	  		</div>
		</div>
	</div>
</div>

<?php endforeach; ?>