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

$document = JFactory::getDocument();
//$document->addStyleSheet("components/com_digicom/assets/css/digicom.css");

?>
<form id="adminForm" name="adminForm" action="index.php" method="post">
	<fieldset class="adminform">
		<?php
			$usertype = JRequest::getVar('usertype', 3);
			if($usertype == 3) {
		?>
		<legend><?php echo JText::_( 'Existing Customer' ); ?></legend>
		<?php } else { ?>
		<legend><?php echo JText::_( 'New Customer' ); ?></legend>
		<?php } ?>
		<label for="username">Username</label>
		<input id="username" type="text" name="username" value=""/>

		<input type="submit" value="Continue" class="btn btn-success" />
		<input type="hidden" name="option" value="com_digicom"/>
		<input type="hidden" name="controller" value="Orders"/>
		<input type="hidden" name="task" value="newCreateCustomer"/>
		<input type="hidden" name="usertype" value="<?php echo $usertype; ?>"/>
	</fieldset>
</form>