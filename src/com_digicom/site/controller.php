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

		if (!$user->guest || $this->input->getMethod() == 'POST' || ('cart' == $vName || 'checkout' == $vName || 'thankyou' == $vName || 'register' == $vName))
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
		$guest = $user->get('guest');
		if (!$guest && $vName == 'register'){

			// If the user is already logged in, redirect to the profile page.
			// Redirect to profile page.
			$this->setRedirect(JRoute::_('index.php?option=com_digicom&view=profile', false));
			return;
		}elseif($guest && ($vName == 'downloads' or $vName == 'billing' or $vName == 'profile' or $vName == 'dashboard' or $vName == 'orders' or $vName == 'order') ){
			// If the user is not logedin, redirect to the register page.
			$return = base64_encode( JURI::getInstance()->toString() );
			JFactory::getApplication()->enqueueMessage(JText::_('COM_DIGICOM_AUTHORIZED_AREA'),'warning');
			$this->setRedirect(JRoute::_('index.php?option=com_users&view=login&return='.$return, false));
			return;
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
			$query->update($db->quoteName('#__digicom_licenses'));
			$query->set($db->quoteName('active') . ' = ' . $db->quote('0'));
			$query->where('DATEDIFF('.$db->quoteName('expires').', now()) <= ' .$db->quote('-1'));
			//echo $query->__tostring();die;
			$db->setQuery($query);
			$db->execute();

			$dispatcher = JDispatcher::getInstance();
			$dispatcher->trigger('onDigicomOnCronJobOperation',array());

			echo JText::_('COM_DIGICOM_LICENSE_CHECK_SUCCESSFUL');
		}
		catch (Exception $e)
		{
		    JLog::addLogger(
		       array(
		            // Sets file name
		            'text_file' => 'com_digicom.log.php'
		       ),
		       // Sets messages of all log levels to be sent to the file
		       JLog::ALL,
		       // The log category/categories which should be recorded in this file
		       // In this case, it's just the one category from our extension, still
		       // we need to put it inside an array
		       array('com_digicom')
		   );
		    JLog::add(JText::sprintf('COM_DIGICOM_LICENSE_CHECK_FAILED_CHECK_LOG',$e->getMessage()), JLog::ERROR, 'com_digicom');
			echo JText::_('COM_DIGICOM_LICENSE_CHECK_FAILED');
		}

		JFactory::getApplication()->close();
	}

	/*
	* this function need to run through cron job
	* url: index.php?option=com_digicom&task=cron
	* the url for cron job
	*/
	public function getSefUrl(){
		$app		= JFactory::getApplication();
		$input	= $app->input;

		$urlObject = $input->get('sefUrl','','object');
		$nonsefurl = $urlObject['sefUrl'];

		if($nonsefurl){
			$sefurl = JRoute::_($nonsefurl);
		}else {
			$sefurl = JRoute::_('index.php');
		}

		echo $sefurl;die;

		$app->close();

	}
	/*
	* this function need to run through cron job
	* url: index.php?option=com_digicom&task=cron
	* the url for cron job
	*/
	public function getLanguage(){
		$app		= JFactory::getApplication();
		$input	= $app->input;

		$txt = $input->get('txt','','string');
		echo JText::sprintf($txt);
		$app->close();

	}

	function responses($config = array())
	{
		$cmd 	= JFactory::getApplication()->input->get('task') . '.execute';
		$format = JFactory::getApplication()->input->get('format','json');
		// Explode the controller.task command.
		list ($type, $task) = explode('.', $cmd);

		$file		= JControllerLegacy::createFileName('controller', array('name' => 'responses', 'format' => $format));
		$path 	= JPATH_COMPONENT . '/controllers/' . $file;

		// If the controller file path exists, include it.
		if (file_exists($path))
		{
			require_once $path;

			// Get the controller class name.
			$class = ucfirst('Digicom') . 'Controller' . ucfirst($type);
			$controller = new $class($config);
			$controller->execute($task);
			$controller->redirect();
		}
		else
		{
			$class = ucfirst('Digicom') . 'Controller' . ucfirst($type);
			$e = new Exception(JText::sprintf('COM_DIGICOM_ERROR_METHOD_UNDEFINED',$class,$format));
			echo new JResponseJson($e);
			JFactory::getApplication()->close();
		}
	}
}
