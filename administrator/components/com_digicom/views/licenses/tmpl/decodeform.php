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

$encoders = $this->encoders;
$input = $this->input;
$output = $this->output;
define ("_SELECT_ENCODER_TYPE", "Select encoder");
define("_ENTER_DECODED_LICENSE","Decoded License:");
define("_SUBMIT","Submit");
define("_SELECT_DECODER_TYPE", "Select a encoder type:");
define("_ENTER_THE_PATH_TO_LICENSE", "Enter URL to the LICENSE.TXT:");
define("_DECODE_LICENSE", "Decoded License:");
define ("_FILL_IN_PASSPHRASE_AND_LICENSE", "Fill in passphrase and License text fields.");

define ("_ENTER_PASSPHRASE_LICENSE", "Passprhase:");
define("_ENTER_URL_LICENSE","Enter license text:");
define("_ENTER_DECODED_LICENSE","Decoded License:");
?>

		<script language="javascript">
		function submitButton() {
			var form = document.decodeForm;
			//alert(form.password.value=="");
			if (!form.decode_method.checked) {
				alert ("<?php echo _SELECT_DECODER_TYPE; ?>");
			} else if (form.passphrase.value.length < 1 || form.decode_license.value.length < 1){
				alert ("<?php echo _FILL_IN_PASSPHRASE_AND_LICENSE; ?>" );
			} else {


				form.submit();
			}
		}
		</script>
		<form id="adminForm" name="decodeForm" method="post" action="index.php" enctype="multipart/form-data" >
			<?php echo _SELECT_ENCODER_TYPE?><br>
			<?php
			foreach ( $encoders as $encoder ) {
			?>
				<label>
					<input name="decode_method" type="radio" value="<?php echo $encoder->name; ?>" <?php if ($encoder->name == JRequest::getVar('decode_method', '', 'request')) echo 'checked';?>>
					<?php echo $encoder->name; ?>
				</label><br>
			<?php
			}
			?>
			<br>
			<?php echo _ENTER_PASSPHRASE_LICENSE?><br>
			<input type="text" name="passphrase" id="passphrase" value="<?php echo JRequest::getVar('passphrase', '', 'request');?>" />
			<br />
			<?php echo _ENTER_URL_LICENSE?><br>

			<TEXTAREA name="decode_license" id="decode_license" ROWS=5 COLS=50><?php echo ($input == "")?"":$input ?></TEXTAREA>
			<br>
			<?php echo _ENTER_DECODED_LICENSE?><br> 
			<TEXTAREA name="decoded_license" ROWS=10 COLS=80 style="color:black" readonly><?php echo ($output == "")?"":$output ?></TEXTAREA>
			<br/>
			<input type="hidden" name="option" value="com_digicom" />
			<input type="hidden" name="task" value="decode_license" />
			<input type="hidden" name="controller" value="Licenses" />
			<input type="button" name="Submit" value="<?php echo _SUBMIT; ?>" class="button" onclick="submitButton();">
		</form>