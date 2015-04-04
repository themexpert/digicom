<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

jimport( "joomla.aplication.component.model" );


class DigiComModelPlugin extends DigiComModel {
	var $_plugins;
	var $_plugin = null;
	var $_id = null;
	var $allowed_types = array( "payment", "encoding", "tax", "shipping" );
	var $req_methods = array( "getFEData", "getBEData" );
	var $_installpath;
	var $plugins_loaded = 0;
	var $default_payment = null;
	var $payment_plugins = null;

	function __construct() {
		parent::__construct();
		$cids = JRequest::getVar( 'cid', 0, '', 'array' );
		$this->setId( (int) $cids[0] );
		$this->loadPlugins();
	}

	function setId( $id ) {
		$this->_id          = $id;
		$this->_installpath = JPATH_ROOT . DS . "administrator" . DS . "components" . DS . "com_digicom" . DS . "plugins" . DS;
		$this->_plugin      = null;
	}


	function getlistPlugins() {
		if ( empty ( $this->_plugins ) ) {
			$sql            = "select * from #__digicom_plugins";
			$this->_plugins = $this->_getList( $sql );

		}

		return $this->_plugins;

	}

	function loadPlugins() {
		if ( $this->plugins_loaded == 1 ) {
			return;
		}

//		global $mosConfig_absolute_path, $database;
		$plugins = $this->getlistPlugins();
//		$plugin_path = $mosConfig_absolute_path.'/administrator/components/com_digicom/plugins/';
		foreach ( $plugins as $plugin ) {
			$this->registerPlugin( $plugin->filename, $plugin->classname );
			if ( $plugin->published == '1' ) {
				//add published plugins to arrays respective to
				//their types
				if ( $plugin->type == 'payment' ) {
					//$digistor_pay_plugins[] = $plugin;
					$this->payment_plugins[ $plugin->name ] = $plugin;
					if ( $plugin->def == 'default' || $plugin->def == "1" ) {
						$this->default_payment = $plugin; //also select default gateway
					}
				}
				if ( $plugin->type == 'encoding' ) {
					$this->encoding_plugins[ $plugin->name ] = $plugin;
				}

				if ( $plugin->type == 'tax' ) {
					$this->tax_plugins[ $plugin->name ] = $plugin;
					if ( $plugin->def == '1' || $plugin->def == "default" ) {
						$this->default_tax = $plugin;
					}

				}
				if ( $plugin->type == 'shipping' ) {
					$this->shipping_plugins[ $plugin->name ] = $plugin;
					if ( $plugin->def == 'default' || $plugin->def == '1' ) {
						$this->default_shipping = $plugin;
					}

				}
			}
		}
		$this->plugins_loaded = 1;

		return;
	}


	function getPlugin( $id = 0 ) {
		if ( empty ( $this->_plugin ) || $id > 0 ) {
			if ( $id > 0 ) {
				$this->_plugin->id = $id;
			}
			$this->_plugin = $this->getTable( "Plugin" );
			$this->_plugin->load( $this->_id );
			$this->_plugin->instance = $this->registerPlugin( $this->_plugin->filename, $this->_plugin->classname );
//			$this->_plugin->instance = $this->registerPlugin($this->_plugin->filename, $this->_plugin->classname);
			$db  = JFactory::getDBO();
			$sql = "select setting,description,value from #__digicom_plugin_settings where pluginid='" . $this->_plugin->id . "'";
			$db->setQuery( $sql );
			$conf            = $db->loadObjectList();
			$config          = new stdClass;
			$config->headers = array();
			$config->values  = array();
			foreach ( $conf as $value ) {
				$config->headers[]               = $value->setting;
				$config->values[]                = $value->value;
				$config->descrs[]                = $value->description;
				$config->data[ $value->setting ] = $value->value;
			}
			$this->_plugin->config = $config;
		}

		return $this->_plugin;

	}

	function getEncoders() {
		$db  = JFactory::getDBO();
		$sql = "select id from #__digicom_plugins where type='encoding'";
		$db->setQuery( $sql );
		$encs = $db->loadObjectList();


		$this->_plugins = array();
		foreach ( $encs as $enc ) {
			$this->_id        = $enc;
			$this->_plugin    = null;
			$this->_plugins[] = $this->getPlugin();

		}


		return $this->_plugins;
	}


	function registerPlugin( $filename, $classname ) {
		$install_path = $this->_installpath;//JPATH_COMPONENT.DS."plugins".DS;
		if ( ! file_exists( $install_path . $filename ) ) {
			return 0;//_NO_PLUGIN_FILE_EXISTS;
		}

		require_once( $install_path . $filename );
		$plugin = new $classname;//$this->plugins[$classname];
		if ( ! is_object( $plugin ) ) {
			return 0;
		}
		foreach ( $this->req_methods as $method ) {
			if ( ! method_exists( $plugin, $method ) ) {
				return 0;
			}
		}
		//plugin passed basic checks - add it to registered plugins
		//$this->plugin_instances[$classname] = $plugin;
		if ( isset( $this->_plugins[ $classname ] ) ) {
//echo $classname;
//print_R($plugin);
			$this->_plugins[ $classname ]->instance = $plugin;

		} else {
			$this->_plugins[ $classname ]           = new stdClass;
			$this->_plugins[ $classname ]->instance = &$plugin;
		}

		return $plugin;

	}


	function getPluginOptions( $selected = '' ) {
//		$defcon = get_defined_constants();
		$content = '<select name="payment_type" style="width:15.5em">';
		if ( isset( $this->payment_plugins ) ) {
			if ( count( $this->payment_plugins ) > 0 ) {
				foreach ( $this->payment_plugins as $plugin ) {
					$content .= '<option value="' . $plugin->classname . '"';
					if ( strlen( $selected ) > 0 && $selected == $plugin->classname ) {
						$content .= ' selected="selected" ';
					} else if ( strlen( $selected ) < 1 && ( isset( $this->default_payment->classname ) ) && ( $plugin->classname == $this->default_payment->classname ) ) {
						$content .= ' selected="selected" ';
					}

					$content .= '>' . ( JText::_( "DS" . strtoupper( $plugin->name ) ) ) . '</option>';
				}
			}
		}
		$content .= "</select>";

		return $content;
	}

	function getListPluginCurrency() {
		$db  = JFactory::getDBO();
		$sql = "select c.*, p.name as pluginame from #__digicom_currencies c, #__digicom_plugins p where p.id=c.pluginid and p.published=1";

		$db->setQuery( $sql );
		$plugs = $db->loadObjectList();
		$res   = array();
		foreach ( $plugs as $plug ) {
			$res[ $plug->pluginame ][ $plug->currency_name ] = $plug->currency_full;
		}

		return $res;

	}

	function getEncoder( $method ) {
		$handler = - 1;
		if ( count( $this->encoding_plugins ) < 1 ) {
			return ( - 1 );//do not to forget place errorcode switch in calling file so user
			// may know if encoding was successful or not.
		} else {
			foreach ( $this->encoding_plugins as $plugin ) {
				if ( $method == $plugin->classname ) {
					$this->_id = $plugin->id;
					$handler   = $this->getPlugin( $this->_id );

//					$handler = $this->_plugin[$plugin->classname]->instance; //we found handler for encoding
					break;
				}
			}
		}

		return $handler;
	}

