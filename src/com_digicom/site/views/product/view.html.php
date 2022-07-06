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
 * HTML product View class
 *
 * @since  1.0.0
 */
class DigiComViewProduct extends JViewLegacy
{
	protected $item;

	protected $category;

	protected $params;

	protected $print;

	protected $state;

	protected $user;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 */
	public function display($tpl = null)
	{
		$app		= JFactory::getApplication();
		$user		= JFactory::getUser();
		$dispatcher	= JEventDispatcher::getInstance();

		$this->item		= $this->get('Item');
		$this->print	= $app->input->getBool('print');
		$this->state	= $this->get('State');
		$this->user		= $user;
		$this->configs = JComponentHelper::getComponent('com_digicom')->params;

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseWarning(500, implode("\n", $errors));

			return false;
		}

		// Create a shortcut for $item.
		$item = $this->item;
		$item->tagLayout = new JLayoutFile('joomla.content.tags');

		// Add router helpers.
		$item->slug			= $item->alias ? ($item->id . ':' . $item->alias) : $item->id;
		$item->catslug		= $item->category_alias ? ($item->catid . ':' . $item->category_alias) : $item->catid;
		$item->parent_slug	= $item->parent_alias ? ($item->parent_id . ':' . $item->parent_alias) : $item->parent_id;

		// No link for ROOT category
		if ($item->parent_alias == 'root')
		{
			$item->parent_slug = null;
		}

		// TODO: Change based on shownoauth
		$item->readmore_link = JRoute::_(DigiComSiteHelperRoute::getproductRoute($item->id, $item->catid, $item->language));

		// Merge product params. If this is single-product view, menu params override product params
		// Otherwise, product params override menu item params
		$this->params = $this->state->get('params');
		$active = $app->getMenu()->getActive();
		$temp = clone $this->params;

		// Check to see which parameters should take priority
		if ($active)
		{
			$currentLink = $active->link;

			// If the current view is the active item and an product view for this product, then the menu item params take priority
			if (strpos($currentLink, 'view=product') && (strpos($currentLink, '&id=' . (string) $item->id)))
			{

				// $item->params are the product params, $temp are the menu item params
				// Merge so that the menu item params take priority
				$item->params->merge($temp);

			}
			else
			{
				// Current view is not a single product, so the product params take priority here
				// Merge the menu item params with the product params so that the product params take priority
				$temp->merge($item->params);
				$item->params = $temp;

			}
		}
		else
		{
			// Merge so that product params take priority
			$temp->merge($item->params);
			$item->params = $temp;

		}

		$offset = $this->state->get('list.offset');

