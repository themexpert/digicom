<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class DigiComViewConfigs extends JViewLegacy
{

	function display($tpl = null)
	{
		$layout = JFactory::getApplication()->input->set('layout', '');
		if($layout == 'templatepreview'){
			parent::display($tpl);
		}


		$form = null;
		$component = null;

		try
		{
			$form = $this->get('Form');
			$component = $this->get('Component');
			$user = JFactory::getUser();
		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');

			return false;
		}
		
		// Bind the form to the data.

		if ($form && $component->params)
		{
			$form->bind($component->params);
		}

		$this->form = &$form;
		$this->component = &$component;

		$this->userIsSuperAdmin = $user->authorise('core.admin');
		$this->currentComponent = JFactory::getApplication()->input->get('component');
		$this->return = JFactory::getApplication()->input->get('return', '', 'base64');

		JFactory::getApplication()->input->set('hidemainmenu', true);

		//set toolber
		$this->addToolbar();

		DigiComHelperDigiCom::addSubmenu('configs');
		$this->sidebar = JHtmlSidebar::render();

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
		*
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('COM_DIGICOM_SETTINGS_TOOLBAR_TITLE_SITE'));

		$bar = JToolBar::getInstance('toolbar');
		// Instantiate a new JLayoutFile instance and render the layout
		$layout = new JLayoutFile('toolbar.title');
		$title=array(
			'title' => JText::_( 'COM_DIGICOM_SETTINGS_TOOLBAR_TITLE' ),
			'class' => 'title'
		);
		$bar->appendButton('Custom', $layout->render($title), 'title');

		$layout = new JLayoutFile('toolbar.settings');
		$bar->appendButton('Custom', $layout->render(array()), 'settings');

		JToolBarHelper::save('configs.save');
		JToolBarHelper::apply('configs.apply');
		JToolBarHelper::divider();

		JToolBarHelper::cancel('configs.cancel', 'JTOOLBAR_CLOSE');
	}

}

?>