	function encodeZipFile( $method, $phpversion, $srcDir, $srcFile, $tmpDir, $dstDir, $files, $mainFile, $passphrase, $trial, $now, $licenseFile = "license.txt" ) {
//		global $my, $digistor_enc_plugins, $mainframe, $dir, $r, $mosConfig_absolute_path;  

		jimport( "joomla.filesystem.file" );
		define( 'PCLZIP_TEMPORARY_DIR', JPATH_ROOT . DS . 'administrator/components/digicom_product_uploads/tmp/' );
		require_once( JPATH_ROOT . DS . "administrator" . DS . "includes" . DS . "pcl" . DS . "pclzip.lib.php" );
		@unlink( $dstDir . $srcFile );
		@unlink( $dstDir . $mainFile );
		@unlink( $tmpDir . $mainFile );

		$srcZip = new PclZip( $srcDir . $srcFile );


		$x = $srcZip->extract( PCLZIP_OPT_BY_NAME, $mainFile, PCLZIP_OPT_PATH, $tmpDir );
		if ( $x == 0 ) {
			die ( $srcZip->errorInfo( true ) );
		}

		$mainZip = new PclZip( $tmpDir . $mainFile );

		if ( $mainZip->extract( PCLZIP_OPT_PATH, $tmpDir . "1/" ) == 0 ) {
			//		die  ("err1:".$mainZip->errorInfo(true));
		}
		if ( $mainZip->extract( PCLZIP_OPT_PATH, $dstDir ) == 0 ) {
			//		die  ("err2:".$mainZip->errorInfo(true));
		}


		$handler = $this->getEncoder( $method );
		if ( ! is_object( $handler ) ) {
			return ( - 2 );
		} //no handler found - internal error or hijack attempt

		foreach ( $files as $file ) {
			$file = trim( $file );
			//$pos = strrpos($file,"/");
			//if ($pos) $purefile = substr ($file,$pos+1, strlen($file)); else $purefile = $file;
			$handler->performEncoding( $file, $licenseFile, $passphrase, $tmpDir . "1/", $dstDir, $now, $this->encoding_plugins[ $method ] ); // plugin does all required

		}

		$i      = 0;
		$curdir = getcwd();
		chdir( $dstDir );

		foreach ( $files as $file ) {
			$file = trim( $file );
			$mainZip->delete( PCLZIP_OPT_BY_NAME, $file );
			$r = $mainZip->add( $dstDir . $file, PCLZIP_OPT_REMOVE_PATH, $dstDir );

			//		if ($r == 0 && i == 0) $mainZip->create ($dstDir.$file, PCLZIP_OPT_REMOVE_PATH, $dstDir);
			++ $i;
		}

//		$this->recursive_remove_directory($dstDir, TRUE);
		if ( file_exists( $dstDir ) ) {
			JFolder::delete( $dstDir );
		}

		JFolder::create( $dstDir );

		JFile::copy( $srcDir . $srcFile, $dstDir . $srcFile );

		$srcZip = new PclZip ( $dstDir . $srcFile );

		$v_list = $srcZip->delete( PCLZIP_OPT_BY_NAME, $mainFile );

		$res = $srcZip->add( $tmpDir . $mainFile, PCLZIP_OPT_REMOVE_PATH, $tmpDir );

		if ( $res == 0 ) {
			$res = $srcZip->create( $tmpDir . $mainFile, PCLZIP_OPT_REMOVE_PATH, $tmpDir );
		}
		chdir( $curdir );

//		$this->recursive_remove_directory($tmpDir, TRUE);

		JFolder::delete( $tmpDir );
	}


	/*
	 * License file generator - converted code from original digicom
	*/
	function genLicenseFile( $method, $zipFile, $subZipFile, $domain = "localhost", $devdomain = "localhost", $subdomains, $licenseFile, $passphrase, $trial_period = "", $outDir ) {

		//gen the license file
		foreach ( $subdomains as $i => $v ) {

			if ( isset( $subdomains[ $i ] ) && trim( $subdomains[ $i ] ) != '' ) {
				$subdomains[ $i ] = preg_replace( "/^www\./", "", $subdomains[ $i ] );
				$subdomains[ $i ] = str_replace( "." . $domain, "", $subdomains[ $i ] );
				$subdomains[ $i ] = str_replace( $domain, "", $subdomains[ $i ] );

				if ( trim( $subdomains[ $i ] ) != '' ) {
					$tmp = trim( $subdomains[ $i ] );

					if ( strlen( trim( $domain ) ) > 0 ) {
						$subdomains[ $i ] = trim( $subdomains[ $i ] ) . "." . $domain;
						$subdomains[]     = "www." . trim( $subdomains[ $i ] );// . "." . $domain;
					} else {
						unset ( $subdomains[ $i ] );
					}
					if ( strlen( trim( $devdomain ) ) > 0 ) {
						$subdomains[] = trim( $tmp ) . "." . $devdomain;
						$subdomains[] = "www." . trim( $tmp ) . "." . $devdomain;
					}


				} else {
					unset ( $subdomains[ $i ] );
				}
			}
		}

		$subdomains[] = '127.0.0.1';
		//@unlink( $mainframe->getCfg( 'absolute_path' ) . "/components/com_digicom/tmp/" . $my->id . "/$licenseFile" );
		if ( is_array( $subdomains ) ) {
			$subdomains_text = @implode( ",", $subdomains );
		} else {
			$subdomains_text = '';
		}


		$handler = $this->getEncoder( $method );

		if ( $handler == - 1 ) {
			return ( - 2 );
		} //no handler found - internal error or hijack attempt
		//	echo ("lic gen");
		$handler->instance->genLicense( $domain, $devdomain, $subdomains_text, $licenseFile, $passphrase, $trial_period = "", $outDir, $handler->config );

		//common routines - lets they stay in this form for a while
		$curdir = getcwd();

		//extract the sub zip file
		chdir( $outDir );
		jimport( "joomla.filesystem.file" );
		define( 'PCLZIP_TEMPORARY_DIR', JPATH_ROOT . DS . 'administrator/components/digicom_product_uploads/tmp/' );
		require_once( JPATH_ROOT . DS . "administrator" . DS . "includes" . DS . "pcl" . DS . "pclzip.lib.php" );


		$srcZip = new PclZip( $zipFile );
		//die ($srcZip->errorInfo(true));

		if ( $srcZip->extract( PCLZIP_OPT_BY_NAME, $subZipFile ) == 0 ) {
			//		die ($srcZip->errorInfo(true));
		}

		$subZip = new PclZip( $subZipFile );

		$license_file_text = implode( '', file( $licenseFile ) );
		$subZip->add( $licenseFile );


		$srcZip->delete( PCLZIP_OPT_BY_NAME, $subZipFile );
		$res = $srcZip->add( $subZipFile );
		if ( $res == 0 ) {
			$srcZip->create( $subZipFile );
		}

		//remove temp files
		@unlink( $subZipFile );
		@unlink( $licenseFile );
		//done
		chdir( $curdir );

		return $license_file_text;

	}

