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

jimport ("joomla.aplication.component.model");


class DigiComAdminModelLanguage extends DigiComModel
{

	function __construct () {
		parent::__construct();
		$cids = JRequest::getVar('cid', 0, '', 'array');
		$this->setId((int)$cids[0]);
	}


	function setId($id) {
		$this->_id = $id;
		$this->_installpath = JPATH_COMPONENT.DS."plugins".DS;
		$this->_plugin = null;
	}


	function getlistLanguages () {
		if (empty ($this->_plugins)) {
			$sql = "select * from #__digicom_languages";
			$this->_languages = $this->_getList($sql);

		}
		return $this->_languages;
	}

	function getlistMLanguages () {
		$dirname = JPATH_SITE.DS."language".DS;
		$dir = opendir(JPATH_SITE.DS."language".DS);
		$res = array();
		while ($f = readdir($dir)) {
			if ($f != "." && $f != ".." && is_dir($dirname.$f)) {
				$subdir = opendir ($dirname.$f);
				while ($ff = readdir($subdir)) {
					if (strpos($ff, "mod_digicom") !== false) {
//						echo $ff."<br />";
						$x = new stdClass;
						$x->id = 0;
						$x->name = $f;
						$x->fefilename = $ff;
						$x->befilename = $ff;
						$res[] = $x;

					}

				}
				closedir($subdir);
			}

		}
		closedir($dir);

		return $res;

	}

	function getLanguage () {
		$task = JRequest::getVar("task");
		if (intval($this->_id) > 0) {
			$db = JFactory::getDBO();
			$sql = "select * from #__digicom_languages where id=".$this->_id;
			$db->setQuery($sql);
			$lang = $db->loadObjectList();
			$lang = $lang[0];
			$file = $lang->fefilename;
			$code = $lang->name;

			$respathfe = JPATH_ROOT.DS."language".DS.$code.DS.$file;
			$respathbe = JPATH_ROOT.DS."administrator".DS."language".DS.$code.DS.$file;
//			$task = JRequest::getVar("task", "", "request");
			if ($task == "editFE") $lang->path = $respathfe;
			else $lang->path = $respathbe;
			$lang->data = implode ("", file ($lang->path));
			$lang->type = $task;
		} elseif ($task == "editML") {
			$l = JRequest::getVar("cid", '','','array');
			$l = strtolower($l[0]);
			$x = explode (".", $l);
			$x = $x[0];
			$x = explode("-", $x);
			$x[1] = strtoupper($x[1]);
			$x[0] = strtolower($x[0]);
			$code = implode("-", $x);
			$l = explode (".", $l);
			$l[0] = implode("-", $x);
			$l = implode(".", $l);

			$respathfe = JPATH_ROOT.DS."language".DS.$code.DS.$l;
			$lang = new stdClass;
			$lang->path = $respathfe;
			$lang->data = implode ("", file ($lang->path));
			$lang->type = $task;
			$lang->id = strtolower($l);
		}
		return $lang;

	}

	function store () {

		$db = JFactory::getDBO();
		$id = JRequest::getVar("id", "", "request");
		if (intval($id) < 1) {
			$l = strtolower($id);
			$x = explode (".", $l);
			$x = $x[0];
			$x = explode("-", $x);
			$x[1] = strtoupper($x[1]);
			$x[0] = strtolower($x[0]);
			$code = implode("-", $x);
			$l = explode (".", $l);
			$l[0] = implode("-", $x);
			$l = implode(".", $l);
			$path = JPATH_ROOT.DS."language".DS.$code.DS.$l;
			$text = JRequest::getVar('langfiledata', '', 'post', 'string', JREQUEST_ALLOWRAW);
			$f = fopen ($path, "w");

			fwrite ($f, stripslashes($text)); //die ("cant write");
			fclose ($f);
			chmod($path, 0755);

		} else {

			$sql = "select * from #__digicom_languages where id=".$id;
			$db->setQuery($sql);
			$lang = $db->loadObjectList();
			$lang = $lang[0];
			$file = $lang->fefilename;
			$code = $lang->name;

			$respathfe = JPATH_ROOT.DS."language".DS.$code.DS.$file;
			$respathbe = JPATH_ROOT.DS."administrator".DS."language".DS.$code.DS.$file;
			$type = JRequest::getVar("type", "", "request");
			if ($type == "editFE") $path = $respathfe;
			else $path = $respathbe;

			$text = JRequest::getVar('langfiledata','','post', 'string', JREQUEST_ALLOWRAW);
			$f = fopen ($path, "w");

			fwrite ($f, stripslashes($text)); //die ("cant write");
			fclose ($f);
			chmod($path, 0755);
		}

		return true;

	}


