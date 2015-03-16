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

class DigiComModelEmail extends JModelAdmin
{

	function getItems () {

		$type = JRequest::getVar('type','register','string');
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__digicom_mailtemplates'));
		$query->where($db->quoteName('type')." = ".$db->quote($type));
		$db->setQuery($query);
		$result	= $db->loadObject();		
		return $result;

	}

	

	function store () {
		$item = $this->getTable('MailTemplates');
		$data = JRequest::get('post');
		$item->load(array('type'=>$data['type']));
		if (!$item->bind($data)){
			$this->setError($item->getErrorMsg());
			return false;

		}

		if (!$item->check()) {
			$this->setError($item->getErrorMsg());
			return false;

		}

		if (!$item->store()) {
			$this->setError($item->getErrorMsg());
			return false;

		}
		return true;

	}

	/**
	 * Method to get the row form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed    A JForm object on success, false on failure
	 *
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$form = $this->loadForm('com_digicom.email', 'email', array('control' => 'jform', 'load_data' => $loadData));
		
		if (empty($form))
		{
			return false;
		}
		
		return $form;
	}

	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $type    The table name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable  A JTable object
	 *
	 * @since   1.6
	 */
	public function getTable($type = 'MailTemplates', $prefix = 'Table', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}


}