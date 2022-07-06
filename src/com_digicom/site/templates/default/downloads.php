<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;
JHtml::_('jquery.framework');
JPluginHelper::importPlugin('digicom');

$allitems = $this->items;
$firstkey = reset($allitems);
$active = 'cat-'.$firstkey['catid'];
?>
<div id="digicom" class="dc dc-downloads">

	<?php DigiComSiteHelperDigicom::loadModules('digicom_toolber'); ?>

	<h2 class="page-title"><?php echo JText::_("COM_DIGICOM_DOWNLOADS_PAGE_TITLE"); ?></h2>

	<?php echo JHtml::_('bootstrap.startTabSet', 'digicomTab', array('active' => $active)); ?>

	<?php
	$i = 0;
	foreach($this->items as $key=>$items):
	?>
		<?php echo JHtml::_('bootstrap.addTab', 'digicomTab', 'cat-'.$key, $items['title']); ?>
		
		    <h3><?php echo $items['title']; ?></h3>

		    <?php 
		    $configs = JComponentHelper::getComponent('com_digicom')->params;
			$pagination = $configs->get('download_page', 0);
			
			$this->itemList = $items;

		    if(!$pagination)
			{
				echo $this->loadTemplate('files');
			}
			else
			{
				echo $this->loadTemplate('links');
			}
			?>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

	<?php
	$i++;
	endforeach;
	?>

	<?php echo JHtml::_('bootstrap.endTabSet'); ?>

	<?php DigiComSiteHelperDigicom::loadModules('digicom_footer','xhtml'); ?>

	<?php echo DigiComSiteHelperDigiCom::powered_by(); ?>

</div>
