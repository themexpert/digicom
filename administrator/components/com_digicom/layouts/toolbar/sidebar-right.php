<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 455 $
 * @lastmodified	$LastChangedDate: 2014-01-06 05:30:05 +0100 (Mon, 06 Jan 2014) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

defined ('_JEXEC') or die ("Go away.");
$uri = JFactory::getURI(); 
$pageURL = $uri->toString(); 
?>

<div id="toggle_settings_wrap" class="">
	<h3>Global Settings</h3>
	<ul>
		<?php if (JFactory::getUser()->authorise('core.admin', 'com_digicom')) { ?>
			<li><a href="index.php?option=com_config&view=component&component=com_digicom&return=<?php echo base64_encode($pageURL);?>">ACL Settings</a></li>
			<li><a href="index.php?option=com_digicom&view=configs">Settings</a></li>
		<?php } ?>		
	</ul>

	<h3>Add-ons Settings</h3>
	<ul>
		<li><a href="#">Mailchimp</a></li>
		<li><a href="#">2CO Payment</a></li>
		<li><a href="#">Tax</a></li>
	</ul>

</div>