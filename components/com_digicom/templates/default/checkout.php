<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;


JHTML::_('behavior.formvalidation');

$pg_plugin = $this->pg_plugin;
$configs = $this->configs;
$data = $this->data;
?>
<div id="digicom">

	<?php if($configs->get('show_steps',1) == 1){ ?>
	<div class="pagination pagination-centered">
		<ul>
			<li class="disabled"><span><?php echo JText::_("COM_DIGICOM_BUYING_PROCESS_STEP_ONE"); ?></span></li>
			<li class="disabled"><span><?php echo JText::_("COM_DIGICOM_BUYING_PROCESS_STEP_TWO"); ?></span></li>
			<li class="active"><span><?php echo JText::_("COM_DIGICOM_BUYING_PROCESS_STEP_THREE"); ?></span></li>
		</ul>
	</div>
	<?php } ?>

	<?php if ($pg_plugin == "paypal" ){ ?>
	<div class="container-fluid center">
		<h1 class="digi-page-title"><?php echo JText::sprintf("COM_DIGICOM_PAYMENT_WITH_PROGRESS_NOTICE",$pg_plugin); ?></h1>
		<div class="progress progress-striped active" style="width: 50%; margin: 20px auto 40px auto;">
			<div  id="progressBar" class="bar" style="border-radius: 3px; margin: 0; width: 100%;"></div>
		</div>

		<?php echo $data[0]; ?>

	</div>
<?php } else { ?>
	<h1 class="digi-page-title"><?php echo JText::sprintf("COM_DIGICOM_CHECKOUT_PAYMENT_DETAILS_PAGE_TITLE", $pg_plugin); ?></h1>
	<?php echo $data[0]; ?>
<?php } ?>

<?php
echo DigiComSiteHelperDigiCom::powered_by();
