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

jimport( 'joomla.plugin.plugin' );

class plgContentJw_allvideos extends JPlugin {

	function plgContentJw_allvideos( &$subject, $params ){
		parent::__construct( $subject, $params );
	}

	function onPrepareContent( &$article, &$params ){

	// JoomlaWorks reference parameters
		$plg_name								= "jw_allvideos";
		$plg_copyrights_start		= "\n\n<!-- JoomlaWorks \"AllVideos\" Plugin (v3.3) starts here -->\n";
		$plg_copyrights_end			= "\n<!-- JoomlaWorks \"AllVideos\" Plugin (v3.3) ends here -->\n\n";
			
		// API
	$mainframe= JFactory::getApplication();
		$document = JFactory::getDocument();

		// Assign paths
	$sitePath = JPATH_SITE;
	$siteUrl  = substr(JURI::root(), 0, -1);
	
	// Check if plugin is enabled
	if(JPluginHelper::isEnabled('content',$plg_name)==false) return;
	
		// Load the plugin language file the proper way
		if($mainframe->isAdmin()){
			JPlugin::loadLanguage( 'plg_content_'.$plg_name );
		} else {
			JPlugin::loadLanguage( 'plg_content_'.$plg_name, 'administrator' );
		}



		// ----------------------------------- Get plugin parameters -----------------------------------

		// Outside Parameters
		if(!$params) $params = new JParameter(null);

		$plugin =& JPluginHelper::getPlugin('content',$plg_name);
		$pluginParams = new JRegistry( $plugin->params );

		$gzipScripts				= $pluginParams->get('gzipScripts',0);
		/* Video */
		$vfolder 						= ($params->get('vfolder')) ? $params->get('vfolder') : $pluginParams->get('vfolder','images/stories/videos');
		$vwidth 						= ($params->get('vwidth')) ? $params->get('vwidth') : $pluginParams->get('vwidth',400);
		$vheight 						= ($params->get('vheight')) ? $params->get('vheight') : $pluginParams->get('vheight',300);
		$transparency 			= $pluginParams->get('transparency','transparent');
		$background 				= $pluginParams->get('background','#010101');
		$backgroundQT				= $pluginParams->get('backgroundQT','black');
		$controlBarLocation = $pluginParams->get('controlBarLocation','bottom');
		$controlBarLocation = ($controlBarLocation=="over") ? '&controlbar=over' : '';
		/* Audio */
		$afolder 						= $pluginParams->get('afolder','images/stories/audio');
		$awidth 						= $pluginParams->get('awidth',300);
		$aheight 						= $pluginParams->get('aheight',20);
		/* General */
		$autoplay 					= ($params->get('autoplay')) ? $params->get('autoplay') : $pluginParams->get('autoplay',0);
		$autoplay						= ($autoplay) ? 'true' : 'false';
		$lightboxLink 			= ($params->get('lightboxLink')) ? $params->get('lightboxLink') : $pluginParams->get('lightboxLink',1);
		$lightboxWidth			= $pluginParams->get('lightboxWidth',800);
		$lightboxHeight			= $pluginParams->get('lightboxHeight',(800*3/4));
		$downloadLink 			= ($params->get('downloadLink')) ? $params->get('downloadLink') : $pluginParams->get('downloadLink',1);
		$embedForm		 			= ($params->get('embedForm')) ? $params->get('embedForm') : $pluginParams->get('embedForm',1);
		/* Advanced */
		$debugMode					= $pluginParams->get('debugMode',0);
		if($debugMode==0) error_reporting(0); // Turn off all error reporting

		// Variable cleanups for K2
		if(JRequest::getCmd('format')=='raw'){
			$plg_copyrights_start = '';
			$plg_copyrights_end = '';
		}



		// ----------------------------------- Prepare elements -----------------------------------

		// Includes
		require_once(dirname(__FILE__).DS.$plg_name.DS.'includes'.DS.'helper.php');
		require(dirname(__FILE__).DS.$plg_name.DS.'includes'.DS.'sources.php');

		// Simple performance check to determine whether plugin should process further
		$grabTags = str_replace("(","",str_replace(")","",implode(array_keys($tagReplace),"|")));
		if (preg_match("#{(".$grabTags.")}#s",$article->text)==false) return;



		// ----------------------------------- Head tag includes -----------------------------------
		$avCSS 		= AllVideosHelper::getTemplatePath($plg_name,'css/template.css');
		$avCSS 		= $avCSS->http;

		$jwavhead = '

		'.JHTML::_('behavior.mootools').'

<style type="text/css" media="all">
	@import "'.$avCSS.'";
</style>
		';
		if($gzipScripts){
			$jwavhead .= '
<script type="text/javascript" src="'.$siteUrl.'/plugins/content/'.$plg_name.'/includes/jw_allvideos_scripts.php"></script>
			';
		} else {
			$jwavhead .= '
<script type="text/javascript" src="'.$siteUrl.'/plugins/content/'.$plg_name.'/includes/players/wmvplayer/silverlight.js"></script>
<script type="text/javascript" src="'.$siteUrl.'/plugins/content/'.$plg_name.'/includes/players/wmvplayer/wmvplayer.js"></script>
<script type="text/javascript" src="'.$siteUrl.'/plugins/content/'.$plg_name.'/includes/players/quicktimeplayer/AC_QuickTime.js"></script>
<script type="text/javascript" src="'.$siteUrl.'/plugins/content/'.$plg_name.'/includes/jw_allvideos.js"></script>
			';
		}

		if($lightboxLink || $embedForm) {
			$jwavhead .= '
<script type="text/javascript">
	//<![CDATA[
	window.addEvent(\'domready\', function() {
		AllVideosLightBox.Init({
			AVLBWidth:'.$lightboxWidth.',
			AVLBHeight:'.$lightboxHeight.'
		});
		AllVideosEmbed.Init();
	});
	//]]>
</script>
			';
		}