	/*
	 * function from original code... lets see what it does.
	*/
	function genLoaders( $method, $phpVersions, $platforms, $outDir ) {
		//	global $platform_options, $my, $mainframe, $mosConfig_absolute_path, $digistor_enc_plugins;


		//lets select plugin that handles choosen encoding method
		$handler = $this->getEncoder( $method );
		if ( $handler == - 1 ) {
			return ( - 2 );
		} //no handler found - internal error or hijack attempt

		$handler->instance->genLoader( $phpVersions, $platforms, $outDir );

	}

	/*
	 * this function performs the same actions as one before... i wonder if there are any reasons to keep it
	 * adds some strange files to encoded package... lets ommit it for now.
	*/
	function embbedLoader( $method, $zipFile, $subZipFile, $outDir ) {

		$handler = $this->getEncoder( $method );

		if ( $handler == - 1 ) {
			return ( - 2 );
		} //no handler found - internal error or hijack attempt
		//die ("A");
		$loaderFile = $handler->instance->getLoader();

		//insert the license file into the zip file
		jimport( "joomla.filesystem.file" );
		define( 'PCLZIP_TEMPORARY_DIR', JPATH_ROOT . DS . 'administrator/components/digicom_product_uploads/tmp/' );
		require_once( JPATH_ROOT . DS . "administrator" . DS . "includes" . DS . "pcl" . DS . "pclzip.lib.php" );


		$curdir = getcwd();
		chdir( $outDir );

		echo $outDir;


		$srcZip = new PclZip( $zipFile );


		if ( $srcZip->extract( PCLZIP_OPT_BY_NAME, $subZipFile ) == 0 ) {
			die ( $srcZip->errorInfo( true ) );
		}

		$subZip = new PclZip( $subZipFile );
		$subZip->add( $loaderFile );

		$srcZip->delete( PCLZIP_OPT_BY_NAME, $subZipFile );
		$res = $srcZip->add( $subZipFile );
		if ( $res == 0 ) {
			$srcZip->create( $subZipFile );
		}

		@unlink( $subZipFile );
		@unlink( $loaderFile );
		//done
		chdir( $curdir );

	}


	function getEncPlatformsForMethod( $method ) {
		$handler = - 1;
		//lets select plugin that handles choosen encoding method
		if ( count( $this->encoding_plugins ) < 1 ) {
			return ( - 1 );//do not to forget place errorcode switch in calling file so user
			// may know if encoding was successful or not.
		} else {
			foreach ( $this->encoding_plugins as $plugin ) {
				if ( $method == $plugin->name ) {
					$handler = $this->_plugins[ $plugin->classname ]->instance; //we found handler for encoding
					break;
				}

			}

		}
		if ( $handler == - 1 ) {
			return ( - 2 );
		} //no handler found - internal error or hijack attempt

		$platform_options = $handler->getPlatforms();

		//print_r($platform_options); die;
		return ( $platform_options );
	}

	function FEPluginHandler( $pay_type, $items, $tax, $redir = 0, $profile ) {

		$result  = array();
		$total   = $tax['taxed'];
		$conf    = $this->getInstance( "config", "digicomModel" );
		$configs = $conf->getConfigs();

		if ( isset( $this->payment_plugins ) ) {
			if ( count( $this->payment_plugins ) < 1 ) {
				return - 1;
			}
		}
		$plugin_exists = 0;

		if ( ! isset( $this->payment_plugins[ $pay_type ] ) ) {//if selected payment method is not available
			if ( is_object( $this->default_payment ) ) {//try default payment gateway
				$plugin = $this->default_payment;
			} elseif ( ! is_object( $this->default_payment ) ) {//no default available
				if ( isset( $this->payment_plugins ) ) {
					foreach ( $this->payment_plugins as $plug ) {//select first gateway available
						$plugin = $plug;
						break;
					}
				}
			} else {
				die ( JText::_( "DSPAYMENTCANTBEPROC" ) );
			}
		} else { //all os ok - use normal gateway
			$plugin = $this->payment_plugins[ $pay_type ];
		}
		/* */
		if ( $plugin->reqhttps != 0 && ( getenv( 'HTTPS' ) != 'on' && getenv( 'HTTPS' ) != '1' ) ) {//plugin requires https connection to perform checkout
//			return $this->goToHTTPS ($sid);
			return "https";
		}
		/* */
		$this->_id = $plugin->id;
		$plugin    = $this->getPlugin();
		//now we're ready to call plugin
		$result = $plugin->instance->getFEData( $items, $tax, $redir, $profile, $plugin, $configs );

//	echo "Z";				 
		if ( strlen( trim( $result ) ) < 1 ) {
			echo "<script language='javascript'>alert ('" . _INTERNAL_ERROR . "'); self.history.go(-1);</script>";

			return;
		}

		$db = JFactory::getDBO();

		$ser_profile = base64_encode( serialize( $profile ) );
		$sql         = "update #__digicom_session set transaction_details='" . $ser_profile . "' where sid=" . $profile->_sid;
//echo $sql;

		$db->setQuery( $sql );
		$db->query();

		return $result;

	}


	function io() {
		$plug = JRequest::getVar( "plugin", "", "request" );
		$task = JRequest::getVar( "task", "", "request" );
		$flag = 0;

		if ( isset( $this->payment_plugins ) ) {
			if ( count( $this->payment_plugins ) > 0 ) {

				foreach ( $this->payment_plugins as $plugin ) {
					if ( $plug == $plugin->classname && $task == 'notifyPayment' ) {
						$this->_id = $plugin->id;
						$plugin    = $this->getPlugin( $this->_id );

						$content = $this->payment_notify( $plugin );
						echo( $content );
						$flag = 1;
						break;

					}
				}
				if ( isset( $this->payment_plugins ) ) {
					foreach ( $this->payment_plugins as $plugin ) {
						if ( $plug == $plugin->classname && $task == "returnPayment" ) {
							$this->_id = $plugin->id;
							$plugin    = $this->getPlugin( $this->_id );

							$content = $this->payment_return( $plugin );
							echo( $content );
							$flag = 1;
							break;

						}
					}
				}

				if ( isset( $this->payment_plugins ) ) {
					foreach ( $this->payment_plugins as $plugin ) {
						if ( $plug == $plugin->classname && $task == "failPayment" ) {
							$this->_id = $plugin->id;
							$plugin    = $this->getPlugin( $this->_id );

							$content = $this->payment_fail( $plugin );
							echo $content;
							$flag = 1;
							break;

						}
					}
				}
			}
		}

		return $flag;
	}


