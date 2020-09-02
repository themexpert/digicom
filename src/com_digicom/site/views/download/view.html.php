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
 * HTML download View class
 *
 * @since  1.0.0
 */
class DigiComViewDownload extends JViewLegacy
{
	protected $item;

	protected $configs;

	protected $state;

	protected $params;

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


		$customer = new DigiComSiteHelperSession();

		$this->item		= $this->get('Item');
		$this->configs = JComponentHelper::getComponent('com_digicom')->params;
		$this->state	= $this->get('State');
		$this->params = $this->state->get('params');


		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseWarning(500, implode("\n", $errors));

			return false;
		}

		// Process the content plugins.

		JPluginHelper::importPlugin('content');
		$this->item->event = new stdClass;
		$results = $dispatcher->trigger('onDigicomAfterDownloadFilelist', array('com_digicom.product', &$this->item, &$this->params, $offset = 0));
		$this->item->event->afterDownloadFilelist = trim(implode("\n", $results));

		$results = $dispatcher->trigger('onDigicomBeforeDownloadFilelist', array('com_digicom.product', &$this->item, &$this->params, $offset = 0));
		$this->item->event->BeforeDownloadFilelist = trim(implode("\n", $results));

		$template = new DigiComSiteHelperTemplate($this);
		$template->rander('download');

		$this->_prepareDocument();
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
			$this->params->def('page_heading', JText::_('COM_DIGICOM_DOWNLOADS_PAGE_TITLE'));
		}

		$title = $this->params->get('page_title', '');

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

		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}

	}
}
