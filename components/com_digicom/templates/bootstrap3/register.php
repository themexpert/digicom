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
$return = JFactory::getApplication()->input->get('return','');
?>



<div id="digicom">

	<?php if(!empty($return)){ ?>
		<?php
		$this->setLayout('cart');
		echo $this->loadTemplate('steps');
		$this->setLayout('register');
		?>
	<?php } ?>

	<h1 class="digi-page-title"><?php echo JText::_("COM_DIGICOM_LOGIN_REGISTER");?></h1>

	<div id="login-register-wrapper">
    <ul id="login-registerTab" class="nav nav-tabs" role="tablist">
      <li role="presentation" class="active">
				<a href="#digicom-login" id="digicom-login-tab" role="tab" data-toggle="tab" aria-controls="digicom-login" aria-expanded="true">
					<?php echo JText::_("COM_DIGICOM_REGISTER_LOGIN_BELOW"); ?>
				</a>
			</li>

      <li role="presentation">
				<a href="#digicom-register" id="digicom-register-tab" role="tab" data-toggle="tab" aria-controls="digicom-register" aria-expanded="true">
					<?php echo JText::_("COM_DIGICOM_REGISTER_REGISTER_BELOW"); ?>
				</a>
			</li>
    </ul>

    <div id="login-registerContent" class="tab-content">
      <div role="tabpanel" class="tab-pane fade active in" id="digicom-login" aria-labelledby="digicom-login-tab">
				<h3><?php echo JText::_("COM_DIGICOM_LOGIN"); ?></h3>
        <?php echo $this->loadTemplate('login');	?>
      </div>
      <div role="tabpanel" class="tab-pane fade" id="digicom-register" aria-labelledby="digicom-register-tab">
				<h3><?php echo JText::_("COM_DIGICOM_REGISTER"); ?></h3>
				<?php echo $this->loadTemplate('form');	?>
      </div>
    </div>
  </div>


</div>
<?php echo DigiComSiteHelperDigiCom::powered_by(); ?>