	/*
	 * After user returns from payment system we should receive responce on if transaction 
	 * was successfull, tell result to user and store info into db. Has to have
	 * real ip.
	*/
	function payment_notify( $plugin ) {
		//	$result = array();
//		global $database;

		$conf    = $this->getInstance( "config", "digicomModel" );
		$configs = $conf->getConfigs();
		$cart    = $this->getInstance( "cart", "digicomModel" );
		$result  = $plugin->instance->notify( $plugin, $cart, $configs, $this );

		return $result;

	}


	function payment_return( $plugin ) {
//		session_start ();
		$_SESSION['in_trans'] = 1;

//		$this->record_transactiondata($plugin, "success");
		$conf    = $this->getInstance( "config", "digicomModel" );
		$configs = $conf->getConfigs();
		$cart    = $this->getInstance( "cart", "digicomModel" );

		$result = $plugin->instance->return1( $plugin, $cart, $configs, $this );

		return $result;

	}

	function payment_fail( $plugin ) {
//		session_start ();
		$_SESSION['in_trans'] = 1;

//		$this->record_transactiondata($plugin, "fail");
		$conf    = $this->getInstance( "config", "digicomModel" );
		$configs = $conf->getConfigs();
		$cart    = $this->getInstance( "cart", "digicomModel" );

		$result = $plugin->instance->return2( $plugin, $cart, $configs, $this );

		return $result;

	}


	function addOrder( $items, $cust_info, $now, $paymethod, $status = "Active" ) {
		$cart    = $this->getInstance( "cart", "digicomModel" );
		$conf    = $this->getInstance( "config", "digicomModel" );
		$configs = $conf->getConfigs();
		$db      = JFactory::getDBO();
		$tax     = $cart->calc_price( $items, $cust_info, $configs );
		$sid     = $cust_info->_sid;

		$non_taxed = $tax['total'];//$total;
		$total     = $tax['taxed'];
		$taxa      = $tax['value'];
		$shipping  = $tax['shipping'];
		$currency  = $tax['currency'];
		$licenses  = $tax['licenses'];
		//   $total = round( $total + $vat_tax + $state_tax, 2 );

		$promo = $cart->get_promo( $cust_info );
		if ( $promo->id > 0 ) {
			$promoid   = $promo->id;
			$promocode = $promo->code;
		} else {
			$promoid   = '0';
			$promocode = '0';
		}
		$sql = "select shipping_details from #__digicom_session where sid='" . $sid . "'";
		$db->setQuery( $sql );
		$shipto = $db->loadResult();
		$sql    = '';
		if ( $shipto != '0' ) {
			if ( $shipto == '2' ) {
				$sql = "select shipaddress as a, shipzipcode as z, shipcity as c, shipstate as s, shipcountry as r from #__digicom_customers where id='" . $cust_info->_user->id . "'";
			} else if ( $shipto == '1' ) {
				$sql = "select address as a, zipcode as z, city as c, state as s, country as r from #__digicom_customers where id='" . $cust_info->_user->id . "'";
			} else {
				$sql = '';
			}
		}
		if ( strlen( $sql ) > 0 ) {
			$db->setQuery( $sql );
			$d           = $db->loadObjectList();
			$d           = $d[0];
			$shipaddress = $d->r
			               . "\n" . $d->s
			               . "\n" . $d->c
			               . "\n" . $d->a
			               . "\n" . $d->z;
		} else {
			$shipaddress = '';
		}
		$sql = "insert into #__digicom_orders ( userid,order_date,amount, currency, payment_method,number_of_licenses, status, tax, shipping, promocodeid, promocode, promocodediscount, shipto, fullshipto ) "
		       . " values ('{$cust_info->_user->id}','$now','$total', '" . $currency . "','" . $paymethod . "','$licenses', '" . $status . "', '$taxa','$shipping', '" . $promoid . "', '" . $promocode . "', '" . $tax['promo'] . "', '" . $shipto . "', '" . $shipaddress . "') ";
		$db->setQuery( $sql );
		$db->query();
		$orderid = $db->insertid();
		$this->storeTransactionData( $items, $orderid, $tax, $sid );

		if ( $promoid > 0 ) {
			$sql = "update #__digicom_promocodes set `used`=`used`+1 where id=" . $promoid;
			$db->setQuery( $sql );
			$db->query();
		}

		return $orderid;
	}


	function addLicenses( $items, $orderid, $now, $customer, $status = "Active" ) {

		$license = array();
		if ( $status != "Pending" ) {
			$published = 1;
		} else {
			$published = 0;
		}
		$database = JFactory::getDBO();
		//lets add licenses to database
		$sql = "select licenseid from #__digicom_licenses";
		$database->setQuery( $sql );
		$lids = $database->loadObjectList();
		$ls   = array();
		foreach ( $lids as $i => $v ) {
			$ls[] = $v->licenseid;
		}

		$itemnum       = count( $items );
		$license_index = 0;
		foreach ( $items as $i => $v ) {
			if ( $i < 0 ) {
				$itemnum --;
			}
		}
		for ( $j = 0; $j < $itemnum; $j ++ ) {

			$item = &$items[ $j ];

			for ( $i = 0; $i < $item->quantity; $i ++ ) {
				$licenseid = 10000000;
				do {
					++ $licenseid;

				} while ( in_array( $licenseid, $ls ) );

				//if ($licenseid){
				$ls[]  = $licenseid;
				$price = ( isset( $item->discounted_price ) && ( $item->discounted_price > 0 ) ) ? $item->discounted_price : $item->price;
				$sql   = "insert into #__digicom_licenses ( licenseid, userid, productid, orderid, amount_paid, published) "
				         . "values ('$licenseid','{$customer->_user->id}','{$item->item_id}', '" . $orderid . "','{$price}', " . $published . ")";
				//Log::debug($sql);
				$database->setQuery( $sql );
				$database->query();
				//				   	$item->licenseid = $database->insertid();
				$license_index ++;
				$license[ $license_index ] = new stdClass;
				if ( isset( $item ) && isset( $item->productfields ) && ! empty( $item->productfields ) ) {
					$license[ $license_index ]->productfields = $item->productfields;
				}
				$license[ $license_index ]->licenseid = $database->insertid();

				if ( $item->usestock == '1' ) {
					$sql = "update #__digicom_products set used=used+1 where id = '" . $item->item_id . "'";
					$database->setQuery( $sql );
					$database->query();
				}

				if ( $item->domainrequired == '3' ) {//if item is package - we have to update its type
					$sql = "update #__digicom_licenses set ltype='package' where id='" . $license[ $license_index ]->id . "'";
					$database->setQuery( $sql );
					$database->query();
				}

				$sql = '';

			}
			for ( $i = 0; $i < count( $item->featured ); $i ++ ) {
				for ( $k = 0; $k < $item->quantity; ++ $k ) {//inserting as many licenses as there are packages in the order

					do {
						++ $licenseid;
						//$licenseid = 10000000+rand(0,80000000);
						//$sql = "select count(*) from #__digicom_licenses where licenseid=".$licenseid;
						//$database->setQuery($sql);
						//$num = $database->loadResult();
					} while ( in_array( $licenseid, $ls ) );

					if ( $licenseid ) {
						$ls[] = $licenseid;
						$sql  = "insert into #__digicom_licenses ( licenseid, userid, productid, orderid, amount_paid, published, ltype) "
						        . "values ('$licenseid','{$customer->_user->id}','{$item->featured[$i]->id}', '" . $orderid . "','0', " . $published . ", 'package_item')";
						$database->setQuery( $sql );
						$database->query();

						$sql = "update #__digicom_products set used=used+1 where id = '" . $item->featured[ $i ]->id . "' and usestock='1'";
						$database->setQuery( $sql );
						$database->query();

						$sql = '';
					}
				}
			}
		}

		//now we have to fill fields dependencies for each license
		//we no longer need field and option ids - they were
		//required to prevent user from entering something
		//inappropriate in front-end
		//admin should be able to handle license fields himself
		//Log::debug($license);
		foreach ( $license as $i => $v ) {
			if ( ! empty( $v->productfields ) ) {
				foreach ( $v->productfields as $i1 => $v1 ) {
					$options = explode( "\n", $v1->options );
					if ( $v1->optionid >= 0 ) {
						$optionname = $database->getEscaped( trim( $options[ $v1->optionid ] ) );
					} else {
						$optionname = "Nothing Selected";
					}
					$sql = "insert into #__digicom_licensefields(licenseid, fieldname, optioname) values ('" . $v->licenseid . "', '" . $database->getEscaped( $v1->name ) . "', '" . $optionname . "');";
					//Log::debug($sql);
					$database->setQuery( $sql );
					$database->query();
					$sql = "";
				}
			}
		}

		// die;
	}


