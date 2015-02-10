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

jimport ('joomla.application.component.controller');

class DigiComAdminControllerConfigs extends DigiComAdminController {

	var $_model = null;
	var $app = null;

	function __construct () {

		parent::__construct();

		require_once( JPATH_COMPONENT.DS.'helpers'.DS.'helper.php' );
		$this->registerTask ("", "Configs");
		$this->registerTask("apply", "save");
		$this->registerTask("save", "save");
		$this->registerTask("cancel", "cancel");
		$this->registerTask("supportedsites", "supportedsites");
		
		$this->_model = $this->getModel("Config");
		$this->app = JFactory::getApplication();
		
	}

	function Configs() {
		$view = $this->getView("Configs", "html");
		$view->setModel($this->_model, true);
		$view->display();
	}

	function Cancel() {
		$this->app->redirect('index.php?option=com_digicom');
	}


	function save () {
		
		// Check for request forgeries.
		if (!JSession::checkToken())
		{
			$this->app->enqueueMessage(JText::_('JINVALID_TOKEN'));
			$this->app->redirect('index.php?option=com_digicom&controller=configs');
		}
		
		$form   = $this->_model->getForm();
		$data   = $this->input->get('jform', array(), 'array');
		$id     = $this->input->getInt('id');
		$option = $this->input->get('com_digicom');
		$task = $this->input->get('task','apply');
		
		// Check if the user is authorized to do this.
		if (!JFactory::getUser()->authorise('core.admin', $option))
		{
			$this->app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'));
			$this->app->redirect('index.php?option=com_digicom&controller=configs');
		}
		
		// Validate the posted data.
		$return = $this->_model->validate($form, $data);

		// Check for validation errors.
		if ($return === false)
		{
			/*
			 * The validate method enqueued all messages for us, so we just need to redirect back.
			 */

			// Save the data in the session.
			$this->app->setUserState('com_digicom.configs.global.data', $data);

			// Redirect back to the edit screen.
			$this->app->redirect(JRoute::_('index.php?option=com_digicom&controller=configs' . $redirect, false));
		}

		// Attempt to save the configuration.
		$data = array(
			'params' => $return,
			'id'     => $id,
			'option' => $option
		);

		try
		{
			$this->_model->save($data);
		}
		catch (RuntimeException $e)
		{
			// Save the data in the session.
			$this->app->setUserState('com_digicom.configs.global.data', $data);

			// Save failed, go back to the screen and display a notice.
			$this->app->enqueueMessage(JText::sprintf('JERROR_SAVE_FAILED', $e->getMessage()), 'error');
			$this->app->redirect(JRoute::_('index.php?option=com_digicom&controller=configs' . $redirect, false));
		}

		// Set the redirect based on the task.
		switch ($task)
		{
			case 'apply':
				$this->app->enqueueMessage(JText::_('CONFIGSAVED'));
				$this->app->redirect(JRoute::_('index.php?option=com_digicom&controller=configs', false));

				break;

			case 'save':
			default:
				$redirect = 'index.php?option=com_digicom';
				$this->app->enqueueMessage(JText::_('CONFIGSAVED'));
				$this->app->redirect(JRoute::_($redirect, false));

				break;
		}

		return true;
	}

	function supportedsites(){
		$view = $this->getView("Configs", "html");
		$view->setLayout("supportedsites");
		$view->supportedsites();
	}
}

