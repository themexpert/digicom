<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 399 $
 * @lastmodified	$LastChangedDate: 2013-11-04 05:29:41 +0100 (Mon, 04 Nov 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

defined ('_JEXEC') or die ("Go away.");

/*
 * Instantiating new object of our plugin class.
 */

/*
 * Plugin class. Should contain all necessary methods for handling
 * payment routins for given paymnt system.
 * In general such class should have following methods:
 * 	getBEData - retuns an array with be plugin data (header, payment option name 
 * 				  and its value).
 *	getFEData - returns html output for fe payment form.
 *  notify - processes responce from payment system to determine if
 * 			   transaction was successfull.
 * selfile - required for proper plugin handling. In current version should return 
 * 			   filename of plugin.
 *	  
 * 
 */
 
class ioncube {
	function selfile () {
			return "ioncube_encoder.php";
	}
	function type() {
		return ("encoding");
	}

	function getEncPath () {
/*		global $database;
		$query = "select value from #__digicom_plugins where name='".get_class($this)."'";
		$database->setQuery($query);
		$val = $database->loadResult();
		return $val;
*/

	}

	function getBEData ($plugin_conf) {
/*
		global $database;
		$query = "select value from #__digicom_plugins where name='".get_class($this)."'";
		$database->setQuery($query);
		$val = $database->loadResult();

		$sampleform = array(
			"header" => _IONCUBE_ENCODER_MESSAGE,
			"header1" => array(''),
			"name" => array(get_class($this)."_account[]"),
			"value" => explode(" ",$val),

			"isdef"=> get_class($this)."_def"
		);
*/
		return $sampleform;

	}

	function getFEData ( $items, $total, $configs, $redirect, $profile) {


//		return get_class($this);
	}

	function get_info () {
		$info = JText::_("Ioncube encoder plugin.");
		return $info;

	}

	/*
	 * Encoding funciton
	 */
	function performEncoding ($file, $licenseFile, $passphrase, $srcPath, $dstPath, $now, $plugin_conf, $phpversion) {
print_r($plugin_conf);
		$ad_path = $plugin_conf->values[0];//$plugin_conf->ioncube_install_path;//$this->getEncPath();//"ioncube_encoder5_6.5";

		if (trim($passphrase) == '') $passphrase = 'test';
//echo ($srcPath.$file."<br/>");
//echo ($dstPath."<br/>");


//		$command = $mosConfig_absolute_path."/ioncube/".$ad_path."/ioncube_encoder --with-license $licenseFile"
		if ($phpversion != 5) $command = $ad_path."/ioncube_encoder --with-license $licenseFile";
				else $command = $ad_path."/ioncube_encoder5 --with-license $licenseFile";
		$command .= " --passphrase $passphrase --license-check auto "
//			." --passphrase $passphrase --license-check auto "
//			.$mainframe->getCfg( 'absolute_path' )."/components/com_digicom/tmp/".$now."/origin/$file "
//			." -o ".$mainframe->getCfg( 'absolute_path' )."/components/com_digicom/tmp/".$now."/encoded/$method/$file";
			.$srcPath.$file
			." -o ".$dstPath.$file;
		$result = exec( $command );
//echo ($command."<br/>");
	}



	/*
	 * License file creator.
	 */
	function genLicense($domain, $devdomain, $subdomains_text, $licenseFile, $passphrase="test", $trial_period = "", $outDir, $plugin_conf) {
		if (trim ($passphrase) == '' ) $passphrase="test";
		$subdomains_text = explode (",",$subdomains_text);
		foreach ($subdomains_text as $i=>$v){
				if ($v != '') {
					//$subdomains_text[] = "www.".$v;
				} else unset($subdomains_text[$i]);

		}
		$allowed_server = array ();
		if (strlen(trim($domain)) > 1) {
		 	$allowed_server[] = $domain;
			$allowed_server[] = "www.".$domain;
		}

		if (strlen(trim($devdomain)) > 1) {
		 	$allowed_server[] = $devdomain;
			$allowed_server[] = "www.".$devdomain;
		}
//print_r($plugin_conf);
		$subdomains_text = implode (",",$subdomains_text);
		$ad_path = $plugin_conf->values[0];
//		$ad_path = "ioncube_encoder5_6.5";
//		$command = $mosConfig_absolute_path."/ioncube/".IONCUBE_VER."/make_license --passphrase $passphrase --allowed-server $domain,www.$domain,localhost";
//		$command = $mosConfig_absolute_path."/ioncube/".$ad_path."/make_license --passphrase $passphrase --allowed-server $domain,www.$domain,localhost";
//		$command = $ad_path."/make_license --passphrase $passphrase --allowed-server $domain,www.$domain,$devdomain,www.$devdomain,localhost";
			$command = $ad_path."/make_license --passphrase $passphrase --allowed-server localhost,".implode(',', $allowed_server);//$domain,www.$domain,$devdomain,www.$devdomain,localhost";
		if ( $subdomains_text )
			$command.= ",$subdomains_text";
		$command.= "@";
		$command.= " -o ".$outDir.$licenseFile;
//echo ($command);die;
		exec ( $command );
	}

	function genLoader ($phpVersions, $platforms, $outDir) {
		$platform_options = $this->getPlatforms();


//		$ad_path = "loaders";
		$ad_path = JPATH_ROOT."/loaders/ioncube/";//$this->getEncPath();//"ioncube_encoder5_6.5";

		$loaderFile = $this->getLoader();//"ioncube_loader.zip";

		@unlink ($outDir.$loaderFile);

		define( 'PCLZIP_TEMPORARY_DIR', JPATH_ROOT.DS.'administrator/components/digicom_product_uploads/tmp/' );
		require_once (JPATH_ROOT.DS."administrator".DS."includes".DS."pcl".DS."pclzip.lib.php");
		$curdir = getcwd ();
		//chdir ( JPATH_ROOT."/loaders/temp/"  );
//$r = exec ("pwd");
//echo ($r."<br/>");

		$loader = new PclZip($outDir.$loaderFile);
//echo $outDir.$loaderFile;
//		$pathToLoaders = $mosConfig_absolute_path."/ioncube/".$ad_path."/";//.$platform_options[$platform]['value'];
		$pathToLoaders = $ad_path;//.$platform_options[$platform]['value'];
	//	$loader->create ( $pathToLoaders."/ioncube-loader-helper.php", PCLZIP_OPT_REMOVE_PATH, $pathToLoaders );
//echo ("<br/>a:".$loader->errorInfo(true));
		$loader->add ( $pathToLoaders."README.txt", PCLZIP_OPT_REMOVE_PATH, $pathToLoaders);
//echo ("<br/>b:".$loader->errorInfo(true));
			$loader->add ( $pathToLoaders."ioncube-loader-helper.php", PCLZIP_OPT_REMOVE_PATH, $pathToLoaders);
//echo ("<br/>c:".$loader->errorInfo(true));
			$loader->add ( $pathToLoaders."ioncube-encoded-file.php", PCLZIP_OPT_REMOVE_PATH, $pathToLoaders);
//echo ("<br/>d:".$loader->errorInfo(true));
			$loader->add ( $pathToLoaders."LICENSE.txt", PCLZIP_OPT_REMOVE_PATH, $pathToLoaders);
			$loader->add ( $pathToLoaders."ijoomla_test.php", PCLZIP_OPT_REMOVE_PATH, $pathToLoaders);
//echo ("<br/>e:".$loader->errorInfo(true));
		$loaders_added = 0;
//print_r($loader);
		foreach ( $platforms as $platform ) {

			$dir = opendir ( $pathToLoaders );

 			while ($f = readdir ($dir) ) {
	//echo ($pathToLoaders.$f."<br/>");

				if ( is_dir($pathToLoaders.$f) ) {
				$dir1 = opendir($pathToLoaders.$f);

					while ( $file = readdir ( $dir1 ) ) {
//echo ($file);
//						$r = preg_match( "/ioncube_loader_{$platform_options[$platform]['prefix']}_{$phpVersions}(.*)/i", $file );
						if($f === $platform_options[$platform]['value']){
//echo ($f."<br>Prefix : ".$platform_options[$platform]['value']."<br>");

							$r = preg_match( "/ioncube_loader_{$platform_options[$platform]['prefix']}_{$phpVersions}(.*)/", $file );


							if ( $r ) {

							//add this file to the Zip file
								$x = $loader->add($pathToLoaders.$f."/".$file, PCLZIP_OPT_REMOVE_PATH, $pathToLoaders.$f."/");
//if ($x < 0) echo $loader->errorReport(true);
								$loaders_added++;
							}
						}
					}
				}
			}

		}

//	die;
		chdir ($curdir);
		if ($loaders_added < 1) echo ('<script language="javascript">alert ("'.(JText::_("No loader available for selected platform/version")).'"); history.go(-1);</script>');
	}

	function getLoader () {
		$loaderFile = "ioncube_loader.zip";
			return $loaderFile;

	}

	function getPlatforms () {

			$platform_options = array(
			array('title' => 'Windows','value' => 'win_x86','prefix' => 'win'),
			array('title' => 'Linux (i386-i686)','value' => 'lin_x86','prefix' => 'lin'),
			array('title' => 'Linux (_64)','value' => 'lin_x86-64','prefix' => 'lin'),
			array('title' => 'Linux PPC (i386-i686)','value' => 'lin_ppc','prefix' => 'lin'),
			array('title' => 'Linux PPC (_64)','value' => 'lin_ppc64','prefix' => 'lin'),
			array('title' => 'Linux on sparc','value' => 'lin_sparc','prefix' => 'lin'),
			array('title' => 'BSD (i386-i686)','value' => 'bsd_x86','prefix' => 'bsd'),
			array('title' => 'Dragonfly','value' => 'dra_x86','prefix' => 'dra'),
			array('title' => 'FreeBSD 4 (i386-i686)','value' => 'fre_4_x86','prefix' => 'fre'),
			array('title' => 'FreeBSD 6 (i386-i686)','value' => 'fre_6_x86','prefix' => 'fre'),
			array('title' => 'FreeBSD 6 (AMD _64)','value' => 'fre_AMD64','prefix' => 'fre'),
			array('title' => 'Net BSD (i386-i686)','value' => 'net_x86','prefix' => 'net'),
			array('title' => 'Net BSD (_64)','value' => 'net_x86-64','prefix' => 'net'),
			array('title' => 'Open BSD 3.7 (i386-i686-_64)','value' => 'ope_3.7_x86-64','prefix' => 'ope'),
			array('title' => 'Open BSD 3.9 (i386-i686-_64)','value' => 'ope_3.9_x86-64','prefix' => 'ope'),
			array('title' => 'Open BSD 3.8 (i386-i686)','value' => 'ope_3.9_x86','prefix' => 'ope'),
			array('title' => 'SunOS (Sparc)','value' => 'sun_sparc','prefix' => 'sun'),
			array('title' => 'SunOS (Intel i386-i686)','value' => 'sun_x86','prefix' => 'sun'),
			array('title' => 'Mac OS X (PowerPC)','value' => 'dar_ppc','prefix' => 'dar'),
			array('title' => 'Mac OS X (Intel i386-i686)','value' => 'dar_i386','prefix' => 'dar'),
		);
		return $platform_options;
	}


};
?>