<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

/**
 * Installation class to perform additional changes during install/uninstall/update
 *
 * @package     Joomla.Administrator
 * @subpackage  com_digicom
 * @since       3.4
 */
class Com_DigiComInstallerScript
{
	/**
	 * Function to perform changes during install
	 *
	 * @param   JInstallerAdapterComponent  $parent  The class calling this method
	 *
	 * @return  void
	 *
	 * @since   3.4
	 */
	public function postflight($parent)
	{
		// Initialize a new category
		/** @type  JTableCategory  $category  */
		$category = JTable::getInstance('Category');
		// Check if the Uncategorised category exists before adding it
		if (!$category->load(array('extension' => 'com_digicom', 'title' => 'Uncategorised')))
		{
			$category->extension = 'com_digicom';
			$category->title = 'Uncategorised';
			$category->description = '';
			$category->published = 1;
			$category->access = 1;
			$category->params = '{"category_layout":"","image":""}';
			$category->metadata = '{"author":"","robots":""}';
			$category->language = '*';

			// Set the location in the tree
			$category->setLocation(1, 'last-child');

			// Check to make sure our data is valid
			if (!$category->check())
			{
				JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_DIGICOM_ERROR_INSTALL_CATEGORY', $category->getError()));

				return;
			}

			// Now store the category
			if (!$category->store(true))
			{
				JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_DIGICOM_ERROR_INSTALL_CATEGORY', $category->getError()));

				return;
			}

			// Build the path for our category
			$category->rebuildPath($category->id);
		}

		self::createDigiComMenu();
		self::createUploadDirectory();

		return;
	}

	/**
	 * @TODO: get this function works on Joomla 3
	 */
	function createDigiComMenu(){

		$db = JFactory::getDBO();
		$sql = "SELECT COUNT(*) from #__menu_types WHERE `menutype` = 'digicom_toolber' AND `title` = 'DigiCom Toolber'";
		$db->setQuery($sql);
		$count = $db->loadResult();
		if(intval($count) == 0){
			$sql = "
				INSERT IGNORE INTO #__menu_types(`menutype`, `title`, `description`)
				VALUES
					('digicom_toolber', 'DigiCom Toolber', 'DigiCom Toolber Menu')
			";
			$db->setQuery($sql);
			if($db->query()){
				$sql = "SELECT `extension_id` FROM #__extensions WHERE `name`='com_digicom' AND `element`='com_digicom'";
				$db->setQuery($sql);
				$db->query();
				$componentid = intval($db->loadResult());
				$sql = "
					INSERT IGNORE INTO `#__menu` (`menutype`, `title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `level`, `component_id`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `lft`, `rgt`, `home`, `language`, `client_id`)
					VALUES
						('digicom_toolber', 'Dashboard', 'dashboard', '', 'dashboard', 'index.php?option=com_digicom&view=dashboard', 'component', 1, 1, 1, ".$componentid.", 0, '0000-00-00 00:00:00', 0, 1, '', 0, '{\"menu-anchor_title\":\"\",\"menu-anchor_css\":\"\",\"menu_image\":\"\",\"menu_text\":1,\"page_title\":\"\",\"show_page_heading\":0,\"page_heading\":\"\",\"pageclass_sfx\":\"\",\"menu-meta_description\":\"\",\"menu-meta_keywords\":\"\",\"robots\":\"\",\"secure\":0}', 289, 290, 0, '*', 0),
						('digicom_toolber', 'Downloads', 'downloads', '', 'downloads', 'index.php?option=com_digicom&view=downloads', 'component', 1, 1, 1, ".$componentid.", 0, '0000-00-00 00:00:00', 0, 1, '', 0, '{\"menu-anchor_title\":\"\",\"menu-anchor_css\":\"\",\"menu_image\":\"\",\"menu_text\":1,\"page_title\":\"\",\"show_page_heading\":0,\"page_heading\":\"\",\"pageclass_sfx\":\"\",\"menu-meta_description\":\"\",\"menu-meta_keywords\":\"\",\"robots\":\"\",\"secure\":0}', 291, 292, 0, '*', 0),
						('digicom_toolber', 'Orders', 'order', '', 'order', 'index.php?option=com_digicom&view=orders', 'component', 1, 1, 1, ".$componentid.", 0, '0000-00-00 00:00:00', 0, 1, '', 0, '{\"menu-anchor_title\":\"\",\"menu-anchor_css\":\"\",\"menu_image\":\"\",\"menu_text\":1,\"page_title\":\"\",\"show_page_heading\":0,\"page_heading\":\"\",\"pageclass_sfx\":\"\",\"menu-meta_description\":\"\",\"menu-meta_keywords\":\"\",\"robots\":\"\",\"secure\":0}', 293, 294, 0, '*', 0),
						('digicom_toolber', 'Profile', 'profile', '', 'profile', 'index.php?option=com_digicom&view=profile', 'component', 1, 1, 1, ".$componentid.", 0, '0000-00-00 00:00:00', 0, 1, '', 0, '{\"menu-anchor_title\":\"\",\"menu-anchor_css\":\"\",\"menu_image\":\"\",\"menu_text\":1,\"page_title\":\"\",\"show_page_heading\":0,\"page_heading\":\"\",\"pageclass_sfx\":\"\",\"menu-meta_description\":\"\",\"menu-meta_keywords\":\"\",\"robots\":\"\",\"secure\":0}', 295, 296, 0, '*', 0)
				";
				$db->setQuery($sql);
				if (!$db->query()) {
					echo "FIX ME: admin/controller.php, line: ".__LINE__.'<br />';
					echo $db->getErrorMsg();
				}
			}
		}

		return true;
	}
	/*
	* create digicom folder at root filemanager folder
	*/
	function createUploadDirectory(){
		//Import filesystem libraries. Perhaps not necessary, but does not hurt
		jimport('joomla.filesystem.file');
		
		$file_exist = JFolder::exists(JPATH_SITE.DIRECTORY_SEPARATOR.'digicom');
		if(! JFolder::create(JPATH_SITE.DIRECTORY_SEPARATOR.'digicom') ){
			JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_DIGICOM_ERROR_CREATE_FOLDER', 'digicom'));
		}

		return true;
	}
}
