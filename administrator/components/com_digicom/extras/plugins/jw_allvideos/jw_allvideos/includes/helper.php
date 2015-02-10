<?php
/*
// JoomlaWorks "AllVideos" Plugin for Joomla! 1.5.x - Version 3.3
// Copyright (c) 2006 - 2010 JoomlaWorks Ltd. All rights reserved.
// Released under the GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
// More info at http://www.joomlaworks.gr
// Designed and developed by the JoomlaWorks team
// *** Last update: February 18th, 2010 ***
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class AllVideosHelper {

	// Load Includes
	function loadHeadIncludes($headIncludes){
		global $loadAllVideosPluginIncludes;
		$document = JFactory::getDocument();
		if(!$loadAllVideosPluginIncludes){
			$loadAllVideosPluginIncludes=1;
			$document->addCustomTag($headIncludes);
		}
	}

	// Load Module Position
	function loadModulePosition( $position, $style='' ){
		$document	= JFactory::getDocument();
		$renderer	= $document->loadRenderer('module');
		$params		= array('style'=>$style);

		$contents = '';
		foreach (JModuleHelper::getModules($position) as $mod){
			$contents .= $renderer->render($mod, $params);
		}
		return $contents;
	}

	// Path overrides
	function getTemplatePath($pluginName,$file){
		$mainframe= JFactory::getApplication();
		$p = new JObject;
		if(file_exists(JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.$pluginName.DS.str_replace('/',DS,$file))){
			$p->file = JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.$pluginName.DS.$file;
			$p->http = JURI::root()."templates/".$mainframe->getTemplate()."/html/{$pluginName}/{$file}";
		} else {
			$p->file = JPATH_SITE.DS.'plugins'.DS.'content'.DS.$pluginName.DS.'tmpl'.DS.$file;
			$p->http = JURI::root()."plugins/content/{$pluginName}/tmpl/{$file}";
		}
		return $p;
	}

} // end class
