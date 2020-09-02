<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

$com_path = JPATH_SITE . '/components/com_digicom/';
require_once $com_path . 'helpers/route.php';

JModelLegacy::addIncludePath($com_path . '/models', 'DigicomModel');

jimport('joomla.filesystem.file');
use Joomla\Registry\Registry;
// TODO : Remove JRequest to JInput and php visibility

class DigiComModelDownload extends JModelItem
{

	/**
	 * Model context string.
	 *
	 * @var    string
	 * @since  3.1
	 */
	public $_context = 'com_digicom.download';

	/**
	 * Method to auto-populate the model state.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @note Calling getState in this method will result in recursion.
	 *
	 * @since   3.1
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication('site');

		// Load state from the request.
		$pk = $app->input->getInt('id');
		$this->setState('download.id', $pk);

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);

	}

	/**
	 * Redefine the function and add some properties to make the styling more easy
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   3.1
	 */
	
	public function getItem($pk = null)
	{
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('download.id');
		$db = $this->getDbo();
		$query = $db->getQuery(true)
			->select(array('p.id as productid','p.name','p.catid','p.images','p.introtext','p.attribs','p.publish_up'))
			->from($db->quoteName('#__digicom_products','p'))
			->where($db->quoteName('p.id') . ' = ' . $pk);
		$db->setQuery($query);
		$product = $db->loadObject();

		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('id', 'name', 'url', 'hits')));
		$query->from($db->quoteName('#__digicom_products_files'));
		$query->where($db->quoteName('product_id') . ' = '. $db->quote($pk));
		$query->order('ordering ASC');
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

					$parsed = parse_url($fileLink);
					if (empty($parsed['scheme'])){
						$item->filesize = JText::_('COM_DIGICOM_FILE_DOESNT_EXIST');
						$item->filemtime = JText::_('COM_DIGICOM_FILE_DOESNT_EXIST');
					}else{
						$item->filesize = '';
						$item->filemtime = '';
					}
				}

			}
		}

		$product->files = $files;
		// print_r($product);die;
		return $product;
	}

	function getfileinfo()
	{

		$jinput = JFactory::getApplication()->input;
		$fileid = $jinput->get('downloadid', '0');
		
		if($fileid == '0')
		{
			$fileid = $jinput->get('token', '0');
			if($fileid == '0') return false;
		}
		
		//echo $fileid;die;
		$fileid = base64_decode($fileid);
		$fileid = json_decode($fileid);
		//print_r( $fileid );die;
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('p.name','product_name'));
		$query->select($db->quoteName(array('pf.id', 'pf.product_id','pf.name', 'pf.url', 'pf.hits')));
		$query->from($db->quoteName('#__digicom_products_files','pf'));
		$query->join('INNER', $db->quoteName('#__digicom_products','p') . ' ON ( '.$db->quoteName('pf.product_id') . ' = ' . $db->quoteName('p.id') .')' );
		$query->where($db->quoteName('pf.id') . ' = '. $db->quote($fileid->fileid));
		$query->order('id DESC');
		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		return $db->loadObject();

	}

}
