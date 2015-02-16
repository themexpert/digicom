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
jimport('joomla.filesystem.file');
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
				$query->select($db->quoteName(array('id', 'name', 'url', 'hits')));
				$query->from($db->quoteName('#__digicom_products_files'));
				$query->where($db->quoteName('product_id') . ' = '. $db->quote($product->productid));
				$query->order('id DESC');
				// Reset the query using our newly populated query object.
				$db->setQuery($query);
				$files = $db->loadObjectList();
				
				if(count($files) >0){
					foreach($files as $key2=>$item){
						$downloadid = array(
							'fileid' => $item->id
						);
						$downloadcode = json_encode($downloadid);
						$item->downloadid = base64_encode($downloadcode);
						
						$parsed = parse_url($item->url);
						if (empty($parsed['scheme'])) {
							$fileLink = JPATH_BASE.DS.$item->url;
						}else{
							$fileLink = $item->url;
						}
						if (JFile::exists($fileLink)) {
							$filesize = filesize ($fileLink);
							$item->filesize = DigiComHelper::FileSizeConvert($filesize);
							$item->filemtime = date("d F Y", filemtime($fileLink));
						}else{
							$item->filesize = JText::_('COM_DIGICOM_FILE_DONT_EXIST');
							$item->filemtime = JText::_('COM_DIGICOM_FILE_DONT_EXIST');
						}
						
					}
				}
				
				$product->files = $files;
			}
			
			$this->_products = $products ;
		}

		return $this->_products;
	}

	function getfileinfo(){
		
		$jinput = JFactory::getApplication()->input;
		$fileid = $jinput->get('downloadid', '0');
		//echo $fileid;die;
		if($fileid == '0') return false;
		$fileid = base64_decode($fileid);
		$fileid = json_decode($fileid);
		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('id', 'name', 'url', 'hits')));
		$query->from($db->quoteName('#__digicom_products_files'));
		$query->where($db->quoteName('id') . ' = '. $db->quote($fileid->fileid));
		$query->order('id DESC');
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		return $db->loadObject();		
		
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