	//mail sending function
	function dispatchMail( $orderid, $amount, $licenses, $timestamp, $items, $customer ) {

		// 		global $my;

		$sid       = $customer->_sid;
		$cust_info = $customer;

		if ( ! $sid ) {
			return;
		}

		$my = JFactory::getUser( $customer->_user->id );

		$database = JFactory::getDBO();
		$cart     = $this->getInstance( "cart", "digicomModel" );
		$configs  = $this->getInstance( "Config", "digicomModel" );
		$configs  = $configs->getConfigs();

		$mes                  = new stdClass();

		$mes->body = "Template is empty";
		$sql       = "SELECT * FROM #__digicom_mailtemplates where `type`='order'";
		$database->setQuery( $sql );
		$db = JFactory::getDBO();
		$db->setQuery( $sql );
		$mes     = $db->loadObjectList();
		$mes     = $mes[0];
		$message = $mes->body;
		$email   = new stdClass();
//		$email = $this->getTable("Mail");
		$email->date  = $timestamp;
		$email->flag  = "order";
		$email->email = trim( $my->email );


		$subject = $mes->subject;
		// Replace all variables in template
		$flag  = "order";
		$promo = $cart->get_promo( $cust_info );
		if ( $promo->id > 0 ) {
			$promoid   = $promo->id;
			$promocode = $promo->code;
		} else {
			$promoid   = '0';
			$promocode = '0';
		}

		global $mainframe;

		$uri = JURI::getInstance();

		$sitename = ( trim( $configs->get('store_name','DigiCom Store') ) != '' ) ? $configs->get('store_name','DigiCom Store') : $mainframe->getCfg( 'sitename' );

		$siteurl = ( trim( $configs->get('store_url','') ) != '' ) ? $configs->get('store_url','') : $uri->base();

		$ship_add = DigiComHelper::get_customer_shipping_add( $my->id );
		$message  = str_replace( "[SHIPPING_ADDRESS]", $ship_add, $message );

		$message = str_replace( "[SITENAME]", $sitename, $message );
		$message = str_replace( "[CUSTOMER_COMPANY_NAME]", $my->copany, $message );
		$message = str_replace( "../%5BSITEURL%5D", $siteurl, $message );
		$message = str_replace( "%5BSITEURL%5D", $siteurl, $message );
		$message = str_replace( "[SITEURL]", $siteurl, $message );

		$query = "select lastname from #__digicom_customers where id=" . $my->id;
		$database->setQuery( $query );
		$lastname = $database->loadResult();

		$message = str_replace( "[CUSTOMER_USER_NAME]", $my->username, $message );
		$message = str_replace( "[CUSTOMER_FIRST_NAME]", $my->name, $message );
		$message = str_replace( "[CUSTOMER_LAST_NAME]", $lastname, $message );
		$message = str_replace( "[CUSTOMER_EMAIL]", $my->email, $message );

		$message      = str_replace( "[TODAY_DATE]", date( $configs->get('time_format','d-m-Y'), $timestamp ), $message );
		$message      = str_replace( "[ORDER_ID]", $orderid, $message );
		$message      = str_replace( "[ORDER_AMOUNT]", $amount, $message );
		$message      = str_replace( "[NUMBER_OF_LICENSES]", $licenses, $message );
		$message      = str_replace( "[PROMO]", $promo->code, $message );
		$displayed    = array();
		$product_list = '';


		$counter = array();
		foreach ( $items as $i => $item ) {
			if ( $i < 0 ) {
				continue;
			}
			if ( ! isset( $counter[ $item->id ] ) ) {
				$counter[ $item->id ] = 1;
			}
			$counter[ $item->id ] ++;
		}
		foreach ( $items as $i => $item ) {
			if ( $i < 0 ) {
				continue;
			}
			$optionlist = '';
			if ( ! empty( $item->productfields ) ) {
				foreach ( $item->productfields as $i => $v ) {
					$options = explode( "\n", $v->options );
					if ( $v->optionid >= 0 ) {
						$optionname = $options[ $v->optionid ];
					} else {
						$optionname = "Nothing Selected";
					}
					$optionlist .= $v->name . ": " . $optionname . "<br />";
				}
			}
			//Log::debug($item);
			if ( ! in_array( $item->name, $displayed ) ) {
				//$product_list .= $counter[$item->id]." - ".$item->name.'<br />';
				$product_list .= $item->quantity . " - " . $item->name . '<br />';
				$product_list .= $optionlist . '<br />';
			} else if ( count( $item->productfields > 0 ) ) {
				//echo $optionlist;
				$product_list .= $item->quantity . " - " . $item->name . '<br />';
				$product_list .= $optionlist . '<br />';
			}
			$displayed[] = $item->name;
		}
		//Log::debug($product_list);
		//die;
		$message     = str_replace( "[PRODUCTS]", $product_list, $message );
		$email->body = $message;

		//subject
		$subject = str_replace( "[SHIPPING_ADDRESS]", $ship_add, $subject );
		$subject = str_replace( "[SITENAME]", $sitename, $subject );
		$subject = str_replace( "[CUSTOMER_COMPANY_NAME]", $my->copany, $subject );
		$subject = str_replace( "../%5BSITEURL%5D", $siteurl, $subject );
		$subject = str_replace( "%5BSITEURL%5D", $siteurl, $subject );
		$subject = str_replace( "[SITEURL]", $siteurl, $subject );

		$subject = str_replace( "[CUSTOMER_USER_NAME]", $my->username, $subject );
		$subject = str_replace( "[CUSTOMER_FIRST_NAME]", $my->name, $subject );
		$subject = str_replace( "[CUSTOMER_LAST_NAME]", $lastname, $subject );
		$subject = str_replace( "[CUSTOMER_EMAIL]", $my->email, $subject );

		$subject      = str_replace( "[TODAY_DATE]", date( $configs->get('time_format','d-m-Y'), $timestamp ), $subject );
		$subject      = str_replace( "[ORDER_ID]", $orderid, $subject );
		$subject      = str_replace( "[ORDER_AMOUNT]", $amount, $subject );
		$subject      = str_replace( "[NUMBER_OF_LICENSES]", $licenses, $subject );
		$subject      = str_replace( "[PROMO]", $promo->code, $subject );
		$displayed    = array();
		$product_list = '';
		foreach ( $items as $i => $item ) {
			if ( $i < 0 ) {
				continue;
			}
			if ( ! in_array( $item->name, $displayed ) ) {
				$product_list .= $item->name . '<br />';
			}
			$displayed[] = $item->name;
		}
		$subject = str_replace( "[PRODUCTS]", $product_list, $subject );

		$subject = html_entity_decode( $subject, ENT_QUOTES );

		$message = html_entity_decode( $message, ENT_QUOTES );

		// Send email to user
//			global $mosConfig_mailfrom, $mosConfig_fromname, $configs;

		$mosConfig_mailfrom = $mainframe->getCfg( "mailfrom" );
		$mosConfig_fromname = $mainframe->getCfg( "fromname" );
		if ( $configs->get('usestoremail',1) == '1' && strlen( trim( $configs->get('store_name','DigiCom Store') ) ) > 0 && strlen( trim( $configs->get('store_email','') ) ) > 0 ) {
			$adminName2  = $configs->get('store_name','DigiCom Store');
			$adminEmail2 = $configs->get('store_email','');

		} else if ( $mosConfig_mailfrom != "" && $mosConfig_fromname != "" ) {
			$adminName2  = $mosConfig_fromname;
			$adminEmail2 = $mosConfig_mailfrom;

		} else {

			$query = "SELECT name, email"
			         . "\n FROM #__users"
			         . "\n WHERE LOWER( usertype ) = 'superadministrator'"
			         . "\n OR LOWER( usertype ) = 'super administrator'";
			$database->setQuery( $query );
			$rows        = $database->loadObjectList();
			$row2        = $rows[0];
			$adminName2  = $row2->name;
			$adminEmail2 = $row2->email;

		}


		$mailSender = JFactory::getMailer();
		$mailSender->IsHTML( true );
		$mailSender->addRecipient( $my->email );
		$mailSender->setSender( array( $adminEmail2, $adminName2 ) );
		$mailSender->setSubject( $subject );
		$mailSender->setBody( $message );
		Log::write( $message );
		if ( ! $mailSender->Send() ) {
//			<Your error code management>
		}

		if ( $configs->get('sendmailtoadmin',1) != 0 ) {
			$mailSender = JFactory::getMailer();
			$mailSender->IsHTML( true );
			$mailSender->addRecipient( $adminEmail2 );
			$mailSender->setSender( array( $adminEmail2, $adminName2 ) );
			$mailSender->setSubject( $subject );
			$mailSender->setBody( $message );
			Log::write( $message );
			if ( ! $mailSender->Send() ) {
//					<Your error code management>
			}
		}

		$sent = array();

		//send per product emails
		foreach ( $items as $i => $item ) {
			if ( $i < 0 ) {
				continue;
			}
			if ( ! in_array( $item->name, $sent ) && $item->sendmail == '1' ) {
				$subject = $item->productemailsubject;
				$subject = str_replace( "[SHIPPING_ADDRESS]", $ship_add, $subject );
				$subject = str_replace( "[SITENAME]", $sitename, $subject );
				$subject = str_replace( "[CUSTOMER_COMPANY_NAME]", $my->copany, $subject );
				$subject = str_replace( "../%5BSITEURL%5D", $siteurl, $subject );
				$subject = str_replace( "%5BSITEURL%5D", $siteurl, $subject );
				$subject = str_replace( "[SITEURL]", $siteurl, $subject );


				$subject = str_replace( "[CUSTOMER_USER_NAME]", $my->username, $subject );
				$subject = str_replace( "[CUSTOMER_FIRST_NAME]", $my->name, $subject );
				$subject = str_replace( "[CUSTOMER_LAST_NAME]", $lastname, $subject );
				$subject = str_replace( "[CUSTOMER_EMAIL]", $my->email, $subject );

				$subject = str_replace( "[TODAY_DATE]", date( $configs->get('time_format','d-m-Y'), $timestamp ), $subject );

				$message = $item->productemail;
				$message = str_replace( "[SITENAME]", $sitename, $message );

				$message = str_replace( "../%5BSITEURL%5D", $siteurl, $message );
				$message = str_replace( "%5BSITEURL%5D", $siteurl, $message );
				$message = str_replace( "[SITEURL]", $siteurl, $message );

				$query = "select lastname from #__digicom_customers where id=" . $my->id;
				$database->setQuery( $query );
				$lastname = $database->loadResult();

				$message = str_replace( "[CUSTOMER_USER_NAME]", $my->username, $message );
				$message = str_replace( "[CUSTOMER_FIRST_NAME]", $my->name, $message );
				$message = str_replace( "[CUSTOMER_LAST_NAME]", $lastname, $message );
				$message = str_replace( "[CUSTOMER_EMAIL]", $my->email, $message );

				$message = str_replace( "[TODAY_DATE]", date( $configs->get('time_format','d-m-Y'), $timestamp ), $message );

				$optionlist = '';
				if ( ! empty( $item->productfields ) ) {
					foreach ( $item->productfields as $i => $v ) {

						$options = explode( "\n", $v->options );
						if ( $v->optionid >= 0 ) {
							$optionname = $options[ $v->optionid ];
						} else {
							$optionname = "Nothing Selected";
						}
						$optionlist .= $v->name . ": " . $optionname . "<br />";
					}
				}

				$message = str_replace( "[ATTRIBUTES]", $optionlist, $message );
				$message = str_replace( "[PRODUCT_NAME]", $item->name, $message );

				$subject    = str_replace( "[ATTRIBUTES]", $optionlist, $subject );
				$subject    = str_replace( "[PRODUCT_NAME]", $item->name, $subject );
				$mailSender = JFactory::getMailer();
				$mailSender->IsHTML( true );
				$mailSender->addRecipient( $my->email );
				$mailSender->setSender( array( $adminEmail2, $adminName2 ) );
				$mailSender->setSubject( $subject );
				$mailSender->setBody( $message );
				Log::write( $message );
				if ( ! $mailSender->Send() ) {
//						<Your error code management>
				}
			}
		}

	}

