<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class DigiComViewLicense extends JViewLegacy {

	protected $form;

	function display ($tpl =  null )
	{
		$db = JFactory::getDBO();
		$this->item = $this->get('Item');
		$this->params = $this->get("State");
		$this->form		= $this->get('Form');

		$text = JText::_('COM_DIGICOM_LICENSE_EDIT');

		JToolBarHelper::title($text);

		$bar = JToolBar::getInstance('toolbar');
		$layout = new JLayoutFile('toolbar.title');
		$title 	= array(
					'title' => $text,
					'class' => 'title'
				);
		$bar->appendButton('Custom', $layout->render($title), 'title');

		$layout = new JLayoutFile('toolbar.settings');
		$bar->appendButton('Custom', $layout->render(array()), 'settings');

		// $layout = new JLayoutFile('toolbar.video');
		// $bar->appendButton('Custom', $layout->render(array()), 'video');

		JToolBarHelper::apply('license.apply');
		JToolBarHelper::save('license.save');

		JToolBarHelper::divider();
		JToolBarHelper::cancel ('license.cancel');

		DigiComHelperDigiCom::addSubmenu('licenses');
		$this->sidebar = DigiComHelperDigiCom::renderSidebar();
		JFactory::getApplication()->input->set('hidemainmenu', true);

		parent::display($tpl);
	}





}
