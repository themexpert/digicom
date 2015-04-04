<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class DigiComViewProducts extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;

	protected $configs;

	function display( $tpl = null )
	{
		
		if ($this->getLayout() !== 'modal')
		{
			DigiComHelperDigiCom::addSubmenu('products');
		}
		
		// Access check.
		if (!JFactory::getUser()->authorise('digicom.products', 'com_digicom'))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}

		$this->items         = $this->get('Items');
		$this->pagination    = $this->get('Pagination');
		$this->state         = $this->get('State');
		$this->authors       = $this->get('Authors');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		$this->configs 		 = $this->get('configs');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));

			return false;
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

		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal')
		{
			//set toolber
			$this->addToolbar();			
			$this->sidebar = DigiComHelperDigiCom::renderSidebar();
		}
		
		
		parent::display( $tpl );
	}


	

	/**
	 * Add the page title and toolbar.
		*
	 * @since   1.6
	 */

	protected function addToolbar()
	{
		$canDo = JHelperContent::getActions('com_digicom', 'category', $this->state->get('filter.category_id'));
		$user  = JFactory::getUser();

		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');
		JToolbarHelper::title(JText::_('COM_DIGICOM_PRODUCTS_TOOLBAR_TITLE'), 'stack product');

		if ($canDo->get('core.create') || (count($user->getAuthorisedCategories('com_digicom', 'core.create'))) > 0 )
		{
			//JToolbarHelper::addNew('product.add');
			$layout = new JLayoutFile('toolbar.products');
			$bar->appendButton('Custom', $layout->render(array()), 'products');
		}

		
		if ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::publish('products.publish', 'JTOOLBAR_PUBLISH', true);
			JToolbarHelper::unpublish('products.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			//JToolbarHelper::custom('products.featured', 'featured.png', 'featured_f2.png', 'JFEATURE', true);
			//JToolbarHelper::custom('products.unfeatured', 'unfeatured.png', 'featured_f2.png', 'JUNFEATURE', true);
			//JToolbarHelper::archiveList('products.archive');
			//JToolbarHelper::checkin('products.checkin');
		}

		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete'))
		{
			JToolbarHelper::deleteList('', 'products.delete', 'JTOOLBAR_EMPTY_TRASH');
		}
		elseif ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::trash('products.trash');
		}

		// Instantiate a new JLayoutFile instance and render the layout
		$layout = new JLayoutFile('toolbar.title');
		$title=array(
				'title' => JText::_( 'COM_DIGICOM_PRODUCTS_TOOLBAR_TITLE' ),
				'class' => 'product'
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
			'a.publish' => JText::_('JSTATUS'),
			'a.name' => JText::_('JGLOBAL_TITLE'),
			'a.access' => JText::_('JGRID_HEADING_ACCESS'),
			'a.hits' => JText::_('JGLOBAL_HITS'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
	
}

