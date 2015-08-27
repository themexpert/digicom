<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;
JHtml::_('behavior.framework');
JHtml::_('behavior.formvalidation');
?>

<div id="digicom">

	<?php if($this->configs->get('show_steps',0)){ ?>
		<div class="pagination pagination-centered">
			<ul>
				<li><span><?php echo JText::_("COM_DIGICOM_BUYING_PROCESS_STEP_ONE"); ?></span></li>
				<li class="active"><span><?php echo JText::_("COM_DIGICOM_BUYING_PROCESS_STEP_TWO"); ?></span></li>
				<li><span><?php echo JText::_("COM_DIGICOM_BUYING_PROCESS_STEP_THREE"); ?></span></li>
			</ul>
		</div>
	<?php } ?>

	<h1 class="digi-page-title"><?php echo JText::_("COM_DIGICOM_LOGIN_REGISTER");?></h1>

	<div class="accordion" id="accordion2">
		<div class="accordion-group">
			<div class="accordion-heading">
				<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne">
					<?php echo JText::_("COM_DIGICOM_REGISTER_LOGIN_BELOW"); ?>
				</a>
			</div>
			<div id="collapseOne" class="accordion-body collapse in">
				<div class="accordion-inner">
					<div id="log_form">
						<?php echo $this->loadTemplate('login');	?>
					</div>
				</div>
			</div>
		</div>

		<div class="accordion-group">
			<div class="accordion-heading">
				<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseTwo">
					<?php echo JText::_("COM_DIGICOM_REGISTER_REGISTER_BELOW"); ?>
				</a>
			</div>

			<div id="collapseTwo" class="accordion-body collapse">
				<div class="accordion-inner">
					<div id="reg_form">
						<?php echo $this->loadTemplate('form');	?>
					</div>
				</div>
			</div>
		</div>
	</div>

</div>
