<?php
/*
// JoomlaWorks "AllVideos" Plugin for Joomla! 1.5.x - Version 3.3
// Copyright (c) 2006 - 2010 JoomlaWorks Ltd. All rights reserved.
// Released under the GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
// More info at http://www.joomlaworks.gr
// Designed and developed by the JoomlaWorks team
// *** Last update: February 18th, 2010 ***
*/

if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

ob_start ("ob_gzhandler"); 
header("Content-type: text/javascript; charset: UTF-8"); 
header("Cache-Control: must-revalidate"); 
header("Expires: ".gmdate("D, d M Y H:i:s", time() + 60 * 60)." GMT");

// Includes
include(dirname( __FILE__ ).DS."players".DS."wmvplayer".DS."silverlight.js");
echo "\n\n";
include(dirname( __FILE__ ).DS."players".DS."wmvplayer".DS."wmvplayer.js");
echo "\n\n";
include(dirname( __FILE__ ).DS."players".DS."quicktimeplayer".DS."AC_QuickTime.js");
echo "\n\n";
include(dirname( __FILE__ ).DS."jw_allvideos.js");
echo "\n\n";

ob_flush();
