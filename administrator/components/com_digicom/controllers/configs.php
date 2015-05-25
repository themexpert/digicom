<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

jimport ('joomla.application.component.controller');

class DigiComControllerConfigs extends JControllerAdmin {

	function __construct () {

		parent::__construct();

		$this->registerTask("apply", "save");
		$this->registerTask("save", "save");
		
		$this->_model = $this->getModel("Config");
		$this->app = JFactory::getApplication();
		
	}

	function cancel() {
		$this->setRedirect('index.php?option=com_digicom');
	}


	function save () {
		
		// Check for request forgeries.
		if (!JSession::checkToken())
		{
			$this->app->enqueueMessage(JText::_('JINVALID_TOKEN'));
			$this->setRedirect('index.php?option=com_digicom&view=configs');
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
			$this->setRedirect('index.php?option=com_digicom&view=configs');
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
			$this->app->redirect(JRoute::_('index.php?option=com_digicom&view=configs' . $redirect, false));
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
			$this->app->redirect(JRoute::_('index.php?option=com_digicom&view=configs' . $redirect, false));
		}

		// Set the redirect based on the task.
		switch ($task)
		{
			case 'apply':
				$this->app->enqueueMessage(JText::_('CONFIGSAVED'));
				$this->app->redirect(JRoute::_('index.php?option=com_digicom&view=configs', false));

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

	/*
	* copyemail template
	* to set override file;
	*/

	function copytemplate(){
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		$app = JFactory::getApplication();
		$type     = $this->input->get('type','email');
		$file     = $this->input->get('file','');
		
		switch ($type) {
			case 'email':
				//
				$template = $this->getTemplate();
				$client   = JApplicationHelper::getClientInfo($template->client_id);
				$htmlPath = JPath::clean($client->path . '/templates/' . $template->template . '/html/com_digicom/emails/');

				// Check Html override path folder, create if not exist
				if (!JFolder::exists($htmlPath))
				{
					if (!JFolder::create($htmlPath))
					{
						$this->setRedirect('index.php?option=com_digicom&view=configs', JText::_('COM_DIGICOM_CONFIG_EMAIL_TEMPLATES_FOLDER_CREATE_ERROR'));
						return false;
					}
				}

				$originalPath = JPath::clean($client->path . '/components/com_digicom/emails/');
				$htmlFilePath = $originalPath . $file;
				//echo $htmlPath;die;
				if (JFile::exists($htmlFilePath))
				{
					$return = JFile::copy($htmlFilePath, $htmlPath.$file, '', true);
				}else{
					$this->setRedirect('index.php?option=com_digicom&view=configs', JText::_('COM_DIGICOM_CONFIG_EMAIL_TEMPLATES_ORIGINAL_NOT_FOUND'));
					return false;
				}
				break;
			
			default:
				// we will handle only email for now
				break;
		}

		if($return){
			$this->setRedirect('index.php?option=com_digicom&view=configs', JText::_('COM_DIGICOM_CONFIG_EMAIL_TEMPLATES_OVERRIDE_SUCCESS'));
		}else{
			$this->setRedirect('index.php?option=com_digicom&view=configs', JText::_('COM_DIGICOM_CONFIG_EMAIL_TEMPLATES_OVERRIDE_FAILS'));
		}
	}

	/*
	* getTemplate
	* get the site template for frontend
	*/

	public static function getTemplate(){
		// Get the database object.
		$db = JFactory::getDbo();
		// Build the query.
		$query = $db->getQuery(true)
			->select('*')
			->from('#__template_styles')
			->where('client_id = ' . $db->quote(0))
			->where('home = ' . $db->quote(1));

		// Check of the editor exists.
		$db->setQuery($query);
		return $db->loadObject();

	}
}

