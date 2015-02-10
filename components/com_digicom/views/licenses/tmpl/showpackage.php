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

global $Itemid;

$link = JRoute::_("index.php?option=com_digicom&controller=licenses&task=getProduct&licid=".$this->license->id."&no_html=1" );

define ("_CLICK", "click");
define ("_HERE", "here");
define ("_TO_DOWNLOAD", "to download your package");
define ("_PACKAGE_CONTENTS", "Contents of your current package:");
define ("_DOWNLOAD_NOW", "Download Now!");
?>


	<table width="100%">
	<tr><td width="100%"><h2><?php echo _DOWNLOAD_NOW;?></h2><br></td></tr></table>
	<h3><?php echo _CLICK;?> <a href="<?php echo $link;?>"><?php echo _HERE;?></a> <?php echo _TO_DOWNLOAD;?><br>
	</h3><br>

	<table width="100%">
	<tr><td width="100%"><?php echo _PACKAGE_CONTENTS;?> </td></tr>
	<?php
	foreach ($this->contents as $item)  {

		echo ('<tr><td width="100%" style="color:red">'.$item["filename"].'</td></tr>');
	}

		?>
	</table>

<?php echo DigiComHelper::powered_by(); ?>
