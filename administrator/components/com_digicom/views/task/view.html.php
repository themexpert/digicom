<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class DigiComViewTask extends JViewLegacy {

	protected $source;

	function display($tpl =  null){

		$input = JFactory::getApplication()->input;
		$this->source = $input->get('source','');

		// Prepare title
		if(empty($this->source)){
			$pagetitle = JText::_('COM_DIGICOM_TASK_TOOLBAR_TITLE');
		}else{
			JFactory::getLanguage()->load('plg_digicom_'.$this->source, JPATH_ADMINISTRATOR);
			$pagetitle = JText::_('PLG_DIGICOM_'.strtoupper($this->source).'_TOOLBAR_TITLE');
		}

		JToolBarHelper::title($pagetitle, 'generic.png');

		// Prepare the view from plugin
		// $this->info			= $this->get('info');
		// $this->directory	= $this->get('directory');
		// $this->plugins	= $this->get('plugins');

		// Set the title toolber
		$bar = JToolBar::getInstance('toolbar');
		// Instantiate a new JLayoutFile instance and render the layout
		$layout = new JLayoutFile('toolbar.title');
		$title=array(
			'title' => $pagetitle,
			'class' => 'title'
		);
		$bar->appendButton('Custom', $layout->render($title), 'title');

		$layout = new JLayoutFile('toolbar.settings');
		$bar->appendButton('Custom', $layout->render(array()), 'settings');

		DigiComHelperDigiCom::addSubmenu('task');
		$this->sidebar = DigiComHelperDigiCom::renderSidebar();

		parent::display($tpl);
	}

}
