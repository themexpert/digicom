<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  User.profile
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

/**
 * An example custom profile plugin.
 *
 * @package     Joomla.Plugin
 * @subpackage  User.profile
 * @since       1.6
 */
class PlgUserDigiCom extends JPlugin
{
	/**
	 * Date of birth.
	 *
	 * @var    string
	 * @since  3.1
	 */
	private $_date = '';
	
	public static $_customer = null;
	public static $_addscript = false;

	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 * @since  3.1
	 */
	protected $autoloadLanguage = true;

	/**
	 * Constructor
	 *
	 * @param   object  $subject  The object to observe
	 * @param   array   $config   An array that holds the plugin configuration
	 *
	 * @since   1.5
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		JFormHelper::addFieldPath(__DIR__ . '/fields');
	}

	/**
	 * @param   string     $context  The context for the data
	 * @param   integer    $data     The user id
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	public function onContentPrepareData($context, $data)
	{
		// Check we are manipulating a valid form.
		if (!in_array($context, array('com_users.profile', 'com_users.user', 'com_users.registration', 'com_admin.profile')))
		{
			return true;
		}

		if (is_object($data))
		{
			$userId = isset($data->id) ? $data->id : 0;
			if(!self::$_customer && !isset($data->digicom)){
				$db = JFactory::getDbo();
				$sql = 'SELECT * FROM #__digicom_customers WHERE id='.$userId;
				$db->setQuery($sql);
				$results = $db->loadAssoc();
				self::$_customer = $results;
			}
			$data->digicom = self::$_customer;

/*
			if (!JHtml::isRegistered('users.url'))
			{
				JHtml::register('users.url', array(__CLASS__, 'url'));
			}
			if (!JHtml::isRegistered('users.calendar'))
			{
				JHtml::register('users.calendar', array(__CLASS__, 'calendar'));
			}
			if (!JHtml::isRegistered('users.tos'))
			{
				JHtml::register('users.tos', array(__CLASS__, 'tos'));
			}
 */
		}
		return true;
	}

	public static function url($value)
	{
		if (empty($value))
		{
			return JHtml::_('users.value', $value);
		}
		else
		{
			// Convert website url to utf8 for display
			$value = JStringPunycode::urlToUTF8(htmlspecialchars($value));

			if (substr($value, 0, 4) == "http")
			{
				return '<a href="' . $value . '">' . $value . '</a>';
			}
			else
			{
				return '<a href="http://' . $value . '">' . $value . '</a>';
			}
		}
	}

	public static function calendar($value)
	{
		if (empty($value))
		{
			return JHtml::_('users.value', $value);
		}
		else
		{
			return JHtml::_('date', $value, null, null);
		}
	}

	public static function tos($value)
	{
		if ($value)
		{
			return JText::_('JYES');
		}
		else
		{
			return JText::_('JNO');
		}
	}

	/**
	 * @param   JForm    $form    The form to be altered.
	 * @param   array    $data    The associated data for the form.
	 *
	 * @return  boolean
	 * @since   1.6
	 */
	public function onContentPrepareForm($form, $data)
	{
		if( !self::$_addscript ){
			$script = "\n"
					. "\n".'function onPersonChange(echecked){'
					. "\n".'	if(!echecked) return;'
					. "\n".'	document.getElementById("jform_digicom_shipfirstname").value = document.getElementById("jform_digicom_firstname").value;'
					. "\n".'	document.getElementById("jform_digicom_shiplastname").value = document.getElementById("jform_digicom_lastname").value;'
					. "\n".'	document.getElementById("jform_digicom_shipaddress").value = document.getElementById("jform_digicom_shipaddress").value;'
					. "\n".'	document.getElementById("jform_digicom_shipcity").value = document.getElementById("jform_digicom_city").value;'
					. "\n".'	document.getElementById("jform_digicom_shipstate").value = document.getElementById("jform_digicom_state").value;'
					. "\n".'	document.getElementById("jform_digicom_shipprovince").value = document.getElementById("jform_digicom_province").value;'
					. "\n".'	document.getElementById("jform_digicom_shipzipcode").value = document.getElementById("jform_digicom_zipcode").value;'
					. "\n".'	document.getElementById("jform_digicom_shipcountry").value = document.getElementById("jform_digicom_country").value;'
					. "\n".'	document.getElementById("jform_digicom_shipphone").value = document.getElementById("jform_digicom_phone").value;'
					. "\n".'}'
					. "\n".''
					. "\n".'';
			$doc = JFactory::getDocument();
			$doc->addScriptDeclaration($script);
		}

		if (!($form instanceof JForm))
		{
			$this->_subject->setError('JERROR_NOT_A_FORM');
			return false;
		}

		// Check we are manipulating a valid form.
		$name = $form->getName();
		if (!in_array($name, array('com_admin.profile', 'com_users.user', 'com_users.profile', 'com_users.registration')))
		{
			return true;
		}

		// Add the registration fields to the form.
		JForm::addFormPath(__DIR__ . '/profiles');
		$form->loadFile('profile', false);
		return true;
	}


	public function onUserAfterSave($data, $isNew, $result, $error)
	{
		$app = JFactory::getApplication();
		$userId = JArrayHelper::getValue($data, 'id', 0, 'int');
		if ($userId && $result && isset($data['digicom']) && (count($data['digicom'])))
		{
			try
			{
				$db = JFactory::getDbo();
				$query = $db->getQuery(true)
					->delete($db->quoteName('#__digicom_customers'))
					->where($db->quoteName('id') . ' = ' . (int) $userId);
				$db->setQuery($query);
				$db->execute();

				if(!isset($data['digicom']['id'])){
					$data['digicom']['id'] = $userId;
				}

				$table_path = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_digicom'.DIRECTORY_SEPARATOR.'tables';
				JTable::addIncludePath($table_path);
				$customer = JTable::getInstance('Customer','Table');
				$customer->bind($data['digicom']);
				if(!$customer->store()){
					$msg = JText::_('PLG_USER_DIGICOM_MSG_ERROR_ON_STORE_CUSTOMER_INFORMATION');
					$app->enqueueMessage($msg,'error');
				}
			}
			catch (RuntimeException $e)
			{
				$this->_subject->setError($e->getMessage());
				return false;
			}
		}
		return true;
	}

	/**
	 * Remove all user profile information for the given user ID
	 *
	 * Method is called after user data is deleted from the database
	 *
	 * @param   array    $user     Holds the user data
	 * @param   boolean  $success  True if user was succesfully stored in the database
	 * @param   string   $msg      Message
	 *
	 * @return  boolean
	 */
	public function onUserAfterDelete($user, $success, $msg)
	{
		if (!$success)
		{
			return false;
		}
		$userId = JArrayHelper::getValue($user, 'id', 0, 'int');
		if ($userId)
		{
			try
			{
				$db = JFactory::getDbo();
				$sql = 'DELETE FROM #__digicom_customers WHERE id = ' . $userId;
				$db->setQuery( $sql );
				$db->execute();
			}
			catch ( Exception $e )
			{
				$this->_subject->setError($e->getMessage());
				return false;
			}
		}
		return true;
	}
}