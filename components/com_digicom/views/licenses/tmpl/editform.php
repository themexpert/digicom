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

$license = $this->license;
$configs = $this->configs;
//$plugin_handler = $this->plugin_handler;
$currency_options = $this->currency_options;
$lists = $this->lists;
$nullDate = 0;
?>

<script language="javascript" type="text/javascript">
		<!--

		function submitbutton(pressbutton) {
			submitform( pressbutton );
		}

		-->
</script>

 <form action="index.php" method="post" name="adminForm" id="adminForm">
	<fieldset class="adminform">
	<legend><?php echo JText::_('DSLICDET');?></legend>
				<table class="admintable">

  <tr>
						<?php  if ( isset( $license->licenseid ) ) :?>
						<tr>
							<td>
							<?php  echo JText::_("DSLICID");?>:
							</td>
							<td>
							<?php  echo  ( $license->licenseid > 0) ? $license->licenseid : ""; ?>
							</td>
						</tr>
						<?php endif;?>
						<tr>
						<td>
						<?php echo JText::_("DSPUBLISHING");?>
						</td>
						<td nowrap="nowrap">
						<?php echo JText::_("DSYES"); ?> <input type="radio" name="publishing" value="1" <?php echo (( $license->published != 0)?'checked':''); ?> />
						<?php echo JText::_("DSNO"); ?> <input type="radio" name="publishing" value="0" <?php echo (($license->published == 0 )?'checked':''); ?> />
						</td>

						</tr>
						<tr>
							<td>
							<?php  echo JText::_("DSPROD")?>:
							</td>
							<td>
							<?php  echo $lists['productid']; ?>
							</td>
						</tr>
						<tr>
							<td>
							<?php  echo JText::_("DSCUSTOMER");?>:
							</td>
							<td>
							<input id="username" class="text_area" type="text" name="username" size="30" class="digi_textbox" maxlength="100" value="<?php  echo $license->username ;?>" />
							</td>
						</tr>
						<tr><td><b><?php echo JText::_("DSORIGINALATTR");?></b></td><td></td></tr>
				   <?php if ($license->id && $license->fields):?>
						<tr>
							<td>
							<?php  foreach ($license->fields as $field) {
								?>
								<?php  echo $field->fieldname; ?>:<br />
								<input readonly type="hidden" name="fields[]" value="<?php  echo $field->fieldname; ?>" /><br />
								  <?php }?>
							</td>
							<td>
							<?php  foreach ($row->fields as $field) {
								?>
								<input readonly type="hidden" name="options[]" value="<?php  echo $field->optioname; ?>" /> <br />
								<?php  echo $field->optioname; ?> <br />
								  <?php }?>

							</td>
						</tr>
				<?php endif; ?>
						<tr><td><b><?php echo JText::_("DSSETATTR");?></b></td><td></td></tr>
				   <?php if (( $license->id) && $license->productfields):?>
					<?php if ($totalfields > 0 ){ ?>

			  <?php
			  foreach ($license->productfields as $j => $v) { ?>
			  	<tr>
					<?php
						echo "<td>".$v->name.":</td>";
						//echo ($v->mandatory == 1)?"<span class='error' style='color:red;'>*</span>":"";
							?>
							<td>
							<select style="width:<?php echo $optlen*15;?>px" name="field[<?php echo $v->id;?>]" id="attributes[<?php echo $v->id;?>]">
							<option value="-1" <?php echo ($v->optionid < 0|| "z".$v->optionid === "z")?"selected":""; ?>>Select <?php echo $v->name;?></option>
					<?php
						$options = explode ("\n", $v->options);
						foreach ($options as $i1 => $v1) {
							echo ("<option value='".$i1."'");
							echo ($i1==$v->optionid&&"z".$v->optionid!=="z")?"selected":"";
							echo (">".$v1."</option>");
					}

					?></select>
					</td>
					</tr>
			<?php } ?>

			  <?php } ?>

					<?php endif; ?>
					<?php  /* if (count($plugin_handler->encoding_plugins) > 0):?>
						<tr>
							<td>
							<?php  echo JText::_("DSDOMAIN");?>:
							</td>
							<td>
							<input class="text_area" type="text" name="domain" size="30" class="digi_textbox" maxlength="100" value="<?php  echo ( $license->domain ) ? $license->domain : ""; ?>" />
							</td>
						</tr>
						<tr>
							<td>
							<?php  echo JText::_("DSDEVDOMAIN");?>:
							</td>
							<td>
							<input class="text_area" type="text" name="dev_domain" size="30" class="digi_textbox" maxlength="100" value="<?php  echo ( $license->dev_domain ) ? $license->dev_domain: ""; ?>" />
							</td>
						</tr>

						<tr>
							<td>
							<?php  echo JText::_("DSHOSTINGSERV");?>:
							</td>
							<td>
							<input class="text_area" type="text" name="hoster" size="30" class="digi_textbox" maxlength="100" value="<?php  echo ( $license->hosting_service ) ? $license->hosting_service: ""; ?>" />
							</td>
						</tr>
						<?php endif; */ ?>
						<tr>
							<td>
							<?php  echo JText::_("DSAMOUNTPAID");?>:
							</td>
							<td>
							<input class="text_area" type="text" name="amount_paid" size="30" class="digi_textbox" maxlength="100" value="<?php
 //		   global $configs;
			$price_format = '%'.$configs->get('totaldigits','').'.'.$configs->get('decimaldigits','2').'f';
						printf($price_format, (($license->amount_paid) ? $license->amount_paid : "" ));
			?>" />
							<select name="currency">
							<?php  for ( $i = 0;$i < count( $currency_options); $i++){?>
								 <option value="<?php  echo isset ( $currency_options[$i] ) ? $currency_options[$i]->currency_name : "";?>" <?php  if ( isset( $currency_options[$i] ) && isset( $license->currency ) && $currency_options[$i]->currency_name==$license->currency) echo "selected";?>><?php  echo isset ( $currency_options[$i] ) ? $currency_options[$i]->currency_name : "";?></option>
							<?php }?>
							</select>
							</td>
						</tr>
 <tr>
	</table>

	</fieldset>
		<input type="hidden" name="Itemid" value="<?php global $Itemid; echo $Itemid;?>" />
		<input type="hidden" name="images" value="" />
			<input type="hidden" name="option" value="com_digicom" />
			<input type="hidden" name="id" value="<?php echo $license->id; ?>" />
			<input type="hidden" name="task" value="" />
		<input type="hidden" name="controller" value="Licenses" />
		</form>

<?php echo DigiComHelper::powered_by(); ?>
