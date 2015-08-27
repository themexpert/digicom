<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class DigiComViewProfile extends JViewLegacy {


	protected $data;

	protected $form;

	protected $params;

	protected $state;

	public $document;

	public $configs;

	/**
	 * Method to display the view.
	 *
	 * @param   string  $tpl  The template file to include
	 *
	 * @return  mixed
	 *
	 * @since   1.6
	 */
	public function display($tpl = null)
	{
		// Get the view data.
		$app = JFactory::getApplication();
		$input = $app->input;

		$this->data		= $this->get('Data');
		$this->form		= $this->get('Form');
		$this->state	= $this->get('State');
		$this->params	= $this->state->get('params');
		$this->configs	=  JComponentHelper::getComponent('com_digicom')->params;

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));

			return false;
		}

		// Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

		$template = new DigiComSiteHelperTemplate($this);
		$template->rander('profile');

		$this->prepareDocument();

		return parent::display($tpl);
	}

	/**
	 * Prepares the document
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function prepareDocument()
	{
		$app   = JFactory::getApplication();
		$menus = $app->getMenu();
		$user  = JFactory::getUser();
		$title = null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		$title = $menu->params->get('page_title');
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

		if ($menu->params->get('menu-meta_description'))
		{
			$this->document->setDescription($menu->params->get('menu-meta_description'));
		}

		if ($menu->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $menu->params->get('menu-meta_keywords'));
		}

		if ($menu->params->get('robots'))
		{
			$this->document->setMetadata('robots', $menu->params->get('robots'));
		}
	}


}
