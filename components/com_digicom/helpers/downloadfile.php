<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

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

// TODO : PHP property visibility update

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