		// Append head includes, but not when we're outputing raw content in K2
		if(JRequest::getCmd('format')!='raw'){
			AllVideosHelper::loadHeadIncludes($plg_copyrights_start.$jwavhead.$plg_copyrights_end);
		}



		// ----------------------------------- Render the output -----------------------------------
		// START ALLVIDEOS LOOP
		foreach ($tagReplace as $plg_tag => $value) {
			// expression to search for
			$regex = "#{".$plg_tag."}(.*?){/".$plg_tag."}#s";
			// process tags
			if (preg_match_all($regex, $article->text, $matches, PREG_PATTERN_ORDER) > 0) {
				// start the replace loop
				foreach ($matches[0] as $key => $match) {
					$tagcontent 	= preg_replace("/{.+?}/", "", $match);
					$tagparams 		= explode('|',$tagcontent);
					$tagsource 		= trim(strip_tags($tagparams[0]));
					$final_vwidth 	= (@$tagparams[1]) ? $tagparams[1] : $vwidth;
					$final_vheight 	= (@$tagparams[2]) ? $tagparams[2] : $vheight;
					$final_autoplay = (@$tagparams[3]) ? $tagparams[3] : $autoplay;

					// source elements
					$findAVparams = array(
						"{SOURCE}",
						"{SOURCEID}",
						"{FOLDER}",
						"{WIDTH}",
						"{HEIGHT}",
						"{AUTOPLAY}",
						"{TRANSPARENCY}",
						"{BACKGROUND}",
						"{BACKGROUNDQT}",
						"{CONTROLBAR}",
						"{SITEURL}",
					);

					// special treatment
					if($plg_tag=="yahoo"){
						$tagsourceyahoo = explode('/',$tagsource);
						$tagsource = 'id='.$tagsourceyahoo[1].'&amp;vid='.$tagsourceyahoo[0];
					}
					if($plg_tag=="youku") $tagsource = substr($tagsource,3);

					// Prepare the HTML
					$output = new JObject;

					// replacement elements
					if(in_array($plg_tag, array("mp3","mp3remote","wma","wmaremote"))){

						$replaceAVparams = array(
							$tagsource,
							substr(md5($tagsource),1,8),
							$afolder,
							$awidth,
							$aheight,
							$final_autoplay,
							$transparency,
							$background,
							$backgroundQT,
							$controlBarLocation,
							$siteUrl,
						);

						$output->playerWidth = $awidth;
						$output->playerHeight = $aheight;

					} else {

						$replaceAVparams = array(
							$tagsource,
							substr(md5($tagsource),1,8),
							$vfolder,
							$final_vwidth,
							$final_vheight,
							$final_autoplay,
							$transparency,
							$background,
							$backgroundQT,
							$controlBarLocation,
							$siteUrl,
						);

						$output->playerWidth = $final_vwidth;
						$output->playerHeight = $final_vheight;

					}

					$output->playerID = 'AVPlayerID_'.substr(md5($tagsource),1,8);
					$output->player = JFilterOutput::ampReplace(str_replace($findAVparams, $replaceAVparams, $tagReplace[$plg_tag]));
					$output->playerEmbedHTML = preg_replace("#(\r|\t|\n|)#s","",htmlentities($output->player, ENT_QUOTES));

					// Download button
					if($downloadLink){
						if (in_array($plg_tag, array("flv","swf","wmv","mov","mp4","3gp","divx"))) {
							$output->downloadLink = $siteUrl.'/plugins/content/jw_allvideos/includes/download.php?file='.$vfolder.'/'.$tagsource.'.'.$plg_tag;
						} elseif(in_array($plg_tag, array("mp3","wma"))) {
							$output->downloadLink = $siteUrl.'/plugins/content/jw_allvideos/includes/download.php?file='.$afolder.'/'.$tagsource.'.'.$plg_tag;
						} else {
							$output->downloadLink = '';
						}
					} else {
						$output->downloadLink = '';
					}

					// Lightbox popup
					if($lightboxLink && !in_array($plg_tag, array("mp3","mp3remote","wma","wmaremote"))) { // video formats only
						$output->lightboxLink = '#'.$output->playerID;
					} else {
						$output->lightboxLink = '';
					}

					// Embed form
					if($embedForm && !in_array($plg_tag, array("wmv","wmvremote","wma","wmaremote"))){ // no Windows Media formats
						$output->embedLink = 'embed_'.$output->playerID;
					}	else {
						$output->embedLink = '';
					}

					// Fetch the template
					ob_start();
					$getTemplatePath = AllVideosHelper::getTemplatePath($plg_name,'default.php');
					$getTemplatePath = $getTemplatePath->file;
					include($getTemplatePath);
					$getTemplate = $plg_copyrights_start.ob_get_contents().$plg_copyrights_end;
					ob_end_clean();

					// Do the replace
					$article->text = preg_replace("#{".$plg_tag."}".preg_quote($tagcontent)."{/".$plg_tag."}#s", $getTemplate , $article->text);

				} // end foreach

			} // end if

		} // END ALLVIDEOS LOOP

	}

}
