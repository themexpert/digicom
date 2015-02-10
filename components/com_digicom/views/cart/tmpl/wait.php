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

?>
Wait...
<?php

$count_redirect = JRequest::getInt('count_redirect', 0);
$sid = JRequest::getInt('sid', 0);
$processor = JRequest::getVar('processor', '');
$wait_url = "index.php?option=com_digicom&controller=cart&task=payment&processor={$processor}&pay=wait&sid={$sid}&count_redirect={$count_redirect}";

?>
<form method="post" name="paymentFormWait" action="<?php echo $wait_url; ?>">
	<input type="hidden" name="count_redirect" value="<?php echo ($count_redirect + 1); ?>"/>
	<input type="hidden" name="sid" value="<?php echo $sid; ?>"/>
</form>

<?php echo DigiComHelper::powered_by(); ?>

<script type="text/javascript">

	setInterval(wait, 5000);

	function wait() {
		document.paymentFormWait.submit();
	}

</script>
