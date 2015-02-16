<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 436 $
 * @lastmodified	$LastChangedDate: 2013-11-19 15:04:40 +0100 (Tue, 19 Nov 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

defined ('_JEXEC') or die ("Go away.");

jimport ("joomla.aplication.component.model");

class DigiComModelDownloads extends DigiComModel
{
	var $_products;
	var $_product;
	var $_id = null;

	function __construct () {
		parent::__construct();
		$cids = JRequest::getVar('licid', 0, '', 'array');
		$this->setId((int)$cids[0]);
	}

	function setId($id) {
		$this->_id = $id;
		$this->_oid = $id;
		$this->_license = null;
	}

	function incrimentDownloadCount($licid) {
		$db = JFactory::getDBO();
		$sql = 'update #__digicom_licenses set download_count = download_count+1 where id='.intval($licid);
		$db->setQuery($sql);
		$db->query();
	}


	function getlistDownloads(){
		$user = new DigiComSessionHelper();
		//dsdebug($user->_customer->id);die;
		$db = JFactory::getDBO();

		$search = JRequest::getVar('search', '');
		$search = trim($search);

		if (empty ($this->_products)) {

			$query = $db->getQuery(true);
			$query->select('DISTINCT('.$db->quoteName('od.productid').')');
			$query->select($db->quoteName(array('p.name', 'p.catid', 'p.bundle_source')));
			$query->select($db->quoteName('od.package_type').' type');
			$query->from($db->quoteName('#__digicom_products').' p');
			$query->from($db->quoteName('#__digicom_orders_details').' od');
			$query->where($db->quoteName('od.userid') . ' = '. $db->quote($user->_customer->id));
			$query->where($db->quoteName('od.productid') . ' = '. $db->quoteName('p.id'));
			$query->where($db->quoteName('od.published') . ' = '. $db->quote('1'));
			$query->order('ordering ASC');

			// Reset the query using our newly populated query object.
			$db->setQuery($query);
 
			$products = $db->loadObjectList();
			
			foreach($products as $key=>$product){
				if($product->type != 'reguler'){
					switch($product->type){
						case 'category':
							echo 'product type category, solve it man';die;
							break;
						case 'product':
						default:
							
							break;
					}
				}
			}
			
			foreach($products as $key=>$product){
				$query = $db->getQuery(true);
				$query->select($db->quoteName(array('id', 'name','hits')));
				$query->from($db->quoteName('#__digicom_products_files'));
				$query->where($db->quoteName('product_id') . ' = '. $db->quote($product->productid));
				$query->order('id DESC');
				// Reset the query using our newly populated query object.
				$db->setQuery($query);
				$files = $db->loadObjectList();
				
				$product->files = $files;
			}
			
			$this->_products = $products ;
		}

		return $this->_products;
	}

	function prepareDownload($product, $uid) {
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		$resdir = JPATH_ROOT.DS."administrator".DS."components".DS."digicom_product_uploads".DS.$product->id.DS;
		$resdir_orig = $resdir."original".DS;
		$resdir_encoded = $resdir."encoded".DS;
		$srcFile = $resdir_orig.$product->file;
		
		if(!is_file($srcFile)){
			echo '<script> alert("'.JText::_("DIGI_NOT_FILE").'"); history.go(-1); </script>';
			die();
		}
			   
		$usermainDir = JPATH_ROOT.DS."components/com_digicom/download/";
		$prepDir = $usermainDir . $uid."/";  
		
		if (file_exists($prepDir) ) {
			JFolder::delete ($prepDir);
		} 
		JFolder::create($prepDir);		

		DigiComHelper::CreateIndexFile($usermainDir);
		DigiComHelper::CreateIndexFile($prepDir);		
		
		if (!JFile::copy ($srcFile, $prepDir.$product->file)) return false;//die ($srcFile." ".$prepDir.$product->file);;
		return true;
	}

	function prepareEncodedFile ($product, $license, $uid, $dev = 0, $method) {

			$phpVer = JRequest::getVar ('phpVersions', "", "request");
			$platform = JRequest::getVar ('platforms', "", "request");
			$platform = implode (" ", $platform);
			$hoster = JRequest::getVar ('hosting_service_name', "", "request");
			$panel = JRequest::getVar ('control_panel', "", "request");
			$db = JFactory::getDBO();

			$subdomains = JRequest::getVar( 'subdomains' , "", "request") ;
			if (trim( $subdomains ) != "" ) $subdomains = explode( "\n", $subdomains );
			if ( $dev)  
				$domain = $license->domain;
			else
				$domain = $license->dev_domain;
		
			$domain = $license->domain;
			$devdomain = $license->dev_domain;
		
		//remove the existing files
		$license_file = "license.txt";
		$srcFile = JPATH_ROOT."/administrator/components/digicom_product_uploads/".$product->id."/encoded/".$method."/".$phpVer[0]."/".$product->file;
		if (!file_exists ($srcFile)) $srcFile = JPATH_ROOT."/administrator/components/digicom_product_uploads/".$product->id."/original/".$product->file;
		
			$prepDir = JPATH_ROOT."/components/com_digicom/download/".$uid."/";

		if (file_exists($prepDir) ) {
			JFolder::delete ($prepDir);
		} 

		JFolder::create($prepDir);
		
		JFile::copy ($srcFile, $prepDir.$product->file);
	   
		return true;
	}

	function getPackageContents($license, $product) {

		jimport("joomla.filesystem.file");

		define( 'PCLZIP_TEMPORARY_DIR', JPATH_ROOT.DS.'administrator/components/digicom_product_uploads/tmp/' );

		DigiComHelper::CreateIndexFile(JPATH_ROOT.DS.'administrator/components/digicom_product_uploads/');
		DigiComHelper::CreateIndexFile(JPATH_ROOT.DS."components/com_digicom/download/");
		DigiComHelper::CreateIndexFile(JPATH_ROOT.DS."components/com_digicom/download/".$license->userid."/");

		require_once (JPATH_ROOT.DS."administrator".DS."includes".DS."pcl".DS."pclzip.lib.php");

		$srcZip = new PclZip(JPATH_ROOT.DS."components/com_digicom/download/".$license->userid."/".$product->file);
		return ($srcZip->listContent());
	}

}
