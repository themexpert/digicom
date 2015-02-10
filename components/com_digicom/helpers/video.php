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

class DigiComVideo{

	public static function createVideo(){
		$db = JFactory::getDBO();
		$product_id = JRequest::getVar("pid", "0");
		$sql = "select `video_url`, `video_width`, `video_height` from #__digicom_products where id=".intval($product_id);
		$db->setQuery($sql);
		$db->query();
		$product = $db->loadAssocList();
		$media = "";

		if(trim($product["0"]["video_url"]) != ""){
			require_once(JPATH_SITE.DS.'plugins'.DS.'content'.DS.'jw_allvideos'.DS.'jw_allvideos.php');
			$video_url = $product["0"]["video_url"];
			$video_width = $product["0"]["video_width"];
			$video_height = $product["0"]["video_height"];
			$link_ = "";
			if(intval($video_width) != 0 && intval($video_height) != 0){
				//$position_watch = strpos($video_url, 'www.youtube.com/watch');
				if (strpos($video_url, 'www.youtube.com/watch')!==false)
				{ // youtube link - begin
					$link_array = explode('=',$video_url);
					$link_ = $link_array[1];
					$link_2 = explode('&',$link_);
					if(isset($link_2["0"])){
						$media = '{youtube}'.$link_2["0"].'{/youtube}';
					}
					else{
						$media = '{youtube}'.$link_.'{/youtube}';
					}
				} // youtube link - end
				elseif (strpos($video_url, 'www.123video.nl')!==false)
				{ // 123video.nl link - begin
					$link_array = explode('=',$video_url);
					$link_ = $link_array[1]; 
					$media = '{123video}'.$link_.'{/123video}';
				} // 123video.nl link - end
				elseif (strpos($video_url, 'www.aniboom.com')!==false)
				{ // aniboom.com link - begin
					$begin_tag = strpos($video_url, 'video');
					$remaining_link = substr($video_url, $begin_tag + 6, strlen($video_url));
					$end_tag = strpos($remaining_link, '/');
					if($end_tag===false) $end_tag = strlen($remaining_link);
					$link_ = substr($remaining_link, 0, $end_tag);
					$media = '{aniboom}'.$link_.'{/aniboom}';
				} // aniboom.com link - end
				elseif (strpos($video_url, 'www.badjojo.com')!==false)
				{ // badjojo.com [adult] link - begin
					$link_array = explode('=',$video_url);
					$link_ = $link_array[1]; 
					$media = '{badjojo}'.$link_.'{/badjojo}';
					echo $media;
				} // badjojo.com [adult] link - end
				elseif (strpos($video_url, 'www.brightcove.tv')!==false)
				{ // brightcove.tv link - begin
					$begin_tag = strpos($video_url, 'title=');
					$remaining_link = substr($video_url, $begin_tag + 6, strlen($video_url));
					$end_tag = strpos($remaining_link, '&');
					if($end_tag===false) $end_tag = strlen($remaining_link);
					$link_ = substr($remaining_link, 0, $end_tag);
					$media = '{brightcove}'.$link_.'{/brightcove}';
				} // brightcove.tv link - end
				elseif (strpos($video_url, 'www.collegehumor.com')!==false)
				{ // collegehumor.com link - begin
					$link_array = explode(':',$video_url);
					$link_ = $link_array[2]; 
					$media = '{collegehumor}'.$link_.'{/collegehumor}';
				} // collegehumor.com link - end
				elseif (strpos($video_url, 'current.com')!==false)
				{ // current.com link - begin
					$begin_tag = strpos($video_url, 'items/');
					$remaining_link = substr($video_url, $begin_tag + 6, strlen($video_url));
					$end_tag = strpos($remaining_link, '_');
					if($end_tag===false) $end_tag = strlen($remaining_link);
					$link_ = substr($remaining_link, 0, $end_tag);
					$media = '{current}'.$link_.'{/current}';
				} // current.com link - end
				elseif (strpos($video_url, 'dailymotion.com')!==false)
				{ // dailymotion.com link - begin
					$begin_tag = strpos($video_url, 'video/');
					$remaining_link = substr($video_url, $begin_tag + 6, strlen($video_url));
					$end_tag = strpos($remaining_link, '_');
					if($end_tag===false) $end_tag = strlen($remaining_link);
					$link_ = substr($remaining_link, 0, $end_tag);
					$media = '{dailymotion}'.$link_.'{/dailymotion}';
				} // dailymotion.com link - end
				elseif (strpos($video_url, 'espn')!==false)
				{ // video.espn.com link - begin
					$begin_tag = strpos($video_url, 'videoId=');
					$remaining_link = substr($video_url, $begin_tag + 8, strlen($video_url));
					$end_tag = strpos($remaining_link, '&');
					if($end_tag===false) $end_tag = strlen($remaining_link);
					$link_ = substr($remaining_link, 0, $end_tag);
					$media = '{espn}'.$link_.'{/espn}';
				} // video.espn.com link - end
				elseif (strpos($video_url, 'eyespot.com')!==false)
				{ // eyespot.com link - begin
					$link_array = explode('r=',$video_url);
					$link_ = $link_array[1]; 
					$media = '{eyespot}'.$link_.'{/eyespot}';
				} // eyespot.com link - end
				elseif (strpos($video_url, 'flurl.com')!==false)
				{ // flurl.com link - begin
					$begin_tag = strpos($video_url, 'video/');
					$remaining_link = substr($video_url, $begin_tag + 6, strlen($video_url));
					$end_tag = strpos($remaining_link, '_');
					if($end_tag===false) $end_tag = strlen($remaining_link);
					$link_ = substr($remaining_link, 0, $end_tag);
					$media = '{flurl}'.$link_.'{/flurl}';
				} // flurl.com link - end
				elseif (strpos($video_url, 'funnyordie.com')!==false)
				{ // funnyordie.com link - begin
					$link_array = explode('videos/',$video_url);
					$link_ = $link_array[1]; 
					$media = '{funnyordie}'.$link_.'{/funnyordie}';
				} // funnyordie.com link - end
				elseif (strpos($video_url, 'gametrailers.com')!==false)
				{ // gametrailers.com link - begin
					$begin_tag = strpos($video_url, 'player/');
					$remaining_link = substr($video_url, $begin_tag + 7, strlen($video_url));
					$end_tag = strpos($remaining_link, '.');
					if($end_tag===false) $end_tag = strlen($remaining_link);
					$link_ = substr($remaining_link, 0, $end_tag);
					$media = '{gametrailers}'.$link_.'{/gametrailers}';
				} // gametrailers.com link - end
				elseif (strpos($video_url, 'godtube.com')!==false)
				{ // godtube.com link - begin
					$link_array = explode('viewkey=',$video_url);
					$link_ = $link_array[1]; 
					$media = '{godtube}'.$link_.'{/godtube}';
				} // godtube.com link - end
				elseif (strpos($video_url, 'gofish.com')!==false)
				{ // gofish.com link - begin
					$link_array = explode('gfid=',$video_url);
					$link_ = $link_array[1]; 
					$media = '{gofish}'.$link_.'{/gofish}';
				} // gofish.com link - end
				elseif (strpos($video_url, 'google.com')!==false)
				{ // Google Video link - begin
					$link_array = explode('docid=',$video_url);
					$link_ = $link_array[1]; 
					$media = '{google}'.$link_.'{/google}';
				} // Google Video link - end
				elseif (strpos($video_url, 'guba.com')!==false)
				{ // guba.com link - begin
					$link_array = explode('watch/',$video_url);
					$link_ = $link_array[1]; 
					$media = '{guba}'.$link_.'{/guba}';
				} // guba.com link - end
				elseif (strpos($video_url, 'hook.tv')!==false)
				{ // hook.tv link - begin
					$link_array = explode('key=',$video_url);
					$link_ = $link_array[1]; 
					$media = '{hook}'.$link_.'{/hook}';
				} // hook.tv link - end
				elseif (strpos($video_url, 'jumpcut.com')!==false)
				{ // jumpcut.com link - begin
					$link_array = explode('id=',$video_url);
					$link_ = $link_array[1]; 
					$media = '{jumpcut}'.$link_.'{/jumpcut}';
				} // jumpcut.com link - end
				elseif (strpos($video_url, 'kewego.com')!==false)
				{ // kewego.com link - begin
					$begin_tag = strpos($video_url, 'video/');
					$remaining_link = substr($video_url, $begin_tag + 6, strlen($video_url));
					$end_tag = strpos($remaining_link, '.');
					if($end_tag===false) $end_tag = strlen($remaining_link);
					$link_ = substr($remaining_link, 0, $end_tag);
					$media = '{kewego}'.$link_.'{/kewego}';
				} // kewego.com link - end
				elseif (strpos($video_url, 'krazyshow.com')!==false)
				{ // krazyshow.com [adult] link - begin
					$link_array = explode('cid=',$video_url);
					$link_ = $link_array[1]; 
					$media = '{krazyshow}'.$link_.'{/krazyshow}';
				} // krazyshow.com [adult] link - end
				elseif (strpos($video_url, 'ku6.com')!==false)
				{ // ku6.com link - begin
					$begin_tag = strpos($video_url, 'show/');
					$remaining_link = substr($video_url, $begin_tag + 5, strlen($video_url));
					$end_tag = strpos($remaining_link, '.');
					if($end_tag===false) $end_tag = strlen($remaining_link);
					$link_ = substr($remaining_link, 0, $end_tag);
					$media = '{ku6}'.$link_.'{/ku6}';
				} // ku6.com link - end
				elseif (strpos($video_url, 'liveleak.com')!==false)
				{ // liveleak.com link - begin
					$link_array = explode('i=',$video_url);
					$link_ = $link_array[1]; 
					$media = '{liveleak}'.$link_.'{/liveleak}';
				} // liveleak.com link - end
				elseif (strpos($video_url, 'metacafe.com')!==false)
				{ // metacafe.com link - begin
					$begin_tag = strpos($video_url, 'watch/');
					$remaining_link = substr($video_url, $begin_tag + 6, strlen($video_url));
					$end_tag = strlen($remaining_link);
					$link_ = substr($remaining_link, 0, $end_tag);
					$media = '{metacafe}'.$link_.'{/metacafe}';
				} // metacafe.com link - end
				elseif (strpos($video_url, 'mofile.com')!==false)
				{ // mofile.com link - begin
					$begin_tag = strpos($video_url, 'com/');
					$remaining_link = substr($video_url, $begin_tag + 4, strlen($video_url));
					$end_tag = strpos($remaining_link, '/');
					if($end_tag===false) $end_tag = strlen($remaining_link);
					$link_ = substr($remaining_link, 0, $end_tag);
					$media = '{mofile}'.$link_.'{/mofile}';
				} // mofile.com link - end
				elseif (strpos($video_url, 'myspace.com')!==false)
				{ // myspace.com link - begin
					$link_array = explode('VideoID=',$video_url);
					$link_ = $link_array[1]; 
					$media = '{myspace}'.$link_.'{/myspace}';
				} // myspace.com link - end
				elseif (strpos($video_url, 'myvideo.de')!==false)
				{ // myvideo.de link - begin
					$begin_tag = strpos($video_url, 'watch/');
					$remaining_link = substr($video_url, $begin_tag + 6, strlen($video_url));
					$end_tag = strpos($remaining_link, '/');
					if($end_tag===false) $end_tag = strlen($remaining_link);
					$link_ = substr($remaining_link, 0, $end_tag);
					$media = '{myvideo}'.$link_.'{/myvideo}';
				} // myvideo.de link - end
				elseif (strpos($video_url, 'redtube.com')!==false)
				{ // redtube.com [adult] link - begin
					$link_array = explode('/',$video_url);
					$link_ = $link_array[1]; 
					$media = '{redtube}'.$link_.'{/redtube}';
				} // redtube.com [adult] - end
				elseif (strpos($video_url, 'revver.com')!==false)
				{ // revver.com link - begin
					$begin_tag = strpos($video_url, 'video/');
					$remaining_link = substr($video_url, $begin_tag + 6, strlen($video_url));
					$end_tag = strpos($remaining_link, '/');
					if($end_tag===false) $end_tag = strlen($remaining_link);
					$link_ = substr($remaining_link, 0, $end_tag);
					$media = '{revver}'.$link_.'{/revver}';
				} // revver.com link - end
				elseif (strpos($video_url, 'sapo.pt')!==false)
				{ // sapo.pt link - begin
					$link_array = explode('pt/',$video_url);
					$link_ = $link_array[1]; 
					$media = '{sapo}'.$link_.'{/sapo}';
				} // sapo.pt - end
				elseif (strpos($video_url, 'sevenload.com')!==false)
				{ // sevenload.com link - begin
					$begin_tag = strpos($video_url, 'videos/');
					$remaining_link = substr($video_url, $begin_tag + 7, strlen($video_url));
					$end_tag = strpos($remaining_link, '-');
					if($end_tag===false) $end_tag = strlen($remaining_link);
					$link_ = substr($remaining_link, 0, $end_tag);
					$media = '{sevenload}'.$link_.'{/sevenload}';
				} // sevenload.com link - end
				elseif (strpos($video_url, 'sohu.com')!==false)
				{ // sohu.com link - begin
					$link_array = explode('/',$video_url);
					$link_ = $link_array[count($link_array)-1];
					$media = '{sohu}'.$link_.'{/sohu}';
				} // sohu.com - end
				elseif (strpos($video_url, 'southparkstudios.com')!==false)
				{ // southparkstudios.com link - begin
					$begin_tag = strpos($video_url, 'clips/');
					$remaining_link = substr($video_url, $begin_tag + 6, strlen($video_url));
					$end_tag = strpos($remaining_link, '/');
					if($end_tag===false) $end_tag = strlen($remaining_link);
					$link_ = substr($remaining_link, 0, $end_tag);
					$media = '{southpark}'.$link_.'{/southpark}';
				} // southparkstudios.com link - end
				elseif (strpos($video_url, 'spike.com')!==false)
				{ // spike.com link - begin
					$link_array = explode('video/',$video_url);
					$link_ = $link_array[1]; 
					$media = '{spike}'.$link_.'{/spike}';
				} // spike.com - end
				elseif (strpos($video_url, 'stickam.com')!==false)
				{ // stickam.com link - begin
					$link_array = explode('mId=',$video_url);
					$link_ = $link_array[1]; 
					$media = '{stickam}'.$link_.'{/stickam}';
				} // stickam.com - end
				elseif (strpos($video_url, 'stupidvideos.com')!==false)
				{ // stupidvideos.com link - begin
					$link_array = explode('#',$video_url);
					$link_ = $link_array[1]; 
					$media = '{stupidvideos}'.$link_.'{/stupidvideos}';
				} // stupidvideos.com - end
				elseif (strpos($video_url, 'tudou.com')!==false)
				{ // tudou.com link - begin
					$begin_tag = strpos($video_url, 'view/');
					$remaining_link = substr($video_url, $begin_tag + 5, strlen($video_url));
					$end_tag = strpos($remaining_link, '/');
					if($end_tag===false) $end_tag = strlen($remaining_link);
					$link_ = substr($remaining_link, 0, $end_tag);
					$media = '{tudou}'.$link_.'{/tudou}';
				} // tudou.com link - end
				elseif (strpos($video_url, 'ustream.tv')!==false)
				{ // ustream.tv link - begin
					$link_array = explode('recorded/',$video_url);
					$link_ = $link_array[1]; 
					$media = '{ustream}'.$link_.'{/ustream}';
				} // ustream.tv - end
				elseif (strpos($video_url, 'veoh.com')!==false)
				{ // veoh.com link - begin
					$link_array = explode('videos/',$video_url);
					$link_ = $link_array[1];
					$media = '{veoh}'.$link_.'{/veoh}';
				} // veoh.com - end
				elseif (strpos($video_url, 'videotube.de')!==false)
				{ // videotube.de link - begin
					$link_array = explode('watch/',$video_url);
					$link_ = $link_array[1]; 
					$media = '{videotube}'.$link_.'{/videotube}';
				} // videotube.de - end
				elseif (strpos($video_url, 'vidiac.com')!==false)
				{ // vidiac.com link - begin
					$begin_tag = strpos($video_url, 'video/');
					$remaining_link = substr($video_url, $begin_tag + 6, strlen($video_url));
					$end_tag = strpos($remaining_link, '.');
					if($end_tag===false) $end_tag = strlen($remaining_link);
					$link_ = substr($remaining_link, 0, $end_tag);
					$media = '{vidiac}'.$link_.'{/vidiac}';
				} // vidiac.com link - end
				elseif (strpos($video_url, 'vimeo.com')!==false)
				{ // vimeo.com link - begin
					$link_array = explode('.com/',$video_url);
					$link_ = $link_array[1]; 
					$media = '{vimeo}'.$link_.'{/vimeo}';
				} // vimeo.com - end
				elseif (strpos($video_url, 'yahoo.com')!==false)
				{ // video.yahoo.com link - begin
					$link_array = explode('watch/',$video_url);
					$link_ = $link_array[1]; 
					$media = '{yahoo}'.$link_.'{/yahoo}';
				} // video.yahoo.com - end
				elseif (strpos($video_url, 'youare.tv')!==false)
				{ // youare.tv link - begin
					$link_array = explode('id=',$video_url);
					$link_ = $link_array[1];
					$media = '{youare}'.$link_.'{/youare}';
				} // youare.tv - end
				elseif (strpos($video_url, 'youku.com')!==false)
				{ // youku.com link - begin
					$begin_tag = strpos($video_url, 'v_show/');
					$remaining_link = substr($video_url, $begin_tag + 7, strlen($video_url));
					$end_tag = strpos($remaining_link, '.');
					if($end_tag===false) $end_tag = strlen($remaining_link);
					$link_ = substr($remaining_link, 0, $end_tag);
					$media = '{youku}'.$link_.'{/youku}';
				} // youku.com link - end
				elseif (strpos($video_url, 'youmaker.com')!==false)
				{ // youmaker.com  link - begin
					$link_array = explode('id=',$video_url);
					$link_ = $link_array[1];
					$media = '{youmaker}'.$link_.'{/youmaker}';
				} // youmaker.com  - end
				else
				{
					//----------- not special link - begin
					$extension_array=explode('.',$video_url);
					$extension = $extension_array[count($extension_array)-1];

					if(strtolower($extension)=='flv' || strtolower($extension)=='swf' || strtolower($extension)=='mov' || strtolower($extension)=='wmv' || strtolower($extension)=='mp4' || strtolower($extension)=='divx')
						{
							$tag_begin = '{'.strtolower($extension).'remote}';
							$tag_end = '{/'.strtolower($extension).'remote}';
						}
					if(!isset($tag_begin)) {$tag_begin=NULL;}
					if(!isset($tag_end)) {$tag_end=NULL;}
					$media = $tag_begin.$video_url.$auto_play.$tag_end;
					//----------- not special link - begin
				}
				$media = digicomVideo::jwAllVideos( $media, $video_width, $video_height, $video_width, $video_height);
			}//if height and width
		}//if url
		return $media;
	}


