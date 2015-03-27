<?php
/**
 * @package		DigiCom
 * @copyright	Copyright (c)2010-2015 ThemeXpert
 * @license 	GNU General Public License version 3, or later
 * @author 		ThemeXpert http://www.themexpert.com
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

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