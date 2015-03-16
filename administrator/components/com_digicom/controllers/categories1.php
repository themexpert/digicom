<?php
/**
* @package			DigiCom Joomla Extension
 * @author			themexpert.com
 * @version			$Revision: 341 $
 * @lastmodified	$LastChangedDate: 2013-10-10 14:28:28 +0200 (Thu, 10 Oct 2013) $
 * @copyright		Copyright (C) 2013 themexpert.com. All rights reserved.
* @license			GNU/GPLv3
*/

defined ('_JEXEC') or die ("Go away.");

jimport ('joomla.application.component.controller');

class DigiComAdminControllerCategories extends JControllerAdmin {
	var $_model = null;
	var $option = 'com_digicom';
	var $text_prefix = 'COM_DIGICOM';
	var $view_list = 'categories';

	function __construct () {

		parent::__construct();

		$this->registerTask ("", "listCategories");
		$this->registerTask ("add", "add");
		$this->registerTask ("edit", "edit");
		$this->registerTask ("apply", "save");
		$this->registerTask ("unpublish", "publish");
		$this->registerTask ("orderdown", "orderdown");
		$this->registerTask ("orderup", "orderup");
		$this->registerTask ("saveorder", "saveorder");
		$this->registerTask ("saveOrderAjax", "saveOrderAjax");

		$this->_model = $this->getModel("Categories");
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
	public function getModel($name = 'Categories', $prefix = 'DigiComAdminModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}

	function listCategories() {
		JRequest::setVar ("view", "Categories");
		$view = $this->getView("Categories", "html");
		$view->setModel($this->_model, true);
		$view->display();
	}


	function add () {
		$url =  "index.php?option=com_digicom&controller=categories&task=edit";
		$this->setRedirect($url);

	}

	function edit () {
		$model = $this->getModel("Category");
		$this->input->setVar('hidemainmenu',1);
		$id = $this->input->get('id');
		$cid = $this->input->get('cid', array(), 'ARRAY');
		
		if(isset($cid[0]) and empty($id)) $this->setRedirect('index.php?option=com_digicom&controller=categories&task=edit&id='.$cid[0]);

		$view = $this->getView("Categories", "html");
		$view->setLayout("editForm");
		$view->setModel($model, true);
		$view->editForm();

	}

	function save () {

		$data = JFactory::getApplication()->input->get('jform', array(), 'ARRAY');
		//print_r($data);die;
		$model = $this->getModel("Category");
		$result = $model->save($data);
		//print_r($result);die;
		if ($result) {
			$msg = JText::_('CATEGORYSAVED');
		} else {
			$msg = JText::_('CATEGORYSAVEFAILED');
		}

		if ( JRequest::getVar('task','') == 'save' ) {
			$save_url =  "index.php?option=com_digicom&controller=categories";
			$this->setRedirect($save_url, $msg);
		} else {
			$data = JFactory::getApplication()->input->get('jform', array(), 'ARRAY');
			$category_id = $data['id'];
			$apply_url = "index.php?option=com_digicom&controller=categories&task=edit&id=" . $category_id;
			$this->setRedirect($apply_url, $msg);
		}
	}

	function remove () {
		if (!$this->_model->delete()) {
			$msg = JText::_('CATEGORYREMOVEFAILL');
		} else {
			$msg = JText::_('CATEGORYREMOVEFAILL');
		}

		$link = "index.php?option=com_digicom&controller=categories";
		$this->setRedirect($link, $msg);
	}

	function cancel () {
		$msg = JText::_('CATEGORYCANCELED');
		$link = "index.php?option=com_digicom&controller=categories";
		$this->setRedirect($link, $msg);
	}

	/*
	function publish () {
		$res = $this->_model->publish();

		if (!$res) {
			$msg = JText::_('CATEGORYPUBLISHINGERROR');
		} elseif ($res == -1) {
			$msg = JText::_('CATEGORYUNPUBLISHINGSUCC');
		} elseif ($res == 1) {
			$msg = JText::_('CATEGORYPUBLISHINGSUCC');
		} else {
			$msg = JText::_('CATEGORYUNSPECERROR');
		}

		$link = "index.php?option=com_digicom&controller=categories";
		$this->setRedirect($link, $msg);


	}
	*/

	function orderdown() {
		$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
		$this->_model->orderField( $cid[0], 1 );
		$link = "index.php?option=com_digicom&controller=categories";
		$this->setRedirect($link, $msg);
	}

	function orderup() {
		$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
		$msg = $this->_model->orderField( $cid[0], -1 );
		$link = "index.php?option=com_digicom&controller=categories";
		$this->setRedirect($link, $msg);
	}

	/**
	 * Save the manual order inputs from the categories list page.
	 *
	 * @return      void
	 *
	 * @since       1.6
	 * @see         JControllerAdmin::saveorder()
	 * @deprecated  4.0
	 */
	public function saveorder()
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Get the arrays from the Request
		$order = $this->input->post->get('order', null, 'array');
		$originalOrder = explode(',', $this->input->getString('original_order_values'));

		// Make sure something has changed
		if (!($order === $originalOrder))
		{
			// Get the input
			$pks = $this->input->post->get('cid', array(), 'array');
			$order = $this->input->post->get('order', array(), 'array');
			
			// Sanitize the input
			JArrayHelper::toInteger($pks);
			JArrayHelper::toInteger($order);

			// Save the ordering
			$return = $this->_model->saveorder($pks, $order);

			if ($return === false)
			{
				// Reorder failed
				$message = JText::sprintf('JLIB_APPLICATION_ERROR_REORDER_FAILED', $this->_model->getError());
				$this->setRedirect(JRoute::_('index.php?option=com_digicom&controller=categories', false), $message, 'error');

				return false;
			}
			else
			{
				// Reorder succeeded.
				$this->setMessage(JText::_('JLIB_APPLICATION_SUCCESS_ORDERING_SAVED'));
				$this->setRedirect(JRoute::_('index.php?option=com_digicom&controller=categories', false));

				return true;
			}

		}
		else
		{
			// Nothing to reorder
			$this->setRedirect(JRoute::_('index.php?option=com_digicom&controller=categories', false));

			return true;
		}
	}

	/**
	 * Method to save the submitted ordering values for records via AJAX.
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public function saveOrderAjax()
	{
		//print_r($_GET);die;
		// Get the input
		$pks = $this->input->post->get('cid', array(), 'array');
		$order = $this->input->post->get('order', array(), 'array');
		
		$pks = $this->input->get->get('cid', array(), 'array');
		$order = $this->input->get->get('order', array(), 'array');

		// Sanitize the input
		JArrayHelper::toInteger($pks);
		JArrayHelper::toInteger($order);

		// Save the ordering
		$return = $this->_model->saveorder($pks, $order);

		if ($return)
		{
			echo "1";
		}

		// Close the application
		JFactory::getApplication()->close();
	}

}
