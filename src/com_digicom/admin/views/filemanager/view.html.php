<?php
/**
 * @package		DigiCom
 * @author 		ThemeXpert http://www.themexpert.com
 * @copyright	Copyright (c) 2010-2015 ThemeXpert. All rights reserved.
 * @license 	GNU General Public License version 3 or later; see LICENSE.txt
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

jimport ("joomla.application.component.view");

class DigiComViewFileManager extends JViewLegacy {

	function display($tpl =  null){
		if (!JFactory::getUser()->authorise('core.filemanager', 'com_digicom'))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
		
		JToolBarHelper::title(JText::_('COM_DIGICOM_FILE_MANAGER_TOOLBAR_TITLE_SITE'), 'generic.png');

		$bar = JToolBar::getInstance('toolbar');
		// Instantiate a new JLayoutFile instance and render the layout
		$layout = new JLayoutFile('toolbar.title');
		$title=array(
			'title' => JText::_( 'COM_DIGICOM_FILE_MANAGER_TOOLBAR_TITLE' ),
			'class' => 'title'
		);
		$bar->appendButton('Custom', $layout->render($title), 'title');

		$layout = new JLayoutFile('toolbar.settings');
		$bar->appendButton('Custom', $layout->render(array()), 'settings');

		$mainframe = JFactory::getApplication();
    $user = JFactory::getUser();
    $document = JFactory::getDocument();
    $document->addStyleSheet(JURI::root(true).'/media/com_digicom/css/smoothness/jquery-ui.css?v=1.8.0');
    $document->addStyleSheet(JURI::root(true).'/media/com_digicom/css/theme.css?v=2.7.0');
    $document->addStyleSheet(JURI::root(true).'/media/com_digicom/css/elfinder.min.css?v=2.7.0');

		if ($document->getType() == 'html')
		{
      $document->addScript(JURI::root(true).'/media/com_digicom/js/jquery-ui-1.8.24.custom.min.js');
      $document->addScript(JURI::root(true).'/media/com_digicom/js/elfinder.js?v=1.0.0');
  	}

    $type = JRequest::getCmd('type');
    $fieldID = JRequest::getCmd('fieldID');
		$mimes = '';

    $this->assignRef('mimes', $mimes);
    $this->assignRef('type', $type);
    $this->assignRef('fieldID', $fieldID);

		$tmpl = JRequest::getCmd('tmpl','');
		if($tmpl != 'component'){
			DigiComHelperDigiCom::addSubmenu('filemanager');
			$this->sidebar = DigiComHelperDigiCom::renderSidebar();
		}

		parent::display($tpl);
	}

	function vimeo($tpl = null) {
		$id = JRequest::getVar('id', '0');
		$this->assignRef('id', $id);
		parent::display($tpl);
	}
}
