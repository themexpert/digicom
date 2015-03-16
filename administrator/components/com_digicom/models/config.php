<?php
/**
* @package			DigiCom Joomla Extension
* @author			themexpert.com
* @version			$Revision: 341 $
* @lastmodified	$LastChangedDate: 2013-10-10 14:28:28 +0200 (Thu, 10 Oct 2013) $
* @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license
*/

defined ('_JEXEC') or die ("Go away.");
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
	 * @since	3.2
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
	 * @since	3.2
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
	 * @since	3.2
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

		return true;
	}

	public static function getConfigs(){
		$comInfo = JComponentHelper::getComponent('com_digicom');
		return $comInfo->params;
	}
}
