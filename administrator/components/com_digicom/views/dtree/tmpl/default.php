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

$task = JRequest::getVar("task", "");
if($task != "export" && $task != "vimeo"&& $task != "changeCategory"){
?>
	<div class="dtree">
	<div class="dTreeNode">
			<a class="node" href="javascript: d.openAll();"><?php  echo JText::_("DTREEOPEN_ALL");?></a> | <a class="node" href="javascript: d.closeAll();"><?php  echo JText::_("DTREECLOSE_ALL")?></a><br />
	</div>
	</div>
	<br />

	<span id="dtreespan">&nbsp;</span>
<?php
}
?>
