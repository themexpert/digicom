<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

// TODO : PHP property visibility need to be added

class DigiComControllerProfile extends JControllerLegacy
{

	function logCustomerIn()
	{
		$app = JFactory::getApplication("site");
		$Itemid = JRequest::getInt('Itemid', 0);

		// $returnpage = JRequest::getVar("returnpage", "");
		// if($return = JRequest::getVar('return', '', 'request', 'base64'))
		// {
		// 	$return = base64_decode($return);
		// }

		$return = JRoute::_('index.php?option=com_digicom&view=cart&layout=summary');

		$options = array();
		$options['remember'] = JRequest::getBool('remember', false);
		$options['return'] = $returnpage;

		$username = JRequest::getVar("username", "", 'request','username');
		$password = JRequest::getVar("passwd", "", 'post',JREQUEST_ALLOWRAW);

		$credentials = array();
		$credentials['username'] = $username; //JRequest::getVar('username', '', 'method', 'username');
		$credentials['password'] = $password; //JRequest::getString('passwd', '', 'post', JREQUEST_ALLOWRAW);

		$err = $app->login($credentials, $options);

		// if($return){
			$this->setRedirect($return);
			return true;
		// }

		// $link = $this->getLink();
		// if($returnpage != 'checkout'){
		// 	$this->setRedirect($link);
		// 	return true;
		// }

		//$this->checkNextAction($err);

	}

	function save()
	{

		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$getreturn = JRequest::getVar("return", "");
		if($getreturn){
			$return = base64_decode( $getreturn );
		}else{
			$return = JRoute::_('index.php?option=com_digicom&view=profile');
		}

		$app	= JFactory::getApplication();
		$model	= $this->getModel('Profile', 'DigicomModel');

		// Get the user data.
		$requestData = $this->input->post->get('jform', array(), 'array');

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
			$app->setUserState('com_digicom.profile.data', $requestData);

			// Redirect back to the register screen.
			$this->setRedirect(JRoute::_('index.php?option=com_digicom&view=profile', false));

			return false;
		}

		// Attempt to save the data.
		$result	= $model->save($data);
		//print_r($return);die;
		// Check for errors.
		if ($result === false)
		{
			// Save the data in the session.
			$app->setUserState('com_digicom.profile.data', $data);

			// Redirect back to the edit screen.
			$this->setMessage($model->getError(), 'warning');
			$this->setRedirect(JRoute::_('index.php?option=com_digicom&view=profile', false));

			return false;
		}
		// Flush the data from the session.
		$app->setUserState('com_digicom.profile.data', null);
		$this->setMessage(JText::_('COM_DIGICOM_PROFILE_UPDATED_SUCCESSFULL'));

		$this->setRedirect($return, false);

		return true;
	}


}
