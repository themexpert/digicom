<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 341 $
 * @lastmodified	$LastChangedDate: 2013-10-10 14:28:28 +0200 (Thu, 10 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

defined ('_JEXEC') or die ("Go away.");

JHtml::_('behavior.tooltip');

$document = JFactory::getDocument();
//$document->addStyleSheet("components/com_digicom/assets/css/digicom.css");

?>
<div class="digicom">
<fieldset>
	<legend><?php echo JText::_('Select product type'); ?></legend>
	
		<div class="media">
			<a class="pull-left" 
				href="#" 
				onclick="window.parent.location.href='<?php echo JURI::root().'administrator/index.php?option=com_digicom&controller=products&task=add&product_type=0'; ?>'"
				>
				<i class="digicom_icon_big icon-download"></i>
			</a>
			<div class="media-body">
				<h4 class="media-heading">
					<a 
						href="#" 
						onclick="window.parent.location.href='<?php echo JURI::root().'administrator/index.php?option=com_digicom&controller=products&task=add&product_type=0'; ?>'"
						>
						<?php echo JText::_("DIGI_DONWNLOADABLE"); ?>
					</a>
				</h4>
				<?php echo JText::_('DIGI_DONWNLOADABLE_TIP'); ?>
			</div>
		</div>
		
		<div class="media">
			<a class="pull-left" 
				href="#" 
				onclick="window.parent.location.href='<?php echo JURI::root().'administrator/index.php?option=com_digicom&controller=products&task=add&product_type=3'; ?>'"
				>
				<i class="digicom_icon_big icon-box-add"></i>
			</a>
			<div class="media-body">
				<h4 class="media-heading">
					<a 
						href="#" 
						onclick="window.parent.location.href='<?php echo JURI::root().'administrator/index.php?option=com_digicom&controller=products&task=add&product_type=3'; ?>'"
						>
						<?php echo JText::_("DIGI_PACKAGE_NO_UPLOAD"); ?>
					</a>
				</h4>
				<?php echo JText::_('COM_DIGICOM_PRODPACKAGE_TIP'); ?>
			</div>
		</div>
		
		<form action="index.php" method="post" name="adminForm" id="adminForm" >
			<input type="hidden" name="option" value="com_digicom"/>
			<input type="hidden" name="controller" value="Products" />
			<input type="hidden" name="task" value=""/>
		</form>
</fieldset>
</div>