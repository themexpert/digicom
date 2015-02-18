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

 <form action="index.php" method="post" name="adminForm" id="adminForm">

		<fieldset class="adminform">
			<legend><?php echo JText::_('VIEWTAXRULE');?></legend>
				<table class="admintable">

				<tr>
					<td>
					<?php echo JText::_('VIEWTAXRULENAME');?>:
					</td>
					<td >
					<input class="text_area" type="text" name="name" value="<?php echo stripslashes( $this->rule->name ); ?>" size="50" maxlength="50" title="" />
					</td>
				</tr>
				<tr>
					<td>
					<?php echo JText::_('VIEWTAXRULECUSTOMERCLASSES');;?>:
					</td>
					<td>
					<?php echo $this->lists['customer_classes']; ?>
					</td>
				</tr>
				<tr>
					<td>
					<?php echo JText::_('VIEWTAXRULEPRODUCTTAXCLASSES');?>
					</td>
					<td>
					<?php echo $this->lists['product_tax_classes']; ?>
					</td>
				</tr>


				<tr>
					<td>
					<?php echo JText::_('VIEWTAXRULEPTAXRATES');?>
					</td>
					<td>
					<?php echo $this->lists['tax_rates']; ?>
					</td>
				</tr>

				<tr>
					<td>
					<?php echo JText::_('VIEWTAXRULEPRODUCTCLASSES');?>
					</td>
					<td>
					<?php echo $this->lists['product_classes']; ?>
					</td>
				</tr>


				</table>
		</fieldset>
			<input type="hidden" name="option" value="com_digicom" />
			<input type="hidden" name="id" value="<?php echo $this->rule->id; ?>" />
			<input type="hidden" name="task" value="" />
		<input type="hidden" name="controller" value="TaxRules" />
		<input type="hidden" name="images" id="images" value="" />
		</form>