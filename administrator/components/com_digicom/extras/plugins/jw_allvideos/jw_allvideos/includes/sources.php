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

$tagReplace = array(

/* -------------------- Audio/Video formats -------------------- */
"flv" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"{SITEURL}/plugins/content/jw_allvideos/includes/players/mediaplayer/player.swf\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"{SITEURL}/plugins/content/jw_allvideos/includes/players/mediaplayer/player.swf\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
	<param name=\"allowfullscreen\" value=\"true\" />
	<param name=\"allowscriptaccess\" value=\"always\" />
	<param name=\"flashvars\" value=\"file={SITEURL}/{FOLDER}/{SOURCE}.flv&image={SITEURL}/{FOLDER}/{SOURCE}.jpg&autostart={AUTOPLAY}{CONTROLBAR}&fullscreen=true\" />
</object>
",

"flvremote" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"{SITEURL}/plugins/content/jw_allvideos/includes/players/mediaplayer/player.swf\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"{SITEURL}/plugins/content/jw_allvideos/includes/players/mediaplayer/player.swf\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
	<param name=\"allowfullscreen\" value=\"true\" />
	<param name=\"allowscriptaccess\" value=\"always\" />
	<param name=\"flashvars\" value=\"file={SOURCE}&autostart={AUTOPLAY}{CONTROLBAR}&fullscreen=true\" />
</object>
",

"mp3" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"{SITEURL}/plugins/content/jw_allvideos/includes/players/mediaplayer/player.swf\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"{SITEURL}/plugins/content/jw_allvideos/includes/players/mediaplayer/player.swf\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
	<param name=\"allowfullscreen\" value=\"false\" />
	<param name=\"allowscriptaccess\" value=\"always\" />
	<param name=\"flashvars\" value=\"file={SITEURL}/{FOLDER}/{SOURCE}.mp3&autostart={AUTOPLAY}\" />
</object>
",

"mp3remote" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"{SITEURL}/plugins/content/jw_allvideos/includes/players/mediaplayer/player.swf\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"{SITEURL}/plugins/content/jw_allvideos/includes/players/mediaplayer/player.swf\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
	<param name=\"allowfullscreen\" value=\"false\" />
	<param name=\"allowscriptaccess\" value=\"always\" />
	<param name=\"flashvars\" value=\"file={SOURCE}&autostart={AUTOPLAY}\" />
</object>
",

"swf" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"{SITEURL}/{FOLDER}/{SOURCE}.swf\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"{SITEURL}/{FOLDER}/{SOURCE}.swf\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
</object>
",

"swfremote" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"{SOURCE}\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"{SOURCE}\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
</object>
",

"wmv" => "
<span id=\"avID_{SOURCEID}\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" title=\"JoomlaWorks AllVideos Player\"></span>
<script type=\"text/javascript\">
var cnt = document.getElementById('avID_{SOURCEID}');
var src = '{SITEURL}/plugins/content/jw_allvideos/includes/players/wmvplayer/wmvplayer.xaml';
var cfg = {
	file:'{SITEURL}/{FOLDER}/{SOURCE}.wmv',
	width:'{WIDTH}',
	height:'{HEIGHT}',
	autostart:'{AUTOPLAY}',
	image:'{SITEURL}/{FOLDER}/{SOURCE}.jpg'
};
var ply = new jeroenwijering.Player(cnt,src,cfg);
</script>
",

"wmvremote" => "
<span id=\"avID_{SOURCEID}\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" title=\"JoomlaWorks AllVideos Player\"></span>
<script type=\"text/javascript\">
var cnt = document.getElementById('avID_{SOURCEID}');
var src = '{SITEURL}/plugins/content/jw_allvideos/includes/players/wmvplayer/wmvplayer.xaml';
var cfg = {
	file:'{SOURCE}',
	width:'{WIDTH}',
	height:'{HEIGHT}',
	autostart:'{AUTOPLAY}'
};
var ply = new jeroenwijering.Player(cnt,src,cfg);
</script>
",

"wma" => "
<span id=\"avID_{SOURCEID}\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" title=\"JoomlaWorks AllVideos Player\"></span>
<script type=\"text/javascript\">
var cnt = document.getElementById(\"avID_{SOURCEID}\");
var src = '{SITEURL}/plugins/content/jw_allvideos/includes/players/wmvplayer/wmvplayer.xaml';
var cfg = {
	file:'{SITEURL}/{FOLDER}/{SOURCE}.wma',
	width:'{WIDTH}',
	height:'{HEIGHT}',
	autostart:'{AUTOPLAY}',
	usefullscreen:'false'
};
var ply = new jeroenwijering.Player(cnt,src,cfg);
</script>
",

"wmaremote" => "
<span id=\"avID_{SOURCEID}\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" title=\"JoomlaWorks AllVideos Player\"></span>
<script type=\"text/javascript\">
var cnt = document.getElementById(\"avID_{SOURCEID}\");
var src = '{SITEURL}/plugins/content/jw_allvideos/includes/players/wmvplayer/wmvplayer.xaml';
var cfg = {
	file:'{SOURCE}',
	width:'{WIDTH}',
	height:'{HEIGHT}',
	autostart:'{AUTOPLAY}',
	usefullscreen:'false'
};
var ply = new jeroenwijering.Player(cnt,src,cfg);
</script>
",

"mov" => "
<script type=\"text/javascript\">
	QT_WriteOBJECT_XHTML('{SITEURL}/{FOLDER}/{SOURCE}.mov', '{WIDTH}', '{HEIGHT}', '', 'autoplay', '{AUTOPLAY}', 'bgcolor', '{BACKGROUNDQT}', 'scale', 'aspect');
</script>
",

"movremote" => "
<script type=\"text/javascript\">
	QT_WriteOBJECT_XHTML('{SOURCE}', '{WIDTH}', '{HEIGHT}', '', 'autoplay', '{AUTOPLAY}', 'bgcolor', '{BACKGROUNDQT}', 'scale', 'aspect');
</script>
",

"mp4" => "
<script type=\"text/javascript\">
	QT_WriteOBJECT_XHTML('{SITEURL}/{FOLDER}/{SOURCE}.mp4', '{WIDTH}', '{HEIGHT}', '', 'autoplay', '{AUTOPLAY}', 'bgcolor', '{BACKGROUNDQT}', 'scale', 'aspect');
</script>
",

"mp4remote" => "
<script type=\"text/javascript\">
	QT_WriteOBJECT_XHTML('{SOURCE}', '{WIDTH}', '{HEIGHT}', '', 'autoplay', '{AUTOPLAY}', 'bgcolor', '{BACKGROUNDQT}', 'scale', 'aspect');
</script>
",

"3gp" => "
<script type=\"text/javascript\">
	QT_WriteOBJECT_XHTML('{SITEURL}/{FOLDER}/{SOURCE}.3gp', '{WIDTH}', '{HEIGHT}', '', 'autoplay', '{AUTOPLAY}', 'bgcolor', '{BACKGROUNDQT}', 'scale', 'aspect');
</script>
",

"3gpremote" => "
<script type=\"text/javascript\">
	QT_WriteOBJECT_XHTML('{SOURCE}', '{WIDTH}', '{HEIGHT}', '', 'autoplay', '{AUTOPLAY}', 'bgcolor', '{BACKGROUNDQT}', 'scale', 'aspect');
</script>
",

"divx" => "
<object type=\"video/divx\" data=\"{SITEURL}/{FOLDER}/{SOURCE}.divx\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"type\" value=\"video/divx\" />
	<param name=\"src\" value=\"{SITEURL}/{FOLDER}/{SOURCE}.divx\" />
	<param name=\"data\" value=\"{SITEURL}/{FOLDER}/{SOURCE}.divx\" />
	<param name=\"codebase\" value=\"{SITEURL}/{FOLDER}/{SOURCE}.divx\" />
	<param name=\"url\" value=\"{SITEURL}/{FOLDER}/{SOURCE}.divx\" />
	<param name=\"mode\" value=\"full\" />
	<param name=\"pluginspage\" value=\"http://go.divx.com/plugin/download/\" />
	<param name=\"allowContextMenu\" value=\"true\" />
	<param name=\"previewImage\" value=\"{SITEURL}/{FOLDER}/{SOURCE}.jpg\" />
	<param name=\"autoPlay\" value=\"{AUTOPLAY}\" />
	<param name=\"minVersion\" value=\"1.0.0\" />
	<param name=\"custommode\" value=\"none\" />
	<p>No video? Get the DivX browser plug-in for <a href=\"http://download.divx.com/player/DivXWebPlayerInstaller.exe\">Windows</a> or <a href=\"http://download.divx.com/player/DivXWebPlayer.dmg\">Mac</a></p>
</object>
",

"divxremote" => "
<object type=\"video/divx\" data=\"{SOURCE}\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"type\" value=\"video/divx\" />
	<param name=\"src\" value=\"{SOURCE}\" />
	<param name=\"data\" value=\"{SOURCE}\" />
	<param name=\"codebase\" value=\"{SOURCE}\" />
	<param name=\"url\" value=\"{SOURCE}\" />
	<param name=\"mode\" value=\"full\" />
	<param name=\"pluginspage\" value=\"http://go.divx.com/plugin/download/\" />
	<param name=\"allowContextMenu\" value=\"true\" />
	<param name=\"previewImage\" value=\"\" />
	<param name=\"autoPlay\" value=\"{AUTOPLAY}\" />
	<param name=\"minVersion\" value=\"1.0.0\" />
	<param name=\"custommode\" value=\"none\" />
	<p>No video? Get the DivX browser plug-in for <a href=\"http://download.divx.com/player/DivXWebPlayerInstaller.exe\">Windows</a> or <a href=\"http://download.divx.com/player/DivXWebPlayer.dmg\">Mac</a></p>
</object>
",



/* -------------------- 3rd party video providers -------------------- */
// YouTube
"youtube" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://www.youtube.com/v/{SOURCE}&hl=en&fs=1\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"http://www.youtube.com/v/{SOURCE}&hl=en&fs=1\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"allowfullscreen\" value=\"true\" />
	<param name=\"allowscriptaccess\" value=\"always\" />
</object>
",

// Google Video
"(google|google.co.uk|google.com.au|google.de|google.es|google.fr|google.it|google.nl|google.pl)" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://video.google.com/googleplayer.swf?docid={SOURCE}&hl=en&fs=true\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"http://video.google.com/googleplayer.swf?docid={SOURCE}&hl=en&fs=true\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"allowfullscreen\" value=\"true\" />
	<param name=\"allowscriptaccess\" value=\"always\" />
</object>
",

// 123video.nl - http://www.123video.nl/playvideos.asp?MovieID=248020
"123video" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://www.123video.nl/123video_share.swf?mediaSrc={SOURCE}\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"http://www.123video.nl/123video_share.swf?mediaSrc={SOURCE}\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
</object>
",

// aniboom.com - http://www.aniboom.com/video/28604/Kashe-Li-Its-Hard/
"aniboom" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://api.aniboom.com/embedded.swf?videoar={SOURCE}\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"http://api.aniboom.com/embedded.swf?videoar={SOURCE}\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"allowscriptaccess\" value=\"sameDomain\" />
</object>
",

// badjojo.com [adult] - http://www.badjojo.com/video_play_front.php?Id=6718
"badjojo" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://www.badjojo.com/flvplayer.swf\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"http://www.badjojo.com/flvplayer.swf\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
	<param name=\"allowfullscreen\" value=\"true\" />
	<param name=\"allowscriptaccess\" value=\"always\" />
	<param name=\"flashvars\" value=\"config=http://www.badjojo.com/videoConfigXmlIndraCode.php?vId={SOURCE}\" />
</object>
",

// brightcove.tv - http://www.brightcove.tv/title.jsp?title=1656387563&channel=151854679
"brightcove" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://www.brightcove.tv/playerswf\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"http://www.brightcove.tv/playerswf\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
	<param name=\"allowfullscreen\" value=\"true\" />
	<param name=\"allowscriptaccess\" value=\"always\" />
	<param name=\"flashvars\" value=\"allowfullscreen=true&initVideoId={SOURCE}&servicesURL=http://www.brightcove.tv&viewerSecureGatewayURL=https://www.brightcove.tv&cdnURL=http://admin.brightcove.com&autoStart={AUTOPLAY}\" />
	<param name=\"base\" value=\"http://admin.brightcove.com\" />
	<param name=\"seamlesstabbing\" value=\"false\" />
	<param name=\"swLiveConnect\" value=\"true\" />
</object>
",

// collegehumor.com - http://www.collegehumor.com/video:1824771
"collegehumor" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://www.collegehumor.com/moogaloop/moogaloop.swf?clip_id={SOURCE}&fullscreen=1\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"http://www.collegehumor.com/moogaloop/moogaloop.swf?clip_id={SOURCE}&fullscreen=1\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
	<param name=\"allowfullscreen\" value=\"true\" />
	<param name=\"allowscriptaccess\" value=\"always\" />
</object>
",

// current.com - http://current.com/items/89150801_campaign_update_07_30_08
"current" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://current.com/e/{SOURCE}/en_US\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"http://current.com/e/{SOURCE}/en_US\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
	<param name=\"allowfullscreen\" value=\"true\" />
	<param name=\"allowscriptaccess\" value=\"always\" />
</object>
",

// dailymotion.com - http://www.dailymotion.com/featured/video/x35714_cap-nord-projet-1_creation
"dailymotion" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://www.dailymotion.com/swf/{SOURCE}\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"http://www.dailymotion.com/swf/{SOURCE}\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
	<param name=\"allowfullscreen\" value=\"true\" />
	<param name=\"allowscriptaccess\" value=\"always\" />
</object>
",

// video.espn.com - http://sports.espn.go.com/broadband/video/videopage?videoId=3503001&categoryId=3025809&n8pe6c=2
"espn" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://sports.espn.go.com/broadband/player.swf?mediaId={SOURCE}\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"http://sports.espn.go.com/broadband/player.swf?mediaId={SOURCE}\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
	<param name=\"allowfullscreen\" value=\"true\" />
	<param name=\"allowscriptaccess\" value=\"always\" />
</object>
",

// eyespot.com - http://eyespot.com/share?cmd=permalink&r=0XCzIG2UEAy3criEJW0wIWu85o
"eyespot" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://eyespot.com/flash/medialoader.swf?vurl=http://downloads.eyespot.com/direct/play?r={SOURCE}&_autoPlay={AUTOPLAY}\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"http://eyespot.com/flash/medialoader.swf?vurl=http://downloads.eyespot.com/direct/play?r={SOURCE}&_autoPlay={AUTOPLAY}\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
	<param name=\"allowfullscreen\" value=\"true\" />
	<param name=\"allowscriptaccess\" value=\"always\" />
</object>
",

// flurl.com - http://www.flurl.com/video/18402409_airport_musical.htm
"flurl" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://www.flurl.com/flashplayer/FLVPlayer.swf?xml=http://www.flurl.com/flashplayer/play_flash_xml.php?entryid={SOURCE}\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"http://www.flurl.com/flashplayer/FLVPlayer.swf?xml=http://www.flurl.com/flashplayer/play_flash_xml.php?entryid={SOURCE}\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
	<param name=\"allowfullscreen\" value=\"true\" />
	<param name=\"allowscriptaccess\" value=\"sameDomain\" />
</object>
",

// funnyordie.com - http://www.funnyordie.com/videos/7c52bd0f81
"funnyordie" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://www2.funnyordie.com/public/flash/fodplayer.swf?1203120643\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"http://www2.funnyordie.com/public/flash/fodplayer.swf?1203120643\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
	<param name=\"allowfullscreen\" value=\"true\" />
	<param name=\"allowscriptaccess\" value=\"always\" />
	<param name=\"flashvars\" value=\"key={SOURCE}\" />
</object>
",

// gametrailers.com - http://www.gametrailers.com/player/37719.html
"gametrailers" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://www.gametrailers.com/remote_wrap.php?mid={SOURCE}\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"http://www.gametrailers.com/remote_wrap.php?mid={SOURCE}\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
	<param name=\"allowfullscreen\" value=\"true\" />
	<param name=\"allowscriptaccess\" value=\"sameDomain\" />
</object>
",

// godtube.com - http://www.godtube.com/view_video.php?viewkey=3336db1900a0d4d2df7e
"godtube" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://godtube.com/flvplayer.swf\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"http://godtube.com/flvplayer.swf\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
	<param name=\"allowfullscreen\" value=\"true\" />
	<param name=\"allowscriptaccess\" value=\"sameDomain\" />
	<param name=\"flashvars\" value=\"viewkey={SOURCE}\" />
</object>
",

// gofish.com - http://www.gofish.com/player.gfp?gfid=30-1212872
"gofish" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://www.gofish.com/player/fwplayer.swf\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"http://www.gofish.com/player/fwplayer.swf\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
	<param name=\"allowfullscreen\" value=\"true\" />
	<param name=\"allowscriptaccess\" value=\"always\" />
	<param name=\"flashvars\" value=\"&loc=blog&gf=true&ns=false&fs=false&gfid={SOURCE}&c=grey&autoPlay=false&getAd=false&wm=false&ct=true&tb=false&svr=www.gofish.com\" />
</object>
",

// guba.com - http://www.guba.com/watch/3000156661
"guba" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://www.guba.com/f/root.swf?video_url=http://free.guba.com/uploaditem/{SOURCE}/flash.flv&isEmbeddedPlayer=true\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"http://www.guba.com/f/root.swf?video_url=http://free.guba.com/uploaditem/{SOURCE}/flash.flv&isEmbeddedPlayer=true\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
	<param name=\"allowfullscreen\" value=\"true\" />
	<param name=\"allowscriptaccess\" value=\"always\" />
</object>
",

// hook.tv - http://www.hook.tv/player.php?key=51AAAF57E594269E
"hook" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://flyfishing.hook.tv/player.swf?key={SOURCE}\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"http://flyfishing.hook.tv/player.swf?key={SOURCE}\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
	<param name=\"allowfullscreen\" value=\"true\" />
	<param name=\"allowscriptaccess\" value=\"always\" />
</object>
",

// jumpcut.com - http://www.jumpcut.com/view?id=B4AC2D1607ED11DDA411000423CF0184
"jumpcut" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://www.jumpcut.com/media/flash/jump.swf?id={SOURCE}&asset_type=movie&asset_id={SOURCE}&eb=1\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"http://www.jumpcut.com/media/flash/jump.swf?id={SOURCE}&asset_type=movie&asset_id={SOURCE}&eb=1\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
	<param name=\"allowfullscreen\" value=\"true\" />
	<param name=\"allowscriptaccess\" value=\"always\" />
</object>
",

// kewego.com - http://www.kewego.com/video/iLyROoafYcaT.html
"kewego" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://www.kewego.com/p/en/{SOURCE}.html\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"http://www.kewego.com/p/en/{SOURCE}.html\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
	<param name=\"allowfullscreen\" value=\"true\" />
	<param name=\"allowscriptaccess\" value=\"always\" />
</object>
",

// krazyshow.com [adult] - http://www.krazyshow.com/media/playvideo.aspx?f=flash7&cid=FFE2C64AF5F843FB88A00B2FE31BD3BA
"krazyshow" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://www.krazyshow.com/media/flvplayer2.swf?autoStart=0&popup=1&video=http%3a%2f%2fwww.krazyshow.com%2fmedia%2fgetflashvideo.ashx%3fcid%3d{SOURCE}\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"http://www.krazyshow.com/media/flvplayer2.swf?autoStart=0&popup=1&video=http%3a%2f%2fwww.krazyshow.com%2fmedia%2fgetflashvideo.ashx%3fcid%3d{SOURCE}\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
	<param name=\"allowfullscreen\" value=\"true\" />
	<param name=\"allowscriptaccess\" value=\"always\" />
</object>
",

// liveleak.com - http://www.liveleak.com/view?i=2eb_1217374911
"liveleak" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://www.liveleak.com/e/{SOURCE}\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"http://www.liveleak.com/e/{SOURCE}\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
	<param name=\"allowfullscreen\" value=\"true\" />
	<param name=\"allowscriptaccess\" value=\"always\" />
</object>
",

// livevideo.com - http://www.livevideo.com/video/APnews/F19F90BB55C64182A7F2AA222A982893/raw-video-at-least-7-killed-i.aspx
"livevideo" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://www.livevideo.com/flvplayer/embed/{SOURCE}\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"http://www.livevideo.com/flvplayer/embed/{SOURCE}\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
	<param name=\"allowfullscreen\" value=\"true\" />
	<param name=\"allowscriptaccess\" value=\"always\" />
</object>
",

// metacafe.com - http://www.metacafe.com/watch/1560301/jet_car_goes_324_mph_for_texas_speed_record/
"metacafe" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://www.metacafe.com/fplayer/{SOURCE}.swf\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"http://www.metacafe.com/fplayer/{SOURCE}.swf\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
	<param name=\"allowfullscreen\" value=\"true\" />
	<param name=\"allowscriptaccess\" value=\"always\" />
</object>
",

// mofile.com - http://tv.mofile.com/WGCQWS8D/
"mofile" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://tv.mofile.com/cn/xplayer.swf\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"http://tv.mofile.com/cn/xplayer.swf\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
	<param name=\"allowfullscreen\" value=\"true\" />
	<param name=\"allowscriptaccess\" value=\"sameDomain\" />
	<param name=\"flashvars\" value=\"v={SOURCE}&autoplay=0&nowSkin=1_1\" />
</object>
",

// myspace.com - http://vids.myspace.com/index.cfm?fuseaction=vids.individual&VideoID=37910278
"myspace" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://lads.myspace.com/videos/vplayer.swf\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"http://lads.myspace.com/videos/vplayer.swf\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
	<param name=\"allowfullscreen\" value=\"true\" />
	<param name=\"allowscriptaccess\" value=\"sameDomain\" />
	<param name=\"flashvars\" value=\"m={SOURCE}&v=2&type=video\" />
</object>
",

// myvideo.de - http://www.myvideo.de/watch/4027656/Webcam_Julia_Privat_Akt_mein_erster_Song?p=hm21
"myvideo" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://www.myvideo.de/movie/{SOURCE}\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"http://www.myvideo.de/movie/{SOURCE}\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
	<param name=\"allowfullscreen\" value=\"true\" />
	<param name=\"allowscriptaccess\" value=\"always\" />
</object>
",

// redtube.com [adult] - http://www.redtube.com/9194
"redtube" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://embed.redtube.com/player/\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"http://embed.redtube.com/player/\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
	<param name=\"allowfullscreen\" value=\"true\" />
	<param name=\"allowscriptaccess\" value=\"always\" />
	<param name=\"flashvars\" value=\"id={SOURCE}&style=redtube\" />
</object>
",

// revver.com - http://www.revver.com/video/1072440/gnarls-barkley-whos-gonna-save-my-soul/
"revver" => "

<script src=\"http://flash.revver.com/player/1.0/player.js?mediaId:{SOURCE};width:{WIDTH};height:{HEIGHT};\" type=\"text/javascript\"></script>
</span>
",

// sapo.pt - http://videos.sapo.pt/34NipYH7bWgUzc3pZgwo
"sapo" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://rd3.videos.sapo.pt/play?file=http://rd3.videos.sapo.pt/{SOURCE}/mov/1\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"http://rd3.videos.sapo.pt/play?file=http://rd3.videos.sapo.pt/{SOURCE}/mov/1\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
	<param name=\"allowfullscreen\" value=\"true\" />
	<param name=\"allowscriptaccess\" value=\"always\" />
</object>
",

// sevenload.com - http://en.sevenload.com/videos/C4vgVtx-Startrek-Just-Got-Smaller
"sevenload" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://www.sevenload.com/pl/{SOURCE}/445x364/swf\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"http://www.sevenload.com/pl/{SOURCE}/445x364/swf\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
	<param name=\"allowfullscreen\" value=\"true\" />
	<param name=\"allowscriptaccess\" value=\"always\" />
</object>
",

// spike.com [former iFilm.com] - http://www.spike.com/video/2881531
"(spike|ifilm)" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://www.spike.com/efp\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"http://www.spike.com/efp\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
	<param name=\"allowfullscreen\" value=\"true\" />
	<param name=\"allowscriptaccess\" value=\"always\" />
	<param name=\"flashvars\" value=\"flvbaseclip={SOURCE}&\" />
</object>
",

// stickam.com - http://www.stickam.com/viewMedia.do?mId=180191003
"stickam" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://player.stickam.com/flashVarMediaPlayer/{SOURCE}\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"http://player.stickam.com/flashVarMediaPlayer/{SOURCE}\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
	<param name=\"allowfullscreen\" value=\"true\" />
	<param name=\"allowscriptaccess\" value=\"always\" />
</object>
",

// stupidvideos.com - http://www.stupidvideos.com/video/just_plain_stupid/Spoon_Prank_1/#175073
"stupidvideos" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://img.purevideo.com/images/player/player.swf?sa=1&i={SOURCE}\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"http://img.purevideo.com/images/player/player.swf?sa=1&i={SOURCE}\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
	<param name=\"allowfullscreen\" value=\"true\" />
	<param name=\"allowscriptaccess\" value=\"always\" />
</object>
",

// ustream.tv - http://www.ustream.tv/recorded/140603
"ustream" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://www.ustream.tv/flash/video/{SOURCE}\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"http://www.ustream.tv/flash/video/{SOURCE}\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
	<param name=\"allowfullscreen\" value=\"true\" />
	<param name=\"allowscriptaccess\" value=\"always\" />
</object>
",

// veoh.com - http://www.veoh.com/videos/v458872KnKgCCNF
"veoh" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://www.veoh.com/veohplayer.swf?permalinkId={SOURCE}&id=anonymous&player=videodetailsembedded&affiliateId=&videoAutoPlay=0\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"http://www.veoh.com/veohplayer.swf?permalinkId={SOURCE}&id=anonymous&player=videodetailsembedded&affiliateId=&videoAutoPlay=0\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
	<param name=\"allowfullscreen\" value=\"true\" />
	<param name=\"allowscriptaccess\" value=\"always\" />
</object>
",

// videotube.de - http://www.videotube.de/watch/41819
"videotube" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://www.videotube.de/flash/videotube_player_4.swf\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"http://www.videotube.de/flash/videotube_player_4.swf\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
	<param name=\"allowfullscreen\" value=\"true\" />
	<param name=\"allowscriptaccess\" value=\"always\" />
	<param name=\"flashvars\" value=\"videoId={SOURCE}&svsf=1&lang=german&host=www.videotube.de\" />
	<param name=\"swLiveConnect\" value=\"true\" />
</object>
",

// vidiac.com - http://www.vidiac.com/video/fee38abd-b421-4873-bf7b-9841003cff17.htm
"vidiac" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://www.vidiac.com/vidiac.swf\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"http://www.vidiac.com/vidiac.swf\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
	<param name=\"allowfullscreen\" value=\"true\" />
	<param name=\"allowscriptaccess\" value=\"sameDomain\" />
	<param name=\"flashvars\" value=\"video={SOURCE}\" />
</object>
",

// vimeo.com - http://www.vimeo.com/1319796
"vimeo" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://www.vimeo.com/moogaloop.swf?clip_id={SOURCE}&amp;server=www.vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"http://www.vimeo.com/moogaloop.swf?clip_id={SOURCE}&amp;server=www.vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
	<param name=\"allowfullscreen\" value=\"true\" />
	<param name=\"allowscriptaccess\" value=\"always\" />
	<param name=\"scale\" value=\"showAll\" />
</object>
",

// video.yahoo.com - http://video.yahoo.com/watch/3169238/8981933
"yahoo" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://d.yimg.com/static.video.yahoo.com/yep/YV_YEP.swf\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"http://d.yimg.com/static.video.yahoo.com/yep/YV_YEP.swf\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
	<param name=\"allowfullscreen\" value=\"true\" />
	<param name=\"allowscriptaccess\" value=\"always\" />
	<param name=\"flashvars\" value=\"{SOURCE}\" />
</object>
",

// youare.tv - http://www.youare.tv/watch.php?id=2859
"youare" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://www.youare.tv/yatvplayer.swf?videoID={SOURCE}&serverDomain=www.youare.tv\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"http://www.youare.tv/yatvplayer.swf?videoID={SOURCE}&serverDomain=www.youare.tv\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
	<param name=\"allowfullscreen\" value=\"true\" />
	<param name=\"allowscriptaccess\" value=\"never\" />
</object>
",

// youmaker.com - http://www.youmaker.com/video/sv?id=508ae75247584c5f8c5e6af6b8278edf001
"youmaker" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://www.youmaker.com/v.swf\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"http://www.youmaker.com/v.swf\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
	<param name=\"allowfullscreen\" value=\"true\" />
	<param name=\"allowscriptaccess\" value=\"always\" />
	<param name=\"flashvars\" value=\"file=http://www.youmaker.com/video/v%3Fid%3D{SOURCE}%26nu%3Dnu&showdigits=true&overstretch=fit&autostart={AUTOPLAY}&rotatetime=12&linkfromdisplay=false&repeat=list&shuffle=false&showfsbutton=false&fsreturnpage=&fullscreenpage=\" />
</object>
",



// --- Added in 2.5.2 - CHINESE video providers ---

// ku6.com - http://v.ku6.com/show/mXbA6Nvfwba9H3m4.html - mXbA6Nvfwba9H3m4
"ku6" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://player.ku6.com/refer/{SOURCE}/v.swf\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"http://player.ku6.com/refer/{SOURCE}/v.swf\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
	<param name=\"allowfullscreen\" value=\"true\" />
	<param name=\"allowscriptaccess\" value=\"always\" />
</object>
",

// sohu.com - http://v.blog.sohu.com/u/vw/1478211 - 1478211
"sohu" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://v.blog.sohu.com/fo/v4/{SOURCE}\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"http://v.blog.sohu.com/fo/v4/{SOURCE}\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
	<param name=\"allowfullscreen\" value=\"true\" />
	<param name=\"allowscriptaccess\" value=\"Always\" />
</object>
",

// tudou.com - http://www.tudou.com/programs/view/sUMj-5Qpxr8/ - sUMj-5Qpxr8
"tudou" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://www.tudou.com/v/{SOURCE}\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"http://www.tudou.com/v/{SOURCE}\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
	<param name=\"allowfullscreen\" value=\"true\" />
	<param name=\"allowscriptaccess\" value=\"always\" />
</object>
",

// youku.com - http://v.youku.com/v_show/id_XMzc2MDU3OTY=.html - id_XMzc2MDU3OTY=
"youku" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://player.youku.com/player.php/sid/{SOURCE}/v.swf\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"http://player.youku.com/player.php/sid/{SOURCE}/v.swf\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
	<param name=\"allowfullscreen\" value=\"true\" />
	<param name=\"allowscriptaccess\" value=\"sameDomain\" />
</object>
",

// --- Added in 2.5.3 ---
// southparkstudios.com - http://www.southparkstudios.com/clips/165195/
"southpark" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{WIDTH}px;height:{HEIGHT}px;\" data=\"http://media.mtvnservices.com/mgid:cms:item:southparkstudios.com:{SOURCE}:\" title=\"JoomlaWorks AllVideos Player\">
	<param name=\"movie\" value=\"http://media.mtvnservices.com/mgid:cms:item:southparkstudios.com:{SOURCE}:\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
	<param name=\"allowfullscreen\" value=\"true\" />
	<param name=\"allowscriptaccess\" value=\"always\" />
</object>
",

// --- Added in 3.2 ---
// goal4replay.net
"goal4replay" => "
<object type=\"application/x-shockwave-flash\" style=\"width:{VWIDTH}px;height:{VHEIGHT}px;\" data=\"http://www.goal4replay.net/videoEmbedLa.swf?ID={AVSOURCE}&amp;MediaID=1\">
	<param name=\"movie\" value=\"http://www.goal4replay.net/videoEmbedLa.swf?ID={AVSOURCE}&amp;MediaID=1\" />
	<param name=\"quality\" value=\"high\" />
	<param name=\"wmode\" value=\"{TRANSPARENCY}\" />
	<param name=\"bgcolor\" value=\"{BACKGROUND}\" />
	<param name=\"autoplay\" value=\"{AUTOPLAY}\" />
	<param name=\"allowfullscreen\" value=\"true\" />
	<param name=\"allowscriptaccess\" value=\"always\" />
</object>
",

);
