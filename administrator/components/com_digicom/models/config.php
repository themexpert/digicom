<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class DigiComModelConfig extends JModelForm
{

	/**
	 * Method to get a form object.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 *
	 * @since	1.0.0
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$form = $this->loadForm('com_digicom.config', 'config', array('control' => 'jform', 'load_data' => $loadData));
		
		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Get the component information.
	 *
	 * @return	object
	 *
	 * @since	1.0.0
	 */
	public function getComponent()
	{
		$state = $this->getState();
		$option = 'com_digicom';
		// Load common and local language files.
		$lang = JFactory::getLanguage();
		$lang->load($option, JPATH_BASE, null, false, true)
		|| $lang->load($option, JPATH_BASE . "/components/com_digicom", null, false, true);

		$result = JComponentHelper::getComponent($option);
		
		return $result;
	}

	/**
	 * Method to save the configuration data.
	 *
	 * @param   array  $data  An array containing all global config data.
	 *
	 * @return  boolean  True on success, false on failure.
	 *
	 * @since	1.0.0
	 * @throws  RuntimeException
	 */
	public function save($data)
	{
		$table	= JTable::getInstance('extension');

		// Load the previous Data
		if (!$table->load($data['id']))
		{
			throw new RuntimeException($table->getError());
		}

		unset($data['id']);

		// Bind the data.
		if (!$table->bind($data))
		{
			throw new RuntimeException($table->getError());
		}

		// Check the data.
		if (!$table->check())
		{
			throw new RuntimeException($table->getError());
		}

		// Store the data.
		if (!$table->store())
		{
			throw new RuntimeException($table->getError());
		}


		// Store email templates to file
		// TODO:: Lets push it man
		$app = JFactory::getApplication();
		$fileName = base64_decode($app->input->get('file'));
		$client = JApplicationHelper::getClientInfo($template->client_id);
		$filePath = JPath::clean($client->path . '/templates/' . $template->element . '/' . $fileName);

		// Include the extension plugins for the save events.
		JPluginHelper::importPlugin('extension');

		$user = get_current_user();
		chown($filePath, $user);
		JPath::setPermissions($filePath, '0644');

		// Try to make the template file writable.
		if (!is_writable($filePath))
		{
			$app->enqueueMessage(JText::_('COM_TEMPLATES_ERROR_SOURCE_FILE_NOT_WRITABLE'), 'warning');
			$app->enqueueMessage(JText::_('COM_TEMPLATES_FILE_PERMISSIONS' . JPath::getPermissions($filePath)), 'warning');

			if (!JPath::isOwner($filePath))
			{
				$app->enqueueMessage(JText::_('COM_TEMPLATES_CHECK_FILE_OWNERSHIP'), 'warning');
			}

			return false;
		}

		$return = JFile::write($filePath, $data['source']);
		if (!$return)
		{
			$app->enqueueMessage(JText::sprintf('COM_TEMPLATES_ERROR_FAILED_TO_SAVE_FILENAME', $fileName), 'error');

			return false;
		}


		return true;
	}

	public static function getConfigs(){
		$comInfo = JComponentHelper::getComponent('com_digicom');
		return $comInfo->params;
	}
}
