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

jimport('joomla.html.pane');

$document = JFactory::getDocument();
//$document->addStyleSheet("components/com_digicom/assets/css/digicom.css");

?>

		<script language="javascript" type="text/javascript">
		<!--
		function submitbutton(pressbutton) {

			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform(pressbutton);
				return;
			}
			submitform( pressbutton );
		}
		-->
		</script>
							<script>
							<?php
							sajax_show_javascript();
							?>

							function changeProvince_cb(province_option) {
								//alert(province_option+'MYYYYYYYYYYYY');

								document.getElementById("province").innerHTML = province_option;
							}

							function changeProvince() {
								 // get the folder name
								var country;
								country = document.getElementById('country').value;
								//alert(country);
								x_phpchangetaxProvince(country, 'main', changeProvince_cb);
							}
							</script>

 <form action="index.php" method="post" name="adminForm" id="adminForm">

		<fieldset class="adminform">
			<legend><?php echo JText::_('VIEWTAXRATE');?></legend>
				<table class="admintable">

				<tr>
					<td>
					<?php echo JText::_('VIEWTAXRATENAME');?>:
					</td>
					<td >
					<input class="text_area" type="text" name="name" value="<?php echo stripslashes( $this->rate->name ); ?>" size="50" maxlength="50" title="" />
					</td>
				</tr>
				<tr>
					<td>
					<?php echo JText::_('VIEWTAXRATECOUNTRY');;?>:
					</td>
					<td>
					<?php echo $this->lists['country_option']; ?>
					</td>
				</tr>
				<tr>
					<td>
					<?php echo JText::_('VIEWTAXRATESTATE');?>
					</td>
					<td>
					<?php echo $this->lists['location_option']; ?>
					</td>
				</tr>

				<tr>
					<td>
					<?php echo JText::_('VIEWTAXRATEZIP');?>:
					</td>
					<td >
					<input class="text_area" type="text" name="zip" value="<?php echo stripslashes( $this->rate->zip ); ?>" size="50" maxlength="50" title="" /> <?php echo JText::_('DSSTARFORALL');?>
					</td>
				</tr>

				<tr>
					<td>
					<?php echo JText::_('VIEWTAXRATERATE');?>:
					</td>
					<td >
					<input class="text_area" type="text" name="rate" value="<?php echo stripslashes( $this->rate->rate ); ?>" size="50" maxlength="50" title="" /> %
					</td>
				</tr>

				</table>
		</fieldset>
			<input type="hidden" name="option" value="com_digicom" />
			<input type="hidden" name="id" value="<?php echo $this->rate->id; ?>" />
			<input type="hidden" name="task" value="" />
		<input type="hidden" name="controller" value="TaxRates" />
		<input type="hidden" name="images" id="images" value="" />
		</form>