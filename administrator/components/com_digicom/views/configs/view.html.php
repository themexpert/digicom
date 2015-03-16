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
class DigiComViewConfigs extends JViewLegacy 
{

	function display($tpl = null)
	{
		//$comInfo = JComponentHelper::getComponent('com_digicom');
		//print_r ( $comInfo->params );die;
		
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

	function supportedsites($tpl = null)
	{
		parent::display($tpl);
	}
	
	/**
	 * Add the page title and toolbar.
		*
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('VIEWDSADMINSETTINGS'));

		$bar = JToolBar::getInstance('toolbar');
		// Instantiate a new JLayoutFile instance and render the layout
		$layout = new JLayoutFile('toolbar.title');
		$title=array(
			'title' => JText::_( 'VIEWDSADMINSETTINGS' ),
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