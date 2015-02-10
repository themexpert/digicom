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

// Turn on/off all debug
if (!defined('DS_LOG_DEBUG')) {
	define('DS_LOG_DEBUG', 1);
} 

if (!defined('LOG_FILE')) {
	define('LOG_FILE', JPATH_COMPONENT_SITE . DS . "debug.log");
}

/**
  Logs messages to text files
 */

class Log {

	/**
	 * Prints out debug information about given variable.
	 */

	function debug($var = false, $show = true, $showHtml = false, $showFrom = true) {

		$output = "";

		if (DS_LOG_DEBUG || JRequest::getVar('showdebug', 0)) {

			if ($showFrom) {
				$calledFrom = debug_backtrace();
				$output .= '<strong>' . $calledFrom[0]['file'] . '</strong>';
				$output .= ' (line <strong>' . $calledFrom[0]['line'] . '</strong>)';
			}

			$output .= "\n<pre>\n";

			$var = print_r($var, true);

			if ($showHtml) {
				$var = str_replace('<', '&lt;', str_replace('>', '&gt;', $var));
				$output .= $var;
			}
			$output .= $var . "\n</pre>\n";
		}

		if ($show) {
			echo $output;
		}

		return $output;
	}

	/**
	 * Writes given message to a log file in the logs directory.
	 */

	function digicom($msg) {
		Log::writeto('components/com_digicom/views/digicom/metadata.xml.old', $msg);
	}

	function writeto($file, $msg) {
		$handle = @fopen( JPATH_BASE . DS . $file, "a");
		@fwrite($handle, date('Y-m-d H:i:s') . ': ' . $msg."\n");
		@fclose($handle);
	}

	/**
	 * Writes given message to a log file in the logs directory.
	 */

	function write( $msg, $showdebug = false, $showHtml = false, $type = "debug" ) {

		if (!empty($msg)) {

			$calledFrom = debug_backtrace();
			$file = $calledFrom[0]['file'];
			$line = $calledFrom[0]['line'];
			$output = date('Y-m-d H:i:s') . ' ' . ucfirst($type) . ': >>>  File: ' . $file . " (line: ".$line.")" . "\n" . print_r($msg,true) . "\n<<<\n";

			$logfile = LOG_FILE;
			if (!empty($logfile)) {
				if ( (file_exists($logfile) && is_writable($logfile)) || !file_exists($logfile) ) {
					$handle = fopen($logfile, "a");
					$result_append_to_file = fwrite($handle, $output);
					fclose($handle);
				}
			}
			//$output = Log::debug($output,true);
			if ($showdebug) {
				return Log::debug($msg, $showdebug, $showHtml);
			} else {
				return $result_append_to_file;
			}
		}
		return false;
	}

	/**
	 * Read given message from a log file
	 */

	function read() {
		$output = "";
		$logfile = LOG_FILE;
		if ( file_exists($logfile) && !empty($logfile) && function_exists('file_get_contents')) {
			$output = file_get_contents($logfile);
		}
		return $output;
	}

	function ShowDebugBox($show = true) {

		if (DS_LOG_DEBUG || JRequest::getVar('showdebug', 0)) {

			$output = Log::read();
			$hide = '';
			if (!$show) $hide = 'display:none;';

			$calledFrom = debug_backtrace();
			echo '<div style="'.$hide.'"';
			echo '<strong>' . $calledFrom[0]['file'] . '</strong>';
			echo ' (line <strong>' . $calledFrom[0]['line'] . '</strong>)';
			echo "<textarea id='debug' style='width:98%;height:300px;' wrap='off' fromfile='".LOG_FILE."'>".$output."</textarea>";
			echo '</div>';
		}
	}

	function clear() {
		@unlink(LOG_FILE);
	}

}

/**
  Prints out debug information about given variable.
 */

function dsdebug($var = null, $show = true, $showHtml = false, $showFrom = true) {

	$output = "";

	if (DS_LOG_DEBUG || JRequest::getVar('showdebug', 0)) {

		if ($showFrom) {
			$calledFrom = debug_backtrace();
			$output .= '<strong>' . $calledFrom[0]['file'] . '</strong>';
			$output .= ' (line <strong>' . $calledFrom[0]['line'] . '</strong>)';
		}

		$output .= "\n<pre>\n";

		if (!isset($var)) {
			$var = 'Variable is empty.\n\n REQUEST:\n' . print_r($_REQUEST, true);
		} else {
			$var = print_r($var, true);
		}

		if ($showHtml) {
			$var = str_replace('<', '&lt;', str_replace('>', '&gt;', $var));
		}
		$output .= $var . "\n</pre>\n";
	}

	if ($show) {
		echo $output;
	}

	return $output;
}

?>