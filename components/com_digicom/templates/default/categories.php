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

echo '<div class="digicom">';
if(isset($this->category_name)){
	echo '<h4>'.$this->category_name.'</h4>';
}else{
	echo '<h4>'.JText::_("CATEGORIES").'</h4>';
}


echo DigiComHelper::powered_by();
