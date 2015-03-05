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

jimport ("joomla.application.component.view");

class DigiComAdminViewFileManager extends DigiComView {

	function display($tpl =  null){
		JToolBarHelper::title(JText::_('COM_DIGICOM_FILE_MANAGER'), 'generic.png');
		

		// Access check.
		if (!JFactory::getUser()->authorise('digicom.filemanager', 'com_digicom'))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}

		$bar = JToolBar::getInstance('toolbar');
		// Instantiate a new JLayoutFile instance and render the layout
		$layout = new JLayoutFile('toolbar.title');
		$title=array(
			'title' => JText::_( 'COM_DIGICOM_FILE_MANAGER' ),
			'class' => 'title'
		);
		$bar->appendButton('Custom', $layout->render($title), 'title');
		
		$layout = new JLayoutFile('toolbar.settings');
		$bar->appendButton('Custom', $layout->render(array()), 'settings');
		
		$mainframe = JFactory::getApplication();
        $user = JFactory::getUser();
        $document = JFactory::getDocument();
        $document->addStyleSheet(JURI::root(true).'/media/digicom/assets/css/smoothness/jquery-ui.css?v=1.8.0');
        $document->addStyleSheet(JURI::root(true).'/media/digicom/assets/css/theme.css?v=2.7.0');
        $document->addStyleSheet(JURI::root(true).'/media/digicom/assets/css/elfinder.min.css?v=2.7.0');
        
		if ($document->getType() == 'html')
		{
            $document->addScript(JURI::root(true).'/media/digicom/assets/js/jquery-ui-1.8.24.custom.min.js');
            $document->addScript(JURI::root(true).'/media/digicom/assets/js/elfinder.js?v=1.0.0');
        }
		
        $type = JRequest::getCmd('type');
        $fieldID = JRequest::getCmd('fieldID');
		$mimes = '';
        
        $this->assignRef('mimes', $mimes);
        $this->assignRef('type', $type);
        $this->assignRef('fieldID', $fieldID);
		
		$tmpl = JRequest::getCmd('tmpl','');
		if($tmpl != 'component'){
			DigiComAdminHelper::addSubmenu('filemanager');
			$this->sidebar = DigiComAdminHelper::renderSidebar();
		}
		
		parent::display($tpl);
	}

	function vimeo($tpl = null) {
		$id = JRequest::getVar('id', '0');
		$this->assignRef('id', $id);
		parent::display($tpl);
	}
}
