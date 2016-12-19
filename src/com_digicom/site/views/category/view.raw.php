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
 * HTML View class
 *
 * @since  1.5
 */
class DigiComViewCategory extends JViewCategory
{
	/**
	 * @var    array  Array of products to display
	 * @since  3.2
	 */
	protected $items = array();

	/**
	 * @var    array  Array of intro (multicolumn display) items for blog display
	 * @since  3.2
	 */
	protected $intro_items = array();

	/**
	 * @var    array  Array of links in blog display
	 * @since  3.2
	 */
	protected $link_items = array();

	/**
	 * @var    integer  Number of columns in a multi column display
	 * @since  3.2
	 */
	protected $columns = 1;

	/**
	 * @var    string  The name of the extension for the category
	 * @since  3.2
	 */
	protected $extension = 'com_digicom';

	/**
	 * @var    string  The name of the view to link individual items to
	 * @since  3.2
	 */
	protected $viewName = 'product';

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 */
	public function display($tpl = null)
	{
		//parent::commonCategoryDisplay();
		$this->commonCategoryDisplay();

		// import plugins
		JPluginHelper::importPlugin('content');
		JPluginHelper::importPlugin('digicom');
		$dispatcher = JEventDispatcher::getInstance();
		$this->configs = JComponentHelper::getComponent('com_digicom')->params;

		// Prepare the data
		// Get the metrics for the structural page layout.
		$params		= $this->params;
		$numLeading	= 0;
		$numIntro	= $params->def('num_products', 9);
		$numLinks	= 0;

		// Prepare category
		$this->category->event = new stdClass;
		$results = $dispatcher->trigger('onDigicomBeforeCategory', array('com_digicom.category', &$this->category, &$this->params));
		$this->category->event->beforeDisplayContent = trim(implode("\n", $results));

		$results = $dispatcher->trigger('onDigicomBeforeItems', array('com_digicom.category', &$this->category, &$this->params));
		$this->category->event->beforeDisplayItems = trim(implode("\n", $results));
		
		$results = $dispatcher->trigger('onDigicomAfterCategory', array('com_digicom.category', &$this->category, &$this->params));
		$this->category->event->afterDisplayContent = trim(implode("\n", $results));

		// Compute the product slugs and prepare introtext (runs content plugins).
		if(count($this->items))
		{
			foreach ($this->items as $item)
			{
				$item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;

				$item->parent_slug = ($item->parent_alias) ? ($item->parent_id . ':' . $item->parent_alias) : $item->parent_id;

				// No link for ROOT category
				if ($item->parent_alias == 'root')
				{
					$item->parent_slug = null;
				}

				$item->catslug = $item->category_alias ? ($item->catid . ':' . $item->category_alias) : $item->catid;
				$item->event   = new stdClass;

				// Old plugins: Ensure that text property is available
				if (!isset($item->text))
				{
					$item->text = $item->introtext;
				}

				$dispatcher->trigger('onContentPrepare', array ('com_digicom.category', &$item, &$item->params, 0));

				// Old plugins: Use processed text as introtext
				$item->introtext = $item->text;

				$results = $dispatcher->trigger('onContentAfterTitle', array('com_digicom.category', &$item, &$item->params, 0));
				$item->event->afterDisplayTitle = trim(implode("\n", $results));

				$results = $dispatcher->trigger('onContentBeforeDisplay', array('com_digicom.category', &$item, &$item->params, 0));
				$item->event->beforeDisplayContent = trim(implode("\n", $results));

				$results = $dispatcher->trigger('onContentAfterDisplay', array('com_digicom.category', &$item, &$item->params, 0));
				$item->event->afterDisplayContent = trim(implode("\n", $results));

			}
		}else{
			$this->items = array();
		}

		// Check for layout override only if this is not the active menu item
		// If it is the active menu item, then the view and category id will match
		$app 		= JFactory::getApplication();
		$menus	= $app->getMenu();
		$title	= null;

		// Get the layout from the merged category params
		if ($layout = $this->category->params->get('category_layout'))
		{
			$this->setLayout($layout);
		}
		else{
			$layout = $this->configs->get('template','default');
			$this->setLayout($layout);
		}

		$this->columns = max(1, $params->def('num_columns', 1));

		$order = $params->def('multi_column_order', 1);

		if ($order == 0 && $this->columns > 1)
		{
			// Call order down helper
			$this->intro_items = DigiComHelperQuery::orderDownColumns($this->intro_items, $this->columns);
		}

		$template = new DigiComSiteHelperTemplate($this);
		$template->rander('category', $this->getLayout());

		return parent::display($tpl);

	}

	/**
	 * Method with common display elements used in category list displays
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	public function commonCategoryDisplay()
	{
		$app    = JFactory::getApplication();
		$menus		= $app->getMenu();
		$user   = JFactory::getUser();
		$params = $app->getParams();

		// Get some data from the models
		$state      = $this->get('State');
		$items      = $this->get('Items');
		$category   = $this->get('Category');
		$children   = $this->get('Children');
		$parent     = $this->get('Parent');
		$pagination = $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));

			return false;
		}

		if ($category == false)
		{
			return JError::raiseError(404, JText::_('JGLOBAL_CATEGORY_NOT_FOUND'));
		}

		// Check whether category access level allows access.
		$groups = $user->getAuthorisedViewLevels();

		if (!in_array($category->access, $groups))
		{
			return JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
		}

		// Lets get template layout
		$categoryParams = new Registry;
		$categoryParams->loadString($category->getParams());
		$category_layout = $categoryParams->get('category_layout','');
		if(!empty($category_layout)){
			$currentTemplate = true;
		}else{
			$currentTemplate = false;
		}

		if(!$currentTemplate){
			while ($category && $category->id > 1 && is_int($category->id))
			{
				$category = $category->getParent();

				if($currentTemplate) continue;

				$catParams = new Registry;
				$catParams->loadString($category->getParams());

				$category_layout = $catParams->get('category_layout','');
				if(!empty($category_layout)){
					$currentTemplate = true;
					$categoryParams->set('category_layout',$catParams->get('category_layout'));
				}else{
					$currentTemplate = false;
				}

			}

		}

		// lets marge
		$category->params = $categoryParams;
		$category->params = clone $params;
		$category->params->merge($categoryParams);

		$children = array($category->id => $children);

		// Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

		$maxLevel         = $params->get('maxLevel', -1);
		$this->maxLevel   = &$maxLevel;
		$this->state      = &$state;
		$this->items      = &$items;
		$this->category   = &$category;
		$this->children   = &$children;
		$this->params     = &$params;
		$this->parent     = &$parent;
		$this->pagination = &$pagination;
		$this->user       = &$user;

		$this->category->tags = new JHelperTags;
		$this->category->tags->getItemTags($this->extension . '.category', $this->category->id);
	}
}