	public static function jwAllVideos(&$row, $parawidth=300, $paraheight=20, $parvwidth=400, $parvheight=300) {
		// Globals
		global $mainframe;

		// JoomlaWorks reference parameters
		$plg_name					= "jw_allvideos";
		$plg_tag					= "";
		$plg_copyrights_start		= "\n\n<!-- JoomlaWorks \"AllVideos\" Plugin (v2.5.3) starts here -->\n";
		$plg_copyrights_end			= "\n<!-- JoomlaWorks \"AllVideos\" Plugin (v2.5.3) ends here -->\n\n";

		// Paths without the ending slash

		$mosConfig_live_site = JURI::root();

		//$mosConfig_live_site		= $mainframe->isAdmin() ? $mainframe->getSiteURL() : JURI::base();
		if(substr($mosConfig_live_site, -1)=="/") $mosConfig_live_site = substr($mosConfig_live_site, 0, -1);

		include(JPATH_SITE.DS."plugins".DS."content".DS."jw_allvideos".DS."jw_allvideos".DS."includes".DS."sources.php");
		// simple performance check to determine whether plugin should process further
		$grabTags = str_replace("(","",str_replace(")","",implode(array_keys($tagReplace),"|")));
		if (preg_match("#{(".$grabTags.")}#s",$row)==false) {return true;}

		// general
		$av_template			= 'getault';
		$av_compressjs			= 0;
		// video
		$vfolder 				= 'images/stories/videos';
		$vwidth 				= $parvwidth;
		$vheight 				= $parvheight;
		// audio
		$afolder 				= '';
		$awidth 				= $parawidth;
		$aheight 				= $paraheight;
		// global
		$autoplay 				= 'false';
		$transparency 			= 'transparent';
		$background 			= '';
		// FLV playback
		$av_flvcontroller 		= 'bottom';

		if($av_flvcontroller == "over"){
			$av_flvcontroller = "&controlbar=over";
		} else {
			$av_flvcontroller = "";
		}

		if (0) {
			foreach ($tagReplace as $plg_tag => $value) {
				$regex = "#{".$plg_tag."}(.*?){/".$plg_tag."}#s";
				$row = preg_replace( $regex, "", $row );
			}
			return $row;
		} else {
			$plg_name = "jw_allvideos";
			require_once(JPATH_SITE.DS."plugins".DS."content".DS.$plg_name.DS.$plg_name.DS.'includes'.DS.'helper.php');
			/*
			$plugin = JPluginHelper::getPlugin('content', $plg_name);
			$pluginParams = new JRegistry(@$plugin->params);
			$backgroundQT = $pluginParams->get('backgroundQT','black');
			$controlBarLocation = $pluginParams->get('controlBarLocation','bottom');
			$gzipScripts = $pluginParams->get('gzipScripts',0);
			*/

			$backgroundQT = 'black';
			$controlBarLocation = 'bottom';
			$gzipScripts = 0;

			// add CSS/JS to the head
			static $loadJWAVcss;
			if(!$loadJWAVcss) {
				$loadJWAVcss=1;
				$jwavhead = '
				<style type="text/css" media="all">
					@import "'.$mosConfig_live_site.'/plugins/content/jw_allvideos/jw_allvideos/tmpl/css/template.css";
				</style>
				';

				if($av_compressjs){
				$jwavhead .= '
		<script type="text/javascript" src="'.$mosConfig_live_site.'/plugins/content/jw_allvideos/jw_allvideos/includes/jw_allvideos_scripts.php"></script>
				';
				}
				else {
					// Assign paths
					$sitePath = JPATH_SITE;
					$pluginLivePath = "";
					$siteUrl  = JURI::base(true); //substr(JURI::base(),0,-1);
					$document = JFactory::getDocument();

					if(version_compare(JVERSION,'1.6.0','ge')) {
						$pluginLivePath = JURI::base(true).'/plugins/content/'.$plg_name.'/'.$plg_name;
					} else {
						$pluginLivePath = JURI::base(true).'/plugins/content/'.$plg_name;
					}

					$document->addScript($pluginLivePath.'/includes/jw_allvideos_scripts.php');
				}
			}
		}

		// START ALLVIDEOS LOOP
		foreach ($tagReplace as $plg_tag => $value) {
			// expression to search for
			$regex = "#{".$plg_tag."}(.*?){/".$plg_tag."}#s";
			// process tags
			if (preg_match_all($regex, $row, $matches, PREG_PATTERN_ORDER) > 0) {
				// start the replace loop
				foreach ($matches[0] as $key => $match) {
					$tagcontent 	= preg_replace("/{.+?}/", "", $match);
					$tagparams 		= explode('|',$tagcontent);
					$tagsource 		= $tagparams[0];
					$final_vwidth 	= (@$tagparams[1]) ? $tagparams[1] : $vwidth;
					$final_vheight 	= (@$tagparams[2]) ? $tagparams[2] : $vheight;
					$final_autoplay = (@$tagparams[3]) ? $tagparams[3] : $autoplay;

					// replacements
					$findAVparams = array(
						"{SITEURL}",
						"{SOURCE}",
						"{SOURCEID}",
						"{FOLDER}",
						"{WIDTH}",
						"{HEIGHT}",
						"{AUTOPLAY}",
						"{TRANSPARENCY}",
						"{BACKGROUND}",
						"{CONTROLBAR}"
					);

					// special treatment
					if($plg_tag=="yahoo"){
						$tagsourceyahoo = explode('/',$tagsource);
						$tagsource = 'id='.$tagsourceyahoo[1].'&amp;vid='.$tagsourceyahoo[0];
					}
					if($plg_tag=="youku"){
						$tagsource = substr($tagsource,3);
					}

					// replacement elements
						if(in_array($plg_tag, array("mp3","mp3remote","wma","wmaremote"))){

							$replaceAVparams = array(
								JURI::root(),
								$tagsource,
								substr(md5($tagsource),1,8),
								$afolder,
								$awidth,
								$aheight,
								$final_autoplay,
								$transparency,
								$background,
								$backgroundQT,
								$controlBarLocation
							);

							$output->playerWidth = $awidth;
							$output->playerHeight = $aheight;

						} else {

							$replaceAVparams = array(
								JURI::root(),
								$tagsource,
								substr(md5($tagsource),1,8),
								$vfolder,
								$final_vwidth,
								$final_vheight,
								$final_autoplay,
								$transparency,
								$background,
								$backgroundQT,
								$controlBarLocation
							);
						}

					// wrap HTML around players
					$wrapstart = '<span class="allvideos">';
					$wrapend = '</span>';

					//$plg_html = JFilterOutput::ampReplace($wrapstart.str_replace($findAVparams, $replaceAVparams, $tagReplace[$plg_tag]).$wrapend);
						$plg_html = str_replace($findAVparams, $replaceAVparams, $tagReplace[$plg_tag]);

					// Do the replace
					$row = preg_replace("#{".$plg_tag."}".preg_quote($tagcontent)."{/".$plg_tag."}#s", $plg_html , $row);
				} // end foreach

			} // end if

		} // END ALLVIDEOS LOOP
		return $row;
	} // END FUNCTION

}

