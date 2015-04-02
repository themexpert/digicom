<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

$mosConfig_absolute_path = JPATH_ROOT;

$document = JFactory::getDocument();

$app = JFactory::getApplication();
$input = $app->input;
$input->set('layout', 'dgform');
?>
<form action="<?php echo JRoute::_('index.php?option=com_digicom&controller=about'); ?>" method="post" name="adminForm" autocomplete="off" class="form-validate form-horizontal">
	<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="">
<?php else : ?>
	<div id="j-main-container" class="">
<?php endif;?>
		
		<?php echo JHtml::_('bootstrap.startTabSet', 'digicomTab', array('active' => 'about')); ?>

			<?php echo JHtml::_('bootstrap.addTab', 'digicomTab', 'about', JText::_('COM_DIGICOM_ABOUT_TAB_TITLE_ABOUT', true)); ?>

			<div class="about-dglogo">
				<a href="#">Digicom Logo</a>
			</div>

		<table class="adminform table">
			<tr>
				<td>
					<table class="table table-striped">
						
			<?php

			//angek: magazine-66 : start: lets do this dynamically
			$installed_parts	= array();
			$notinstalled_parts	= array();
			global $counter;

			$titles = array(
				(JText::_("COM_DIGICOM_ABOUT_COMPONENT")),
				(JText::_('COM_DIGICOM_ABOUT_MODULES')),
				(JText::_('COM_DIGICOM_ABOUT_PLUGIN'))
			);

			$total_file_titles = array (
				"component"	=> "DigiCom",
				"module1"	=> "DigiCom Categories",
				"module2"	=> "DigiCom Shopping Cart",
			 	"plugin1"	=> "Add to Cart System Plugin",
			 	"plugin2"	=> "Offline Payment Plugin",
			 	"plugin3"	=> "Paypal Payment Plugin"
			);

			$total_file_paths = array(
				"component"	=> "/administrator/components/com_digicom/digicom.xml",
				"module1"	=> "/modules/mod_digicom_categories/mod_digicom_categories.xml",
				"module2"	=> "/modules/mod_digicom_cart/mod_digicom_cart.xml",
				"plugin1"	=> "/plugins/system/digicom_addtocart/digicom_addtocart.xml",
				"plugin2"	=> "/plugins/digicom_pay/offline/offline.xml",
				"plugin3"	=> "/plugins/digicom_pay/paypal/paypal.xml"
			);

			foreach ($total_file_paths as $var=>$val) :
				$counter += 1;
				if ($counter == 1) {
					echo "<td colspan=\"4\"><strong>".$titles[0]."</strong></td>";
				} else if ($counter == 2) {
					echo "<td colspan=\"4\"><strong>".$titles[1]."</strong></td>";
				} else if ($counter == 4 ) {
					echo "<td colspan=\"4\"><strong>".$titles[2]."</strong></td>";
				}
				echo "</tr>";
				$f_data = $mosConfig_absolute_path.$val;

				if (is_file($f_data)) {
					$data = implode ("", file ( $f_data ) );
					$pos1 = strpos ($data,"<version>");
					$pos2 = strpos ($data,"</version>");
					$version = substr ($data, $pos1+strlen("<version>"), $pos2-$pos1-strlen("<version>"));

				//  fclose( file ($f_data) );
					echo "<tr><td width=\"20px\">&nbsp;</td><td width=\"90px\" align=\"left\"><font color=\"green\"><strong>".(JText::_('COM_DIGICOM_INSTALLED'))."</strong></font></td><td width=\"130px\" nowrap>+ ".$total_file_titles[$var]. "<td nowrap>version ";
					echo $version;
					echo "</td></tr>";
				} else {
					echo "<tr><td width=\"20px\">&nbsp;</td><td  nowrap width=\"90px\" align=\"left\"><font color=\"red\"><strong><nowrap>".(JText::_('COM_DIGICOM_NOT_INSTALLED'))."</nowrap></strong></font></td><td width=\"130px\" nowrap>+ ".$total_file_titles[$var]. "<td nowrap>&nbsp;</td></tr>";
					array_push($notinstalled_parts,$var);
				}
			endforeach;

			echo "
					</td>
					</tr>
				</table>
			";
			echo "<tr><td> ";
			echo JText::_("COM_DIGICOM_ABOUT_DIGICOM_DETAILS");
			?>

			</td></tr>
		</table>

			<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JHtml::_('bootstrap.endTabSet'); ?>

		
	</div>
</form>
