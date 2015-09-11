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
 * View to edit a product.
 *
 * @since  1.5
 */
class DigiComViewProduct extends JViewLegacy
{
	protected $state;

	protected $item;

	protected $form;

	protected $configs;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');
		$this->configs 		 = $this->get('configs');
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal')
		{
			//set toolber
			DigiComHelperDigiCom::addSubmenu('products');
			$this->addToolbar();
			$this->sidebar = DigiComHelperDigiCom::renderSidebar();
		}

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));

		// Since we don't track these assets at the item level, use the category id.
		$canDo		= JHelperContent::getActions('com_digicom', 'category', $this->item->catid);

		$text = ($isNew ? JText::_('COM_DIGICOM_MANAGER_PRODUCT_NEW_TITLE') : JText::_('COM_DIGICOM_MANAGER_PRODUCT_EDIT_TITLE') );

		JToolbarHelper::title(JText::_('COM_DIGICOM_MANAGER_PRODUCT_TITLE_SITE'), 'link products');


		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit')||(count($user->getAuthorisedCategories('com_digicom', 'core.create')))))
		{
			JToolbarHelper::apply('product.apply');
			JToolbarHelper::save('product.save');
		}

		if (!$checkedOut && (count($user->getAuthorisedCategories('com_digicom', 'core.create'))))
		{
			JToolbarHelper::save2new('product.save2new');
		}

		// If an existing item, can save to a copy.
		if (!$isNew && (count($user->getAuthorisedCategories('com_digicom', 'core.create')) > 0))
		{
			JToolbarHelper::save2copy('product.save2copy');
		}

		if (empty($this->item->id))
		{
			JToolbarHelper::cancel('product.cancel');
		}
		else
		{
			if ($this->state->params->get('save_history', 0) && $user->authorise('core.edit'))
			{
				JToolbarHelper::versions('com_digicom.product', $this->item->id);
			}

			JToolbarHelper::cancel('product.cancel', 'JTOOLBAR_CLOSE');
		}

		JToolbarHelper::divider();

		$bar = JToolBar::getInstance('toolbar');

		$layout = new JLayoutFile('toolbar.video');
		$bar->appendButton('Custom', $layout->render(array()), 'video');

		$layout = new JLayoutFile('toolbar.title');
		$title=array(
				'title' => $text,
				'class' => 'product'
			);
		$bar->appendButton('Custom', $layout->render($title), 'title');

	}
}
