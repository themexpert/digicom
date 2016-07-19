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
 * DigiCom Main Controller
 *
 * @since  1.0.0
 */
class DigiComController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JControllerLegacy  This object to support chaining.
	 *
	 * @since   1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		DigiComHelperDigiCom::addAdminStyles();
		$view   = $this->input->get('view', 'digicom');
		$layout = $this->input->get('layout', 'default');
		$id     = $this->input->getInt('id');

		//all edit view
		$editviews = array("category", "product", "customer", "order", "license", "ordernew", "discount");
		// Check for edit form.
		if (in_array($view, $editviews) && $layout == 'edit' && !$this->checkEditId('com_digicom.edit.'.$view, $id))
		{
			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_digicom', false));

			return false;
		}

		return parent::display();
	}

	public function action(){
		$input = JFactory::getApplication()->input;
		$class = $input->get('class');
		$action =  $input->get('action');
		$country =  $input->get('country');
		$field_name =  $input->get('field_name');

		$result = $class::$action($country, $field_name);
		echo json_encode($result);
		JFactory::getApplication()->close();
	}

}