	function copyLangFile ($path, $type, $code, $file) {
		$respath = "";
		if ($type == "fe") {
			$respath = JPATH_ROOT.DS."language".DS.$code.DS;
		} elseif ($type == "be") {
			$respath = JPATH_ROOT.DS."administrator".DS."language".DS.$code.DS;
		}
		if (!file_exists($respath) ) return -2;
		if (file_exists($respath.$file)) return -1;
//echo $path." ".$respath.$file."<br />";
		JFile::copy($path, $respath.$file, '');
//		@copy($path, $respath.$file);


//		$menufile = str_replace (".ini", ".menu.ini", $file);
//echo $menufile;die;
//		JFile::copy($path, $respath.$menufile, "");

//		@copy($path, $respath.$menufile);
		return 1;

	}

	function installLanguage($path, $language_file = '') {
		$db = JFactory::getDBO();

		$language_file = trim ($language_file);
		if (strlen($language_file) < 1) return JText::_('MODLANGNOUPLOAD');

		$ext = substr ($language_file, strrpos($language_file, ".") + 1);
		if ($ext != 'zip') return JText::_('MODLANGNOZIP');

		jimport('joomla.filesystem.archive');
		if (!JArchive::extract($path.$language_file, $path)) {
			return JText::_('MODLANGERREXTRACT');
		}
		$dir = opendir ($path);
		if (!file_exists($path."install")) return JText::_("MODLANGMISSINSTALL");
		$install = parse_ini_file($path."install");
		if (count ($install) < 1) return JText::_("MODLANGCORRUPTEDINSTALL");
		$lang_code = explode (" ", $install['langcode']);
		foreach ($lang_code as $code ) {
			$be = 0;
			$fe = 0;

			$fe_path = $path."fe".DS.$code.DS.$code.".com_digicom.ini";
			$lang_file = $code.".com_digicom.ini";
			$be_path = $path."be".DS.$code.DS.$code.".com_digicom.ini";
			if (!file_exists($fe_path)) $fe = 0; else $fe = 1;
			if (!file_exists($be_path)) $be = 0; else $be = 1;
//			echo $be." ".$be_path." ".$fe." ".$fe_path;die;
			if ($be && $fe ) {
				$query = "select count(*) from #__digicom_languages where fefilename='".$lang_file."' or befilename='".$lang_file."'";
				$db->setQuery($query);
				$isthere = $db->loadResult();
				if ($isthere) {
					return JText::_('MODLANGALLREDYEXIST');//
				} else {
					$fe = 0;
					$be = 0;
					$fe = $this->copyLangFile($fe_path, "fe", $code, $lang_file);
					if ($fe) {
						$be = $this->copyLangFile($be_path, "be", $code, $lang_file);
					}
//die;
					if (!$fe || !$be) {
						return JText::_("MODLANGCOPYERR");
					} else if ($fe == -1 || $be == -1) {
						return JText::_("MODLANGCANTCOPY");
					} else if ($fe < 0 || $be < 0) {
						return JText::_("MODLANGFOLDERNOTEXITST");
					} else {
						$sql = "insert into #__digicom_languages(`name`, `fefilename`, `befilename`) values ('".$code."', '".$lang_file."', '".$lang_file."')";
						$db->setQuery($sql);
						$db->query();

					}

				}
			} else {
				return JText::_("MODLANGMISSLANGFILE");
			}

		}
		$install_path = $this->_installpath;
		JFile::copy ($path.$install['filename'], $install_path.$install['filename']);

		return JText::_("MODLANGSUCCESSFUL");
	}

	function upload() {

		jimport('joomla.filesystem.file');
		$file = JRequest::getVar('langfile', array(), 'files');
		$install_path = JPATH_ROOT.DS."tmp".DS."digicomlanguage".DS;
		Jfolder::create ($install_path);
		if (JFile::copy($file['tmp_name'], $install_path.$file['name'], '')) {

			$res = $this->installLanguage($install_path, $file['name']);
			JFolder::delete ($install_path);
		} else {
			$res = JText::_('MODLANGCOPYERR');
		}

		return $res;

	}


	function delete () {
		$cids = JRequest::getVar('cid', array(0), 'post', 'array');
		jimport('joomla.filesystem.file');
		$db = JFactory::getDBO();

		foreach ($cids as $cid) {
			$sql = "select name,fefilename from #__digicom_languages where id=".$cid;
			$db->setQuery($sql);
			$tmp = $db->loadObjectList();
			$file = $tmp[0]->fefilename;
			$code = $tmp[0]->name;
			$respathfe = JPATH_ROOT.DS."language".DS.$code.DS;
			$respathbe = JPATH_ROOT.DS."administrator".DS."language".DS.$code.DS;
			$menufile = str_replace (".ini", ".menu.ini", $file);
			if ((JFile::delete($respathfe.$file)) && (JFile::delete($respathfe.$menufile))
					&& (JFile::delete($respathbe.$file)) && (JFile::delete($respathbe.$menufile))) {

				$sql = "delete from #__digicom_languages where id=".$cid;
				$db->setQuery($sql);
				$db->query();
			}
		}
		return true;
	}

}

?>