<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

/**
 * Billing model class for Digicom.
 *
 * @since  1.6
 */
class DigicomModelBilling extends JModelForm
{
	/**
	 * @var		object	The user billing data.
	 * @since   1.6
	 */
	protected $data;


	/**
	 * Constructor
	 *
	 * @param   array  $config  An array of configuration options (name, state, dbo, table_path, ignore_request).
	 *
	 * @since   3.2
	 *
	 * @throws  Exception
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		// Load the Joomla! RAD layer
		if (!defined('FOF_INCLUDED'))
		{
			include_once JPATH_LIBRARIES . '/fof/include.php';
		}

		// Load the helper and model used for two factor authentication
		require_once JPATH_ADMINISTRATOR . '/components/com_users/models/user.php';
		require_once JPATH_ADMINISTRATOR . '/components/com_users/helpers/users.php';
	}
	/**
	 * Method to check in a user.
	 *
	 * @param   integer  $userId  The id of the row to check out.
	 *
	 * @return  boolean  True on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function checkin($userId = null)
	{
		// Get the user id.
		$userId = (!empty($userId)) ? $userId : (int) $this->getState('user.id');

		if ($userId)
		{
			// Initialise the table with JUser.
			$table = JTable::getInstance('Customer','Table');

			// Attempt to check the row in.
			if (!$table->checkin($userId))
			{
				$this->setError($table->getError());

				return false;
			}
		}

		return true;
	}

	/**
	 * Method to check out a user for editing.
	 *
	 * @param   integer  $userId  The id of the row to check out.
	 *
	 * @return  boolean  True on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function checkout($userId = null)
	{
		// Get the user id.
		$userId = (!empty($userId)) ? $userId : (int) $this->getState('user.id');

		if ($userId)
		{
			// Initialise the table with JUser.
			$table = JTable::getInstance('Customer','Table');

			// Get the current user object.
			$user = JFactory::getUser();

			// Attempt to check the row out.
			if (!$table->checkout($user->get('id'), $userId))
			{
				$this->setError($table->getError());

				return false;
			}
		}

		return true;
	}

	/**
	 * Method to get the billing form data.
	 *
	 * The base form data is loaded and then an event is fired
	 * for users plugins to extend the data.
	 *
	 * @return  mixed  	Data object on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function getData()
	{
		if ($this->data === null)
		{
			$userId = $this->getState('user.id');

			// Initialise the table with JUser.
			$customer = new DigiComSiteHelperSession();
			$this->data	= $customer->_customer;
			$this->data->username	= $customer->_user->username;

			// Override the base user data with any data in the session.
			$temp = (array) JFactory::getApplication()->getUserState('com_digicom.edit.billing.data', array());

			foreach ($temp as $k => $v)
			{
				$this->data->$k = $v;
			}

			if(empty($this->data->name)){
				$this->data->name = $customer->_user->name;
			}

			if(empty($this->data->email)){
				$this->data->email = $customer->_user->email;
			}


			// Unset the passwords.
			unset($this->data->password1);
			unset($this->data->password2);

			// Get the dispatcher and load the users plugins.
			$dispatcher	= JEventDispatcher::getInstance();
			JPluginHelper::importPlugin('user');

			// Trigger the data preparation event.
			$results = $dispatcher->trigger('onContentPrepareData', array('com_digicom.billing', $this->data));

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
	 * Method to get the billing form.
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
		$form = $this->loadForm('com_digicom.billing', 'register_billing', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		$form->setFieldAttribute('country', 'required', 'true');
		$form->setFieldAttribute('state', 'required', 'true');
		$form->setFieldAttribute('city', 'required', 'true');
		$form->setFieldAttribute('zipcode', 'required', 'true');
		$form->setFieldAttribute('address', 'required', 'true');

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

		$this->preprocessData('com_digicom.billing', $data);

		return $data;
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
		$params	= JFactory::getApplication()->getParams('com_digicom');

		// Get the user id.
		$userId = JFactory::getApplication()->getUserState('com_digicom.edit.billing.id');
		$userId = !empty($userId) ? $userId : (int) JFactory::getUser()->get('id');

		// Set the user id.
		$this->setState('user.id', $userId);

		// Load the parameters.
		$this->setState('params', $params);
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  mixed  The user id on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function save($data)
	{
		$userId = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('user.id');
		
		$customer = $this->getTable( 'Customer' );
		$customer->load($userId);

		if(empty($customer->id)){
			// Bind the data.
			if (!$customer->bind($data))
			{
				$this->setError(JText::sprintf('COM_DIGICOM_CUSTOMER_BIND_FAILED', $customer->getError()));

				return false;
			}
			// create user
			if (!$customer->create())
			{
				$this->setError(JText::sprintf('COM_DIGICOM_CUSTOMER_SAVE_FAILED', $customer->getError()));

				return false;
			}
		}else{
			// Bind the data.
			if (!$customer->bind($data))
			{
				$this->setError(JText::sprintf('COM_DIGICOM_CUSTOMER_BIND_FAILED', $customer->getError()));

				return false;
			}
			// create user
			if (!$customer->store())
			{
				$this->setError(JText::sprintf('COM_DIGICOM_CUSTOMER_SAVE_FAILED', $customer->getError()));

				return false;
			}
		}

		return $user->id;
	}

	/**
	 * Gets the configuration forms for all two-factor authentication methods
	 * in an array.
	 *
	 * @param   integer  $user_id  The user ID to load the forms for (optional)
	 *
	 * @return  array
	 *
	 * @since   3.2
	 */
	public function getTwofactorform($user_id = null)
	{
		$user_id = (!empty($user_id)) ? $user_id : (int) $this->getState('user.id');

		$model = new DigicomModelUser;

		$otpConfig = $model->getOtpConfig($user_id);

		FOFPlatform::getInstance()->importPlugin('twofactorauth');

		return FOFPlatform::getInstance()->runPlugins('onUserTwofactorShowConfiguration', array($otpConfig, $user_id));
	}

	/**
	 * Returns the one time password (OTP) – a.k.a. two factor authentication –
	 * configuration for a particular user.
	 *
	 * @param   integer  $user_id  The numeric ID of the user
	 *
	 * @return  stdClass  An object holding the OTP configuration for this user
	 *
	 * @since   3.2
	 */
	public function getOtpConfig($user_id = null)
	{
		$user_id = (!empty($user_id)) ? $user_id : (int) $this->getState('user.id');

		$model = new DigicomModelUser;

		return $model->getOtpConfig($user_id);
	}
}
