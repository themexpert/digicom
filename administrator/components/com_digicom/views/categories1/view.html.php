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

class DigiComAdminViewCategories extends DigiComView {

	protected $items;

	protected $pagination;

	protected $state;

	protected $assoc;

	function display($tpl=null)
	{
		

		$this->state         = $this->get('State');
		$this->items         = $this->get('Items');
		$this->pagination    = $this->get('Pagination');
		$this->assoc         = $this->get('Assoc');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));

			return false;
		}

		// Preprocess the list of items to find ordering divisions.
		foreach ($this->items as &$item)
		{
			$this->ordering[$item->parent_id][] = $item->id;
		}

		// Levels filter.
		$options	= array();
		$options[]	= JHtml::_('select.option', '1', JText::_('J1'));
		$options[]	= JHtml::_('select.option', '2', JText::_('J2'));
		$options[]	= JHtml::_('select.option', '3', JText::_('J3'));
		$options[]	= JHtml::_('select.option', '4', JText::_('J4'));
		$options[]	= JHtml::_('select.option', '5', JText::_('J5'));
		$options[]	= JHtml::_('select.option', '6', JText::_('J6'));
		$options[]	= JHtml::_('select.option', '7', JText::_('J7'));
		$options[]	= JHtml::_('select.option', '8', JText::_('J8'));
		$options[]	= JHtml::_('select.option', '9', JText::_('J9'));
		$options[]	= JHtml::_('select.option', '10', JText::_('J10'));

		$this->f_levels = $options;

		//set toolber
		$this->addToolbar();
		
		DigiComAdminHelper::addSubmenu('categories');
		$this->sidebar = DigiComAdminHelper::renderSidebar();
		
		parent::display($tpl);
	}

	function editForm($tpl = null)
	{
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		//print_r($this->item);die;
		$this->state = $this->get('State');
		$section = 'category';
		$component = 'com_digicom';
		$this->canDo = JHelperContent::getActions($component , 'category', $this->item->id);
		$this->assoc = $this->get('Assoc');

		$input = JFactory::getApplication()->input;

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));

			return false;
		}

		// Check for tag type
		$this->checkTags = JHelperTags::getTypes('objectList', array($this->state->get('category.extension') . '.category'), true);

		$input->set('hidemainmenu', true);

		if ($this->getLayout() == 'modal')
		{
			$this->form->setFieldAttribute('language', 'readonly', 'true');
			$this->form->setFieldAttribute('parent_id', 'readonly', 'true');
		}

		DigiComAdminHelper::addSubmenu('categories');
		$this->sidebar = DigiComAdminHelper::renderSidebar();

		$this->addToolbarEdit();
		parent::display($tpl);

	}
	
	/**
	 * Add the page title and toolbar.
		*
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		$categoryId	= $this->state->get('filter.category_id');
		$component	= $this->state->get('filter.component','com_digicom');
		$section	= $this->state->get('filter.section','category');
		$canDo		= JHelperContent::getActions($component, 'category', $categoryId);
		$user		= JFactory::getUser();
		$extension  = JFactory::getApplication()->input->get('extension', 'com_digicom', 'word');

		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');

		// Need to load the menu language file as mod_menu hasn't been loaded yet.
		$lang = JFactory::getLanguage();
		$lang->load($component, JPATH_BASE, null, false, true)
		|| $lang->load($component, JPATH_ADMINISTRATOR . '/components/' . $component, null, false, true);

		// Load the category helper.
		//require_once JPATH_COMPONENT . '/helpers/categories.php';

		// If a component categories title string is present, let's use it.
		if ($lang->hasKey($component_title_key = strtoupper($component . ($section ? "_$section" : '')) . '_CATEGORIES_TITLE'))
		{
			$ptitle = JText::_($component_title_key);
		}
		elseif ($lang->hasKey($component_section_key = strtoupper($component . ($section ? "_$section" : ''))))
		// Else if the component section string exits, let's use it
		{
			$ptitle = JText::sprintf('COM_DIGICOM_CATEGORIES_TITLE', $this->escape(JText::_($component_section_key)));
		}
		else
		// Else use the base title
		{
			$ptitle = JText::_('COM_DIGICOM_CATEGORIES_BASE_TITLE');
		}

		// Prepare the toolbar.
		JToolbarHelper::title($ptitle, 'folder categories ' . substr($component, 4) . ($section ? "-$section" : '') . '-categories');

		if ($canDo->get('core.create') || (count($user->getAuthorisedCategories($component, 'core.create'))) > 0)
		{
			JToolbarHelper::addNew('add');
		}

		if ($canDo->get('core.edit') || $canDo->get('core.edit.own'))
		{
			JToolbarHelper::editList('edit');
		}

		if ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::publish('publish', 'JTOOLBAR_PUBLISH', true);
			JToolbarHelper::unpublish('unpublish', 'JTOOLBAR_UNPUBLISH', true);
			//JToolbarHelper::archiveList('archive');
		}

		if (JFactory::getUser()->authorise('core.admin'))
		{
			JToolbarHelper::checkin('checkin');
		}

		JToolBarHelper::deleteList();

		// Compute the ref_key if it does exist in the component
		if (!$lang->hasKey($ref_key = strtoupper($component . ($section ? "_$section" : '')) . '_CATEGORIES_HELP_KEY'))
		{
			$ref_key = 'JHELP_COMPONENTS_' . strtoupper(substr($component, 4) . ($section ? "_$section" : '')) . '_CATEGORIES';
		}

		/*
		 * Get help for the categories view for the component by
		 * -remotely searching in a language defined dedicated URL: *component*_HELP_URL
		 * -locally  searching in a component help file if helpURL param exists in the component and is set to ''
		 * -remotely searching in a component URL if helpURL param exists in the component and is NOT set to ''
		 */
		if ($lang->hasKey($lang_help_url = strtoupper($component) . '_HELP_URL'))
		{
			$debug = $lang->setDebug(false);
			$url = JText::_($lang_help_url);
			$lang->setDebug($debug);
		}
		else
		{
			$url = null;
		}

		JToolbarHelper::help($ref_key, JComponentHelper::getParams($component)->exists('helpURL'), $url);

		$bar = JToolBar::getInstance('toolbar');
		// Instantiate a new JLayoutFile instance and render the layout
		$layout = new JLayoutFile('toolbar.title');
		$title=array(
			'title' => $ptitle,  
			'class' => 'title'
		);
		$bar->appendButton('Custom', $layout->render($title), 'title');
		
		$layout = new JLayoutFile('toolbar.settings');
		$bar->appendButton('Custom', $layout->render(array()), 'settings');
	}
	
	
	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   3.0
	 */
	protected function getSortFields()
	{
		return array(
			'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
			'a.published' => JText::_('JSTATUS'),
			'a.name' => JText::_('JGLOBAL_TITLE'),
			'a.access' => JText::_('JGRID_HEADING_ACCESS'),
			'a.hits' => JText::_('JGLOBAL_HITS'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}

	/**
	 * Add the page title and toolbar.
		*
	 * @since   1.6
	 */
	protected function addToolbarEdit(){
		$input = JFactory::getApplication()->input;
		$extension = $input->get('extension','com_digicom');
		$user = JFactory::getUser();
		$userId = $user->get('id');

		$isNew = ($this->item->id == 0);
		$checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $userId);

		// Check to see if the type exists
		$ucmType = new JUcmType;
		$this->typeId = $ucmType->getTypeId($extension . '.category');

		// The extension can be in the form com_foo.section
		$component = 'com_digicom';
		$section = 'category';
		$componentParams = JComponentHelper::getParams($component);

		// Need to load the menu language file as mod_menu hasn't been loaded yet.
		$lang = JFactory::getLanguage();
		$lang->load($component, JPATH_BASE, null, false, true)
		|| $lang->load($component, JPATH_ADMINISTRATOR . '/components/' . $component, null, false, true);

		// Get the results for each action.
		$canDo = $this->canDo;

		// If a component categories title string is present, let's use it.
		if ($lang->hasKey($component_title_key = $component . ($section ? "_$section" : '') . '_CATEGORY_' . ($isNew ? 'ADD' : 'EDIT') . '_TITLE'))
		{
			$title = JText::_($component_title_key);
		}
		// Else if the component section string exits, let's use it
		elseif ($lang->hasKey($component_section_key = $component . ($section ? "_$section" : '')))
		{
			$title = JText::sprintf('COM_DIGICOM_CATEGORIES_' . ($isNew ? 'ADD' : 'EDIT') . '_TITLE', $this->escape(JText::_($component_section_key)));
		}
		// Else use the base title
		else
		{
			$title = JText::_('COM_DIGICOM_CATEGORIES_BASE_' . ($isNew ? 'ADD' : 'EDIT') . '_TITLE');
		}

		// Prepare the toolbar.
		JToolbarHelper::title(
			$title,
			'folder category-' . ($isNew ? 'add' : 'edit')
				. ' ' . substr($component, 4) . ($section ? "-$section" : '') . '-category-' . ($isNew ? 'add' : 'edit')
		);

		// For new records, check the create permission.
		if ($isNew && (count($user->getAuthorisedCategories($component, 'core.create')) > 0))
		{
			JToolbarHelper::apply('apply');
			JToolbarHelper::save('save');
			JToolbarHelper::save2new('save2new');
		}

		// If not checked out, can save the item.
		elseif (!$checkedOut && ($canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_user_id == $userId)))
		{
			JToolbarHelper::apply('apply');
			JToolbarHelper::save('save');

			if ($canDo->get('core.create'))
			{
				JToolbarHelper::save2new('save2new');
			}
		}

		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create'))
		{
			JToolbarHelper::save2copy('save2copy');
		}

		if (empty($this->item->id))
		{
			JToolbarHelper::cancel('cancel');
		}
		else
		{
			if ($componentParams->get('save_history', 0) && $user->authorise('core.edit'))
			{
				$typeAlias = $extension . '.category';
				JToolbarHelper::versions($typeAlias, $this->item->id);
			}

			JToolbarHelper::cancel('cancel', 'JTOOLBAR_CLOSE');
		}

		$bar = JToolBar::getInstance('toolbar');
		$layout = new JLayoutFile('toolbar.title');
		$title=array(
			'title' => $title,
			'class' => 'product'
		);
		$bar->appendButton('Custom', $layout->render($title), 'title');

		$layout = new JLayoutFile('toolbar.settings');
		$bar->appendButton('Custom', $layout->render(array()), 'settings');

		JToolbarHelper::divider();

		// Compute the ref_key if it does exist in the component
		if (!$lang->hasKey($ref_key = strtoupper($component . ($section ? "_$section" : '')) . '_CATEGORY_' . ($isNew ? 'ADD' : 'EDIT') . '_HELP_KEY'))
		{
			$ref_key = 'JHELP_COMPONENTS_' . strtoupper(substr($component, 4) . ($section ? "_$section" : '')) . '_CATEGORY_' . ($isNew ? 'ADD' : 'EDIT');
		}

		/* Get help for the category/section view for the component by
		 * -remotely searching in a language defined dedicated URL: *component*_HELP_URL
		 * -locally  searching in a component help file if helpURL param exists in the component and is set to ''
		 * -remotely searching in a component URL if helpURL param exists in the component and is NOT set to ''
		 */
		if ($lang->hasKey($lang_help_url = strtoupper($component) . '_HELP_URL'))
		{
			$debug = $lang->setDebug(false);
			$url = JText::_($lang_help_url);
			$lang->setDebug($debug);
		}
		else
		{
			$url = null;
		}

		JToolbarHelper::help($ref_key, $componentParams->exists('helpURL'), $url, $component);
	}
}

