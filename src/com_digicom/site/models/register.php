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
 * Register model class for digicom.
 *
 * @since  1.0.0
 */
class DigicomModelRegister extends JModelForm
{
	/**
	 * @var    object  The user register data.
	 * @since  1.6
	 */
	protected $data;


	/**
	 * Method to get the register form data.
	 *
	 * The base form data is loaded and then an event is fired
	 * for users plugins to extend the data.
	 *
	 * @return  mixed  Data object on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function getData()
	{
		if ($this->data === null)
		{
			$this->data = new stdClass;
			$app = JFactory::getApplication();
			$params = JComponentHelper::getParams('com_digicom');

			// Override the base user data with any data in the session.
			$temp = (array) $app->getUserState('com_digicom.register.data', array());

			foreach ($temp as $k => $v)
			{
				$this->data->$k = $v;
			}

			// Get the groups the user should be added to after register.
			$this->data->groups = array();

			// Get the default new user group, Registered if not specified.
			$system = $params->get('new_usertype', 2);

			$this->data->groups[] = $system;

			// Unset the passwords.
			unset($this->data->password1);
			unset($this->data->password2);

			// Get the dispatcher and load the users plugins.
			$dispatcher = JEventDispatcher::getInstance();
			JPluginHelper::importPlugin('user');
			JPluginHelper::importPlugin('digicom');

			// Trigger the data preparation event.
			$results = $dispatcher->trigger('onContentPrepareData', array('com_digicom.register', $this->data));

			// Check for errors encountered while preparing the data.
			if (count($results) && in_array(false, $results, true))
			{
				$this->setError($dispatcher->getError());
				$this->data = false;
			}
		}

		return $this->data;
	}

	/**
	 * Method to get the register form.
	 *
	 * The base form is loaded from XML and then an event is fired
	 * for users plugins to extend the form with extra fields.
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm  A JForm object on success, false on failure
	 *
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_digicom.register', 'register', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		$params = JComponentHelper::getParams('com_digicom');
		$askforbilling = $params->get('askforbilling',0);
		if($askforbilling){
			$form->setFieldAttribute('country', 'required', 'true');
			$form->setFieldAttribute('state', 'required', 'true');
			$form->setFieldAttribute('city', 'required', 'true');
			$form->setFieldAttribute('zipcode', 'required', 'true');
			$form->setFieldAttribute('address', 'required', 'true');
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.6
	 */
	protected function loadFormData()
	{
		$data = $this->getData();

		$this->preprocessData('com_digicom.register', $data);

		return $data;
	}

	/**
	 * Override preprocessForm to load the user plugin group instead of content.
	 *
	 * @param   JForm   $form   A JForm object.
	 * @param   mixed   $data   The data expected for the form.
	 * @param   string  $group  The name of the plugin group to import (defaults to "content").
	 *
	 * @return  void
	 *
	 * @since   1.6
	 * @throws  Exception if there is an error in the form event.
	 */
	protected function preprocessForm(JForm $form, $data, $group = 'user')
	{
		$userParams = JComponentHelper::getParams('com_digicom');

		// Add the choice for site language at register time
		if ($userParams->get('askforbilling') == 1)
		{
			$form->loadFile('register_billing', false);
		}
		$form->loadFile('register_captcha', false);
		parent::preprocessForm($form, $data, $group);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState()
	{
		// Get the application object.
		$app = JFactory::getApplication();
		$params = $app->getParams('com_digicom');

		// Load the parameters.
		$this->setState('params', $params);
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $temp  The form data.
	 *
	 * @return  mixed  The user id on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function register($temp)
	{

		$params = JComponentHelper::getParams('com_digicom');

		// Initialise the table with JUser.
		$user = new JUser;
		$data = (array) $this->getData();

		// Merge in the register data.
		foreach ($temp as $k => $v)
		{
			$data[$k] = $v;
		}

		// Prepare the data for the user object.
		$data['email'] = JStringPunycode::emailToPunycode($data['email']);
		$data['password'] = $data['password1'];

		// Bind the data.
		if (!$user->bind($data))
		{
			$this->setError(JText::sprintf('COM_DIGICOM_REGISTRATION_BIND_FAILED', $user->getError()));

			return false;
		}

		// Load the users plugin group.
		JPluginHelper::importPlugin('user');
		JPluginHelper::importPlugin('digicom');
		//print_r($data);die;
		// Store the data.
		if (!$user->save())
		{
			$this->setError(JText::sprintf('COM_DIGICOM_REGISTRATION_SAVE_FAILED', $user->getError()));

			return false;
		}

		$data['id'] = $user->id;
		$customer = $this->getTable( 'Customer' );
		// Bind the data.
		if (!$customer->bind($data))
		{
			$this->setError(JText::sprintf('COM_DIGICOM_CUSTOMER_BIND_FAILED', $customer->getError()));

			return false;
		}

		//print_r($customer);die;

		// create user
		if (!$customer->create())
		{
			$this->setError(JText::sprintf('COM_DIGICOM_CUSTOMER_SAVE_FAILED', $customer->getError()));

			return false;
		}

		return $user->id;

	}


}