		// Check the view access to the product (the model has already computed the values).
		if ($item->params->get('access-view') == false && ($item->params->get('show_noauth', '0') == '0'))
		{
			JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));

			return;
		}

		$item->text = $item->fulltext;

		$item->tags = new JHelperTags;
		$item->tags->getItemTags('com_digicom.product', $this->item->id);

		// Process the content plugins.

		JPluginHelper::importPlugin('content');
		$dispatcher->trigger('onContentPrepare', array ('com_digicom.product', &$item, &$this->params, $offset));

		$item->event = new stdClass;
		$results = $dispatcher->trigger('onContentAfterTitle', array('com_digicom.product', &$item, &$this->params, $offset));
		$item->event->afterDisplayTitle = trim(implode("\n", $results));

		$results = $dispatcher->trigger('onContentBeforeDisplay', array('com_digicom.product', &$item, &$this->params, $offset));
		$item->event->beforeDisplayContent = trim(implode("\n", $results));

		$results = $dispatcher->trigger('onContentAfterDisplay', array('com_digicom.product', &$item, &$this->params, $offset));
		$item->event->afterDisplayContent = trim(implode("\n", $results));

		// Increment the hit counter of the product.
		if (!$this->params->get('intro_only') && $offset == 0)
		{
			$model = $this->getModel();
			$model->hit();
		}

		// Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars((string)$this->item->params->get('pageclass_sfx'));

		$this->_prepareDocument();
		$this->category->params = $this->category->getParams();

		// Get the layout from the merged category params
		if ($layout = $this->category->params->get('category_layout'))
		{
			$this->setLayout($layout);
		}else{
			$layout = $this->configs->get('template','default');
			$this->setLayout($layout);
		}

		$template = new DigiComSiteHelperTemplate($this);
		$template->rander('product', $this->getLayout());

		parent::display($tpl);
	}

	/**
	 * Prepares the document.
	 *
	 * @return  void.
	 */
	protected function _prepareDocument()
	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu();
		$pathway	= $app->getPathway();
		$title		= null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', JText::_('COM_DIGICOM_PRODUCT'));
		}

		$title = $this->params->get('page_title', '');

		$id = (int) @$menu->query['id'];

		// If the menu item does not concern this product
		if ($menu && ($menu->query['option'] != 'com_digicom' || $menu->query['view'] != 'product' || $id != $this->item->id))
		{
			// If this is not a single product menu item, set the page title to the product title
			if ($this->item->name)
			{
				$title = $this->item->name;
			}
			if ($this->item->metatitle)
			{
				$title = $this->item->metatitle;
			}

			$path = array(array('title' => $this->item->name, 'link' => ''));
			$this->category = $category = JCategories::getInstance('Digicom')->get($this->item->catid);

			$categoryParams = new Registry;
			$categoryParams->loadString($category->getParams());
			$category_layout = $categoryParams->get('category_layout','');
			if(!empty($category_layout)){
				$currentTemplate = true;
			}else{
				$currentTemplate = false;
			}
			// $mergedParams = clone $categoryParams;

			while ($category && ($menu->query['option'] != 'com_digicom' || $menu->query['view'] == 'product' || $id != $category->id) && $category->id > 1)
			{
				$path[] = array('title' => $category->title, 'link' => DigiComSiteHelperRoute::getCategoryRoute($category->id));
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

			// get the parent category
			$this->category->params = $categoryParams;

			$path = array_reverse($path);

			foreach ($path as $item)
			{
				$pathway->addItem($item['title'], $item['link']);
			}
		}else{
			$this->category = $category = JCategories::getInstance('Digicom')->get($this->item->catid);
		}

		// Check for empty title and add site name if param is set
		if (empty($title))
		{
			$title = $app->get('sitename');
		}
		elseif ($app->get('sitename_pagetitles', 0) == 1)
		{
			$title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
		}
		elseif ($app->get('sitename_pagetitles', 0) == 2)
		{
			$title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
		}

		if (empty($title))
		{
			$title = $this->item->name;
		}

		$this->document->setTitle($title);

		if ($this->item->metadesc)
		{
			$this->document->setDescription($this->item->metadesc);
		}
		elseif ($this->item->introtext)
		{
			$this->document->setDescription($this->item->introtext);
		}
		elseif ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->item->metakey)
		{
			$this->document->setMetadata('keywords', $this->item->metakey);
		}
		elseif (!$this->item->metakey && $this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}

		if ($app->get('MetaAuthor') == '1')
		{
			$author = $this->item->author;
			$this->document->setMetaData('author', $author);
		}

		$mdata = $this->item->metadata->toArray();

		foreach ($mdata as $k => $v)
		{
			if ($v)
			{
				$this->document->setMetadata($k, $v);
			}
		}

		// If there is a pagebreak heading or title, add it to the page title
		if (!empty($this->item->page_title))
		{
			$this->item->name = $this->item->name . ' - ' . $this->item->page_title;
			$this->document->setTitle(
				$this->item->page_title . ' - ' . JText::sprintf('PLG_CONTENT_PAGEBREAK_PAGE_NUM', $this->state->get('list.offset') + 1)
			);
		}

		if ($this->print)
		{
			$this->document->setMetaData('robots', 'noindex, nofollow');
		}
	}
}
