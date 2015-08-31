<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_digicom
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Registration controller class for Users.
 *
 * @since  1.6
 */
class DigicomControllerRegister extends JControllerLegacy
{

	/**
	 * Method to register a user.
	 *
	 * @return  boolean  True on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function register()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$app	= JFactory::getApplication();
		$model	= $this->getModel('Register', 'DigicomModel');

		// Get the user data.
		$requestData = $this->input->post->get('jform', array(), 'array');

		if($returnpage =  $this->input->get('return', '', 'request', 'base64'))
		{
		 	$returnurl = base64_decode($returnpage);
		}else{
			$returnurl = JRoute::_('index.php?option=com_digicom&view=dashboard');
		}
		// Validate the posted data.
		$form	= $model->getForm();

		if (!$form)
		{
			JError::raiseError(500, $model->getError());

			return false;
		}

		$data	= $model->validate($form, $requestData);

		// Check for validation errors.
		if ($data === false)
		{
			// Get the validation messages.
			$errors	= $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception)
				{
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				}
				else
				{
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Save the data in the session.
			$app->setUserState('com_digicom.register.data', $requestData);

			// Redirect back to the register screen.
			$this->setRedirect(JRoute::_('index.php?option=com_digicom&view=register&return='.$returnpage, false));

			return false;
		}

		// Attempt to save the data.
		$return	= $model->register($data);
		//print_r($return);die;
		// Check for errors.
		if ($return === false)
		{
			// Save the data in the session.
			$app->setUserState('com_digicom.register.data', $data);

			// Redirect back to the edit screen.
			$this->setMessage($model->getError(), 'warning');
			$this->setRedirect(JRoute::_('index.php?option=com_digicom&view=register&return'.$returnpage, false));

			return false;
		}
		// Flush the data from the session.
		$app->setUserState('com_digicom.register.data', null);
		$this->setMessage(JText::_('COM_DIGICOM_REGISTRATION_SUCCESSFULL'));

		$options                 = array();
		$options['remember']     = true;
		//$options['return']       = JRoute::_('index.php?option=com_digicom&view=cart&layout=summary');
		$options['return']       = $returnurl;
		$credentials             = array();
		$credentials['username'] = $data['username'];
		$credentials['password'] = $data['password2'];

		// Perform the log in.
		if (true === $app->login($credentials, $options))
		{
			// Success
			if ($options['remember'] == true)
			{
				$app->setUserState('rememberLogin', true);
			}

			$app->setUserState('users.login.form.data', array());
			//$this->setRedirect(JRoute::_('index.php?option=com_digicom&view=cart&layout=summary', false));
			$this->setRedirect($returnurl, false));

		}
		else
		{
			// Login failed !
			$data['remember'] = (int) $options['remember'];
			$app->setUserState('com_digicom.register.login.data', $data);
			$this->setRedirect(JRoute::_('index.php?option=com_digicom&view=register&return'.$returnpage, false));
		}

		return true;
	}
}