	//integrate with idev_affiliate
	function affiliate( $total, $orderid, $configs ) {

		$mosConfig_live_site = DigiComHelper::getLiveSite();

		$my = JFactory::getUser();
		if ( $configs->get('idevaff','notapplied') == 'notapplied' ) {
			return;
		}
		@session_start();
		$idev_psystems_1 = $total;
		$idev_psystems_2 = $orderid;
		$name            = "iJoomla Products";
		$email           = $my->email;//"cust@cust.cust";
		$item_number     = 1;
		$ip_address      = $_SERVER['REMOTE_ADDR'];
		if ( $configs->get('idevaff','notapplied') == 'standalone' && file_exists( JPATH_SITE . "/" . $configs->get('idevpath','notapplied') . "/sale.php" ) ) {
			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_URL, $mosConfig_live_site . "/" . $configs->get('idevpath','notapplied') . "/sale.php?profile=72198&idev_saleamt=" . $total . "&idev_ordernum=" . $orderid . "&ip_address=" . $ip_address );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_exec( $ch );
			curl_close( $ch );
		} else if ( $configs->get('idevaff','notapplied') == 'component' ) {
			$orderidvar     = $configs->get('orderidvar','');
			$ordersubtotvar = $configs->get('ordersubtotalvar','');
			echo '<img border="0" src="' . $mosConfig_live_site . '/components/com_idevaffiliate/sale.php?' . $ordersubtotvar . '=' . sprintf( "%.2f", $total ) . '&' . $orderidvar . '=' . $orderid . '" width="1" height="1">';
		}


	}


	function loadCustomer( $sid ) {

		$db  = JFactory::getDBO();
		$sql = "select transaction_details from #__digicom_session where sid=" . $sid;
		$db->setQuery( $sql );
		$prof = $db->loadResult();

		return unserialize( base64_decode( $prof ) );

	}


	function storeTransactionData( $items, $orderid, $tax, $sid ) {
//		global $database, $my;

		global $Itemid;

		$database = JFactory::getDBO();
		$my       = JFactory::getUser();

		$data                    = array();
		$data['cart']['orderid'] = $orderid;
		$data['cart']['total']   = $tax['taxed'];
		$data['cart']['tax']     = $tax['taxed'] - $tax['total'] - $tax['shipping'];
		$query                   = "select state, country, city from #__digicom_customers where id=" . $my->id;
		$database->setQuery( $query );
		$location                = $database->loadObjectList();
		$data['cart']['city']    = $location[0]->city;
		$data['cart']['state']   = $location[0]->state;
		$data['cart']['country'] = $location[0]->country;
		$data['cart']['items']   = serialize( $items );

//		$session = base64_encode(serialize($_SESSION));
//		$data['session'] = $session;

		$data['userid'] = $my->id;
		$data['option'] = 'com_digicom';
		$data['Itemid'] = $Itemid;

		$data['nontaxed'] = $tax['total'];
		$insert           = base64_encode( serialize( $data ) );
		$sql              = "update #__digicom_session set transaction_details='" . $insert . "' where sid='" . $sid . "'";
		$database->setQuery( $sql );
		$database->query();

	}

	function goToSuccessURL( $sid, $msg = '', $orderid = 0 ) {

		global $Itemid;

		$mosConfig_live_site = DigiComHelper::getLiveSite();//$jconf->live_site;

		$conf    = $this->getInstance( "config", "digicomModel" );
		$configs = $conf->getConfigs();

		$cust_info = $this->loadCustomer( $sid );
		//Log::write("cust_info===");
		//Log::write(print_r($cust_info,1));

		//if ( isset($cust_info['cart']) ) {
		if ( isset( $cust_info ) && is_array( $cust_info ) && isset( $cust_info['cart'] ) ) {
			if ( isset( $cust_info['cart']['total'] ) ) {
				$cart_total = $cust_info['cart']['total'];
			}
			if ( isset( $cust_info['cart']['items'] ) ) {
				$cart_items = unserialize( $cust_info['cart']['items'] );
			}
			//debug($cart_items);
		}

		$customer = new DigiComSessionHelper();
		//Log::write("customer===");
		//Log::write(print_r($customer,1));

		$_Itemid = $Itemid;
		if ( isset( $customer->_Itemid ) && ( $customer->_Itemid > 0 ) ) {
			$_Itemid = $customer->_Itemid;
		}

		$cart = $this->getInstance( "cart", "digicomModel" );

		if ( isset( $cart_items ) ) {
			$items = $cart_items;
		} else {
			$items = $cart->getCartItems( $customer, $configs );
		}

		//Log::write("items calculation===");
		//Log::write(print_r($items,1));

		$tax = $cart->calc_price( $items, $customer, $configs );
		//Log::write("tax===");
		//Log::write(print_r($tax,1));

		if ( $orderid == 0 && is_array( $cust_info ) && isset( $cust_info['cart'] ) && isset( $cust_info['cart']['orderid'] ) ) {
			$orderid = $cust_info['cart']['orderid'];
		}
		if ( $orderid == 0 && is_object( $cust_info ) && isset( $cust_info->cart['orderid'] ) ) {
			$orderid = $cust_info->cart['orderid'];
		} // перестраховка если cart это об'ект

		$now      = time();
		$total    = $tax['taxed'];
		$licenses = $tax['licenses'];

		if ( $configs->get('afterpurchase',1) == 0 ) {
			$controller = "Licenses";
			$task       = "show";
		} else {
			$controller = "Orders";
			$task       = "list";
		}

		/* fixed return after payment, before paypal IPN*/
		$plugin = JRequest::getVar( 'plugin', '' );
		if ( $plugin != 'paypal' ) {
			/*
			Log::debug("Order ID from store session: ".$cust_info['cart']['orderid']);
			Log::debug("Order ID: ".$orderid);
			Log::debug($cust_info);die;
			*/
			$this->dispatchMail( $orderid, $total, $licenses, $now, $items, $customer );
			$cart->emptyCart( $sid );
		}

		// Get return urls
		$success_url = /*$mosConfig_live_site.*/
			JRoute::_( "index.php?option=com_digicom&controller=" . $controller . "&task=" . $task . "&success=1&sid=" . $sid . '&Itemid=' . $_Itemid, false, false );
		$failed_url  = /*$mosConfig_live_site.*/
			JRoute::_( "index.php?option=com_digicom&controller=" . $controller . "&task=" . $task . "&success=0&sid=" . $sid . '&Itemid=' . $_Itemid, false, false );

		$uri    = JURI::getInstance();
		$prefix = $uri->toString( array( 'host', 'port' ) );

		// Get Full url with host and port
		$success_url = 'http://' . $prefix . $success_url;
		$failed_url  = 'http://' . $prefix . $failed_url;

		if ( empty( $msg ) && $orderid > 0 ) {
			$msg = JText::_( "DSREFERENCEOID" ) . " " . $orderid;
		}

		// Encode return urls
		$success_url = base64_encode( $success_url );
		$failed_url  = base64_encode( $failed_url );

		$content = '
			<form name="dsform" method="post" action="' . JRoute::_( "index.php?option=com_digicom&controller=cart&task=landingSuccessPage&Itemid=" . $_Itemid, false, false ) . '">
				<input type="hidden" name="success_url" value="' . $success_url . '" />
				<input type="hidden" name="failed_url" value="' . $failed_url . '" />
				<input type="hidden" name="msg" value="' . $msg . '" />
				<input type="hidden" name="sid" value="' . $sid . '" />
				<input type="hidden" name="orderid" value="' . $orderid . '" />
				<input type="hidden" name="plugin" value="' . $plugin . '" />
				<input type="hidden" name="option" value="com_digicom" />
				<input type="hidden" name="controller" value="Cart" />
				<input type="hidden" name="task" value="landingSuccessPage" />
				<input type="hidden" name="Itemid" value="' . $_Itemid . '" />
			</form>
			<script>document.dsform.submit();</script>
		';

		echo $content;

	}

	function goToFailedURL( $sid, $msg = '' ) {

		global $Itemid;

		$mosConfig_live_site = DigiComHelper::getLiveSite();//$jconf->live_site;

		$conf    = $this->getInstance( "config", "digicomModel" );
		$configs = $conf->getConfigs();

		$customer = $this->loadCustomer( $sid );

		$_Itemid = $Itemid;
		if ( isset( $customer->_Itemid ) && ( $customer->_Itemid > 0 ) ) {
			$_Itemid = $customer->_Itemid;
		}

		$cart  = $this->getInstance( "cart", "digicomModel" );
		$items = $cart->getCartItems( $customer, $configs );

		$tax = $cart->calc_price( $items, $customer, $configs );

		$this->storeTransactionData( $items, - 1, $tax, $sid );

		if ( $configs->get('afterpurchase',1) == 0 ) {
			$controller = "Licenses";
			$task       = "show";
		} else {
			$controller = "Orders";
			$task       = "list";
		}

		$success_url = $mosConfig_live_site . "/index.php?option=com_digicom&controller=" . $controller . "&task=" . $task . "&success=1&sid=" . $sid;
		$failed_url  = $mosConfig_live_site . "/index.php?option=com_digicom&controller=" . $controller . "&task=" . $task . "&success=0&sid=" . $sid;

		$success_url = str_replace( "https://", "http://", $success_url );
		$failed_url  = str_replace( "https://", "http://", $failed_url );

		$success_url = base64_encode( $success_url . '&Itemid=' . $_Itemid );
		$failed_url  = base64_encode( $failed_url . '&Itemid=' . $_Itemid );

		$content = '
			<form name="dsform" method="post" action="' . JRoute::_( "index.php?option=com_digicom&controller=cart&task=landingFailedPage&Itemid=" . $_Itemid ) . '">
				<input type="hidden" name="success_url" value="' . $success_url . '" />
				<input type="hidden" name="failed_url" value="' . $failed_url . '" />
				<input type="hidden" name="msg" value="' . $msg . '" />
				<input type="hidden" name="option" value="com_digicom" />
				<input type="hidden" name="controller" value="Cart" />
				<input type="hidden" name="task" value="landingFailedPage" />
				<input type="hidden" name="Itemid" value="' . $_Itemid . '" />
			</form>
			<script>document.dsform.submit();</script>
		';

		echo $content;

	}

	function performCheckout( $customer ) {

		$db = JFactory::getDBO();

		//we're under https (redirection occured) - need to try to restore user session object
		//not under https (or restore failed) user has to be logged in to continue checkout

		//we have user data (either restored or original)
		//lets check if user has DigiCom customer profile
		$res = $this->checkProfileCompletion( $customer );
		if ( ! $res ) {
			return - 1;
		}

		$cart    = $this->getInstance( "cart", "digicomModel" );
		$conf    = $this->getInstance( "config", "digicomModel" );
		$configs = $conf->getConfigs();
		$items   = $cart->getCartItems( $customer, $configs );
		if ( $customer->_sid < 1 || count( $items ) < 1 ) {
			echo( '<script language="javascript"> alert ("' . ( JText::_( "DSCANTGETCARTDET" ) ) . '"); history.go(-1);</script>' );

			return;
		}
		$total = 0;

		$tax          = $cart->calc_price( $items, $customer, $configs );
		$total        = $tax['taxed'];
		$now          = time();
		$payment_type = $customer->_customer->payment_type;
		if ( (double ) $total == 0 ) {
			$cart                 = $this->getInstance( "Cart", "digicomModel" );
			$_SESSION['in_trans'] = 1;
			$cart->addFreeProduct( $items, $customer, $tax );
		} else {
			$_SESSION['in_trans'] = 1;
			$content              = $this->FEPluginHandler( $payment_type, $items, $tax, true, $customer );
			if ( $content == "https" ) {
				return $content;
			}
			if ( $content ) {
				echo( $content );

				return;
			} else {
				echo( '<script> alert("' . ( JText::_( "DSPROCPAYMENTERR" ) ) . '"); history.go(-1);</script>' );

				return;
			}
		}
	}

	function checkProfileCompletion( $customer ) {

		if ( ! $customer->_customer->id || ! $customer->_customer->payment_type
		     || strlen( trim( $customer->_customer->firstname ) ) < 1
		     || strlen( trim( $customer->_customer->lastname ) ) < 1
		     || strlen( trim( $customer->_user->email ) ) < 1
		     || strlen( trim( $customer->_customer->address ) ) < 1
		     || strlen( trim( $customer->_customer->city ) ) < 1
		     || strlen( trim( $customer->_customer->zipcode ) ) < 1

		) {
			return 0;
		}

		return 1;
		//check if this user has filled in profile information
	}

	function getTax( &$tax, $configs, $cust_info, $total ) {


		$plugin    = $this->default_tax;
		$this->_id = $plugin->id;
		$plugin    = $this->getPlugin( $plugin->id );

		if ( is_object( $this->_plugins[ $plugin->name ]->instance ) ) {
			$this->_plugins[ $plugin->name ]->instance->getTax( $tax, $configs, $cust_info, $total, $plugin );
		} else {
			$tax['value'] = 0;
			$tax['type']  = '';
			$tax['type1'] = '';

		}

	}

	function getShipping( &$tax, $items, $configs, $cust_info ) {


		$plugin    = $this->default_shipping;
		$this->_id = $plugin->id;
		$plugin    = $this->getPlugin();
		if ( is_object( $this->_plugins[ $plugin->name ]->instance ) ) {
			$this->_plugins[ $plugin->name ]->instance->getShipping( $tax, $items, $configs, $cust_info, $plugin );
		} else {
			$tax['shipping'] = 0;
		}
		if ( $customer->_user->id > 0 ) {

		} else {
			$tax['shipping'] = 0;
		}
	}

	function getItemShipping( &$item, $configs, $cust_info, $uid ) {
		$plugin    = $this->default_shipping;
		$this->_id = $plugin->id;
		$plugin    = $this->getPlugin();

		if ( is_object( $this->_plugins[ $plugin->name ]->instance ) ) {
			$this->_plugins[ $plugin->name ]->instance->getItemShipping( $item, $configs, $cust_info, $plugin, $uid );
		} else {
			$item->shipping = 0;
		}
		if ( $cust_info->_user->id > 0 ) {
		} else {
			$item->shipping = 0;
		}

	}
}
