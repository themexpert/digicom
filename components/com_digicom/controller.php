<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;
jimport('joomla.log.log');

/**
 * DigiCom Front Controller
 *
 * @package     DigiCom
 * @since       1.0
 */
class DigiComController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param   boolean			If true, the view output will be cached
	 * @param   array  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController		This object to support chaining.
	 * @since   1.0
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$cachable	= true;	// Huh? Why not just put that in the constructor?
		$user		= JFactory::getUser();

		// Set the default view name and format from the Request.
		// Note we are using w_id to avoid collisions with the router and the return page.
		// Frontend is a bit messier than the backend.
		$id    = $this->input->getInt('w_id');
		$vName = $this->input->get('view', 'categories');
		$this->input->set('view', $vName);

		if ($user->get('id') ||($this->input->getMethod() == 'POST' && $vName = 'categories'))
		{
			$cachable = false;
		}

		$safeurlparams = array(
			'id'				=> 'INT',
			'limit'				=> 'UINT',
			'limitstart'		=> 'UINT',
			'filter_order'		=> 'CMD',
			'filter_order_Dir'	=> 'CMD',
			'lang'				=> 'CMD'
		);

		// Check for edit form.
		if ($vName == 'form' && !$this->checkEditId('com_digicom.edit.product', $id))
		{
			// Somehow the person just went to the form - we don't allow that.
			return JError::raiseError(403, JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
		}

		return parent::display($cachable, $safeurlparams);
	}

	/*
	* this function need to run through cron job
	* url: index.php?option=com_digicom&task=cron
	* the url for cron job	
	*/
	function cron()
	{
		try
		{
		    $db 	= JFactory::getDbo();
			$query 	= $db->getQuery(true);
			$query->update($db->quoteName('#__digicom_licensess'));
			$query->set($db->quoteName('active') . ' = ' . $db->quote('0'));
			$query->where('DATEDIFF('.$db->quoteName('expires').', now()) <= ' .$db->quote('-1'));		
			//echo $query->__tostring();die;
			$db->setQuery($query);
			$db->query();

			echo JText::_('COM_DIGICOM_LICENSE_CHECK_SUCCESSFUL');
		}
		catch (Exception $e)
		{
		    JLog::addLogger(array(), JLog::ERROR);
		    JLog::add(JText::sprintf('COM_DIGICOM_LICENSE_CHECK_FAILED_CHECK_LOG',$e->getMessage()), JLog::ERROR, 'com_digicom');
			echo JText::_('COM_DIGICOM_LICENSE_CHECK_FAILED');
		}

		JFactory::getApplication()->close();
	}
}
