<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.file');

// TODO : Remove JRequest to JInput and php visibility

class DigiComModelDownloads extends JModelList
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
		$user = new DigiComSiteHelperSession();
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

			$bundleItems = array();
			foreach($products as $key=>$product){
				if($product->type != 'reguler'){
					switch($product->type){
						case 'category':
							//echo 'product type category, solve it man';die;
							//as its a category type product, remove this key;
							// add products to this $products object
							
							$BundleTable = JTable::getInstance('Bundle', 'Table');
							$BundleList = $BundleTable->getFieldValues('product_id',$product->productid,$product->bundle_source);
							$bundle_ids = $BundleList->bundle_id;
							if($bundle_ids){
								$db = $this->getDbo();
								$query = $db->getQuery(true)
									->select(array('id as productid','name','catid'))
									->from($db->quoteName('#__digicom_products'))
									->where($db->quoteName('bundle_source').' IS NULL')
									->where($db->quoteName('catid').' in ('.$bundle_ids.')');
								$db->setQuery($query);
								$bundleItems[] = $db->loadObjectList();
								//we should show only items
							}

							unset($products[$key]);
							
							break;
						case 'product':
						default:
							$BundleTable = JTable::getInstance('Bundle', 'Table');
							$BundleList = $BundleTable->getFieldValues('product_id',$product->productid,$product->bundle_source);
							$bundle_ids = $BundleList->bundle_id;
							if($bundle_ids){
								$db = $this->getDbo();
								$query = $db->getQuery(true)
									->select(array('id as productid','name','catid'))
									->from($db->quoteName('#__digicom_products'))
									->where($db->quoteName('bundle_source').' IS NULL')
									->where($db->quoteName('id').' in ('.$bundle_ids.')');
								$db->setQuery($query);
								$bundleItems[] = $db->loadObjectList();
							}					
							//we should show only items
							unset($products[$key]);
							
							break;
					}
				}
			}
			//print_r($bundleItems);die;
			//we got all our products
			// now add bundle item to the products array
			if(count($bundleItems) >0){
				foreach($bundleItems as $item2){
					foreach($item2 as $item3){
						$products[] = $item3;
					}
				}
			}
			
			//print_r($products);die;
			$productAdded = array();
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
							$fileLink = JPATH_BASE.DIRECTORY_SEPARATOR.$item->url;
						}else{
							$fileLink = $item->url;
						}
						if (JFile::exists($fileLink)) {
							$filesize = filesize ($fileLink);
							$item->filesize = DigiComSiteHelperDigiCom::FileSizeConvert($filesize);
							$item->filemtime = date("d F Y", filemtime($fileLink));
						}else{
							$item->filesize = JText::_('COM_DIGICOM_FILE_DOESNT_EXIST');
							$item->filemtime = JText::_('COM_DIGICOM_FILE_DOESNT_EXIST');
						}
						
					}
				}
				
				$product->files = $files;
				if(isset($productAdded[$product->productid])) unset($products[$key]);
				$productAdded[$product->productid] = true;
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
		$query->select($db->quoteName(array('id', 'product_id','name', 'url', 'hits')));
		$query->from($db->quoteName('#__digicom_products_files'));
		$query->where($db->quoteName('id') . ' = '. $db->quote($fileid->fileid));
		$query->order('id DESC');
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		return $db->loadObject();		
		
	}

}
