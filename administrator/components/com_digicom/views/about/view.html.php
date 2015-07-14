<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

class DigiComViewAbout extends JViewLegacy {

	/**
	* @var array somme system values
	*/
	protected $info = null;

	/**
	 * @var array informations about writable state of directories
	 */
	protected $directory = null;

	function display($tpl =  null){

		$this->info			= $this->get('info');
		$this->directory	= $this->get('directory');
		$this->plugins	= $this->get('plugins');

		JToolBarHelper::title(JText::_('COM_DIGICOM_ABOUT_TOOLBAR_TITLE'), 'generic.png');

		$bar = JToolBar::getInstance('toolbar');
		// Instantiate a new JLayoutFile instance and render the layout
		$layout = new JLayoutFile('toolbar.title');
		$title=array(
			'title' => JText::_( 'COM_DIGICOM_ABOUT_TOOLBAR_TITLE' ),
			'class' => 'title'
		);
		$bar->appendButton('Custom', $layout->render($title), 'title');

		$layout = new JLayoutFile('toolbar.settings');
		$bar->appendButton('Custom', $layout->render(array()), 'settings');

		DigiComHelperDigiCom::addSubmenu('about');
		$this->sidebar = DigiComHelperDigiCom::renderSidebar();

		parent::display($tpl);
	}

}
