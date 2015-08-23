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
 * Products controller class.
 *
 * @since  1.0
 */
class DigiComControllerProducts extends JControllerAdmin
{

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   1.0.0
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->registerTask('unfeatured', 'featured');
	}

	/**
	 * Method to clone an existing products.
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function duplicate()
	{
		$model = $this->getModel();

		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$pks = $this->input->post->get('cid', array(), 'array');
		JArrayHelper::toInteger($pks);

		if (empty($pks))
		{
			throw new Exception(JText::_('COM_DIGICOM_ERROR_NO_PRODUCTS_SELECTED'));
		}

		// dulicate the selected the items.
		if (!$model->duplicate($pks))
		{
			JError::raiseWarning(500, $model->getError());
		}else{
			$this->setMessage(JText::plural('COM_DIGICOM_N_PRODUCTS_DUPLICATED', count($pks)));
		}

		$this->setRedirect('index.php?option=com_digicom&view=products');
	}

	/**
	 * Method to toggle the featured setting of a list of products.
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function featured()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$user   = JFactory::getUser();
		$ids    = $this->input->get('cid', array(), 'array');
		$values = array('featured' => 1, 'unfeatured' => 0);
		$task   = $this->getTask();
		$value  = JArrayHelper::getValue($values, $task, 0, 'int');

		// Access checks.
		foreach ($ids as $i => $id)
		{
			if (!$user->authorise('core.edit.state', 'com_digicom.product.' . (int) $id))
			{
				// Prune items that you can't change.
				unset($ids[$i]);
				JError::raiseNotice(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
			}
		}

		if (empty($ids))
		{
			JError::raiseWarning(500, JText::_('JERROR_NO_ITEMS_SELECTED'));
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			// Publish the items.
			if (!$model->featured($ids, $value))
			{
				JError::raiseWarning(500, $model->getError());
			}

			if ($value == 1)
			{
				$message = JText::plural('COM_DIGICOM_N_ITEMS_FEATURED', count($ids));
			}
			else
			{
				$message = JText::plural('COM_DIGICOM_N_ITEMS_UNFEATURED', count($ids));
			}
		}

		$view = $this->input->get('view', '');

		if ($view == 'featured')
		{
			$this->setRedirect(JRoute::_('index.php?option=com_digicom&view=products', false), $message);
		}
		else
		{
			$this->setRedirect(JRoute::_('index.php?option=com_digicom&view=products', false), $message);
		}
	}

	/**
	 * Proxy for getModel
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  The array of possible config values. Optional.
	 *
	 * @return  object  The model.
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'Product', $prefix = 'DigiComModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}
}
