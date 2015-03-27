<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 398 $
 * @lastmodified	$LastChangedDate: 2013-11-04 05:07:10 +0100 (Mon, 04 Nov 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license
*/

defined ('_JEXEC') or die ("Go away.");
/*
 * Class has simple interface to download any file from a server
 * without displaying the location of the file
 *
 * Author: Viatcheslav Ivanov, E-Witness Inc., Canada;
 * mail: ivanov@e-witness.ca;
 * web: www.e-witness.ca; www.coolwater.ca; www.strongpost.net;
 * version: 1.1 /08.19.2002
 *
 */


class DigiComSiteHelperDownloadFile {
	var $df_path = "";
	var $df_contenttype = "";
	var $df_contentdisposition = "";
	var $df_filename = "";

	function __construct($df_path, $df_contenttype = "application/octet-stream", $df_contentdisposition = "attachment", $df_filename = "") {
		$this->df_path = $df_path;
		$this->df_contenttype = $df_contenttype;
		$this->df_contentdisposition = $df_contentdisposition;
		$this->df_filename = ($df_filename)? $df_filename : basename($df_path);
	}

	// check is specified file exists?
	function df_exists() {
		if(file_exists($this->df_path)) return true;
		return false;
	}

	// get file size
	function df_size() {
		if($this->df_exists()) return filesize($this->df_path);
		return false;
	}

	// return permission number for user 'other'
	function df_permitother() {
		return substr(decoct(fileperms($this->df_path)),-1);
	}

	// download file
	function df_download() {
		if($this->df_exists() && $this->df_permitother() >= 4) {
			header("Content-type: ".$this->df_contenttype);
			header("Content-Disposition: ".$this->df_contentdisposition."; filename=\"".$this->df_filename."\"");
			header("Content-Length: ".$this->df_size());

			$fp = readfile($this->df_path, "r");
			return $fp;
		}
		return false;
	}

}
