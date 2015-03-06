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

$mosConfig_absolute_path = JPATH_ROOT;

$document = JFactory::getDocument();

$app = JFactory::getApplication();
$input = $app->input;
$input->set('layout', 'dgform');
//$document->addStyleSheet("components/com_digicom/assets/css/digicom.css");
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

			<?php echo JHtml::_('bootstrap.addTab', 'digicomTab', 'about', JText::_('COM_DIGICOM_ABOUT_ABOUT', true)); ?>

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
				(JText::_("DSABOUTCOMPONENT")),
				(JText::_('DSABOUTMODULES')),
				(JText::_('DSABOUTPLUGIN'))
			);

			$total_file_titles = array (
				"component"	=>"DigiCom",
				"module1"	=>"DigiCom Manager",
				"module2"	=>"DigiCom Shopping Cart"
			// 	"module3"=>"DigiCom Categories",
			// 	"module4"=>"Featured Article",
			// 	"module5"=>"Current Articles",
			// 	"module6"=>"Author List",
			// 	"plugin"=>"Magazine"
			);

			$total_file_paths = array(
				"component"	=> "/administrator/components/com_digicom/digicom.xml",
				"module1"	=> "/modules/mod_digicom_manager/mod_digicom_manager.xml",
				"module2"	=> "/modules/mod_digicom_cart/mod_digicom_cart.xml"
			//	"module3"	=> "/modules/mod_digicom_categories.xml",
			//	"module4"	=> "/modules/mod_featured_article.xml",
			//	"module5"	=> "/modules/mod_current_articles.xml",
			//	"module6"	=> "/modules/mod_author_list.xml",
			//	"plugin"	=> "/mambots/content/mos_magazine.xml"
			);

			foreach ($total_file_paths as $var=>$val) :
				$counter += 1;
				if ($counter == 1) {
					echo "<td colspan=\"4\"><strong>".$titles[0]."</strong></td>";
				} else if ($counter == 2) {
					echo "<td colspan=\"4\"><strong>".$titles[1]."</strong></td>";
				} else if ($counter == 8 ) {
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
					echo "<tr><td width=\"20px\">&nbsp;</td><td width=\"90px\" align=\"left\"><font color=\"green\"><strong>".(JText::_('DSINSTALLED'))."</strong></font></td><td width=\"130px\" nowrap>+ ".$total_file_titles[$var]. "<td nowrap>version ";
					echo $version;
					echo "</td></tr>";
				} else {
					//echo "&nbsp;&nbsp;&nbsp;&nbsp;+ ".$total_file_titles[$var]. "&nbsp;&nbsp<font color=\"red\"><strong>Not Installed</strong></font><br />";
					echo "<tr><td width=\"20px\">&nbsp;</td><td  nowrap width=\"90px\" align=\"left\"><font color=\"red\"><strong><nowrap>".(JText::_('DSNOTINSTALLED'))."</nowrap></strong></font></td><td width=\"130px\" nowrap>+ ".$total_file_titles[$var]. "<td nowrap>&nbsp;</td></tr>";
					array_push($notinstalled_parts,$var);
				}
			endforeach;

			echo "
					</td>
					</tr>
				</table>
			";
			echo "<tr><td> ";
			echo JText::_("DSABOUTBODY");
			?>

			</td></tr>
		</table>

			<?php echo JHtml::_('bootstrap.endTab'); ?>

			<?php echo JHtml::_('bootstrap.addTab', 'digicomTab', 'system', JText::_('COM_DIGICOM_ABOUT_SYSTEM', true)); ?>

				<fieldset class="adminform">
					<legend>System Information</legend>
					<table class="table table-striped">
						<thead>
							<tr>
								<th width="25%">
									Setting				</th>
								<th>
									Value				</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<td colspan="2">&nbsp;</td>
							</tr>
						</tfoot>
						<tbody>
							<tr>
								<td>
									<strong>PHP Built On</strong>
								</td>
								<td>
									Windows NT EVAN-THE 6.2 build 9200 (Windows 8 Business Edition) i586				</td>
							</tr>
							<tr>
								<td>
									<strong>Database Version</strong>
								</td>
								<td>
									5.6.14				</td>
							</tr>
							<tr>
								<td>
									<strong>Database Collation</strong>
								</td>
								<td>
									utf8_general_ci				</td>
							</tr>
							<tr>
								<td>
									<strong>PHP Version</strong>
								</td>
								<td>
									5.5.6				</td>
							</tr>
							<tr>
								<td>
									<strong>Web Server</strong>
								</td>
								<td>
									Apache/2.4.7 (Win32) OpenSSL/1.0.1e PHP/5.5.6				</td>
							</tr>
							<tr>
								<td>
									<strong>WebServer to PHP Interface</strong>
								</td>
								<td>
									apache2handler				</td>
							</tr>
							<tr>
								<td>
									<strong>Joomla! Version</strong>
								</td>
								<td>
									Joomla! 3.4.0 Stable [ Ember ] 24-February-2015 23:00 GMT				</td>
							</tr>
							<tr>
								<td>
									<strong>Joomla! Platform Version</strong>
								</td>
								<td>
									Joomla Platform 13.1.0 Stable [ Curiosity ] 24-Apr-2013 00:00 GMT				</td>
							</tr>
							<tr>
								<td>
									<strong>User Agent</strong>
								</td>
								<td>
									Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.115 Safari/537.36				</td>
							</tr>
						</tbody>
					</table>
				</fieldset>
								

			<?php echo JHtml::_('bootstrap.endTab'); ?>

			<?php echo JHtml::_('bootstrap.addTab', 'digicomTab', 'support', JText::_('COM_DIGICOM_ABOUT_SUPPORT', true)); ?>


			<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JHtml::_('bootstrap.endTabSet'); ?>

		
	</div>
</form>
